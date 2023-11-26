<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create("users", function (Blueprint $table) {
            $table->uuid("id");
            $table->string("name");
            $table->string("email");
            $table->timestamp("email_verified_at")->nullable();
            $table->string("password");
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->primary("id");
            $table->unique("email");
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("users");
    }
};
