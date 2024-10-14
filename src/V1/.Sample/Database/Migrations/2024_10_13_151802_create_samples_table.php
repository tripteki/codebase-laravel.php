<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create("samples", function (Blueprint $table) {

            $table->ulid("id");
            $table->text("content");
            $table->timestamps();
            $table->softDeletes();

            $table->primary("id");
            $table->foreignUuid("user_id")->constrained()->onUpdate("cascade")->onDelete("cascade");
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("samples");
    }
};
