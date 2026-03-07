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
        Schema::create("content_trans", function (Blueprint $table) {

            $table->ulid("id")->primary();
            $table->string("tenant_id")->nullable();
            $table->string("group");
            $table->string("key");
            $table->json("value")->nullable();
            $table->timestamps();

            $table->index("tenant_id");
            $table->foreign("tenant_id")->references("id")->on("tenants")->onDelete("cascade");
            $table->unique(["tenant_id", "group", "key"]);
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists("content_trans");
    }
};
