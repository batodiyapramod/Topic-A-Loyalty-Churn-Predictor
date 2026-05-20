<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoyaltyMember;
use App\Services\SynapCores\SynapCoresClient;

class SynapCoresSeedCommand extends Command
{
    protected $signature = 'synapcores:seed';
    protected $description = 'Seed local database and mirror records into SynapCores AIDB';

    public function handle(SynapCoresClient $client)
    {
        // 1. Wipe local tables to start completely fresh
        $this->warn('Wiping existing local loyalty member logs...');
        LoyaltyMember::truncate();

        $this->info('Generating 5,000 loyalty members with mathematical churn indicators...');

        $tiers = ['Bronze', 'Silver', 'Gold', 'Platinum'];
        $batch = [];

        for ($i = 0; $i < 5000; $i++) {
            $tier = $tiers[array_rand($tiers)];
            $tenure = rand(1, 48);

            // Injecting explicit behavioral signal
            $isLowEngagement = (rand(1, 100) <= 75);
            if ($isLowEngagement) {
                $visits = rand(0, 2);
                $spend = rand(5, 35);
                $churned = (rand(1, 100) <= 85);
            } else {
                $visits = rand(4, 25);
                $spend = rand(60, 450);
                $churned = (rand(1, 100) <= 5);
            }

            $batch[] = [
                'tier' => $tier,
                'tenure_months' => $tenure,
                'visits_30d' => $visits,
                'spend_30d' => $spend,
                'last_visit_at' => now()->subDays(rand(0, 45)),
                'churned' => $churned,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) === 1000) {
                LoyaltyMember::insert($batch);
                $batch = [];
            }
        }

        // Catch remaining items in memory loop
        if (count($batch) > 0) {
            LoyaltyMember::insert($batch);
        }

        $this->info('Local relational tables populated successfully.');
        $this->info('Rebuilding SynapCores target mirror schema containers...');

        // Re-initialize tables inside AIDB
        $client->query("DROP TABLE IF EXISTS loyalty_members_mirror");
        $client->query("CREATE TABLE loyalty_members_mirror (
            id INT, tier VARCHAR(20), tenure_months INT, visits_30d INT, spend_30d FLOAT, churned INT
        )");

        // 2. Read back generated IDs from DB to sync them accurately down to SynapCores
        $this->info('Streaming data records directly into SynapCores sandbox...');

        LoyaltyMember::chunk(250, function ($members) use ($client) {
            foreach ($members as $member) {
                $client->query(
                    "INSERT INTO loyalty_members_mirror VALUES (?, ?, ?, ?, ?, ?)",
                    [
                        (int)$member->id,
                        (string)$member->tier,
                        (int)$member->tenure_months,
                        (int)$member->visits_30d,
                        (float)$member->spend_30d,
                        (int)$member->churned
                    ]
                );
            }
        });

        $this->info('Data sync fully synchronized.');
    }
}
