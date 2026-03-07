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
        Schema::create("profiles", function (Blueprint $table) {
            $table->ulid("id")->primary();
            $table->foreignUlid("user_id")->unique()->references("id")->on("users")->cascadeOnDelete();
            $table->string("full_name")->nullable();
            $table->string("avatar")->nullable();
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
