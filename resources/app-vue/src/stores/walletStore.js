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
            localStorage.setItem("selectedWallet", JSON.stringify(wallet));

        },
        setWallets(wallets) {
            this.wallets = [...wallets];
            localStorage.setItem("wallets", JSON.stringify(wallets));
        },
        clearWalletStore() {
            this.wallets = [];
            this.selectedWallet = null;
            localStorage.removeItem("selectedWallet");
            localStorage.removeItem("wallets");
        }
    },
});
