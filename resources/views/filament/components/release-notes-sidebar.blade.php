<div class="mx-3 mb-3 rounded-xl border border-stone-200 bg-white/85 p-3 text-xs text-stone-700 shadow-sm dark:border-white/10 dark:bg-white/5 dark:text-stone-200">
    <p class="font-bold uppercase tracking-wide text-primary">Release Notes</p>
    <p class="mt-2"><span class="font-semibold">Version:</span> {{ $release['version'] }}</p>
    <p class="mt-1"><span class="font-semibold">Branch:</span> {{ $release['branch'] }}</p>
    <p class="mt-1"><span class="font-semibold">Commit:</span> {{ $release['commit_hash'] }}</p>
    <p class="mt-2 leading-relaxed"><span class="font-semibold">Ultimo cambio:</span> {{ $release['commit_subject'] }}</p>
</div>
