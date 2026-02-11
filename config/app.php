<?php

// Load .env
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

define('APP_PASSWORD', $_ENV['APP_PASSWORD'] ?? '');
define('APP_SECRET', $_ENV['APP_SECRET'] ?? '');
define('DATA_DIR', __DIR__ . '/../data');
define('CSV_DIR', __DIR__ . '/../csv');
define('MAX_CSV_SIZE', 10 * 1024 * 1024); // 10MB

// Genre normalization mappings
define('GENRE_MAPPINGS', [
    'fantasy' => 'Fantasía',
    'epic fantasy' => 'Fantasía',
    'high fantasy' => 'Fantasía',
    'dark fantasy' => 'Fantasía',
    'urban fantasy' => 'Fantasía',
    'sword and sorcery' => 'Fantasía',
    'magic' => 'Fantasía',
    'dragons' => 'Fantasía',
    'wizards' => 'Fantasía',
    'elves' => 'Fantasía',

    'science fiction' => 'Ciencia Ficción',
    'sci-fi' => 'Ciencia Ficción',
    'sf' => 'Ciencia Ficción',
    'space opera' => 'Ciencia Ficción',
    'cyberpunk' => 'Ciencia Ficción',
    'dystopia' => 'Ciencia Ficción',
    'dystopian' => 'Ciencia Ficción',
    'post-apocalyptic' => 'Ciencia Ficción',
    'time travel' => 'Ciencia Ficción',
    'aliens' => 'Ciencia Ficción',
    'robots' => 'Ciencia Ficción',
    'space' => 'Ciencia Ficción',
    'futuristic' => 'Ciencia Ficción',

    'mystery' => 'Misterio',
    'detective' => 'Misterio',
    'crime' => 'Misterio',
    'crime fiction' => 'Misterio',
    'thriller' => 'Misterio',
    'thrillers' => 'Misterio',
    'suspense' => 'Misterio',
    'whodunit' => 'Misterio',
    'noir' => 'Misterio',
    'hard-boiled' => 'Misterio',

    'horror' => 'Terror',
    'gothic' => 'Terror',
    'supernatural' => 'Terror',
    'ghost stories' => 'Terror',
    'vampires' => 'Terror',
    'zombies' => 'Terror',
    'werewolves' => 'Terror',
    'lovecraftian' => 'Terror',
    'cosmic horror' => 'Terror',

    'romance' => 'Romance',
    'love' => 'Romance',
    'romantic' => 'Romance',
    'chick lit' => 'Romance',
    'contemporary romance' => 'Romance',
    'historical romance' => 'Romance',
    'paranormal romance' => 'Romance',

    'historical fiction' => 'Histórica',
    'historical' => 'Histórica',
    'history' => 'Histórica',
    'medieval' => 'Histórica',
    'world war' => 'Histórica',
    'ancient history' => 'Histórica',
    'war' => 'Histórica',
    'military history' => 'Histórica',

    'literary fiction' => 'Narrativa',
    'literary' => 'Narrativa',
    'literature' => 'Narrativa',
    'contemporary fiction' => 'Narrativa',
    'modern literature' => 'Narrativa',
    'general fiction' => 'Narrativa',
    'fiction' => 'Narrativa',
    'novels' => 'Narrativa',

    'philosophy' => 'Filosofía',
    'existentialism' => 'Filosofía',
    'ethics' => 'Filosofía',
    'metaphysics' => 'Filosofía',
    'stoicism' => 'Filosofía',
    'political philosophy' => 'Filosofía',

    'non-fiction' => 'No Ficción',
    'nonfiction' => 'No Ficción',
    'biography' => 'No Ficción',
    'autobiography' => 'No Ficción',
    'memoir' => 'No Ficción',
    'memoirs' => 'No Ficción',
    'self-help' => 'No Ficción',
    'self help' => 'No Ficción',
    'science' => 'No Ficción',
    'psychology' => 'No Ficción',
    'sociology' => 'No Ficción',
    'economics' => 'No Ficción',
    'business' => 'No Ficción',
    'politics' => 'No Ficción',
    'true crime' => 'No Ficción',
    'essays' => 'No Ficción',
    'journalism' => 'No Ficción',
    'travel' => 'No Ficción',

    'comics' => 'Cómic',
    'comic' => 'Cómic',
    'graphic novel' => 'Cómic',
    'graphic novels' => 'Cómic',
    'manga' => 'Cómic',
    'comic book' => 'Cómic',
    'sequential art' => 'Cómic',
    'bande dessinée' => 'Cómic',

    'young adult' => 'Juvenil',
    'ya' => 'Juvenil',
    'teen' => 'Juvenil',
    'adolescent' => 'Juvenil',
    'coming of age' => 'Juvenil',
    'children' => 'Juvenil',
    "children's" => 'Juvenil',
    'juvenile' => 'Juvenil',

    'classics' => 'Clásicos',
    'classic' => 'Clásicos',
    'classic literature' => 'Clásicos',
    'classic fiction' => 'Clásicos',
    'great books' => 'Clásicos',
    'canonical' => 'Clásicos',

    'poetry' => 'Poesía',
    'poems' => 'Poesía',
    'verse' => 'Poesía',
]);
