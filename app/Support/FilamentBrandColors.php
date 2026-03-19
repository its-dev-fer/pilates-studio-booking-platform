<?php

namespace App\Support;

use Filament\Support\Colors\Color;

/**
 * Paleta compartida para paneles Filament (dashboard empleados + portal clientes).
 * Alineada con la landing: primary #5e6b58 y neutros cálidos (stone).
 */
final class FilamentBrandColors
{
    /**
     * @return array<string, array<int, string>>
     */
    public static function panelColors(): array
    {
        return [
            'primary' => Color::hex('#5e6b58'),
            'gray' => Color::Stone,
        ];
    }
}
