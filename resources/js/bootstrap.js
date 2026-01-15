import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// JWT token handling
const token = localStorage.getItem('jwt_token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
}
