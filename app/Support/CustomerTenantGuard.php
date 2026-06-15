<?php

namespace App\Support;

use App\Models\Call;
use App\Models\ConversationAnalysis;
use App\Models\Customer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerTenantGuard
{
    public static function assertCustomerInOrganization(Customer $customer, int $organizationId): void
    {
        if ($customer->organization_id !== $organizationId) {
            throw new NotFoundHttpException;
        }
    }

    public static function assertAnalysisInOrganization(ConversationAnalysis $analysis, int $organizationId): void
    {
        if ($analysis->organization_id !== $organizationId) {
            throw new NotFoundHttpException;
        }
    }

    public static function assertCallBelongsToCustomer(Call $call, Customer $customer): void
    {
        if ($call->organization_id !== $customer->organization_id) {
            throw new \RuntimeException('Call organization does not match customer tenant.');
        }

        if ($call->customer_id !== null && $call->customer_id !== $customer->id) {
            throw new \RuntimeException('Call is already linked to a different customer in this tenant.');
        }
    }

    public static function assertCanLinkCallToCustomer(Call $call, Customer $customer): void
    {
        self::assertCallBelongsToCustomer($call, $customer);
    }

    /** @return array{organization_id: int, normalized_phone: string} */
    public static function tenantPhoneKey(int $organizationId, string $normalizedPhone): array
    {
        return [
            'organization_id' => $organizationId,
            'normalized_phone' => $normalizedPhone,
        ];
    }
}
