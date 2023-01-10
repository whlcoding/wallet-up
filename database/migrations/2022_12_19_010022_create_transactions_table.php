<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('wallet_id')->unsigned();
            $table->bigInteger('category_id')->unsigned();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['income', 'expense']);
            $table->date('due_date');
            $table->enum('status', ['pending', 'paid', 'overdue']);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_recurring')->default(false);
            $table->bigInteger('recurring_transaction_id')->unsigned()->nullable();
            $table->foreign('wallet_id')->references('id')->on('wallets');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('recurring_transaction_id')->references('id')->on('recurring_transactions');
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
        Schema::dropIfExists('transactions');
    }
}
