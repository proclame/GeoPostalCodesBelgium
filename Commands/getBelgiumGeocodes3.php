<?php

namespace App\Console\Commands;

use App\PostalCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class getBelgiumGeocodes3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geocodes3';

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
        echo <<<SVG
<?xml version="1.0" encoding="utf-8"?>
<!-- Generator: Adobe Illustrator 24.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
<svg version="1.1" id="Laag_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
     viewBox="23.89 0 40.29 31.0"  xml:space="preserve">
<style type="text/css">
   .st0 {
   stroke: white;
  stroke-width: 0.01;
  stroke-linecap: butt;
  stroke-dasharray: 0;
   }
   * {
   background: #999999;
   }
</style>
SVG;

        $postalcodes->each(function ($postalcode) {
            $geocodes = Storage::get('postalcodes_3/' . $postalcode->postal_code . '.json');

            if (trim($geocodes) == 'None') {
                //echo $postalcode->postal_code . PHP_EOL;

                return;
            }
            $geocodesArray = json_decode($geocodes, true);

            if ($geocodesArray['type'] == 'GeometryCollection') {
                $geocodesArray = $geocodesArray['geometries'][0];
            }

            //echo $postalcode->postal_code;

            foreach ($geocodesArray['coordinates'] as $shapes) {
                foreach ($shapes as $shape) {
                    $shapeString = '<path class="st0 ' . $postalcode->postal_code . '" d="M';

                    foreach ($shape as $coordinates) {
                        $shapeString .= ($coordinates[0]*10) . ',' . round((515.51 - ($coordinates[1]*10))*1.5, 2) . ' ';
                    }

                    $shapeString .= 'Z" title="' . $postalcode->postal_code . ' ' . $postalcode->city . '"/>' . PHP_EOL;

                    echo $shapeString;
                }
            }
            //Storage::put('postalcodes_3/' . $postalcode->postal_code . '.json', $response);
        });
        echo '</svg>';
    }
}
