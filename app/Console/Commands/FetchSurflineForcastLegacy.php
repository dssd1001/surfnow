<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Surfspot;

class AddSupportByGeography extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forecast:surfline {surfline_spot_id_legacy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch forecasting data from surfline (from Surfline Forecasts API).';

    /**
     * Endpoint base-url of Surfline forecasts API.
     *
     * @var string
     */
    protected $API_BASE = "http://api.surfline.com/v1/forecasts/";
    protected $API_OPTIONS = "?showOptimal=true&interpolate=true&fullAnalysis=true&usenearshore=true";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("[".Carbon::now()."] Adding new surfspots. Might take a while...");

        $id = $this->argument('surfline_spot_id_legacy');

        $bar = $this->output->createProgressBar(1);

        $url = $this->API_BASE . $id . $this->API_OPTIONS;

        $this->comment($url);

        $bar->finish();

        $this->info("\nDONE\n");
    }
}
