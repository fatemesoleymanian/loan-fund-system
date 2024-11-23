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
            $table->decimal('principal_amount',10,2);
            $table->decimal('fee',10,2)->default(4);
            $table->enum('type',['وام قرض الحسنه'])->default('وام قرض الحسنه');
            $table->integer('number_of_installments')->nullable(false);
            $table->boolean('status')->default(true);
            $table->integer('year')->nullable(false);
            $table->integer('intervalDays')->default(1);
            $table->timestamp('due_date');
            $table->timestamp('issue_date');
            $table->timestamp('end_date');
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
