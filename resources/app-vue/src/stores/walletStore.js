import { defineStore } from "pinia";

export const useWalletStore = defineStore("wallet", {
    state: () => ({
        selectedWallet: localStorage.getItem("selectedWallet") ? JSON.parse(localStorage.getItem("selectedWallet")) : null,
        wallets: localStorage.getItem("wallets") ? JSON.parse(localStorage.getItem("wallets")) : [],
    }),
    getters: {
        // walletBalance: (state) => {
        //     return state.selectedWallet
        //         ? state.selectedWallet.transactions.reduce(
        //               (acc, t) => acc + t.amount,
        //               0
        //           )
        //         : 0;
        // },
    },
    actions: {
        setSelectedWallet(wallet) {
            this.selectedWallet = wallet;
            if (wallet) {
                localStorage.setItem("selectedWallet", JSON.stringify(wallet));
            } else {
                localStorage.removeItem("selectedWallet");
            }
        },
        setWallets(wallets) {
            this.wallets = [...wallets];
            localStorage.setItem("wallets", JSON.stringify(wallets));
        },
        addWallet(wallet) {
            this.setWallets([...this.wallets, wallet]);
        },
        updateWallet(updatedWallet) {
            const wallets = this.wallets.map((wallet) =>
                wallet.id === updatedWallet.id ? { ...wallet, ...updatedWallet } : wallet
            );

            this.setWallets(wallets);

            if (this.selectedWallet?.id === updatedWallet.id) {
                this.setSelectedWallet(wallets.find((wallet) => wallet.id === updatedWallet.id) || null);
            }
        },
        removeWallet(walletId) {
            const wallets = this.wallets.filter((wallet) => wallet.id !== walletId);

            this.setWallets(wallets);

            if (this.selectedWallet?.id === walletId) {
                this.setSelectedWallet(wallets[0] || null);
            }
        },
        clearWalletStore() {
            this.wallets = [];
            this.selectedWallet = null;
            localStorage.removeItem("selectedWallet");
            localStorage.removeItem("wallets");
        }
    },
});
