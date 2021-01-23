<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerStatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_status_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('status', ['contact', 'prospect', 'active']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_status_histories');
    }
}
