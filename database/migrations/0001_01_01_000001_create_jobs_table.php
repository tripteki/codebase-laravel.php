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
        Schema::create("jobs", function (Blueprint $table) {

            $table->bigIncrements("id");
            $table->string("queue");
            $table->longText("payload");
            $table->unsignedTinyInteger("attempts");
            $table->unsignedBigInteger("created_at");
            $table->unsignedBigInteger("available_at");
            $table->unsignedBigInteger("reserved_at")->nullable();

            $table->index("queue");
        });

        Schema::create("job_batches", function (Blueprint $table) {

            $table->string("id");
            $table->string("name");
            $table->longText("failed_job_ids");
            $table->mediumText("options")->nullable();
            $table->unsignedBigInteger("total_jobs");
            $table->unsignedBigInteger("pending_jobs");
            $table->unsignedBigInteger("failed_jobs");
            $table->unsignedBigInteger("created_at");
            $table->unsignedBigInteger("cancelled_at")->nullable();
            $table->unsignedBigInteger("finished_at")->nullable();

            $table->primary("id");
        });

        Schema::create("failed_jobs", function (Blueprint $table) {

            $table->bigIncrements("id");
            $table->string("uuid");
            $table->text("connection");
            $table->text("queue");
            $table->longText("payload");
            $table->longText("exception");
            $table->timestamp("failed_at")->useCurrent();

            $table->unique("uuid");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists("jobs");
        Schema::dropIfExists("job_batches");
        Schema::dropIfExists("failed_jobs");
    }
};
