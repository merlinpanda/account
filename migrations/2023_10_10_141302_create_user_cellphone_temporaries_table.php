<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCellphoneTemporariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_cellphone_temporaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->string("cellphone");
            $table->foreignId("cellphone_notification_task_id")->default(0);
            $table->enum("status", ["WAITING", "VERIFIED"])->default("WAITING");
            $table->string("verify_code", 8);
            $table->dateTime("expired_at");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_cellphone_temporaries');
    }
}
