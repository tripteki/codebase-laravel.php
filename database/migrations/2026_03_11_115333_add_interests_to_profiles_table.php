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
        Schema::table("profiles", function (Blueprint $table) {
            $table->json("interests")->nullable()->after("avatar");
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table("profiles", function (Blueprint $table) {
            $table->dropColumn("interests");
        });
    }
};
