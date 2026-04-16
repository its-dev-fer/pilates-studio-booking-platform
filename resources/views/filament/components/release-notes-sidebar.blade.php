<div class="mx-3 mb-3 text-xs text-stone-600 dark:text-stone-300">
    <a
        href="{{ \App\Filament\Pages\ReleaseNotesPage::getUrl(panel: 'dashboard') }}"
        class="font-semibold text-primary underline-offset-2 hover:underline"
    >
        Cambios y novedades
    </a>
    <p class="mt-1">
        <span class="font-semibold">Version:</span> {{ $release['version'] }}
    </p>
</div>
