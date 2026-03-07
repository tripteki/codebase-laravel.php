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
        Schema::create("stage_meetings", function (Blueprint $table) {
            $table->ulid("id")->primary();
            $table->string("room_id", 6)->nullable();
            $table->string("tenant_id")->nullable()->index();
            $table->string("title");
            $table->text("description")->nullable();
            $table->dateTime("start_at")->nullable();
            $table->dateTime("end_at")->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("tenant_id")->references("id")->on("tenants")->onDelete("cascade");
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists("stage_meetings");
    }
};
