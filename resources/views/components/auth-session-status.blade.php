@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-100 dark:bg-gray-800 dark:text-emerald-400 dark:border-emerald-900']) }} role="alert">
        <svg class="flex-shrink-0 inline w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
        </svg>
        <span class="sr-only">Success</span>
        <div>
            <span class="font-semibold">{{ $status }}</span>
        </div>
    </div>
@endif
