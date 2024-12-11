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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
//            $table->foreignId('account_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('title')->nullable(false);
            $table->decimal('static_fee',10,2)->default(0);
            $table->integer('fee_percent')->default(4);
//            $table->integer('interest')->default(0);
            $table->integer('number_of_installments')->nullable(false);
            $table->integer('installment_interval')->nullable(false)->default(30);
            $table->integer('max_amount')->default(0);
            $table->integer('min_amount')->default(0);
            $table->boolean('emergency')->default(false);
            $table->boolean('no_need_to_pay')->default(false);
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
        Schema::dropIfExists('loans');
    }
};
