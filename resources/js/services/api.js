import axios from 'axios';

// Always same-origin: avoids localhost vs 127.0.0.1 mismatches after Vite build.
const baseURL = '/api';

const api = axios.create({
    baseURL,
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
});

// 401: redirect to login
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

/**
 * Get axios config with Bearer token for authenticated requests.
 * Only adds Authorization header when token is truthy (avoids invalid "Bearer ").
 */
export function withAuth(token) {
    const headers = {};
    if (token && typeof token === 'string' && token.trim()) {
        headers.Authorization = `Bearer ${token.trim()}`;
    }
    return { headers };
}

export default api;
