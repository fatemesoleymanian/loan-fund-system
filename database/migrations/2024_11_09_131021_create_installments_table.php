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
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            //for loan
            $table->foreignId('loan_id')->nullable(true)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('loan_account_id')->nullable(true)->constrained()->onUpdate('cascade')->onDelete('cascade');
//            $table->integer('interest')->default(0);//sood
            //for charge
            $table->foreignId('monthly_charge_id')->nullable(true)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('year')->nullable(true);
            //moshtarak
            $table->foreignId('account_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('account_name')->nullable(false);
            $table->string('inst_number')->nullable(false)->default('1');
            $table->decimal('amount',10,2)->nullable(false);
            $table->timestamp('due_date');//tarikhe saresis
            $table->timestamp('paid_date')->nullable(true)->default(null);//tarikhe pardakht
            $table->integer('delay_days')->default(0);
            $table->integer('type')->nullable(false);//1 => mahiane  & 2 => vam
            $table->string('title')->nullable(false);//esme mahiane ya emse vam
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
        Schema::dropIfExists('installments');
    }
};
