import axios from "axios";

export default class AuthService {
    constructor() {
        this.axios = axios;
        this.axios.defaults.baseURL = 'http://localhost:8000/api/v1/';
    }

    async login(email, password) {
        return await this.axios.post('/login', { email, password });
    }

    async logout() {
        return await this.axios.post('/logout');
    }
}
