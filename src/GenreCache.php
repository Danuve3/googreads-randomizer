<?php

class GenreCache
{
    private static function getPath(): string
    {
        return DATA_DIR . '/genres.json';
    }

    public static function load(): array
    {
        $path = self::getPath();
        if (!file_exists($path)) return [];
        return json_decode(file_get_contents($path), true) ?: [];
    }

    public static function save(array $genres): void
    {
        if (!is_dir(DATA_DIR)) {
            mkdir(DATA_DIR, 0755, true);
        }
        file_put_contents(
            self::getPath(),
            json_encode($genres, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }

    public static function get(string $isbn): ?array
    {
        $genres = self::load();
        return $genres[$isbn] ?? null;
    }

    public static function set(string $isbn, array $genreList): void
    {
        $genres = self::load();
        $genres[$isbn] = $genreList;
        self::save($genres);
    }

    public static function setBatch(array $batch): void
    {
        $genres = self::load();
        foreach ($batch as $isbn => $genreList) {
            $genres[$isbn] = $genreList;
        }
        self::save($genres);
    }
}
