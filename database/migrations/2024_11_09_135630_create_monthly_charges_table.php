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
        Schema::create('monthly_charges', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(false);
            $table->decimal('amount')->nullable(false);
            $table->integer('year')->unique(true)->nullable(false);
//            $table->foreignId('fund_account_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('monthly_charges');
    }
};
