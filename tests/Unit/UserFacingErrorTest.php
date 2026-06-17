<?php

namespace Tests\Unit;

use App\Support\UserFacingError;
use Tests\TestCase;

class UserFacingErrorTest extends TestCase
{
    public function test_upload_error_hides_technical_details_in_production(): void
    {
        config(['app.debug' => false]);

        $this->assertSame(
            __('ui.processing.upload_error'),
            UserFacingError::upload('SQLSTATE connection refused')
        );
    }

    public function test_processing_error_hides_technical_details_in_production(): void
    {
        config(['app.debug' => false]);

        $this->assertSame(
            __('ui.processing.error'),
            UserFacingError::processing('No recording URL available for call.')
        );
    }
}
