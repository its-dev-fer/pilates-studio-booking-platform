<div class="mx-3 mb-3 text-xs text-stone-600 dark:text-stone-300">
    <button
        type="button"
        x-on:click="$dispatch('open-modal', { id: 'release-notes-modal' })"
        class="font-semibold text-primary underline-offset-2 hover:underline"
    >
        Cambios y novedades
    </button>
    <p class="mt-1">
        <span class="font-semibold">Version:</span> {{ $release['version'] }}
    </p>
</div>

<x-filament::modal id="release-notes-modal" width="md">
    <x-slot name="heading">
        Cambios y novedades
    </x-slot>

    <div class="space-y-3 text-sm text-stone-700 dark:text-stone-200">
        @if(!empty($release['recent_commits']))
            <ul class="list-disc space-y-2 pl-5">
                @foreach($release['recent_commits'] as $commit)
                    <li>
                        <span class="text-xs text-stone-500 dark:text-stone-400">{{ $commit['date'] }}</span><br>
                        <span>{{ $commit['subject'] }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-xs text-stone-500 dark:text-stone-400">Correcciones y mejoras.</p>
        @endif
    </div>
</x-filament::modal>
