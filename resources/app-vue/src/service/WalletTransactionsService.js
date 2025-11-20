import axios from "axios";

const walletTransactionsService = {
    async getTransactions(walletId) {
        return await axios.get(`http://localhost:8000/api/v1/wallets/${walletId}/transactions`);
    },
    async createTransaction(walletId, transaction) {
        return await axios.post(`http://localhost:8000/api/v1/wallets/${walletId}/transactions`, transaction);
    },
    async update(walletId, transactionId, data) {
        return await axios.put(`http://localhost:8000/api/v1/wallets/${walletId}/transactions/${transactionId}`, data);
    },
    async delete(walletId, transactionId) {
        return await axios.delete(`http://localhost:8000/api/v1/wallets/${walletId}/transactions/${transactionId}`);
    }

};

export default walletTransactionsService;
