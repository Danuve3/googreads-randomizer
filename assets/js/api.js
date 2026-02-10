// HTTP client with auth headers
const Api = {
    baseUrl: 'api.php',

    getToken() {
        return localStorage.getItem('gr_token') || '';
    },

    async request(action, options = {}) {
        const { method = 'GET', body = null, params = {} } = options;

        let url = `${this.baseUrl}?action=${encodeURIComponent(action)}`;
        if (method === 'GET' && Object.keys(params).length > 0) {
            for (const [key, val] of Object.entries(params)) {
                if (val !== '' && val !== null && val !== undefined) {
                    url += `&${encodeURIComponent(key)}=${encodeURIComponent(val)}`;
                }
            }
        }

        const headers = {};
        const token = this.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const fetchOptions = { method, headers };

        if (body instanceof FormData) {
            fetchOptions.body = body;
        } else if (body) {
            headers['Content-Type'] = 'application/json';
            fetchOptions.body = JSON.stringify(body);
        }

        const res = await fetch(url, fetchOptions);
        const data = await res.json();

        if (!data.ok) {
            if (res.status === 401) {
                localStorage.removeItem('gr_token');
                window.location.hash = '';
                window.location.reload();
            }
            throw new Error(data.message || 'Error desconocido');
        }

        return data.data;
    },

    // Convenience methods
    get(action, params = {}) {
        return this.request(action, { method: 'GET', params });
    },

    post(action, body = null) {
        return this.request(action, { method: 'POST', body });
    },

    upload(action, formData) {
        return this.request(action, { method: 'POST', body: formData });
    },
};
