// Main SPA controller
const App = {
    async init() {
        if (!Auth.isLoggedIn()) {
            this.showScreen('login');
            Auth.init();
            return;
        }

        Auth.init();
        this.showScreen('app');
        this.setupNavigation();
        this.setupFilters();
        Components.initAuthorSearch();
        this.setupRandomizer();
        this.setupImport();
        this.setupGenreFetch();
        await this.loadData();
    },

    showScreen(name) {
        document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
        const screen = document.getElementById(`screen-${name}`);
        if (screen) screen.classList.add('active');
    },

    // Navigation
    setupNavigation() {
        document.querySelectorAll('[data-nav]').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.nav;
                navigateTo(target);
            });
        });
    },

    navigateTo(view) {
        document.querySelectorAll('.view').forEach(v => v.classList.add('hidden'));
        document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));

        const viewEl = document.getElementById(`view-${view}`);
        if (viewEl) {
            viewEl.classList.remove('hidden');
            viewEl.classList.add('active');
        }

        document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
        document.querySelector(`[data-nav="${view}"]`)?.classList.add('active');

        if (view === 'settings') {
            this.loadGenreProgress();
        }
    },

    // Load initial data
    async loadData() {
        try {
            const [stats, filters] = await Promise.all([
                Api.get('stats'),
                Api.get('filters'),
            ]);

            Components.renderStats(stats);
            Components.populateFilters(filters);
            Components.toggleEmptyState(stats.total > 0);
        } catch (err) {
            console.error('Error loading data:', err);
            Components.toggleEmptyState(false);
        }
    },

    // Filters
    setupFilters() {
        const filterIds = ['filter-shelf', 'filter-genre', 'filter-author', 'filter-bookshelf', 'filter-min-pages', 'filter-max-pages'];
        filterIds.forEach(id => {
            document.getElementById(id)?.addEventListener('change', () => {
                Components.updateClearButton();
            });
            document.getElementById(id)?.addEventListener('input', () => {
                Components.updateClearButton();
            });
        });

        document.getElementById('clear-filters')?.addEventListener('click', () => {
            Components.clearFilters();
        });
    },

    // Randomizer
    setupRandomizer() {
        const btn = document.getElementById('randomize-btn');
        const dice = document.getElementById('dice-icon');

        btn.addEventListener('click', async () => {
            btn.disabled = true;
            dice.classList.add('dice-spinning');

            try {
                const filters = Components.getFilters();
                const book = await Api.get('random', filters);
                Components.renderBook(book);
            } catch (err) {
                // Show inline error
                const result = document.getElementById('book-result');
                result.classList.remove('hidden');
                result.querySelector('.book-card').innerHTML = `
                    <div class="p-6 text-center">
                        <p class="text-text-dim text-sm">${err.message}</p>
                    </div>
                `;
            } finally {
                btn.disabled = false;
                setTimeout(() => dice.classList.remove('dice-spinning'), 600);
            }
        });
    },

    // CSV Import
    setupImport() {
        const fileInput = document.getElementById('csv-file');
        const importBtn = document.getElementById('import-btn');
        const label = document.getElementById('csv-file-label');
        const status = document.getElementById('import-status');

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                label.textContent = fileInput.files[0].name;
                importBtn.disabled = false;
            } else {
                label.textContent = 'Seleccionar archivo CSV';
                importBtn.disabled = true;
            }
        });

        importBtn.addEventListener('click', async () => {
            if (!fileInput.files.length) return;

            importBtn.disabled = true;
            importBtn.textContent = 'Importando...';
            status.classList.add('hidden');

            const formData = new FormData();
            formData.append('csv', fileInput.files[0]);

            try {
                const result = await Api.upload('import', formData);
                status.textContent = result.message;
                status.className = 'text-sm text-center text-green-400';
                status.classList.remove('hidden');

                // Reload data
                await this.loadData();

                // Reset file input
                fileInput.value = '';
                label.textContent = 'Seleccionar archivo CSV';
            } catch (err) {
                status.textContent = err.message;
                status.className = 'text-sm text-center text-red-400';
                status.classList.remove('hidden');
            } finally {
                importBtn.disabled = false;
                importBtn.textContent = 'Importar';
            }
        });
    },

    // Genre fetch
    setupGenreFetch() {
        const btn = document.getElementById('fetch-genres-btn');
        const status = document.getElementById('genre-status');

        btn.addEventListener('click', async () => {
            btn.disabled = true;
            btn.textContent = 'Obteniendo...';
            status.classList.add('hidden');

            const bar = document.getElementById('genre-progress-bar');
            bar.classList.add('progress-shimmer');

            try {
                // Run multiple batches
                let hasMore = true;
                while (hasMore) {
                    const result = await Api.post('fetch-genres');
                    await this.loadGenreProgress();

                    if (result.total_pending === 0 || result.processed === 0) {
                        hasMore = false;
                    }
                }

                status.textContent = 'Géneros actualizados';
                status.className = 'text-sm text-center text-green-400';
                status.classList.remove('hidden');

                // Reload filters to include new genres
                const filters = await Api.get('filters');
                Components.populateFilters(filters);
            } catch (err) {
                status.textContent = err.message;
                status.className = 'text-sm text-center text-red-400';
                status.classList.remove('hidden');
            } finally {
                bar.classList.remove('progress-shimmer');
                await this.loadGenreProgress();
            }
        });
    },

    async loadGenreProgress() {
        try {
            const progress = await Api.get('genre-progress');
            Components.renderGenreProgress(progress);
        } catch (err) {
            console.error('Error loading genre progress:', err);
        }
    },
};

// Global navigation function
function navigateTo(view) {
    App.navigateTo(view);
}

// Boot
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});
