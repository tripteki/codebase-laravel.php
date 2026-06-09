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
        Schema::connection(config("webpush.database_connection"))->create(config("webpush.table_name"), function (Blueprint $table): void {
            $table->bigIncrements("id");

            $table->string("subscribable_id", 26);
            $table->string("subscribable_type");
            $table->index([ "subscribable_type", "subscribable_id", ], "subscribable_type_id_index");

            $table->string("endpoint", 500)->unique();
            $table->string("public_key")->nullable();
            $table->string("auth_token")->nullable();
            $table->string("content_encoding")->nullable();
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::connection(config("webpush.database_connection"))->dropIfExists(config("webpush.table_name"));
    }
};
