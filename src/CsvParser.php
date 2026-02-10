<?php

class CsvParser
{
    private const REQUIRED_HEADERS = [
        'Title',
        'Author',
        'ISBN',
        'ISBN13',
        'Number of Pages',
        'Exclusive Shelf',
    ];

    public static function parseUpload(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Response::error('Error al subir el archivo');
        }

        if ($file['size'] > MAX_CSV_SIZE) {
            Response::error('El archivo excede el tamaño máximo de 10MB');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ['text/csv', 'text/plain', 'application/csv', 'application/octet-stream'])) {
            Response::error('El archivo debe ser un CSV válido');
        }

        return self::parse($file['tmp_name']);
    }

    public static function parse(string $filepath): array
    {
        $handle = fopen($filepath, 'r');
        if (!$handle) {
            Response::error('No se pudo leer el archivo CSV');
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            Response::error('El CSV está vacío');
        }

        // Clean BOM from first header
        $headers[0] = preg_replace('/^\x{FEFF}/u', '', $headers[0]);
        $headers = array_map('trim', $headers);

        // Validate required headers
        $missing = array_diff(self::REQUIRED_HEADERS, $headers);
        if (!empty($missing)) {
            fclose($handle);
            Response::error('Faltan columnas requeridas: ' . implode(', ', $missing));
        }

        $books = [];
        $id = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) continue;
            $data = array_combine($headers, $row);
            $book = self::normalizeBook($data, $id);
            if ($book) {
                $books[] = $book;
                $id++;
            }
        }

        fclose($handle);

        if (empty($books)) {
            Response::error('No se encontraron libros válidos en el CSV');
        }

        // Save to JSON
        if (!is_dir(DATA_DIR)) {
            mkdir(DATA_DIR, 0755, true);
        }
        file_put_contents(
            DATA_DIR . '/books.json',
            json_encode($books, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

        // Save original CSV
        if (!is_dir(CSV_DIR)) {
            mkdir(CSV_DIR, 0755, true);
        }
        copy($filepath, CSV_DIR . '/goodreads_library_export.csv');

        return $books;
    }

    private static function normalizeBook(array $data, int $id): ?array
    {
        $title = trim($data['Title'] ?? '');
        $author = trim($data['Author'] ?? '');
        if (!$title || !$author) return null;

        $isbn = self::cleanIsbn($data['ISBN'] ?? '');
        $isbn13 = self::cleanIsbn($data['ISBN13'] ?? '');

        $pages = (int) ($data['Number of Pages'] ?? 0);
        $rating = (float) ($data['My Rating'] ?? 0);
        $avgRating = (float) ($data['Average Rating'] ?? 0);
        $shelf = trim($data['Exclusive Shelf'] ?? 'to-read');
        $dateAdded = trim($data['Date Added'] ?? '');
        $dateRead = trim($data['Date Read'] ?? '');
        $bookshelves = trim($data['Bookshelves'] ?? '');

        return [
            'id' => $id,
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn ?: $isbn13,
            'isbn13' => $isbn13 ?: $isbn,
            'pages' => $pages,
            'my_rating' => $rating,
            'avg_rating' => $avgRating,
            'shelf' => $shelf,
            'bookshelves' => $bookshelves ? array_map('trim', explode(',', $bookshelves)) : [],
            'date_added' => $dateAdded,
            'date_read' => $dateRead,
        ];
    }

    private static function cleanIsbn(string $isbn): string
    {
        // GoodReads wraps ISBNs in ="XXXXX"
        $isbn = preg_replace('/[=""\s]/', '', $isbn);
        return $isbn;
    }
}
