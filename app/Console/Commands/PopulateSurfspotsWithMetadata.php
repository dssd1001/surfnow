<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use App\Surfspot;
use App\Taxonomy;

class PopulateSurfspotsWithMetadata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surfspots:populate:meta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the surfline-metadata columns of the surfspots table.';

    /**
     * Endpoint base-url of Surfline forecasts API.
     *
     * @var string
     */
    protected $API_BASE = "http://services.surfline.com/kbyg/";

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
        $this->info("[".Carbon::now()."] Populating surfspots with Surfline metadata. Might take a while...");

        $subregions = Taxonomy::where('surfline_type', 'subregion')->get();
        $bar = $this->output->createProgressBar($subregions->count());

        foreach ($subregions as $subregion) {
            try {

                $client = new Client();
                $url = $this->API_BASE . "regions/forecasts?subregionId=" . $subregion->surfline_type_id;
                $response = $client->request('GET', $url);

                if ($response->getStatusCode() == 200) {
                    $responseJSON = json_decode($response->getBody());

                    if ($coords = $responseJSON->data->mapBounds) {
                        $url2 = $this->API_BASE . "mapview?" .
                            "south=" . $coords->south .
                            "&west=" . $coords->west .
                            "&north=" . $coords->north .
                            "&east=" . $coords->east;

                        $response2 = $client->request('GET', $url2);

                        if ($response2->getStatusCode() == 200) {
                            $responseJSON2 = json_decode($response2->getBody());

                            foreach ($responseJSON2->data->spots as $surfspot) {
                                $this->populateSurfspotMetadata($surfspot);
                            }
                        }
                    }
                }

            } catch (RequestException $e) {

                $response = $e->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $this->error("Failed to fetch ".$url);
                $this->error($responseBodyAsString);

            } catch (\Exception $e) {

                $this->error("Something went wrong while handling \n\t" . $url);
                $this->error($e->getMessage());

            }

            $bar->advance();
        }
        $bar->finish();

        $this->info("\nDONE\n");
    }

    protected function populateSurfspotMetadata($surfspot)
    {
        $surfline_cam_url = count($surfspot->cameras) > 0 ? $surfspot->cameras[0]->streamUrl : '';
        $surfline_spot_id_legacy = $surfspot->legacyId;
        $surfline_region_id_legacy = $surfspot->legacyRegionId;
        $surfline_subregion_id = $surfspot->subregionId;
        $timezone = $surfspot->timezone;

        Surfspot::updateOrCreate(
            ['surfline_spot_id' => $surfspot->_id],
            [
                'surfline_cam_url' => $surfline_cam_url,
                'surfline_spot_id_legacy' => $surfline_spot_id_legacy,
                'surfline_region_id_legacy' => $surfline_region_id_legacy,
                'surfline_subregion_id' => $surfline_subregion_id,
                'timezone' => $timezone
            ]
        );
    }
}
