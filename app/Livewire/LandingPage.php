<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LandingPage extends Component
{
    // Propiedades del Formulario
    public $tenants;
    public $selectedTenant = null;
    public $selectedDate = null;
    public $selectedSlot = null;
    public $name;
    public $last_name;
    public $email;
    public $phone;

    // Estado de la UI
    public $availableSlots = [];
    public $maxDate;

    public function mount()
    {
        $this->tenants = Tenant::all();
        // Límite: Solo el mes en curso
        $this->maxDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    // Se dispara cuando el usuario cambia la fecha o la sucursal
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['selectedTenant', 'selectedDate'])) {
            $this->calculateAvailableSlots();
        }
    }

    public function render()
    {
        return view('livewire.landing-page')->layout('layouts.landing');
    }

    public function calculateAvailableSlots()
    {
        if (!$this->selectedTenant || !$this->selectedDate) return;

        $tenant = Tenant::find($this->selectedTenant);
        $date = Carbon::parse($this->selectedDate);
        $today = Carbon::now();
        $capacity = $tenant->capacity_per_slot ?? 5;

        if ($date->gt($today->copy()->endOfMonth()) || $date->lt($today->copy()->startOfDay())) {
            $this->availableSlots = [];
            return;
        }

        $appointments = Appointment::where('tenant_id', $tenant->id)
            ->whereDate('date', $date->format('Y-m-d'))
            ->where('status', 'scheduled')
            ->get();

        $dayOfWeek = $date->dayOfWeekIso;
        $businessHours = collect($tenant->business_hours ?? [])->firstWhere('day', $dayOfWeek);

        if (!$businessHours || empty($businessHours['slots'])) {
            $this->availableSlots = [];
            return;
        }

        $slots = [];

        foreach ($businessHours['slots'] as $timeString) {
            $slotTime = Carbon::parse($date->format('Y-m-d') . ' ' . $timeString);
            
            if ($slotTime->isPast()) {
                continue;
            }

            $bookedCount = $appointments->filter(function ($app) use ($timeString) {
                return Carbon::parse($app->time_slot)->format('H:i') === $timeString;
            })->count();

            $availableSpots = $capacity - $bookedCount;

            if ($availableSpots > 0) {
                // Definir color para la vista de Livewire
                if ($availableSpots >= 3) $color = 'emerald';
                elseif ($availableSpots == 2) $color = 'amber';
                else $color = 'orange';

                $slots[] = [
                    'time' => $timeString,
                    'formatted' => date('h:i A', strtotime($timeString)),
                    'available' => $availableSpots,
                    'color' => $color
                ];
            }
        }

        $this->availableSlots = $slots; // Ahora es un arreglo de arreglos, no solo un texto
    }

    public function selectSlot($slot)
    {
        $this->selectedSlot = $slot;
    }

    public function bookAppointment()
    {
        $this->validate([
            'selectedTenant' => 'required',
            'selectedDate' => 'required|date',
            'selectedSlot' => 'required',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
        ]);

        $user = User::where('email', $this->email)->first();

        // Regla 1: Si el usuario existe, redirigir al login
        if ($user) {
            session()->flash('message', 'Parece que ya tienes una cuenta. Inicia sesión para continuar con tu reserva.');
            return redirect()->route('filament.clientes.auth.login'); // Ruta de login del panel clientes
        }

        // Regla 2: Si no existe, crearlo
        $password = Str::random(8);
        $user = User::create([
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($password),
        ]);

        // Asignar rol y vincular a todos los tenants
        $user->assignRole('cliente');
        $user->tenants()->sync(Tenant::pluck('id')->toArray());

        Auth::login($user);
        // TODO: Enviar correo con credenciales
        // Mail::to($user->email)->send(new WelcomeNewClientMail($user, $password));

        // Regla 3: Un usuario nuevo tiene 0 créditos. Redirigir a comprar.
        // Guardamos en sesión la intención de reserva para retomarla post-pago.
        session(['pending_appointment' => [
            'tenant_id' => $this->selectedTenant,
            'date' => $this->selectedDate,
            'time_slot' => $this->selectedSlot,
        ]]);

        session()->flash('success_registro', '¡Cuenta creada! Tu contraseña temporal es: ' . $password . ' (Cópiala). Redirigiendo al pago...');

        return redirect()->route('checkout.credits');
    }
}
