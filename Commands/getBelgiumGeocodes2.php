<?php

namespace App\Console\Commands;

use App\PostalCode;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class getBelgiumGeocodes2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geocodes2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $postalcodes = PostalCode::all();

        $bar = $this->output->createProgressBar(count($postalcodes));
        $bar->start();
        $postalcodes->each(function ($postalcode) use ($bar) {
            $bar->advance();
            $searchResults = json_decode(Storage::get('postalcodes_2/' . $postalcode->postal_code . '.json'), true);

            if (Storage::has('postalcodes_3/' . $postalcode->postal_code . '.json')) {
                $oldresult = Storage::get('postalcodes_3/' . $postalcode->postal_code . '.json');

                if (trim($oldresult) != 'None' && trim($oldresult) != '') {
                    return;
                }
            }
            $id = null;

            foreach ($searchResults as $result) {
                if ($result['osm_id'] == null || $result['osm_id'] == 'null') {
                    continue;
                }

                if ($result['osm_type'] == 'W') {
                    continue;
                }

                if ($result['osm_type'] == 'N') {
                    continue;
                }

                if ($result['osm_type'] == 'R') {
                    continue;
                }

                if ($result['label'] == 'Country') {
                    continue;
                }
                $id = $result['osm_id'];
                break;
            }

            if ($id === null) {
                return;
            }
            echo $id;

            $client = new Client();

            $response = $client->request('POST', 'http://polygons.openstreetmap.fr/?id=' . $id, [
                'form_params' => [
                    'x' => '0',
                    'y' => '0.001000',
                    'z' => '0.001000',
                ],
            ]);

            $response = $client->request('GET', 'http://polygons.openstreetmap.fr/get_geojson.py?params=0.000000-0.001000-0.001000&id=' . $id)->getBody();

            Storage::put('postalcodes_3/' . $postalcode->postal_code . '.json', $response);
            sleep(1);
        });
        $bar->finish();
    }
}
