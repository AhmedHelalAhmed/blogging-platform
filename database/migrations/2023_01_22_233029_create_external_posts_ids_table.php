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
    public function up()
    {
        Schema::create('external_posts_ids', function (Blueprint $table) {
            $table->unsignedBigInteger('external_id')
                ->index()
                ->comment('This is the external id of the post that comes in the payload that is imported from external API I stored it to identify which posts that already synced to the system and prevent duplication');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('external_posts_ids');
    }
};
