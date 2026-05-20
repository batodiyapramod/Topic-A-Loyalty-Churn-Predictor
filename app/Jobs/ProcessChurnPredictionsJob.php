<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\LoyaltyMember;
use App\Models\ChurnPrediction;
use App\Services\SynapCores\SynapCoresClient;

class ProcessChurnPredictionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(SynapCoresClient $client)
    {
        // 1. Fetch the target premium members
        $premiumMembers = LoyaltyMember::whereIn('tier', ['Gold', 'Platinum'])->get();

        // DEBUGLINE: Check if we actually found members to loop through
        dump("Found " . $premiumMembers->count() . " Gold/Platinum members in the database.");

        if ($premiumMembers->isEmpty()) {
            dump("WARNING: No premium records found. Did you run php artisan synapcores:seed first?");
            return;
        }

        foreach ($premiumMembers as $index => $member) {
            // 2. Query SynapCores for the churn probability
            $predictSql = "SELECT AUTOML.PREDICT('churn_v1', ARRAY[?, ?, ?, ?]) AS probability";

            try {
                $predictionResult = $client->query($predictSql, [
                    (string)$member->tier,
                    (int)$member->tenure_months,
                    (int)$member->visits_30d,
                    (float)$member->spend_30d
                ]);

                $probability = $predictionResult[0]['probability'] ?? 0.0;

                // 3. STRETCH GOAL: Personalize the retention offer copy
                $retentionOfferText = null;
                if ($probability >= 0.40) {
                    $prompt = "Write a short 2-sentence friendly retention offer for a retail loyalty member with tenure {$member->tenure_months} months and recent monthly spend of \${$member->spend_30d}. Do not use placeholders.";
                    $generationResult = $client->query("SELECT GENERATE(?) AS offer", [$prompt]);
                    $retentionOfferText = $generationResult[0]['offer'] ?? null;
                }

                // 4. Save to database
                ChurnPrediction::updateOrCreate(
                    ['loyalty_member_id' => $member->id],
                    [
                        'predicted_probability' => $probability,
                        'generated_retention_offer' => $retentionOfferText,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                // Print out the first 5 records in the console to confirm progress
                if ($index < 5) {
                    dump("Saved prediction for Member #{$member->id}: Prob = " . ($probability * 100) . "%");
                }

            } catch (\Exception $e) {
                dump("ERROR tracking Member #{$member->id}: " . $e->getMessage());
            }
        }

        dump("Batch prediction run complete!");
    }
}
