<div class="space-y-8">
    <h1 class="text-3xl font-semibold tracking-tight">مربیگری هوش مصنوعی</h1>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="saas-card">
            <h2 class="font-semibold">نقاط قوت</h2>
            <ul class="mt-4 space-y-2 text-sm">@foreach ($strengths as $item => $count)<li>{{ $item }} <span class="text-zinc-400">({{ $count }})</span></li>@endforeach</ul>
        </div>
        <div class="saas-card">
            <h2 class="font-semibold">نقاط ضعف</h2>
            <ul class="mt-4 space-y-2 text-sm">@foreach ($weaknesses as $item => $count)<li>{{ $item }} <span class="text-zinc-400">({{ $count }})</span></li>@endforeach</ul>
        </div>
        <div class="saas-card">
            <h2 class="font-semibold">پیشنهادها</h2>
            <ul class="mt-4 space-y-2 text-sm">@foreach ($actions as $item => $count)<li>{{ $item }}</li>@endforeach</ul>
        </div>
    </div>
</div>
