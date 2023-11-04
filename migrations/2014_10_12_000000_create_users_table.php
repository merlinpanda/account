<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('avatar')->default(null)->nullable()->comment('头像');
            $table->string('first_name')->nullable()->default(null);
            $table->string('middle_name')->nullable()->default(null);
            $table->string('last_name')->nullable()->default(null);
            $table->string('password');
            $table->enum("gender", ['F', 'M', 'NA'])->default("NA")->comment("NA未知");
            $table->foreignId("region_id")->default(0);
            $table->foreignId("city_id")->default(0);
            $table->string("address")->nullable()->default(null);
            $table->foreignId("prefix_id")->default(0)->comment("职称");
            $table->string("fixed_line")->nullable()->default(null)->comment("固定电话");
            $table->string("fax")->nullable()->default(null)->comment("传真");
            $table->string('last_login_ip')->default(null)->nullable()->comment('最后登录的IP');
            $table->dateTime("last_login_at")->nullable()->default(null);
            $table->enum("status", ['ACTIVE', 'INACTIVE', 'DISABLED'])->default("ACTIVE");
            $table->dateTime("disabled_expired_at")->nullable()->default(null)->comment("禁用过期时间");
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
