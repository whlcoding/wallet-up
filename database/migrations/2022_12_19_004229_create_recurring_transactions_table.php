<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecurringTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('wallet_id')->unsigned();
            $table->bigInteger('category_id')->unsigned();
            $table->bigInteger('sub_category_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['income', 'expense']);
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreign('wallet_id')->references('id')->on('wallets');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recurring_transactions');
    }
}
