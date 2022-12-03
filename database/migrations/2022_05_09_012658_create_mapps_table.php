<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapps', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->bigInteger('cafe24_id');
            $table->bigInteger('doosoun_id');
            $table->string('mall_id');
            $table->string('shop_no');
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
        Schema::dropIfExists('mapps');
    }
};
