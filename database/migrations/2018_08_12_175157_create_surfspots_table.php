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
            $table->string('taxonomy_id')->comment('Unique Surfline taxonomy ID for this spot.');
            $table->string('surfline_spot_id')->unique()->comment('Unique Surfline ID for this spot.');
            $table->string('surfline_url');

            // Surfline Metadata
            $table->string('surfline_cam_url');
            $table->integer('surfline_spot_id_legacy')->unsigned()->comment('Legacy Surfline ID for this spot.');
            $table->integer('surfline_region_id_legacy')->unsigned();
            $table->string('surfline_subregion_id');
            $table->string('timezone');

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
