<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0f0e17">
    <meta name="description" content="Elige tu próxima lectura al azar desde tu biblioteca de GoodReads">
    <title>GoodReads Randomizer</title>

    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/svg+xml" href="assets/icons/icon.svg">
    <link rel="apple-touch-icon" href="assets/icons/icon-192.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Literata:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: '#0f0e17',
                        surface: '#1a1926',
                        'surface-2': '#232136',
                        accent: '#e94560',
                        purple: '#533483',
                        'purple-light': '#7b5ea7',
                        text: '#fffffe',
                        'text-dim': '#94a1b2',
                    },
                    fontFamily: {
                        heading: ['"Space Grotesk"', 'system-ui', 'sans-serif'],
                        book: ['"Literata"', 'Georgia', 'serif'],
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="bg-bg text-text font-heading min-h-screen">

    <!-- Login Screen -->
    <div id="screen-login" class="screen hidden">
        <div class="min-h-screen flex items-center justify-center px-4">
            <div class="w-full max-w-sm">
                <div class="text-center mb-8">
                    <div class="inline-block mb-4">
                        <svg class="w-16 h-16 text-accent" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="8" y="4" width="16" height="56" rx="2" fill="currentColor" opacity="0.8"/>
                            <rect x="28" y="8" width="16" height="52" rx="2" fill="#533483" opacity="0.9"/>
                            <rect x="48" y="12" width="12" height="48" rx="2" fill="currentColor" opacity="0.6"/>
                            <circle cx="50" cy="20" r="6" fill="#533483"/>
                            <text x="48" y="23" font-size="10" fill="white" font-weight="bold" text-anchor="middle">?</text>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">GoodReads<br><span class="text-accent">Randomizer</span></h1>
                    <p class="text-text-dim text-sm">Tu próxima lectura, al azar</p>
                </div>

                <form id="login-form" class="space-y-4">
                    <div>
                        <input
                            type="password"
                            id="login-password"
                            placeholder="Contraseña"
                            autocomplete="current-password"
                            class="w-full px-4 py-3 bg-surface border border-surface-2 rounded-xl text-text placeholder-text-dim focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
                        >
                    </div>
                    <button
                        type="submit"
                        id="login-btn"
                        class="w-full py-3 bg-accent hover:bg-accent/90 text-white font-semibold rounded-xl transition-all active:scale-[0.98]"
                    >
                        Entrar
                    </button>
                    <p id="login-error" class="text-red-400 text-sm text-center hidden"></p>
                </form>
            </div>
        </div>
    </div>

    <!-- Main App -->
    <div id="screen-app" class="screen hidden">
        <!-- Header -->
        <header class="sticky top-0 z-40 bg-bg/80 backdrop-blur-lg border-b border-surface-2">
            <div class="max-w-lg mx-auto px-4 py-3 flex items-center justify-between">
                <h1 class="text-lg font-bold">
                    <span class="text-accent">GR</span> Randomizer
                </h1>
                <nav class="flex items-center gap-1">
                    <button data-nav="home" class="nav-btn active px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                        Inicio
                    </button>
                    <button data-nav="settings" class="nav-btn px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                        Config
                    </button>
                </nav>
            </div>
        </header>

        <!-- Home View -->
        <div id="view-home" class="view">
            <div class="max-w-lg mx-auto px-4 py-6 space-y-6">

                <!-- Stats Bar -->
                <div id="stats-bar" class="grid grid-cols-3 gap-3">
                    <div class="bg-surface rounded-xl p-3 text-center">
                        <p class="text-2xl font-bold text-accent" id="stat-total">-</p>
                        <p class="text-xs text-text-dim">Libros</p>
                    </div>
                    <div class="bg-surface rounded-xl p-3 text-center">
                        <p class="text-2xl font-bold text-purple-light" id="stat-read">-</p>
                        <p class="text-xs text-text-dim">Leídos</p>
                    </div>
                    <div class="bg-surface rounded-xl p-3 text-center">
                        <p class="text-2xl font-bold text-text" id="stat-toread">-</p>
                        <p class="text-xs text-text-dim">Por leer</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-surface rounded-xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-text-dim uppercase tracking-wider">Filtros</h2>
                        <button id="clear-filters" class="text-xs text-accent hover:text-accent/80 transition-colors hidden">
                            Limpiar
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-text-dim mb-1 block">Estantería</label>
                            <select id="filter-shelf" class="filter-select">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-text-dim mb-1 block">Género</label>
                            <select id="filter-genre" class="filter-select">
                                <option value="">Todos</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-text-dim mb-1 block">Autor</label>
                            <select id="filter-author" class="filter-select">
                                <option value="">Todos</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-text-dim mb-1 block">Bookshelf</label>
                            <select id="filter-bookshelf" class="filter-select">
                                <option value="">Todos</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-text-dim mb-1 block">Mín. páginas</label>
                            <input type="number" id="filter-min-pages" placeholder="0" min="0" class="filter-input">
                        </div>
                        <div>
                            <label class="text-xs text-text-dim mb-1 block">Máx. páginas</label>
                            <input type="number" id="filter-max-pages" placeholder="∞" min="0" class="filter-input">
                        </div>
                    </div>
                </div>

                <!-- Randomize Button -->
                <button
                    id="randomize-btn"
                    class="w-full py-4 bg-gradient-to-r from-accent to-purple text-white font-bold text-lg rounded-xl transition-all active:scale-[0.97] hover:shadow-lg hover:shadow-accent/20 flex items-center justify-center gap-3"
                >
                    <svg id="dice-icon" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="4"/>
                        <circle cx="8" cy="8" r="1.5" fill="currentColor"/>
                        <circle cx="16" cy="8" r="1.5" fill="currentColor"/>
                        <circle cx="8" cy="16" r="1.5" fill="currentColor"/>
                        <circle cx="16" cy="16" r="1.5" fill="currentColor"/>
                        <circle cx="12" cy="12" r="1.5" fill="currentColor"/>
                    </svg>
                    Elegir libro al azar
                </button>

                <!-- Result Card -->
                <div id="book-result" class="hidden">
                    <div class="book-card bg-surface rounded-2xl overflow-hidden border border-surface-2">
                        <div class="flex p-4 gap-4">
                            <!-- Cover -->
                            <div class="flex-shrink-0">
                                <div id="book-cover-container" class="w-28 h-40 bg-surface-2 rounded-lg overflow-hidden flex items-center justify-center">
                                    <img id="book-cover" class="w-full h-full object-cover hidden" alt="Portada">
                                    <svg id="book-cover-placeholder" class="w-10 h-10 text-text-dim" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0 flex flex-col justify-between">
                                <div>
                                    <h3 id="book-title" class="font-book text-lg font-bold leading-tight mb-1"></h3>
                                    <p id="book-author" class="text-text-dim text-sm mb-2"></p>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2 text-xs text-text-dim">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                                        <span id="book-pages"></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-text-dim">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        <span id="book-rating"></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-text-dim">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/></svg>
                                        <span id="book-shelf"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Genres -->
                        <div id="book-genres" class="px-4 pb-4 flex flex-wrap gap-1.5 hidden"></div>
                    </div>
                </div>

                <!-- Empty state (no books imported) -->
                <div id="empty-state" class="hidden text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-text-dim mb-4 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                    </svg>
                    <p class="text-text-dim mb-4">No hay libros importados</p>
                    <button onclick="navigateTo('settings')" class="text-accent hover:underline text-sm">
                        Ir a Config para importar tu CSV
                    </button>
                </div>
            </div>
        </div>

        <!-- Settings View -->
        <div id="view-settings" class="view hidden">
            <div class="max-w-lg mx-auto px-4 py-6 space-y-6">
                <h2 class="text-xl font-bold">Configuración</h2>

                <!-- Import CSV -->
                <div class="bg-surface rounded-xl p-4 space-y-4">
                    <h3 class="font-semibold text-sm uppercase tracking-wider text-text-dim">Importar CSV</h3>
                    <p class="text-text-dim text-xs">Exporta tu biblioteca desde GoodReads y sube el archivo CSV aquí.</p>

                    <div class="relative">
                        <input
                            type="file"
                            id="csv-file"
                            accept=".csv"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        >
                        <div class="border-2 border-dashed border-surface-2 rounded-xl p-6 text-center hover:border-accent/50 transition-colors">
                            <svg class="w-8 h-8 mx-auto text-text-dim mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                            </svg>
                            <p class="text-sm text-text-dim" id="csv-file-label">Seleccionar archivo CSV</p>
                        </div>
                    </div>

                    <button
                        id="import-btn"
                        class="w-full py-2.5 bg-accent hover:bg-accent/90 text-white font-semibold rounded-xl transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled
                    >
                        Importar
                    </button>
                    <p id="import-status" class="text-sm text-center hidden"></p>
                </div>

                <!-- Fetch Genres -->
                <div class="bg-surface rounded-xl p-4 space-y-4">
                    <h3 class="font-semibold text-sm uppercase tracking-wider text-text-dim">Géneros</h3>
                    <p class="text-text-dim text-xs">Obtiene géneros desde Open Library API usando el ISBN de cada libro.</p>

                    <div id="genre-progress-container" class="space-y-2">
                        <div class="flex justify-between text-xs text-text-dim">
                            <span id="genre-progress-text">-</span>
                            <span id="genre-progress-pct">0%</span>
                        </div>
                        <div class="w-full bg-surface-2 rounded-full h-2">
                            <div id="genre-progress-bar" class="bg-gradient-to-r from-accent to-purple h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <button
                        id="fetch-genres-btn"
                        class="w-full py-2.5 bg-purple hover:bg-purple/90 text-white font-semibold rounded-xl transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Obtener géneros
                    </button>
                    <p id="genre-status" class="text-sm text-center hidden"></p>
                </div>

                <!-- Logout -->
                <button
                    id="logout-btn"
                    class="w-full py-2.5 bg-surface border border-surface-2 text-text-dim hover:text-text hover:border-accent/30 font-medium rounded-xl transition-all"
                >
                    Cerrar sesión
                </button>
            </div>
        </div>
    </div>

    <!-- Install PWA Banner -->
    <div id="pwa-install" class="hidden fixed bottom-4 left-4 right-4 max-w-lg mx-auto bg-surface border border-surface-2 rounded-2xl p-4 shadow-xl z-50">
        <div class="flex items-center gap-3">
            <div class="flex-1">
                <p class="font-semibold text-sm">Instalar app</p>
                <p class="text-xs text-text-dim">Acceso rápido desde tu pantalla</p>
            </div>
            <button id="pwa-install-btn" class="px-4 py-2 bg-accent text-white text-sm font-semibold rounded-lg">
                Instalar
            </button>
            <button id="pwa-dismiss-btn" class="text-text-dim hover:text-text p-1">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <script src="assets/js/api.js"></script>
    <script src="assets/js/auth.js"></script>
    <script src="assets/js/components.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/pwa.js"></script>
</body>
</html>
