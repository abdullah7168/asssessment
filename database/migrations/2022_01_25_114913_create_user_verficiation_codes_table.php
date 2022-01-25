<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserVerficiationCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_verficiation_codes', function (Blueprint $table) {
            $table->id();
			$table->string('code')->unique();
	        $table->unsignedBigInteger('user_id')->nullable();
	        $table->foreign('user_id')
	              ->references('id')
	              ->on('users')
	              ->onDelete('SET NULL');
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
        Schema::dropIfExists('user_verficiation_codes');
    }
}
