<?php

namespace Database\Seeders\Demo;

use App\Domain\Billing\Enums\WalletTransactionType;
use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\OrganizationWallet;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\Demo\DemoEmployeeProvisioner;
use App\Support\Seeding\DemoAvatarAssigner;
use App\Support\Seeding\DemoCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoOrganizationsSeeder extends Seeder
{
    public function run(): void
    {
        $provisioner = app(DemoEmployeeProvisioner::class);

        foreach (DemoCatalog::organizations() as $index => $definition) {
            $employerProfile = DemoCatalog::employerProfiles()[$index];
            $employerEmail = DemoCatalog::employerEmail($index);

            $employer = User::query()->updateOrCreate(
                ['email' => $employerEmail],
                [
                    'name' => $employerProfile['name'],
                    'password' => Hash::make(DemoCatalog::DEMO_PASSWORD),
                    'role' => UserRole::Employer,
                    'email_verified_at' => now(),
                ],
            );

            $organization = Organization::query()->updateOrCreate(
                ['user_id' => $employer->id],
                [
                    'title' => $definition['title'],
                    'disabled' => false,
                    'is_demo' => true,
                ],
            );

            $this->seedWallet($organization);

            $avatarAssigner = new DemoAvatarAssigner;
            $this->seedEmployees($provisioner, $organization, $index, $avatarAssigner);

            $employer->update([
                'avatar_path' => $avatarAssigner->assign($employerProfile['gender']),
            ]);
        }
    }

    private function seedWallet(Organization $organization): void
    {
        $wallet = OrganizationWallet::query()->firstOrCreate(
            ['organization_id' => $organization->id],
            [
                'balance' => DemoCatalog::WALLET_BALANCE_IRR,
                'currency' => 'IRR',
            ],
        );

        if ((float) $wallet->balance < DemoCatalog::WALLET_BALANCE_IRR) {
            $wallet->update(['balance' => DemoCatalog::WALLET_BALANCE_IRR, 'currency' => 'IRR']);
        }

        WalletTransaction::query()->firstOrCreate(
            [
                'organization_id' => $organization->id,
                'type' => WalletTransactionType::Deposit,
                'description' => 'شارژ اولیه دمو (۲۰٬۰۰۰ تومان)',
            ],
            [
                'amount' => DemoCatalog::WALLET_BALANCE_IRR,
                'balance_before' => 0,
                'balance_after' => DemoCatalog::WALLET_BALANCE_IRR,
                'created_at' => now()->subDays(30),
            ],
        );
    }

    private function seedEmployees(
        DemoEmployeeProvisioner $provisioner,
        Organization $organization,
        int $orgIndex,
        DemoAvatarAssigner $avatarAssigner,
    ): void {
        for ($i = 1; $i <= DemoCatalog::EMPLOYEES_PER_ORGANIZATION; $i++) {
            $provisioner->provision($organization, $i, $orgIndex, $avatarAssigner);
        }
    }
}
