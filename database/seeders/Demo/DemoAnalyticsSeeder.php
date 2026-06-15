<?php

namespace Database\Seeders\Demo;

use App\Models\Organization;
use App\Support\Seeding\DemoAnalyticsBuilder;
use App\Support\Seeding\DemoCatalog;
use Illuminate\Database\Seeder;

class DemoAnalyticsSeeder extends Seeder
{
    public function run(): void
    {
        $builder = app(DemoAnalyticsBuilder::class);

        foreach (DemoCatalog::organizations() as $index => $definition) {
            $organization = Organization::query()
                ->where('is_demo', true)
                ->where('title', $definition['title'])
                ->first();

            if (! $organization) {
                continue;
            }

            $this->command?->info("Seeding analytics for {$organization->title}...");

            $builder->seedForOrganization($organization, $index + 1);
        }
    }
}
