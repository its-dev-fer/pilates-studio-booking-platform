<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
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

        return $this->tenants()->whereKey($tenant)->exists();
    }

    public function getTenants(Panel $panel): array|Collection
    {
        // Si es admin, ve todos los tenants disponibles. Si no, solo a los que está asignado.
        if ($this->hasRole('admin')) {
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
