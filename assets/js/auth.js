// Authentication module
const Auth = {
    isLoggedIn() {
        return !!localStorage.getItem('gr_token');
    },

    async login(password) {
        const data = await Api.post('login', { password });
        localStorage.setItem('gr_token', data.token);
        return data;
    },

    logout() {
        localStorage.removeItem('gr_token');
        window.location.hash = '';
        window.location.reload();
    },

    init() {
        const form = document.getElementById('login-form');
        const errorEl = document.getElementById('login-error');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const password = document.getElementById('login-password').value.trim();
            if (!password) return;

            const btn = document.getElementById('login-btn');
            btn.disabled = true;
            btn.textContent = 'Verificando...';
            errorEl.classList.add('hidden');

            try {
                await Auth.login(password);
                App.init();
            } catch (err) {
                errorEl.textContent = err.message;
                errorEl.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Entrar';
            }
        });

        document.getElementById('logout-btn')?.addEventListener('click', () => {
            Auth.logout();
        });
    },
};
