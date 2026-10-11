<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns = [
            ['name' => 'Eid Collection Launch', 'channel' => 'email', 'audience_count' => 4200, 'status' => 'ended', 'starts_at' => Carbon::now()->subMonths(2), 'ends_at' => Carbon::now()->subMonths(2)->addWeek()],
            ['name' => 'Sunglasses Flash Sale', 'channel' => 'sms', 'audience_count' => 3100, 'status' => 'active', 'starts_at' => Carbon::now()->subDays(3), 'ends_at' => Carbon::now()->addDays(4)],
            ['name' => 'Back-to-School Blue-Light Glasses', 'channel' => 'social', 'audience_count' => 2600, 'status' => 'scheduled', 'starts_at' => Carbon::now()->addWeeks(2), 'ends_at' => Carbon::now()->addWeeks(4)],
            ['name' => 'New Customer Welcome Series', 'channel' => 'email', 'audience_count' => 900, 'status' => 'draft', 'starts_at' => null, 'ends_at' => null],
        ];

        foreach ($campaigns as $campaign) {
            Campaign::updateOrCreate(['name' => $campaign['name']], $campaign);
        }
    }
}
