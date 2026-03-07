<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create("password_reset_tokens", function (Blueprint $table) {

            $table->string("email");
            $table->string("token");
            $table->timestamp("created_at")->nullable();
            $table->timestamp("deleted_at")->nullable();

            $table->primary("email");
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists("password_reset_tokens");
    }
};
