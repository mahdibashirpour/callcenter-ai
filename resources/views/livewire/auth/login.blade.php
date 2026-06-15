<div>
    <div class="saas-card text-center">
        <div class="mx-auto mb-6 flex h-12 w-12 items-center justify-center rounded-lg bg-zinc-900 text-lg font-bold text-white dark:bg-white dark:text-zinc-900">
            {{ strtoupper(substr(config('app.name', 'C'), 0, 1)) }}
        </div>
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">ورود</h1>
        <p class="mt-2 text-sm text-zinc-500">ایمیل و رمز عبور خود را وارد کنید.</p>
    </div>

    <form wire:submit="authenticate" class="saas-card mt-6 space-y-5">
        <div>
            <label class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">ایمیل</label>
            <input wire:model="email" type="email" class="saas-input" autocomplete="email" required>
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">رمز عبور</label>
            <input wire:model="password" type="password" class="saas-input" autocomplete="current-password" required>
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="saas-btn-primary w-full" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="authenticate">ورود</span>
            <span wire:loading wire:target="authenticate">در حال ورود...</span>
        </button>
    </form>
</div>
