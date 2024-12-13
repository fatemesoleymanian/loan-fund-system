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
        Schema::create('loan_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('fund_account_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('amount',10,2)->nullable(false);
            $table->decimal('paid_amount',10,2)->nullable(false)->default(0);
            $table->timestamp('granted_at')->nullable(false);
            $table->timestamp('payback_at')->nullable(false);
            $table->integer('number_of_installments')->nullable(false);
            $table->integer('no_of_paid_inst')->nullable(false)->default(0);
//            $table->integer('interest')->default(0);
            $table->decimal('fee_amount',10,2)->nullable(false);
            $table->string('description')->nullable(true);
            $table->string('account_name');
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
        Schema::dropIfExists('loan_accounts');
    }
};
