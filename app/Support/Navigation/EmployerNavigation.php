<?php

namespace App\Support\Navigation;

class EmployerNavigation
{
    public static function items(): array
    {
        return [
            ['label' => 'داشبورد تیم', 'route' => 'employer.dashboard', 'icon' => 'home'],
            ['label' => 'کارشناسان', 'route' => 'employer.employees.index', 'icon' => 'users'],
            ['label' => 'تحلیل تماس‌ها', 'route' => 'employer.intelligence.index', 'icon' => 'sparkles'],
            ['label' => 'مشتریان', 'route' => 'employer.customers.index', 'icon' => 'users'],
            ['label' => 'آپلود دستی تماس', 'route' => 'employer.manual-analyses.index', 'icon' => 'upload'],
            ['label' => 'صف تحلیل تماس', 'route' => 'employer.processing-queue.index', 'icon' => 'cloud'],
            ['label' => 'عملکرد کارشناسان', 'route' => 'employer.intelligence.performance', 'icon' => 'chart'],
            ['label' => 'CRM', 'route' => 'employer.crm.index', 'icon' => 'cloud'],
            ['label' => 'خطوط تلفنی', 'route' => 'employer.voip.index', 'icon' => 'phone'],
            ['label' => 'گزارش‌های مدیریتی', 'route' => 'employer.reports.index', 'icon' => 'document'],
            ['label' => 'اعتبار هوش مصنوعی', 'route' => 'employer.wallet.index', 'icon' => 'wallet'],
        ];
    }
}
