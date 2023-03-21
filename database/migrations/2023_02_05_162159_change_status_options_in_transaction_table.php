<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeStatusOptionsInTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // $table->enum('status', ['pending', 'paid', 'overdue', 'voided'])->change(); // This will not work because it's not supported by Doctrine DBAL. try to update to laravel 9+

            DB::statement("ALTER TABLE transactions MODIFY status ENUM('pending', 'paid', 'overdue', 'voided') NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            DB::statement("ALTER TABLE transactions MODIFY status ENUM('pending', 'paid', 'overdue', 'voided') NOT NULL DEFAULT 'pending'");
        });
    }
}
