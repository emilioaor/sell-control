<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumObservationsToWholesalers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wholesalers', function (Blueprint $table) {
            $table->text('observations')->nullable();
        });

        \App\Wholesaler::query()->update(['observations' => 'No comments']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wholesalers', function (Blueprint $table) {
            $table->dropColumn('observations');
        });
    }
}
