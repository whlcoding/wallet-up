<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CalcWalletBalanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:calc-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and update the balance for all wallets based on their transactions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $wallets = Wallet::where('id', 9)->get();

        foreach ($wallets as $wallet) {
            $this->calculateBalanceForWallet($wallet);
            $this->info("Calculated balance for wallet ID: {$wallet->id}");
        }

        return CommandAlias::SUCCESS;
    }

    public function calculateBalanceForWallet(Wallet $wallet): void
    {
        $incomes = $wallet->transactions()->where('type', 'income')->sum('amount');
        $expenses = $wallet->transactions()->where('type', 'expense')->sum('amount');

        // we probably need an initial balance too
        $balance = $incomes - $expenses;
        $wallet->balance = $balance;
        $wallet->save();
    }
}
