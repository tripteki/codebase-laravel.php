<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePushSubscriptionsTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::connection(config("webpush.database_connection"))->create(config("webpush.table_name"), function (Blueprint $table) {

            $table->bigIncrements("id");

            $table->string("subscribable_id", 26);
            $table->string("subscribable_type");
            $table->index(["subscribable_type", "subscribable_id"], "subscribable_type_id_index");

            $table->string("endpoint", 500)->unique();
            $table->string("public_key")->nullable(true);
            $table->string("auth_token")->nullable(true);
            $table->string("content_encoding")->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::connection(config("webpush.database_connection"))->dropIfExists(config("webpush.table_name"));
    }
};
