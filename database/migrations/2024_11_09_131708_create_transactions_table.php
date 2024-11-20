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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('amount',10,2)->nullable(false);
            $table->enum('type',['پرداخت ماهیانه','پرداخت قسط','پرداخت وام','پرداخت جریمه','پرداخت کارمزد','واریز','برداشت']);
            $table->text('description')->nullable(true);
            $table->integer('delay_days')->default(0);
            $table->foreignId('fund_account_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('monthly_charge_id')->nullable(true)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('installment_id')->nullable(true)->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('transactions');
    }
};
