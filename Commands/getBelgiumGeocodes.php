<?php

namespace App\Console\Commands;

use App\PostalCode;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class getBelgiumGeocodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getBelgiumGeocodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get Belgium Geocodes';

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
        require_once base_path('vendor/andreychumak/simplify-php/Simplify.php');
        $postalcodes = PostalCode::all();
        $bar = $this->output->createProgressBar(count($postalcodes));
        $bar->start();

        $postalcodes->each(function ($postalcode) use ($bar) {
            $searchURL = 'https://nominatim.openstreetmap.org/search.php?q=' . $postalcode->postal_code . '%2C+Belgium';
            $client = new Client();

            // $response = $client->request('POST', 'http://theopenmap.herokuapp.com/api/v2_coordinates/', [
            //     'form_params' => [
            //         'location' => $postalcode->postal_code . ', Belgium',
            //     ],
            // ]);
            $response = $client->request('GET', $searchURL)->getBody();

            preg_match('/nominatim_results = (\[[\w\W.]*]);    <\/script>/', $response, $matches);

            Storage::put('postalcodes_2/' . $postalcode->postal_code . '.json', $matches[1]);
            $bar->advance();
            sleep(5);
        });
        $bar->finish();
    }
}
