<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('malls', function (Blueprint $table) {
            $table->id();
            $table->string('mall_id')->index();
            $table->string("shop_no");
            $table->string('mall_name');
            $table->string('mall_url');
            $table->integer('pc_skin_no');
            $table->integer('mobile_skin_no');
            $table->string('refresh_token_expires_at');
            $table->string('access_token');
            $table->string('refresh_token');
            $table->string('default');
            $table->tinyInteger('is_disabled')->default(0);
            $table->tinyInteger('is_deleted')->default(0);
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
        Schema::dropIfExists('malls');
    }
}
