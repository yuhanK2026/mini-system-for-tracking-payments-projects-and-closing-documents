import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Set the base URL for API requests
window.axios.defaults.baseURL = '/api';
