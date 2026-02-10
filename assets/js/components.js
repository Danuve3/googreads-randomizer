// UI Components
const Components = {

    // Populate filter select options
    populateFilters(data) {
        const populate = (id, items) => {
            const select = document.getElementById(id);
            const current = select.value;
            // Keep first option
            while (select.options.length > 1) select.remove(1);
            items.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item;
                opt.textContent = item;
                select.appendChild(opt);
            });
            // Restore selection if still valid
            if (current && [...select.options].some(o => o.value === current)) {
                select.value = current;
            }
        };

        populate('filter-shelf', data.shelves);
        populate('filter-genre', data.genres);
        populate('filter-author', data.authors);
        populate('filter-bookshelf', data.bookshelves);
    },

    // Render stats
    renderStats(stats) {
        document.getElementById('stat-total').textContent = stats.total || 0;
        document.getElementById('stat-read').textContent = stats.by_shelf?.read || 0;
        document.getElementById('stat-toread').textContent = stats.by_shelf?.['to-read'] || 0;
    },

    // Get current filter values
    getFilters() {
        return {
            shelf: document.getElementById('filter-shelf').value,
            genre: document.getElementById('filter-genre').value,
            author: document.getElementById('filter-author').value,
            bookshelf: document.getElementById('filter-bookshelf').value,
            min_pages: document.getElementById('filter-min-pages').value,
            max_pages: document.getElementById('filter-max-pages').value,
        };
    },

    // Check if any filter is active
    hasActiveFilters() {
        const f = this.getFilters();
        return Object.values(f).some(v => v !== '');
    },

    // Clear all filters
    clearFilters() {
        document.getElementById('filter-shelf').value = '';
        document.getElementById('filter-genre').value = '';
        document.getElementById('filter-author').value = '';
        document.getElementById('filter-bookshelf').value = '';
        document.getElementById('filter-min-pages').value = '';
        document.getElementById('filter-max-pages').value = '';
        document.getElementById('clear-filters').classList.add('hidden');
    },

    // Show/hide clear filters button
    updateClearButton() {
        const btn = document.getElementById('clear-filters');
        if (this.hasActiveFilters()) {
            btn.classList.remove('hidden');
        } else {
            btn.classList.add('hidden');
        }
    },

    // Render a book card
    renderBook(book) {
        const container = document.getElementById('book-result');
        container.classList.remove('hidden');

        // Force re-animation
        const card = container.querySelector('.book-card');
        card.style.animation = 'none';
        card.offsetHeight; // trigger reflow
        card.style.animation = '';

        // Title & Author
        document.getElementById('book-title').textContent = book.title;
        document.getElementById('book-author').textContent = book.author;

        // Cover
        const coverImg = document.getElementById('book-cover');
        const coverPlaceholder = document.getElementById('book-cover-placeholder');
        const coverContainer = document.getElementById('book-cover-container');

        coverContainer.classList.remove('cover-error');

        if (book.cover_url) {
            coverImg.classList.remove('hidden');
            coverPlaceholder.classList.add('hidden');
            coverImg.src = book.cover_url;
            coverImg.onerror = () => {
                coverImg.classList.add('hidden');
                coverPlaceholder.classList.remove('hidden');
                coverContainer.classList.add('cover-error');
            };
        } else {
            coverImg.classList.add('hidden');
            coverPlaceholder.classList.remove('hidden');
            coverContainer.classList.add('cover-error');
        }

        // Details
        document.getElementById('book-pages').textContent = book.pages > 0 ? `${book.pages} páginas` : 'Páginas desconocidas';
        document.getElementById('book-rating').textContent = book.avg_rating > 0 ? `${book.avg_rating} promedio` : 'Sin calificación';
        document.getElementById('book-shelf').textContent = book.shelf;

        // Genres
        const genresContainer = document.getElementById('book-genres');
        genresContainer.innerHTML = '';
        if (book.genres && book.genres.length > 0) {
            genresContainer.classList.remove('hidden');
            book.genres.forEach(genre => {
                const tag = document.createElement('span');
                tag.className = 'genre-tag';
                tag.textContent = genre;
                genresContainer.appendChild(tag);
            });
        } else {
            genresContainer.classList.add('hidden');
        }
    },

    // Show genre progress
    renderGenreProgress(progress) {
        document.getElementById('genre-progress-text').textContent =
            `${progress.cached}/${progress.with_isbn} libros con género (${progress.without_isbn} sin ISBN)`;
        document.getElementById('genre-progress-pct').textContent = `${progress.percent}%`;
        document.getElementById('genre-progress-bar').style.width = `${progress.percent}%`;

        if (progress.pending === 0) {
            document.getElementById('fetch-genres-btn').textContent = 'Completado';
            document.getElementById('fetch-genres-btn').disabled = true;
        } else {
            document.getElementById('fetch-genres-btn').textContent = `Obtener géneros (${progress.pending} pendientes)`;
            document.getElementById('fetch-genres-btn').disabled = false;
        }
    },

    // Show empty state or randomizer
    toggleEmptyState(hasBooks) {
        document.getElementById('empty-state').classList.toggle('hidden', hasBooks);
        document.getElementById('stats-bar').classList.toggle('hidden', !hasBooks);
        document.getElementById('randomize-btn').classList.toggle('hidden', !hasBooks);
        document.querySelector('#view-home .bg-surface').classList.toggle('hidden', !hasBooks);
    },
};
