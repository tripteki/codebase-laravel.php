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
        Schema::create("attachments", function (Blueprint $table) {
            $table->ulid("id")->primary();
            $table->string("tenant_id")->nullable()->index();
            $table->ulidMorphs("attachable");
            $table->string("disk")->default("public");
            $table->string("path");
            $table->string("original_name");
            $table->string("mime_type")->nullable();
            $table->unsignedBigInteger("size")->nullable();
            $table->timestamps();

            $table->foreign("tenant_id")
                ->references("id")
                ->on("tenants")
                ->onDelete("cascade");
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists("attachments");
    }
};
