import axios from "axios";

const walletTransactionsService = {
    async getTransactions(walletId) {
        return await axios.get(`http://localhost:8000/api/v1/wallets/${walletId}/transactions`);
    },
    async createTransaction(walletId, transaction) {
        return await axios.post(`http://localhost:8000/api/v1/wallets/${walletId}/transactions`, transaction);
    }

};

export default walletTransactionsService;
