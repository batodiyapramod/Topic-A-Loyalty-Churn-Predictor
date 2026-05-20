<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SynapCores\SynapCoresClient;

class SynapCoresTrainCommand extends Command
{
    protected $signature = 'synapcores:train';
    protected $description = 'Initialize and compile the churn classification model inside SynapCores';

    public function handle(SynapCoresClient $client)
    {
        // CHANGE THIS LINE: Use DROP MODEL instead of DROP EXPERIMENT
        $this->info('Removing old training model iterations...');
        $client->query("DROP MODEL IF EXISTS churn_v1");

        $this->info('Compiling new experiment structural rules on target column [churned]...');
        $client->query("CREATE EXPERIMENT churn_v1 WITH (
            target = 'churned',
            model_type = 'classification'
        )");

        $this->info('Running model training inside SynapCores engine (this may take a moment)...');
        $client->query("TRAIN churn_v1 FROM loyalty_members_mirror");

        $this->info('Model churn_v1 successfully compiled and ready for scoring operations.');
    }
}
