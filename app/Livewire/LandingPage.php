<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Category;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

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
        $storeCategories = Category::query()
            ->whereHas('products', function ($query) {
                $query->where('is_active', true)->where('stock', '>', 0);
            })
            ->withCount([
                'products as active_products_count' => function ($query) {
                    $query->where('is_active', true)->where('stock', '>', 0);
                },
            ])
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('livewire.landing-page', [
            'storeCategories' => $storeCategories,
        ])->layout('layouts.landing');
    }

    public function calculateAvailableSlots()
    {
        if (! $this->selectedTenant || ! $this->selectedDate) {
            return;
        }

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

        if (! $businessHours || empty($businessHours['slots'])) {
            $this->availableSlots = [];

            return;
        }

        $slots = [];

        foreach ($businessHours['slots'] as $timeString) {
            $slotTime = Carbon::parse($date->format('Y-m-d').' '.$timeString);

            if ($slotTime->isPast()) {
                continue;
            }

            $bookedCount = $appointments->filter(function ($app) use ($timeString) {
                return Carbon::parse($app->time_slot)->format('H:i') === $timeString;
            })->count();

            $availableSpots = $capacity - $bookedCount;

            if ($availableSpots > 0) {
                // Definir color para la vista de Livewire
                if ($availableSpots >= 3) {
                    $color = 'emerald';
                } elseif ($availableSpots == 2) {
                    $color = 'amber';
                } else {
                    $color = 'orange';
                }

                $slots[] = [
                    'time' => $timeString,
                    'formatted' => date('h:i A', strtotime($timeString)),
                    'available' => $availableSpots,
                    'color' => $color,
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
            session(['pending_booking' => [
                'tenant_id' => $this->selectedTenant,
                'date' => $this->selectedDate,
                'time_slot' => $this->selectedSlot,
            ]]);

            session()->save();

            if (auth()->check() && auth()->id() === $user->id) {
                // Cambia '/dashboard' por la URL real de tu panel de clientes
                return redirect('/clientes');
            }

            // 2. Le avisamos y lo mandamos al login de tu panel de clientes
            Notification::make()
                ->title('¡Hola de nuevo!')
                ->body('Detectamos que ya tienes una cuenta. Por favor, inicia sesión para confirmar tu lugar de inmediato.')
                ->warning()
                ->duration(8000) // Le damos 8 segundos para que lo alcance a leer
                ->persistent()
                ->send();

            return redirect()->route('filament.clientes.auth.login'); // Ruta de login del panel clientes
        }

        // Regla 2: Si no existe, crearlo
        $user = User::create([
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
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

        session()->flash('success_registro', '¡Cuenta creada! Redirigiendo al pago...');

        return redirect()->route('checkout.credits');
    }

    public function selectTenant($tenantId)
    {
        $this->selectedTenant = $tenantId;
        $this->selectedSlot = null; // Borramos la hora seleccionada
        $this->calculateAvailableSlots(); // Recalculamos
    }

    // Se ejecuta al cambiar el input de fecha (gracias al wire:model.live)
    public function updatedSelectedDate($value)
    {
        $this->selectedSlot = null;
        $this->calculateAvailableSlots();
    }
}
