<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->unique();
            $table->string('address');
            $table->mediumText('shortDescription');
            $table->longText('Description');
            $table->string('profile_image');
            // $table->string('images'); TODO
            $table->string('website');
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
        Schema::dropIfExists('mos');
    }
}
