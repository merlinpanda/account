<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMailUuidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_mail_uuids', function (Blueprint $table) {
            $table->id();
            $table->string("uuid")->unique();
            $table->foreignId("user_id")->constrained();
            $table->string("subject");
            $table->string("email");
            $table->text("html");
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
        Schema::dropIfExists('user_mail_uuids');
    }
}
