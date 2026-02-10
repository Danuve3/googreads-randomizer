<?php

class GenreFetcher
{
    private const API_URL = 'https://openlibrary.org/api/books';
    private const BATCH_SIZE = 10;

    public static function fetchBatch(array $isbns): array
    {
        $results = [];
        $chunks = array_chunk($isbns, self::BATCH_SIZE);

        foreach ($chunks as $chunk) {
            $bibkeys = array_map(fn($isbn) => "ISBN:$isbn", $chunk);
            $url = self::API_URL . '?' . http_build_query([
                'bibkeys' => implode(',', $bibkeys),
                'format' => 'json',
                'jscmd' => 'data',
            ]);

            $context = stream_context_create([
                'http' => [
                    'timeout' => 15,
                    'header' => "User-Agent: GoodReadsRandomizer/1.0\r\n",
                ],
            ]);

            $response = @file_get_contents($url, false, $context);
            if ($response === false) continue;

            $data = json_decode($response, true);
            if (!$data) continue;

            foreach ($chunk as $isbn) {
                $key = "ISBN:$isbn";
                if (isset($data[$key]['subjects'])) {
                    $subjects = array_map(
                        fn($s) => is_array($s) ? ($s['name'] ?? '') : (string) $s,
                        $data[$key]['subjects']
                    );
                    $results[$isbn] = self::normalizeSubjects($subjects);
                } else {
                    $results[$isbn] = [];
                }
            }

            // Rate limiting
            if (count($chunks) > 1) {
                usleep(200000); // 200ms between batches
            }
        }

        return $results;
    }

    public static function normalizeSubjects(array $subjects): array
    {
        $normalized = [];
        foreach ($subjects as $subject) {
            $lower = strtolower(trim($subject));
            foreach (GENRE_MAPPINGS as $keyword => $genre) {
                if ($lower === $keyword || str_contains($lower, $keyword)) {
                    $normalized[$genre] = true;
                    break;
                }
            }
        }
        $result = array_keys($normalized);
        sort($result);
        return $result;
    }

    public static function processBooks(array $books, int $offset = 0, int $limit = 30): array
    {
        $cache = GenreCache::load();

        // Get books with ISBN that haven't been cached yet
        $pending = [];
        foreach ($books as $book) {
            $isbn = $book['isbn'] ?? '';
            if (!$isbn) continue;
            if (isset($cache[$isbn])) continue;
            $pending[] = $isbn;
        }

        // Apply offset and limit
        $batch = array_slice($pending, $offset, $limit);

        if (empty($batch)) {
            return [
                'processed' => 0,
                'total_pending' => 0,
                'total_cached' => count($cache),
            ];
        }

        $results = self::fetchBatch($batch);
        GenreCache::setBatch($results);

        return [
            'processed' => count($results),
            'total_pending' => count($pending) - count($batch),
            'total_cached' => count($cache) + count($results),
        ];
    }

    public static function getProgress(array $books): array
    {
        $cache = GenreCache::load();
        $withIsbn = 0;
        $cached = 0;
        $withoutIsbn = 0;

        foreach ($books as $book) {
            $isbn = $book['isbn'] ?? '';
            if (!$isbn) {
                $withoutIsbn++;
                continue;
            }
            $withIsbn++;
            if (isset($cache[$isbn])) {
                $cached++;
            }
        }

        return [
            'total' => count($books),
            'with_isbn' => $withIsbn,
            'without_isbn' => $withoutIsbn,
            'cached' => $cached,
            'pending' => $withIsbn - $cached,
            'percent' => $withIsbn > 0 ? round(($cached / $withIsbn) * 100) : 0,
        ];
    }
}
