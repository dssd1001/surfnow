<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurfspotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surfspots', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->float('latitude', 10, 6);
            $table->float('longitude', 10, 6);
            $table->integer('surfline_spot_id')->unsigned()->comment('Unique Surfline identifier for this spot.');

            $table->string('surfline_url')->comment('JSON Object._metadata.canonicalUrl');
            $table->string('surfline_cam_url')->comment('JSON Object.Quickspot.M3U8URL');
            $table->string('surfline_tide_location')->comment('JSON Object.Quickspot.nearest_tide_location');
            $table->integer('surfline_area_id')->unsigned()->comment('JSON Object.Quickspot.areaid');
            $table->string('surfline_area_name')->comment('JSON Object.Quickspot.areaname');
            $table->integer('surfline_region_id')->unsigned()->comment('JSON Object.Quickspot.regionid');
            $table->string('surfline_region_name')->comment('JSON Object.Quickspot.regionname');
            $table->integer('surfline_subregion_id')->unsigned()->comment('JSON Object.Quickspot.subregionid');
            $table->string('surfline_subregion_name')->comment('JSON Object.Quickspot.subregionname');
            $table->integer('surfline_travel_id')->unsigned()->comment('JSON Object.Quickspot.travelid');
            $table->integer('surfline_timezone')->comment('JSON Object.Quickspot.timezone');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surfspots');
    }
}
