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
        $tableNames = config("permission.table_names");

        Schema::table("users", function (Blueprint $table) {

            $table->string("tenant_id")->nullable()->after("id");
            $table->index("tenant_id");
            $table->foreign("tenant_id")->references("id")->on("tenants")->onDelete("cascade");

            $table->dropUnique(["email"]);
            $table->unique(["tenant_id", "email"]);
        });

        Schema::table("settings", function (Blueprint $table) {

            $table->string("tenant_id")->nullable()->after("id");
            $table->index("tenant_id");
            $table->foreign("tenant_id")->references("id")->on("tenants")->onDelete("cascade");

            $table->dropUnique(["key"]);
            $table->unique(["tenant_id", "key"]);
        });

        Schema::connection(config('activitylog.database_connection'))->table(config('activitylog.table_name'), function (Blueprint $table) {

            $table->string("tenant_id")->nullable()->after("id");
            $table->index("tenant_id");
            $table->foreign("tenant_id")->references("id")->on("tenants")->onDelete("cascade");
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $tableNames = config("permission.table_names");

        Schema::table("users", function (Blueprint $table) {

            $table->dropUnique(["tenant_id", "email"]);
            $table->unique(["email"]);

            $table->dropForeign(["tenant_id"]);
            $table->dropIndex(["tenant_id"]);
            $table->dropColumn("tenant_id");
        });

        Schema::table("settings", function (Blueprint $table) {

            $table->dropUnique(["tenant_id", "key"]);
            $table->unique(["key"]);

            $table->dropForeign(["tenant_id"]);
            $table->dropIndex(["tenant_id"]);
            $table->dropColumn("tenant_id");
        });

        Schema::connection(config('activitylog.database_connection'))->table(config('activitylog.table_name'), function (Blueprint $table) {

            $table->dropForeign(["tenant_id"]);
            $table->dropIndex(["tenant_id"]);
            $table->dropColumn("tenant_id");
        });
    }
};
