<?php

namespace App\Support;

use App\Models\ConversationAnalysis;

class CustomerAnalysisVisibility
{
    public static function canViewEmployeePerformance(?int $viewerMembershipId, ConversationAnalysis $analysis, bool $isEmployer): bool
    {
        if ($isEmployer) {
            return true;
        }

        if ($viewerMembershipId === null) {
            return false;
        }

        return $analysis->organization_user_id === $viewerMembershipId;
    }

    public static function mode(?int $viewerMembershipId, ConversationAnalysis $analysis, bool $isEmployer): string
    {
        return self::canViewEmployeePerformance($viewerMembershipId, $analysis, $isEmployer)
            ? 'full'
            : 'customer-only';
    }
}
