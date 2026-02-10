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
    'fantasy' => 'Fantasy',
    'epic fantasy' => 'Fantasy',
    'high fantasy' => 'Fantasy',
    'dark fantasy' => 'Fantasy',
    'urban fantasy' => 'Fantasy',
    'sword and sorcery' => 'Fantasy',
    'magic' => 'Fantasy',
    'dragons' => 'Fantasy',
    'wizards' => 'Fantasy',
    'elves' => 'Fantasy',

    'science fiction' => 'Sci-Fi',
    'sci-fi' => 'Sci-Fi',
    'sf' => 'Sci-Fi',
    'space opera' => 'Sci-Fi',
    'cyberpunk' => 'Sci-Fi',
    'dystopia' => 'Sci-Fi',
    'dystopian' => 'Sci-Fi',
    'post-apocalyptic' => 'Sci-Fi',
    'time travel' => 'Sci-Fi',
    'aliens' => 'Sci-Fi',
    'robots' => 'Sci-Fi',
    'space' => 'Sci-Fi',
    'futuristic' => 'Sci-Fi',

    'mystery' => 'Mystery',
    'detective' => 'Mystery',
    'crime' => 'Mystery',
    'crime fiction' => 'Mystery',
    'thriller' => 'Mystery',
    'thrillers' => 'Mystery',
    'suspense' => 'Mystery',
    'whodunit' => 'Mystery',
    'noir' => 'Mystery',
    'hard-boiled' => 'Mystery',

    'horror' => 'Horror',
    'gothic' => 'Horror',
    'supernatural' => 'Horror',
    'ghost stories' => 'Horror',
    'vampires' => 'Horror',
    'zombies' => 'Horror',
    'werewolves' => 'Horror',
    'lovecraftian' => 'Horror',
    'cosmic horror' => 'Horror',

    'romance' => 'Romance',
    'love' => 'Romance',
    'romantic' => 'Romance',
    'chick lit' => 'Romance',
    'contemporary romance' => 'Romance',
    'historical romance' => 'Romance',
    'paranormal romance' => 'Romance',

    'historical fiction' => 'Historical',
    'historical' => 'Historical',
    'history' => 'Historical',
    'medieval' => 'Historical',
    'world war' => 'Historical',
    'ancient history' => 'Historical',
    'war' => 'Historical',
    'military history' => 'Historical',

    'literary fiction' => 'Literary',
    'literary' => 'Literary',
    'literature' => 'Literary',
    'contemporary fiction' => 'Literary',
    'modern literature' => 'Literary',
    'general fiction' => 'Literary',
    'fiction' => 'Literary',
    'novels' => 'Literary',

    'philosophy' => 'Philosophy',
    'existentialism' => 'Philosophy',
    'ethics' => 'Philosophy',
    'metaphysics' => 'Philosophy',
    'stoicism' => 'Philosophy',
    'political philosophy' => 'Philosophy',

    'non-fiction' => 'Non-Fiction',
    'nonfiction' => 'Non-Fiction',
    'biography' => 'Non-Fiction',
    'autobiography' => 'Non-Fiction',
    'memoir' => 'Non-Fiction',
    'memoirs' => 'Non-Fiction',
    'self-help' => 'Non-Fiction',
    'self help' => 'Non-Fiction',
    'science' => 'Non-Fiction',
    'psychology' => 'Non-Fiction',
    'sociology' => 'Non-Fiction',
    'economics' => 'Non-Fiction',
    'business' => 'Non-Fiction',
    'politics' => 'Non-Fiction',
    'true crime' => 'Non-Fiction',
    'essays' => 'Non-Fiction',
    'journalism' => 'Non-Fiction',
    'travel' => 'Non-Fiction',

    'comics' => 'Comics',
    'comic' => 'Comics',
    'graphic novel' => 'Comics',
    'graphic novels' => 'Comics',
    'manga' => 'Comics',
    'comic book' => 'Comics',
    'sequential art' => 'Comics',
    'bande dessinée' => 'Comics',

    'young adult' => 'Young Adult',
    'ya' => 'Young Adult',
    'teen' => 'Young Adult',
    'adolescent' => 'Young Adult',
    'coming of age' => 'Young Adult',
    'children' => 'Young Adult',
    "children's" => 'Young Adult',
    'juvenile' => 'Young Adult',

    'classics' => 'Classics',
    'classic' => 'Classics',
    'classic literature' => 'Classics',
    'classic fiction' => 'Classics',
    'great books' => 'Classics',
    'canonical' => 'Classics',

    'poetry' => 'Poetry',
    'poems' => 'Poetry',
    'verse' => 'Poetry',
]);
