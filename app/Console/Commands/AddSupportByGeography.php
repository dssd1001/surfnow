<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use App\Surfspot;
use App\Taxonomy;

class AddSupportByGeography extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surfspots:support:geo {surfline_taxonomy_id : surfline taxonomy id of the geography.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add support for a geography by adding surfspots (from Surfline Taxonomy API).';

    /**
     * Endpoint base-url of Surfline forecasts API.
     *
     * @var string
     */
    protected $API_BASE = "http://services.surfline.com/taxonomy?type=taxonomy";

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
        $this->info("[".Carbon::now()."] Populating surfspots. Might take a while...");

        $id = $this->argument('surfline_taxonomy_id');
        $url = $this->API_BASE . "&id=" . $id . "&maxDepth=10";

        try {

            $client = new Client();
            $response = $client->request('GET', $url);
            if ($response->getStatusCode() == 200) {
                $responseJSON = json_decode($response->getBody());

                $taxonomy_array = $responseJSON->contains;

                $bar = $this->output->createProgressBar(count($taxonomy_array) + 1);
                foreach ($taxonomy_array as $taxonomy) {
                    try {
                        $this->saveTaxonomy($taxonomy);
                    } catch (\Exception $e) {
                        $this->error("Something went wrong while saving taxonomy for ".$taxonomy->name." with id ".$taxonomy->_id);
                        $this->error($e->getMessage());
                    }
                    $bar->advance();
                }

                $this->saveTaxonomy($responseJSON);
                $bar->finish();
            }

        } catch (RequestException $e) {

            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $this->error("Failed to fetch ".$url."...");
            $this->error($responseBodyAsString);

        } catch (\Exception $e) {

            $this->error("Something went wrong...");
            $this->error($e->getMessage());

        }

        $this->info("\nDONE\n");
    }

    protected function saveTaxonomy($taxonomy)
    {
        if (!Taxonomy::where('surfline_taxonomy_id',$taxonomy->_id)->first()) {
            $t = new Taxonomy;
            $t->name = $taxonomy->name;
            $t->surfline_taxonomy_id = $taxonomy->_id;
            $t->surfline_type = $taxonomy->type;
            $t->surfline_type_id =
                $t->surfline_type == 'spot' ? $taxonomy->spot : (
                    $t->surfline_type == 'subregion' ? $taxonomy->subregion : (
                        $t->surfline_type == 'region' ? $taxonomy->region : (
                            $t->surfline_type == 'geoname' ? $taxonomy->geonameId : '')));
            $t->save();

            if ($t->surfline_type == 'spot') {
                $this->saveSurfspot($taxonomy);
            }
        }
    }

    protected function saveSurfspot($taxonomy)
    {
        if (!Surfspot::where('surfline_spot_id',$taxonomy->spot)->first()) {
            $s = new Surfspot;
            $s->name = $taxonomy->name;
            $s->latitude = $taxonomy->location->coordinates[1];
            $s->longitude = $taxonomy->location->coordinates[0];
            $s->taxonomy_id = $taxonomy->_id;
            $s->surfline_spot_id = $taxonomy->spot;
            foreach ($taxonomy->associated->links as $link) {
                if ($link && $link->key == 'www') {
                    $s->surfline_url = $link->href;
                }
            }
            $s->save();
        }
    }
}
