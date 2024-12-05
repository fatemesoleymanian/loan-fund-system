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
        Schema::create('fund_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->nullable(false);
            $table->decimal('balance',10,2)->default(0);
            $table->decimal('fees',10,2)->default(0);
            $table->decimal('total_balance',10,2)->default(0);
            $table->decimal('expenses',10,2)->default(0);
            $table->enum('status',['بستانکار','بدهکار','تسویه'])->nullable(false)->default('بستانکار');
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
        Schema::dropIfExists('fund_accounts');
    }
};
