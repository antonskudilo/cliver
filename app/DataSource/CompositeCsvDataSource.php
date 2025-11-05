<?php

namespace App\DataSource;

use FilesystemIterator;

final readonly class CompositeCsvDataSource implements DataSourceInterface
{
    use CsvQueryTrait;

    /**
     * @param string|null $directory
     * @param string $pattern
     * @param string|array|null $fileKeyField
     */
    public function __construct(
        private string|null       $directory = null,
        private string            $pattern = '*.csv',
        private string|array|null $fileKeyField = null,
    ) {}

    /**
     * @param string $table
     * @param array $conditions
     * @param int|null $limit
     * @param int|null $offset
     * @param array $orderBy
     * @return iterable
     */
    public function get(string $table, array $conditions = [], ?int $limit = null, ?int $offset = null, array $orderBy = []): iterable
    {
        $files = $this->getMatchingFiles($conditions);
        $rows = [];

        foreach ($files as $file) {
            foreach ($this->readCsv($file, $conditions, $limit, $offset, $orderBy) as $row) {
                $rows[] = $row;

                if ($limit !== null && count($rows) >= $limit) {
                    break 2;
                }
            }
        }

        return $rows;
    }

    /**
     * @param string $table
     * @param mixed $value
     * @param string $field
     * @return array|null
     */
    public function find(string $table, mixed $value, string $field = 'id'): ?array
    {
        foreach ($this->getMatchingFiles() as $file) {
            if ($row = $this->findInCsv($file, $value, $field)) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @param array $conditions
     * @return array<string>
     */
    private function getMatchingFiles(array $conditions = []): array
    {
        if (!isset($this->fileKeyField, $conditions[$this->fileKeyField][0]['value'])) {
            return $this->allFiles();
        }

        $matchingValue = $conditions[$this->fileKeyField][0]['value'];
        $result = [];

        foreach ((array)$matchingValue as $value) {
            $file = $this->fileResolver($value);

            if (is_file($file)) {
                $result []= $file;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function allFiles(): array
    {
        $path = $this->pathResolver();

        if (!is_dir($path)) {
            return [];
        }

        $result = [];
        $filesystemIterator = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);

        foreach ($filesystemIterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'csv') {
                $result[]= $file->getPathname();
            }
        }

        return $result;
    }

    /**
     * @param string $value
     * @return string
     */
    private function fileResolver(string $value): string
    {
        return join(DIRECTORY_SEPARATOR, [$this->pathResolver(), sprintf($this->pattern, $value)]);
    }

    /**
     * @return string
     */
    private function pathResolver(): string
    {
        $path = config('datasource.csv.path');

        if (isset($this->directory)) {
            $path = join(DIRECTORY_SEPARATOR, [$path, $this->directory]);
        }

        return $path;
    }
}
