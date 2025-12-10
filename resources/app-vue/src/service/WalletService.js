
import axios from 'axios';
const walletService = {
    async getAll() {
        return await axios.get('http://localhost:8000/api/v1/wallets');
    },
    async create(data) {
        return await axios.post('http://localhost:8000/api/v1/wallets', data);
    },
    async update(walletId, data) {
        return await axios.put(`http://localhost:8000/api/v1/wallets/${walletId}`, data);
    },
    async delete(walletId) {
        return await axios.delete(`http://localhost:8000/api/v1/wallets/${walletId}`);
    }
};
export default walletService;
