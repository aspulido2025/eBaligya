<?php
namespace App\Classes;

class UniversalLookup {
    private $pdo;
    private $cache = [];
    private $loaded = false;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    /** Load all lookups into memory */
    private function loadAll() {
        $stmt = $this->pdo->query("SELECT * FROM universal_lookup ORDER BY category, value");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->cache = [];
        foreach ($rows as $row) {
            $this->cache[$row['category']][] = $row;
        }

        $this->loaded = true;
    }

    /** Refresh cached values */
    public function refresh() {
        $this->loadAll();
    }

    /** Get all categories or a specific one */
    public function get($category = null) {
        if (!$this->loaded) {
            $this->loadAll();
        }

        if ($category === null) {
            return $this->cache;
        }

        return $this->cache[$category] ?? [];
    }

    /** Return unique categories list */
    public function getCategories() {
        if (!$this->loaded) {
            $this->loadAll();
        }
        return array_keys($this->cache);
    }

    /**
     * Return mapping of value => description (fallback to value if description is null/empty)
     * - With category → flat map [value => description_or_value]
     * - Without category → grouped map [category => [value => description_or_value]]
     */
    public function map($category = null) {
        if (!$this->loaded) {
            $this->loadAll();
        }

        // Specific category → flat map
        if ($category !== null && $category !== '') {
            $items = $this->get($category);
            $map = [];
            foreach ($items as $row) {
                $map[$row['value']] = !empty($row['description']) ? $row['description'] : $row['value'];
            }
            return $map;
        }

        // All categories → grouped map
        $groupedMap = [];
        foreach ($this->cache as $cat => $items) {
            foreach ($items as $row) {
                $groupedMap[$cat][$row['value']] = !empty($row['description']) ? $row['description'] : $row['value'];
            }
        }
        return $groupedMap;
    }

    /**
     * Helper: Get a single description (with fallback to value)
     */
    public function getDescription(string $category, string $value): string {
        $map = $this->map($category);
        return $map[$value] ?? $value;
    }
}
