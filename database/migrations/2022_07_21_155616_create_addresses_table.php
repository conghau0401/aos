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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('mall_id');
            $table->string('store_id');
            $table->string('address_name');
            $table->string('full_name');
            $table->string("address_basic");
            $table->string('address_detail')->nullable();
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->string('zip_code')->nullable();
            $table->tinyInteger('is_default')->default(0);
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
        Schema::dropIfExists('addresses');
    }
};
