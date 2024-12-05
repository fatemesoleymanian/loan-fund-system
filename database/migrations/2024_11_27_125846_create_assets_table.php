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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(false);
            $table->decimal('cost',10,2)->default(0)->nullable(true);
            $table->enum('money_source',['از کارمزد','از موجودی','از هیچکدام'])->nullable(false);//az fees bardashtim ya balance ya asan barnmidari
            $table->text('description')->nullable(true);
//            $table->string('accounts')->nullable(true);
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
        Schema::dropIfExists('assets');
    }
};
