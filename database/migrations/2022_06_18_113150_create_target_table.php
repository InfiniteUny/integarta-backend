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
        Schema::create('target', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constraid('users')->onDelete('cascade');
            $table->string('name');
            $table->double('expense', 15, 4);
            $table->double('daily_payment', 15, 4);
            $table->double('percentage', 15, 4);
            $table->double('temp_expense', 15, 4);
            $table->double('temp_percentage', 15, 4);
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
        Schema::dropIfExists('target');
    }
};
