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
        Schema::connection(config("activitylog.database_connection"))->create(config("activitylog.table_name"), function (Blueprint $table) {

            $table->ulid("id")->primary();
            $table->string("log_name")->nullable();
            $table->text("description");
            $table->string("subject_type")->nullable();
            $table->string("event")->nullable();
            $table->ulid("subject_id")->nullable();
            $table->string("causer_type")->nullable();
            $table->ulid("causer_id")->nullable();
            $table->json("properties")->nullable();
            $table->uuid("batch_uuid")->nullable();
            $table->timestamps();

            $table->index("log_name");
            $table->index(["subject_type", "subject_id"], "subject");
            $table->index(["causer_type", "causer_id"], "causer");
        });

        Schema::table("users", function (Blueprint $table) {

            $table->json("log_activities")->nullable()->after("remember_token");
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {

            $table->dropColumn("log_activities");
        });

        Schema::connection(config("activitylog.database_connection"))->dropIfExists(config("activitylog.table_name"));
    }
};
