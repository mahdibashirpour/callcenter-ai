<?php

namespace App\Infrastructure\Voip;

use App\Domain\Voip\Contracts\VoipAdapterInterface;
use App\Domain\Voip\Exceptions\VoipAdapterInstantiationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Log;
use Throwable;

class VoipAdapterFactory
{
    public function __construct(
        private Container $container,
    ) {}

    public function make(?string $adapterClass = null, ?string $context = null): VoipAdapterInterface
    {
        $candidates = $this->candidateClasses($adapterClass);

        foreach ($candidates as $class) {
            try {
                $adapter = $this->instantiate($class);

                Log::info('VoIP adapter selected', [
                    'adapter_class' => $class,
                    'context' => $context,
                ]);

                return $adapter;
            } catch (VoipAdapterInstantiationException $exception) {
                Log::warning('VoIP adapter instantiation failed, trying fallback', [
                    'adapter_class' => $class,
                    'context' => $context,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $fallback = config('voip.fallback_adapter');

        Log::error('All VoIP adapter candidates failed, using final fallback', [
            'fallback_adapter' => $fallback,
            'context' => $context,
            'attempted' => $candidates,
        ]);

        return $this->instantiate($fallback);
    }

    public function isValidAdapterClass(?string $class): bool
    {
        if ($class === null || $class === '') {
            return false;
        }

        try {
            $this->assertValidAdapterClass($class);

            return true;
        } catch (VoipAdapterInstantiationException) {
            return false;
        }
    }

    public function validateConfiguredAdapters(): void
    {
        $classes = collect([
            config('voip.adapter_class'),
            config('voip.default_adapter'),
            config('voip.fallback_adapter'),
            ...array_values(config('voip.adapters', [])),
        ])
            ->filter()
            ->unique()
            ->values();

        foreach ($classes as $class) {
            if (! $this->isValidAdapterClass($class)) {
                Log::warning('VoIP adapter misconfigured at boot', [
                    'adapter_class' => $class,
                ]);
            }
        }
    }

    /** @return list<string> */
    private function candidateClasses(?string $adapterClass): array
    {
        return array_values(array_unique(array_filter([
            $adapterClass,
            config('voip.adapter_class'),
            config('voip.default_adapter'),
            config('voip.fallback_adapter'),
        ])));
    }

    private function instantiate(string $class): VoipAdapterInterface
    {
        $this->assertValidAdapterClass($class);

        try {
            $adapter = $this->container->make($class);
        } catch (Throwable $exception) {
            throw VoipAdapterInstantiationException::instantiationFailed(
                $class,
                $exception->getMessage(),
            );
        }

        if (! $adapter instanceof VoipAdapterInterface) {
            throw VoipAdapterInstantiationException::invalidInterface($class);
        }

        return $adapter;
    }

    private function assertValidAdapterClass(string $class): void
    {
        if (! class_exists($class)) {
            throw VoipAdapterInstantiationException::notFound($class);
        }

        if (! is_subclass_of($class, VoipAdapterInterface::class)) {
            throw VoipAdapterInstantiationException::invalidInterface($class);
        }
    }
}
