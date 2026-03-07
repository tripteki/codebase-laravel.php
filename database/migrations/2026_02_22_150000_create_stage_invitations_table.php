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
        Schema::create("stage_invitations", function (Blueprint $table) {
            $table->ulid("id")->primary();
            $table->string("tenant_id")->nullable()->index();
            $table->ulidMorphs("invitationable");
            $table->string("role");
            $table->ulid("user_id");
            $table->json("staged")->nullable();
            $table->timestamps();

            $table->foreign("tenant_id")->references("id")->on("tenants")->onDelete("cascade");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");

            $table->unique(
                ["invitationable_type", "invitationable_id", "role", "user_id"],
                "stage_invitations_invitationable_role_user_unique"
            );
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists("stage_invitations");
    }
};
