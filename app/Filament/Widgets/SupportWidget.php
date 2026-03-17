<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SupportWidget extends Widget
{
    protected string $view = 'filament.widgets.support-widget';

    // Para que aparezca al final del dashboard (opcional, puedes cambiar el número)
    protected static ?int $sort = 10; 

    // Puedes cambiarlo a 1 si prefieres que ocupe solo la mitad de la pantalla
    protected int | string | array $columnSpan = 'full'; 

    // REGLA: Solo visible para Admin y Empleado
    public static function canView(): bool
    {
        $user = auth()->user();
        
        // Asumiendo que usas Spatie Roles
        return $user && $user->hasRole(['admin', 'empleado']);
    }
}
