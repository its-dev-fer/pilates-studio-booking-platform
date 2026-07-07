<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\CreditsAssignedMail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use Billable;
    use HasFactory;
    use HasRoles;
    use Notifiable;

    public ?string $plain_password = null;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'phone', // Agregado para el registro de la landing
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- Filament Multitenancy ---

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class);
    }

    public function canAccessTenant(Model $tenant): bool
    {
        // Los administradores son omnipresentes
        if ($this->hasRole('admin')) {
            return true;
        }

        if ($this->hasRole('cliente') && ! $this->tenants()->exists()) {
            return true;
        }

        return $this->tenants()->whereKey($tenant)->exists();
    }

    public function getTenants(Panel $panel): array|Collection
    {
        // Si es admin, ve todos los tenants disponibles. Si no, solo a los que está asignado.
        if ($this->hasRole('admin')) {
            return Tenant::all();
        }

        if ($this->hasRole('cliente') && $this->tenants()->doesntExist()) {
            return Tenant::all();
        }

        return $this->tenants;
    }

    // --- Filament Panel Access ---

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'dashboard') {
            return $this->hasRole(['admin', 'empleado']);
        }

        if ($panel->getId() === 'clientes') {
            return $this->hasRole('cliente');
        }

        return false;
    }

    // --- Relaciones de Negocio ---

    public function credits(): HasMany
    {
        return $this->hasMany(UserCredit::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function creditPurchaseRequests(): HasMany
    {
        return $this->hasMany(CreditPurchaseRequest::class);
    }

    public function creditPackagePurchases(): HasMany
    {
        return $this->hasMany(CreditPackagePurchase::class);
    }

    /**
     * Si el usuario ya obtuvo créditos de paquete (Stripe, transferencia/efectivo aprobados, o compras registradas).
     * No cuenta créditos marcados como especiales (p. ej. cortesías manuales).
     */
    public function hasAcquiredCreditsBefore(): bool
    {
        return $this->credits()
            ->where('is_special', false)
            ->exists()
            || $this->creditPurchaseRequests()
                ->where('status', CreditPurchaseRequest::STATUS_APPROVED)
                ->exists()
            || $this->creditPackagePurchases()->exists();
    }

    public function isNewCreditCustomer(?Carbon $at = null): bool
    {
        $at ??= now();

        if (! $this->created_at || $this->created_at->lt($at->copy()->subDays(7))) {
            return false;
        }

        return ! $this->hasAcquiredCreditsBefore();
    }

    /**
     * Calcula los créditos activos para el tenant actual
     */
    public function activeCredits(int $tenantId): int
    {
        return $this->credits()
            ->where('tenant_id', $tenantId)
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->sum('balance');
    }

    /** @return array<int, array{tenant_id: int, tenant_name: string, balance: int, expires_at: ?Carbon}> */
    public function branchCreditSummary(): array
    {
        return Tenant::query()
            ->orderBy('name')
            ->get()
            ->map(function (Tenant $tenant): array {
                $activeCredits = $this->credits()
                    ->where('tenant_id', $tenant->id)
                    ->where('balance', '>', 0)
                    ->where('expires_at', '>', now())
                    ->get();

                return [
                    'tenant_id' => $tenant->id,
                    'tenant_name' => $tenant->name,
                    'balance' => $activeCredits->sum('balance'),
                    'expires_at' => $activeCredits->max('expires_at'),
                ];
            })
            ->all();
    }

    /**
     * @param  array<int, int>  $affectedTenantIds
     */
    public function sendCreditsAssignedNotification(string $source, array $affectedTenantIds = []): void
    {
        if (! filled($this->email)) {
            return;
        }

        Mail::to($this->email)->send(new CreditsAssignedMail(
            user: $this->fresh(),
            source: $source,
            affectedTenantIds: $affectedTenantIds,
        ));
    }

    public function grantAdminCredits(int $tenantId, int $amount): UserCredit
    {
        $credit = $this->credits()
            ->where('tenant_id', $tenantId)
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->first();

        if ($credit) {
            $credit->update([
                'balance' => $credit->balance + $amount,
                'expires_at' => now()->addDays(30),
            ]);

            return $credit;
        }

        return $this->credits()->create([
            'tenant_id' => $tenantId,
            'balance' => $amount,
            'expires_at' => now()->addDays(30),
            'is_special' => true,
        ]);
    }

    public function revokeAdminCredits(int $tenantId, int $amount): void
    {
        if ($amount > $this->activeCredits($tenantId)) {
            throw new \InvalidArgumentException('Créditos insuficientes en la sucursal seleccionada.');
        }

        $remaining = $amount;

        $this->credits()
            ->where('tenant_id', $tenantId)
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->orderBy('expires_at')
            ->orderBy('id')
            ->get()
            ->each(function (UserCredit $credit) use (&$remaining): void {
                if ($remaining <= 0) {
                    return;
                }

                $deduct = min($credit->balance, $remaining);
                $credit->decrement('balance', $deduct);
                $remaining -= $deduct;
            });
    }

    /**
     * Separa un nombre completo en nombre y apellido(s) para columnas NOT NULL.
     * Si solo hay una palabra, se repite en apellido para cumplir la restricción de BD.
     *
     * @return array{name: string, last_name: string}
     */
    public static function splitFullNameForStorage(string $fullName): array
    {
        $trimmed = trim($fullName);
        if ($trimmed === '') {
            return ['name' => 'Cliente', 'last_name' => 'Cliente'];
        }

        $parts = preg_split('/\s+/u', $trimmed) ?: [];
        $first = array_shift($parts) ?? $trimmed;
        $last = trim(implode(' ', $parts));

        return [
            'name' => $first,
            'last_name' => $last !== '' ? $last : $first,
        ];
    }
}
