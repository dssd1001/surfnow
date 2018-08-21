<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use App\Surfspot;

class PopulateSurfspotsWithIdealConditions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surfspots:populate:ideal {surfline_spot_id_legacy?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the ideal conditions columns of surfspots table by scraping the Surfline website.';

    /**
     * Endpoint base-url of Surfline pages to scrape.
     *
     * @var string
     */
    protected $BASE_URL = "http://www.surfline.com/surf-report/-_";

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
        $this->info("[".Carbon::now()."] Populating surfspots with ideal conditions. Might take a while...");

        if ($legacy_id = $this->argument('surfline_spot_id_legacy')) {
            $this->scrapeIdealConditions($legacy_id);
        } else {

            $bar = $this->output->createProgressBar(Surfspot::count());

            foreach (Surfspot::cursor() as $spot) {
                if ($legacy_id = $spot->surfline_spot_id_legacy) {
                    $this->scrapeIdealConditions($legacy_id);
                }
                $bar->advance();
            }
            $bar->finish();
        }

        $this->info("\nDONE\n");
    }

    protected function scrapeIdealConditions($legacy_id)
    {
        $url = $this->BASE_URL . $legacy_id . "/travel";

        try {

            $client = new Client();
            $crawler = $client->request('GET', $url);

            $css_selector_details = 'div.travel-spot-sidebar';
            $css_selector = 'div.travel-spot-text';
            $output_array = $crawler->filter($css_selector)->extract('_text');

            // Get Idea Conditions Data
            $comp = preg_split('/[\t\n]+/', $output_array[0]);
            $conditions_array = array_filter( array_map('trim', $comp) );
            array_shift($conditions_array);

            // Get Spot Summary Data
            $comp2 = preg_split('/[\t\n]+/', $output_array[1]);
            $overview_array = array_filter( array_map('trim', $comp2) );
            array_shift($overview_array);

            $this->populateSurfspotIdealConditions($legacy_id, $conditions_array, $overview_array);

        } catch (RequestException $e) {

            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $this->error("Failed to fetch ".$url);
            $this->error($responseBodyAsString);

        } catch (\Exception $e) {

            $this->error("Something went wrong while handling \n\t" . $url);
            $this->error($e->getMessage());

        }
    }

    protected function populateSurfspotIdealConditions($legacy_id, $conditions_array, $overview_array)
    {
        $spot = SurfSpot::where('surfline_spot_id_legacy', $legacy_id)->first();

        foreach ($conditions_array as $key => $value) {
            if ($value == 'Best Swell Direction:') {
                $spot->ideal_swell_dir = $conditions_array[$key + 1];
            }
            elseif ($value == 'Best Wind:') {
                $spot->ideal_wind = $conditions_array[$key + 1];
            }
            elseif ($value == 'Best Tide:') {
                $spot->ideal_tide = $conditions_array[$key + 1];
            }
            elseif ($value == 'Best Size:') {
                $condition = $conditions_array[$key + 1];
                $numbers_count = preg_match_all('!\d+!', $condition, $matches);

                $spot->ideal_surf_height = $condition;
                if ($numbers_count == 2) {
                    $spot->ideal_surf_height_min = $matches[0][0];
                    $spot->ideal_surf_height_max = $matches[0][1];
                }
            }
            elseif ($value == 'Best Season:') {
                $spot->ideal_season = $conditions_array[$key + 1];
            }
            elseif ($value == 'Bottom:') {
                $spot->surfline_detail_bottom = $conditions_array[$key + 1];
            }
            elseif ($value == 'Ability Level:') {
                $spot->surfline_detail_ability_lvl = $conditions_array[$key + 1];
            }
            elseif ($value == 'Access:') {
                $spot->surfline_detail_access = $conditions_array[$key + 1];
            }
            elseif ($value == 'Crowd Factor:') {
                $spot->surfline_detail_crowd_factor = $conditions_array[$key + 1];
            }
            elseif ($value == 'Hazards:') {
                $spot->surfline_detail_hazards = $conditions_array[$key + 1];
            }
        }

        $surfline_about_spot = '';
        foreach ($overview_array as $key => $value) {
            $surfline_about_spot .= ($value . '\n\n');
        }
        $spot->surfline_about_spot = $surfline_about_spot;

        $spot->save();
    }
}
