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
        if (!$this->selectedTenant || !$this->selectedDate) {
            return;
        }

        $tenant = Tenant::find($this->selectedTenant);
        $date = Carbon::parse($this->selectedDate);
        $today = Carbon::now();

        // Validación 1: Mes en curso y no elegir fechas pasadas
        if ($date->gt($today->copy()->endOfMonth()) || $date->lt($today->copy()->startOfDay())) {
            $this->addError('selectedDate', 'Fecha inválida o fuera del mes en curso.');
            $this->availableSlots = [];
            return;
        }

        // Obtener citas ya agendadas para ese día
        $appointments = Appointment::where('tenant_id', $tenant->id)
            ->whereDate('date', $date->format('Y-m-d'))
            ->where('status', 'scheduled')
            ->get();

        // Validación 2: Capacidad máxima diaria
        if ($appointments->count() >= $tenant->max_appointments_per_day) {
            $this->availableSlots = []; // Forzará a la vista a mostrar que no hay espacio
            return;
        }

        // Validación 3: Extraer horario de apertura del Tenant
        $dayOfWeek = $date->dayOfWeekIso; // 1 (Lunes) a 7 (Domingo)
        $businessHours = collect($tenant->business_hours ?? [])->firstWhere('day', $dayOfWeek);

        // Si no hay configuración para este día, está cerrado
        if (!$businessHours) {
            $this->availableSlots = [];
            return;
        }

        $openTime = Carbon::parse($businessHours['open']);
        $closeTime = Carbon::parse($businessHours['close']);

        $slots = [];
        $currentSlot = $openTime->copy();

        // Generar bloques de 1 hora
        while ($currentSlot->lt($closeTime)) {
            $timeString = $currentSlot->format('H:i');

            // Regla A: Si el día seleccionado es HOY, omitir horarios que ya pasaron
            if ($date->isToday() && $currentSlot->copy()->setDate($date->year, $date->month, $date->day)->isPast()) {
                $currentSlot->addHour();
                continue;
            }

            // Regla B: Omitir si el slot ya fue reservado (1 persona por slot)
            $isBooked = $appointments->contains(function ($appointment) use ($timeString) {
                return Carbon::parse($appointment->time_slot)->format('H:i') === $timeString;
            });

            if (!$isBooked) {
                $slots[] = $timeString;
            }

            $currentSlot->addHour();
        }

        $this->availableSlots = $slots;
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
