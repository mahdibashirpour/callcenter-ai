<?php

namespace App\Infrastructure\Voip;

use App\Domain\Voip\Contracts\VoipAdapterInterface;
use App\Domain\Voip\Enums\VoipProviderCode;
use App\Models\VoipProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class VoipAdapterRegistry
{
    /** @var array<string, class-string<VoipAdapterInterface>> */
    private array $adapters = [];

    public function __construct(
        private VoipAdapterFactory $factory,
    ) {
        foreach (config('voip.adapters', []) as $code => $adapterClass) {
            if (is_string($adapterClass) && $adapterClass !== '') {
                $this->adapters[$code] = $adapterClass;
            }
        }
    }

    public function register(VoipProviderCode|string $provider, string $adapterClass): void
    {
        $code = $provider instanceof VoipProviderCode ? $provider->value : $provider;
        $this->adapters[$code] = $adapterClass;
    }

    public function resolve(VoipProviderCode|string $provider, ?string $adapterClass = null): VoipAdapterInterface
    {
        $code = $provider instanceof VoipProviderCode ? $provider->value : $provider;
        $resolvedClass = $adapterClass ?? $this->resolveAdapterClassForProvider($code);

        return $this->factory->make(
            adapterClass: $resolvedClass,
            context: "provider:{$code}",
        );
    }

    public function resolveFromClass(string $adapterClass): VoipAdapterInterface
    {
        return $this->factory->make(
            adapterClass: $adapterClass,
            context: 'explicit_class',
        );
    }

    public function loadFromDatabase(): void
    {
        if (! Schema::hasTable('voip_providers')) {
            return;
        }

        VoipProvider::query()
            ->whereNotNull('adapter_class')
            ->where('adapter_class', '!=', '')
            ->get(['code', 'adapter_class'])
            ->each(function (VoipProvider $provider): void {
                $this->register($provider->code, $provider->adapter_class);

                if (! $this->factory->isValidAdapterClass($provider->adapter_class)) {
                    Log::warning('VoIP provider has invalid adapter_class in database', [
                        'provider_code' => $provider->code,
                        'adapter_class' => $provider->adapter_class,
                    ]);
                }
            });
    }

    /** @return array<string, class-string<VoipAdapterInterface>> */
    public function all(): array
    {
        return $this->adapters;
    }

    private function resolveAdapterClassForProvider(string $code): ?string
    {
        if (isset($this->adapters[$code])) {
            return $this->adapters[$code];
        }

        if (! Schema::hasTable('voip_providers')) {
            return null;
        }

        $adapterClass = VoipProvider::query()
            ->where('code', $code)
            ->value('adapter_class');

        if (is_string($adapterClass) && $adapterClass !== '') {
            $this->adapters[$code] = $adapterClass;

            return $adapterClass;
        }

        return null;
    }
}
