<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm dark:border-stone-800 dark:bg-stone-900">
            <p class="text-sm text-stone-600 dark:text-stone-300">
                <span class="font-semibold">Version:</span> {{ $release['version'] }}
            </p>
            <p class="mt-1 text-sm text-stone-600 dark:text-stone-300">
                <span class="font-semibold">Commit actual:</span> {{ $release['commit_hash'] }} - {{ $release['commit_subject'] }}
            </p>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm dark:border-stone-800 dark:bg-stone-900">
            <h3 class="text-base font-semibold text-stone-900 dark:text-stone-100">Novedades y mejoras</h3>

            @if (! empty($release['recent_commits']))
                <ul class="mt-4 space-y-3">
                    @foreach ($release['recent_commits'] as $commit)
                        <li class="rounded-lg border border-stone-100 p-3 dark:border-stone-800">
                            <p class="text-xs text-stone-500 dark:text-stone-400">{{ $commit['date'] }} - {{ $commit['hash'] }}</p>
                            <p class="mt-1 text-sm text-stone-800 dark:text-stone-200">{{ $commit['subject'] }}</p>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-3 text-sm text-stone-500 dark:text-stone-400">Correcciones y mejoras realizadas en el sistema.</p>
            @endif
        </div>
    </div>
</x-filament-panels::page>
