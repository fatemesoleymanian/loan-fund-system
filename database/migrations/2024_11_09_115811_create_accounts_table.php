<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('balance',10,2)->nullable(false);
//            $table->string('account_number')->unique()->nullable(false);
            $table->string('member_name');
            $table->integer('stock_units')->nullable(false)->default(0);
            $table->boolean('is_open')->nullable(false)->default(true);
            $table->boolean('have_sms')->nullable(false)->default(false);
            $table->enum('status',['بستانکار','بدهکار','تسویه'])->nullable(false);
            $table->text('description')->nullable(true);
            $table->timestamps();
        });
        DB::statement("INSERT INTO sqlite_sequence (name, seq) VALUES ('accounts', 99)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
