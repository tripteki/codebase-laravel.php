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
    public function up(): void
    {
        Schema::create("notifications", function (Blueprint $table) {

            $table->uuid("id");
            $table->string("type");

            // $table->morphs("notifiable"); //
            $table->ulidMorphs("notifiable");

            $table->json("data");

            $table->timestamp("read_at")->nullable(true);
            $table->timestamp("created_at")->nullable(true);
            $table->timestamp("updated_at")->nullable(true);

            $table->primary("id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists("notifications");
    }
};
