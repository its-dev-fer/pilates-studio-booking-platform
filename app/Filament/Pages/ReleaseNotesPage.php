<?php

namespace App\Filament\Pages;

use App\Support\ReleaseNotes;
use Filament\Panel;
use Filament\Pages\Page;

class ReleaseNotesPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = null;

    protected string $view = 'filament.pages.release-notes-page';

    protected static ?string $title = 'Cambios y novedades';

    protected static bool $shouldRegisterNavigation = false;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'release-notes';
    }

    protected function getViewData(): array
    {
        return [
            'release' => ReleaseNotes::current(),
        ];
    }
}
