<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdealColumnsToSurfspotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surfspots', function (Blueprint $table) {
            $table->string('ideal_swell_dir');
            $table->string('ideal_wind');
            $table->string('ideal_tide');
            $table->string('ideal_surf_height');
            $table->integer('ideal_surf_height_min')->unsigned();
            $table->integer('ideal_surf_height_max')->unsigned();
            $table->string('ideal_season');

            $table->string('surfline_detail_bottom');
            $table->string('surfline_detail_ability_lvl');
            $table->string('surfline_detail_access');
            $table->string('surfline_detail_crowd_factor');
            $table->string('surfline_detail_hazards');
            $table->text('surfline_about_spot');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surfspots', function (Blueprint $table) {
            $table->dropColumn([
                'ideal_swell_dir',
                'ideal_wind',
                'ideal_tide',
                'ideal_surf_height',
                'ideal_surf_height_min',
                'ideal_surf_height_max',
                'ideal_season',
                'surfline_detail_bottom',
                'surfline_detail_ability_lvl',
                'surfline_detail_access',
                'surfline_detail_crowd_factor',
                'surfline_detail_hazards',
                'surfline_about_spot'
            ]);
        });
    }
}
