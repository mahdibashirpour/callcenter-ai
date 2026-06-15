@props([
    'wireModel' => 'audio',
    'state' => 'idle',
    'fileName' => null,
    'fileSize' => null,
    'disabled' => false,
])

@php
    $accept = '.mp3,.wav,.m4a,.ogg,.flac,audio/*';
    $hasFile = filled($fileName);
    $isDragging = false;
    $formatSize = function (?int $bytes): string {
        if (! $bytes) {
            return '';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1).' مگابایت';
        }

        return number_format($bytes / 1024, 0).' کیلوبایت';
    };
@endphp

<div
    {{ $attributes->merge(['class' => 'w-full']) }}
    x-data="{ dragging: false }"
    x-on:dragover.prevent="if (!{{ $disabled ? 'true' : 'false' }}) dragging = true"
    x-on:dragleave.prevent="dragging = false"
    x-on:drop.prevent="dragging = false; if (!{{ $disabled ? 'true' : 'false' }} && $event.dataTransfer.files.length) { $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true })); }"
>
    <input
        x-ref="fileInput"
        type="file"
        wire:model="{{ $wireModel }}"
        accept="{{ $accept }}"
        class="sr-only"
        @disabled($disabled)
    >

    <div
        role="button"
        tabindex="0"
        @class([
            'audio-dropzone relative flex min-h-[220px] cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed px-6 py-10 text-center transition duration-200',
            'border-zinc-300 bg-zinc-50/50 hover:border-zinc-400 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900/40 dark:hover:border-zinc-600' => ! $hasFile && $state === 'idle' && ! $errors->has($wireModel),
            'border-indigo-300 bg-white dark:border-indigo-500/40 dark:bg-zinc-900' => $hasFile && $state !== 'success',
            'border-emerald-400 bg-emerald-50/60 dark:border-emerald-500/50 dark:bg-emerald-950/20' => $state === 'success' && ! $hasFile,
            'border-red-400 bg-red-50/60 dark:border-red-500/50 dark:bg-red-950/20' => $errors->has($wireModel),
            'pointer-events-none opacity-60' => $disabled,
        ])
        x-bind:class="{ 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-950/40 scale-[1.01] shadow-lg shadow-indigo-500/10': dragging }"
        x-on:click="if (!{{ $disabled ? 'true' : 'false' }}) $refs.fileInput.click()"
        x-on:keydown.enter.prevent="if (!{{ $disabled ? 'true' : 'false' }}) $refs.fileInput.click()"
        x-on:keydown.space.prevent="if (!{{ $disabled ? 'true' : 'false' }}) $refs.fileInput.click()"
    >
        <div wire:loading.flex wire:target="{{ $wireModel }}" class="absolute inset-0 z-10 hidden items-center justify-center rounded-lg bg-white/80 backdrop-blur-sm dark:bg-zinc-900/80">
            <div class="flex flex-col items-center gap-3">
                <span class="audio-dropzone__spinner" aria-hidden="true"></span>
                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">در حال آماده‌سازی فایل…</p>
            </div>
        </div>

        <div wire:loading.flex wire:target="submitForAnalysis" class="absolute inset-0 z-10 hidden items-center justify-center rounded-lg bg-white/80 backdrop-blur-sm dark:bg-zinc-900/80">
            <div class="flex flex-col items-center gap-3">
                <span class="audio-dropzone__spinner" aria-hidden="true"></span>
                <p class="text-sm font-medium text-indigo-700 dark:text-indigo-300">در حال ارسال برای تحلیل هوش مصنوعی…</p>
            </div>
        </div>

        @if ($state === 'success' && ! $hasFile)
            <div class="flex flex-col items-center gap-3">
                <div class="flex h-14 w-14 items-center justify-center rounded-md bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-semibold text-emerald-800 dark:text-emerald-300">فایل با موفقیت ارسال شد</p>
                    <p class="mt-1 text-sm text-emerald-700/80 dark:text-emerald-400/80">تحلیل در حال انجام است — می‌توانید تماس جدید آپلود کنید</p>
                </div>
            </div>
        @elseif ($hasFile)
            <div class="flex w-full max-w-md flex-col items-center gap-4" wire:click.stop>
                <div class="flex w-full items-center gap-4 rounded-lg border border-zinc-200 bg-white p-4 text-start shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-2v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-2c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-medium text-zinc-900 dark:text-white">{{ $fileName }}</p>
                        <p class="mt-0.5 text-xs text-zinc-500">{{ $formatSize($fileSize) }} · آماده تحلیل</p>
                    </div>
                    <button
                        type="button"
                        wire:click="removeAudio"
                        class="rounded-lg p-2 text-zinc-400 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10"
                        title="حذف فایل"
                        aria-label="حذف فایل"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-zinc-500">برای جایگزینی، فایل دیگری بکشید یا کلیک کنید</p>
            </div>
        @else
            <div class="flex flex-col items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-500/25">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 7.5 7.5 12M12 7.5v9" />
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-white">
                        فایل صوتی را اینجا رها کنید
                    </p>
                    <p class="mt-2 text-sm text-zinc-500">
                        یا <span class="font-medium text-indigo-600 dark:text-indigo-400">کلیک کنید</span> و از دستگاه انتخاب نمایید
                    </p>
                </div>
                <p class="rounded-md bg-zinc-100 px-3 py-1 text-xs text-zinc-500 dark:bg-zinc-800">
                    mp3 · wav · m4a · ogg · flac — حداکثر ۵۰ مگابایت
                </p>
            </div>
        @endif
    </div>

    @error($wireModel)
        <p class="mt-3 flex items-center gap-2 text-sm text-red-600 dark:text-red-400">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
            {{ $message }}
        </p>
    @enderror
</div>
