@if ($impersonationContext ?? null)
    <div class="impersonation-banner" role="alert">
        <div class="impersonation-banner__content">
            <div class="impersonation-banner__info">
                <span class="impersonation-banner__badge">ورود به‌جای کاربر</span>
                <x-saas.avatar :user="$impersonationContext['target']" size="sm" />
                <div>
                    <p class="impersonation-banner__title">
                        شما در حال حاضر به‌جای این کاربر وارد شده‌اید:
                        <strong>{{ $impersonationContext['target']->name }}</strong>
                        ({{ $impersonationContext['target']->role->label() }})
                    </p>
                    <p class="impersonation-banner__meta">
                        کاربر اصلی: <strong>{{ $impersonationContext['original_admin']->name }}</strong>
                        ({{ $impersonationContext['original_admin']->role->label() }})
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('impersonation.stop') }}">
                @csrf
                <button type="submit" class="impersonation-banner__button">
                    بازگشت به حساب مدیریت
                </button>
            </form>
        </div>
    </div>
@endif
