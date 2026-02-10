# GoodReads Randomizer

Aplicacion web PHP que lee un CSV exportado de GoodReads y permite obtener un libro aleatorio con filtros. Los generos se obtienen via Open Library API usando el ISBN de cada libro.

## Caracteristicas

- **Randomizer con filtros** — estanteria, genero, autor, bookshelf, rango de paginas
- **Import CSV** — sube tu export de GoodReads y se parsea a JSON
- **Generos automaticos** — fetch batch contra Open Library API con cache local
- **Portadas** — via Open Library Covers API
- **PWA** — instalable, funciona offline para assets estaticos
- **Auth simple** — HMAC token sin sesiones, persistente en localStorage
- **Mobile-first** — UI oscura optimizada para movil

## Stack

- PHP puro (sin frameworks)
- Tailwind CSS via CDN
- Vanilla JS (SPA con navegacion por vistas)
- Open Library API (generos + portadas)
- Service Worker para cache

## Setup local

```bash
# Clonar
git clone https://github.com/Danuve3/googreads-randomizer.git
cd googreads-randomizer

# Configurar
cp .env.example .env
# Editar .env con tu password y secret

# Crear directorios de runtime
mkdir -p data csv

# Iniciar servidor
php -S localhost:8000
```

Abrir `http://localhost:8000`, login con la password del `.env`, e importar el CSV desde Config.

## Estructura

```
├── index.php              # Shell HTML de la SPA
├── api.php                # Router API (7 endpoints)
├── config/app.php         # Carga .env, constantes, genre mappings
├── src/
│   ├── Auth.php           # HMAC token auth
│   ├── CsvParser.php      # Parseo CSV + limpieza ISBN
│   ├── BookRepository.php # Filtrado + seleccion aleatoria
│   ├── GenreFetcher.php   # Cliente Open Library API
│   ├── GenreCache.php     # Cache de generos en JSON
│   └── Response.php       # Helper respuestas JSON
├── assets/
│   ├── css/app.css        # Estilos + animaciones
│   └── js/                # SPA modules (app, api, auth, components, pwa)
├── manifest.json          # PWA manifest
├── sw.js                  # Service Worker
└── .github/workflows/     # Deploy FTP a cPanel
```

## API

| Metodo | Action | Descripcion |
|--------|--------|-------------|
| POST | `login` | Valida password, devuelve token HMAC |
| GET | `stats` | Total libros, por shelf, por rango de paginas |
| GET | `random` | Libro aleatorio con filtros opcionales |
| GET | `filters` | Opciones disponibles para cada filtro |
| POST | `import` | Sube CSV y lo parsea a JSON |
| POST | `fetch-genres` | Procesa batch de libros contra Open Library |
| GET | `genre-progress` | Progreso del fetch de generos |

## Deploy

Push a `main` dispara deploy automatico via GitHub Actions + FTP a cPanel.

Secrets necesarios en GitHub: `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`, `FTP_SERVER_DIR`.

En el servidor crear manualmente `.env` y el directorio `data/`.
