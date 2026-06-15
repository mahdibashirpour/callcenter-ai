<?php

namespace App\Domain\Voip\Enums;

enum VoipOperation: string
{
    case TestConnection = 'test_connection';
    case MakeCall = 'make_call';
    case HangupCall = 'hangup_call';
    case GetCallDetails = 'get_call_details';
    case GetCallRecording = 'get_call_recording';
    case GetActiveCalls = 'get_active_calls';
    case CreateExtension = 'create_extension';
    case UpdateExtension = 'update_extension';
    case GetExtensions = 'get_extensions';
    case HandleWebhook = 'handle_webhook';
    case SyncData = 'sync_data';

    public function label(): string
    {
        return match ($this) {
            self::TestConnection => 'آزمایش اتصال',
            self::MakeCall => 'برقراری تماس',
            self::HangupCall => 'قطع تماس',
            self::GetCallDetails => 'دریافت جزئیات تماس',
            self::GetCallRecording => 'دریافت ضبط تماس',
            self::GetActiveCalls => 'دریافت تماس‌های فعال',
            self::CreateExtension => 'ایجاد داخلی',
            self::UpdateExtension => 'به‌روزرسانی داخلی',
            self::GetExtensions => 'دریافت داخلی‌ها',
            self::HandleWebhook => 'پردازش Webhook',
            self::SyncData => 'همگام‌سازی داده',
        };
    }
}
