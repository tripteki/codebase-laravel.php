<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @param string $table
     * @param string $column
     * @return bool
     */
    private function primaryKeyIncludesColumn(string $table, string $column): bool
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (! ($index["primary"] ?? false)) {
                continue;
            }

            if (in_array($column, $index["columns"] ?? [], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $table
     * @param string $permissionsTable
     * @param string $rolesTable
     * @param string $teamForeignKey
     * @param string $pivotRole
     * @param string $pivotPermission
     * @param array $primaryColumns
     * @param string $primaryKeyName
     * @param array $foreignKeys
     * @param ?callable $addTenantColumn
     * @return void
     */
    private function reshapePivotPrimaryKey(
        string $table,
        string $permissionsTable,
        string $rolesTable,
        string $teamForeignKey,
        string $pivotRole,
        string $pivotPermission,
        array $primaryColumns,
        string $primaryKeyName,
        array $foreignKeys,
        ?callable $addTenantColumn = null,
    ): void {
        if ($this->primaryKeyIncludesColumn($table, $teamForeignKey)) {
            return;
        }

        if ($addTenantColumn !== null && ! Schema::hasColumn($table, $teamForeignKey)) {
            Schema::table($table, $addTenantColumn);
        }

        if (! Schema::hasColumn($table, $teamForeignKey)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use (
            $permissionsTable,
            $rolesTable,
            $pivotRole,
            $pivotPermission,
            $primaryColumns,
            $primaryKeyName,
            $foreignKeys,
        ): void {
            if (in_array("permission", $foreignKeys, true)) {
                $blueprint->dropForeign([$pivotPermission]);
            }

            if (in_array("role", $foreignKeys, true)) {
                $blueprint->dropForeign([$pivotRole]);
            }

            $blueprint->dropPrimary($primaryKeyName);
            $blueprint->primary($primaryColumns, $primaryKeyName);

            if (in_array("permission", $foreignKeys, true)) {
                $blueprint->foreign($pivotPermission)
                    ->references("id")
                    ->on($permissionsTable)
                    ->onDelete("cascade");
            }

            if (in_array("role", $foreignKeys, true)) {
                $blueprint->foreign($pivotRole)
                    ->references("id")
                    ->on($rolesTable)
                    ->onDelete("cascade");
            }
        });
    }

    /**
     * @return void
     */
    public function up(): void
    {
        $tableNames = config("permission.table_names");
        $columnNames = config("permission.column_names");
        $teamForeignKey = $columnNames["team_foreign_key"] ?? "tenant_id";
        $pivotRole = $columnNames["role_pivot_key"] ?? "role_id";
        $pivotPermission = $columnNames["permission_pivot_key"] ?? "permission_id";
        $modelMorphKey = $columnNames["model_morph_key"] ?? "model_id";

        if (! Schema::hasColumn("users", "tenant_id")) {
            Schema::table("users", function (Blueprint $table): void {
                $table->string("tenant_id")->nullable()->after("id");
                $table->index("tenant_id");
                $table->foreign("tenant_id")->references("id")->on("tenants")->nullOnDelete();
                $table->dropUnique(["email"]);
                $table->unique(["tenant_id", "email"]);
            });
        }

        if (
            Schema::hasTable("password_reset_tokens")
            && ! Schema::hasColumn("password_reset_tokens", "tenant_id")
        ) {
            Schema::table("password_reset_tokens", function (Blueprint $table): void {
                $table->dropPrimary(["email"]);
                $table->string("tenant_id")->nullable()->after("email");
                $table->primary(["email", "tenant_id"]);
            });
        }

        if (! Schema::hasColumn("settings", "tenant_id")) {
            Schema::table("settings", function (Blueprint $table): void {
                $table->string("tenant_id")->nullable()->after("id");
                $table->index("tenant_id");
                $table->foreign("tenant_id")->references("id")->on("tenants")->nullOnDelete();
                $table->dropUnique(["key"]);
                $table->unique(["tenant_id", "key"]);
            });
        }

        $activityTable = config("activitylog.table_name");

        if (! Schema::connection(config("activitylog.database_connection"))->hasColumn($activityTable, "tenant_id")) {
            Schema::connection(config("activitylog.database_connection"))->table($activityTable, function (Blueprint $table): void {
                $table->string("tenant_id")->nullable()->after("id");
                $table->index("tenant_id");
                $table->foreign("tenant_id")->references("id")->on("tenants")->nullOnDelete();
            });
        }

        if (! Schema::hasColumn($tableNames["permissions"], $teamForeignKey)) {
            Schema::table($tableNames["permissions"], function (Blueprint $table) use ($teamForeignKey): void {
                $table->string($teamForeignKey)->nullable()->after("id");
                $table->index($teamForeignKey);
                $table->foreign($teamForeignKey)->references("id")->on("tenants")->nullOnDelete();
                $table->dropUnique(["name", "guard_name"]);
                $table->unique([$teamForeignKey, "name", "guard_name"]);
            });
        }

        if (! Schema::hasColumn($tableNames["roles"], $teamForeignKey)) {
            Schema::table($tableNames["roles"], function (Blueprint $table) use ($teamForeignKey): void {
                $table->string($teamForeignKey)->nullable()->after("id");
                $table->index($teamForeignKey);
                $table->foreign($teamForeignKey)->references("id")->on("tenants")->nullOnDelete();
                $table->dropUnique(["name", "guard_name"]);
                $table->unique([$teamForeignKey, "name", "guard_name"]);
            });
        }

        $this->reshapePivotPrimaryKey(
            $tableNames["model_has_permissions"],
            $tableNames["permissions"],
            $tableNames["roles"],
            $teamForeignKey,
            $pivotRole,
            $pivotPermission,
            [$teamForeignKey, $pivotPermission, $modelMorphKey, "model_type"],
            "model_has_permissions_permission_model_type_primary",
            ["permission"],
            function (Blueprint $table) use ($teamForeignKey, $pivotPermission): void {
                $table->string($teamForeignKey)->nullable()->after($pivotPermission);
                $table->index($teamForeignKey, "model_has_permissions_team_foreign_key_index");
            },
        );

        $this->reshapePivotPrimaryKey(
            $tableNames["model_has_roles"],
            $tableNames["permissions"],
            $tableNames["roles"],
            $teamForeignKey,
            $pivotRole,
            $pivotPermission,
            [$teamForeignKey, $pivotRole, $modelMorphKey, "model_type"],
            "model_has_roles_role_model_type_primary",
            ["role"],
            function (Blueprint $table) use ($teamForeignKey, $pivotRole): void {
                $table->string($teamForeignKey)->nullable()->after($pivotRole);
                $table->index($teamForeignKey, "model_has_roles_team_foreign_key_index");
            },
        );
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $tableNames = config("permission.table_names");
        $columnNames = config("permission.column_names");
        $teamForeignKey = $columnNames["team_foreign_key"] ?? "tenant_id";
        $pivotRole = $columnNames["role_pivot_key"] ?? "role_id";
        $pivotPermission = $columnNames["permission_pivot_key"] ?? "permission_id";
        $modelMorphKey = $columnNames["model_morph_key"] ?? "model_id";

        if (Schema::hasColumn($tableNames["model_has_roles"], $teamForeignKey)) {
            Schema::table($tableNames["model_has_roles"], function (Blueprint $table) use ($tableNames, $teamForeignKey, $pivotRole, $modelMorphKey): void {
                $table->dropForeign([$pivotRole]);
                $table->dropPrimary("model_has_roles_role_model_type_primary");
                $table->primary([$pivotRole, $modelMorphKey, "model_type"], "model_has_roles_role_model_type_primary");
                $table->foreign($pivotRole)
                    ->references("id")
                    ->on($tableNames["roles"])
                    ->onDelete("cascade");
                $table->dropIndex("model_has_roles_team_foreign_key_index");
                $table->dropColumn($teamForeignKey);
            });
        }

        if (Schema::hasColumn($tableNames["model_has_permissions"], $teamForeignKey)) {
            Schema::table($tableNames["model_has_permissions"], function (Blueprint $table) use ($tableNames, $teamForeignKey, $pivotPermission, $modelMorphKey): void {
                $table->dropForeign([$pivotPermission]);
                $table->dropPrimary("model_has_permissions_permission_model_type_primary");
                $table->primary([$pivotPermission, $modelMorphKey, "model_type"], "model_has_permissions_permission_model_type_primary");
                $table->foreign($pivotPermission)
                    ->references("id")
                    ->on($tableNames["permissions"])
                    ->onDelete("cascade");
                $table->dropIndex("model_has_permissions_team_foreign_key_index");
                $table->dropColumn($teamForeignKey);
            });
        }

        if (Schema::hasColumn($tableNames["roles"], $teamForeignKey)) {
            Schema::table($tableNames["roles"], function (Blueprint $table) use ($teamForeignKey): void {
                $table->dropUnique([$teamForeignKey, "name", "guard_name"]);
                $table->unique(["name", "guard_name"]);
                $table->dropForeign([$teamForeignKey]);
                $table->dropIndex([$teamForeignKey]);
                $table->dropColumn($teamForeignKey);
            });
        }

        if (Schema::hasColumn($tableNames["permissions"], $teamForeignKey)) {
            Schema::table($tableNames["permissions"], function (Blueprint $table) use ($teamForeignKey): void {
                $table->dropUnique([$teamForeignKey, "name", "guard_name"]);
                $table->unique(["name", "guard_name"]);
                $table->dropForeign([$teamForeignKey]);
                $table->dropIndex([$teamForeignKey]);
                $table->dropColumn($teamForeignKey);
            });
        }

        $activityTable = config("activitylog.table_name");

        if (Schema::connection(config("activitylog.database_connection"))->hasColumn($activityTable, "tenant_id")) {
            Schema::connection(config("activitylog.database_connection"))->table($activityTable, function (Blueprint $table): void {
                $table->dropForeign(["tenant_id"]);
                $table->dropIndex(["tenant_id"]);
                $table->dropColumn("tenant_id");
            });
        }

        if (Schema::hasColumn("settings", "tenant_id")) {
            Schema::table("settings", function (Blueprint $table): void {
                $table->dropUnique(["tenant_id", "key"]);
                $table->unique(["key"]);
                $table->dropForeign(["tenant_id"]);
                $table->dropIndex(["tenant_id"]);
                $table->dropColumn("tenant_id");
            });
        }

        if (Schema::hasColumn("password_reset_tokens", "tenant_id")) {
            Schema::table("password_reset_tokens", function (Blueprint $table): void {
                $table->dropPrimary(["email", "tenant_id"]);
                $table->dropColumn("tenant_id");
                $table->primary(["email"]);
            });
        }

        if (Schema::hasColumn("users", "tenant_id")) {
            Schema::table("users", function (Blueprint $table): void {
                $table->dropUnique(["tenant_id", "email"]);
                $table->unique(["email"]);
                $table->dropForeign(["tenant_id"]);
                $table->dropIndex(["tenant_id"]);
                $table->dropColumn("tenant_id");
            });
        }
    }
};
