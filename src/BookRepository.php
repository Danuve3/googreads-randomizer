<?php

class BookRepository
{
    private array $books;
    private array $genres;

    public function __construct()
    {
        $booksFile = DATA_DIR . '/books.json';
        $genresFile = DATA_DIR . '/genres.json';

        $this->books = file_exists($booksFile)
            ? json_decode(file_get_contents($booksFile), true) ?: []
            : [];

        $this->genres = file_exists($genresFile)
            ? json_decode(file_get_contents($genresFile), true) ?: []
            : [];
    }

    public function getBooks(): array
    {
        return $this->books;
    }

    public function stats(): array
    {
        $total = count($this->books);
        $byShelves = [];
        $byPages = ['0-100' => 0, '101-200' => 0, '201-300' => 0, '301-500' => 0, '500+' => 0];
        $withIsbn = 0;
        $withGenres = 0;

        foreach ($this->books as $book) {
            $shelf = $book['shelf'];
            $byShelves[$shelf] = ($byShelves[$shelf] ?? 0) + 1;

            $p = $book['pages'];
            if ($p <= 100) $byPages['0-100']++;
            elseif ($p <= 200) $byPages['101-200']++;
            elseif ($p <= 300) $byPages['201-300']++;
            elseif ($p <= 500) $byPages['301-500']++;
            else $byPages['500+']++;

            if (!empty($book['isbn'])) $withIsbn++;
            if (isset($this->genres[$book['isbn']]) && !empty($this->genres[$book['isbn']])) {
                $withGenres++;
            }
        }

        return [
            'total' => $total,
            'by_shelf' => $byShelves,
            'by_pages' => $byPages,
            'with_isbn' => $withIsbn,
            'with_genres' => $withGenres,
        ];
    }

    public function filters(): array
    {
        $authors = [];
        $shelves = [];
        $bookshelves = [];
        $genres = [];

        foreach ($this->books as $book) {
            $authors[$book['author']] = true;
            $shelves[$book['shelf']] = true;
            foreach ($book['bookshelves'] as $bs) {
                if ($bs) $bookshelves[$bs] = true;
            }
        }

        foreach ($this->genres as $isbn => $bookGenres) {
            foreach ($bookGenres as $g) {
                $genres[$g] = true;
            }
        }

        ksort($authors);
        ksort($shelves);
        ksort($bookshelves);
        ksort($genres);

        return [
            'authors' => array_keys($authors),
            'shelves' => array_keys($shelves),
            'bookshelves' => array_keys($bookshelves),
            'genres' => array_keys($genres),
        ];
    }

    public function random(array $params): ?array
    {
        $filtered = $this->books;

        // Filter by shelf
        if (!empty($params['shelf'])) {
            $shelf = $params['shelf'];
            $filtered = array_filter($filtered, fn($b) => $b['shelf'] === $shelf);
        }

        // Filter by bookshelf
        if (!empty($params['bookshelf'])) {
            $bs = $params['bookshelf'];
            $filtered = array_filter($filtered, fn($b) => in_array($bs, $b['bookshelves']));
        }

        // Filter by author
        if (!empty($params['author'])) {
            $author = $params['author'];
            $filtered = array_filter($filtered, fn($b) => $b['author'] === $author);
        }

        // Filter by min pages
        if (!empty($params['min_pages'])) {
            $min = (int) $params['min_pages'];
            $filtered = array_filter($filtered, fn($b) => $b['pages'] >= $min);
        }

        // Filter by max pages
        if (!empty($params['max_pages'])) {
            $max = (int) $params['max_pages'];
            $filtered = array_filter($filtered, fn($b) => $b['pages'] > 0 && $b['pages'] <= $max);
        }

        // Filter by genre
        if (!empty($params['genre'])) {
            $genre = $params['genre'];
            $filtered = array_filter($filtered, function ($b) use ($genre) {
                $isbn = $b['isbn'];
                if (!$isbn || !isset($this->genres[$isbn])) return false;
                return in_array($genre, $this->genres[$isbn]);
            });
        }

        $filtered = array_values($filtered);
        if (empty($filtered)) return null;

        $book = $filtered[array_rand($filtered)];

        // Attach genres to the selected book
        $book['genres'] = [];
        if (!empty($book['isbn']) && isset($this->genres[$book['isbn']])) {
            $book['genres'] = $this->genres[$book['isbn']];
        }

        // Cover URL
        $book['cover_url'] = '';
        if (!empty($book['isbn'])) {
            $book['cover_url'] = "https://covers.openlibrary.org/b/isbn/{$book['isbn']}-M.jpg";
        }

        return $book;
    }
}
