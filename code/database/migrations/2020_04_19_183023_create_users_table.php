<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('username');
            $table->string('email')->nullable();
            $table->string('password');
            $table->integer('country_code')->nullable();
            $table->string('phone_number');
            $table->string('verification_id')->nullable();
            $table->string('authy_id')->nullable();
            $table->boolean('ongoing_two_fa')->default(false);
            $table->datetime('verified_at')->nullable();
            $table->rememberToken();

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
        Schema::dropIfExists('users');
    }
}
