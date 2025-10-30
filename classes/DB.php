<?php

    namespace App\Classes;

    use PDO;
    use PDOStatement;

    class DB {
        private PDO $pdo;

        public function __construct(PDO $pdo) {
            $this->pdo = $pdo;
        }

        /*
        * Transaction handling
        */
        public function beginTransaction(): bool {
            return $this->pdo->beginTransaction();
        }

        public function commit(): bool {
            return $this->pdo->commit();
        }

        public function rollBack(): bool {
            return $this->pdo->rollBack();
        }

        public function inTransaction(): bool {
            return $this->pdo->inTransaction();
        }

    /*
     * Sanitize input (single value)
     */
    private function normalize($dataTypes, string $normalize = '') {
        $data = trim((string)$dataTypes);

        switch ($normalize) {
            case 'upper'    : return mb_strtoupper($data, 'UTF-8');
            case 'lower'    : return mb_strtolower($data, 'UTF-8');
            case 'title'    : return mb_convert_case(mb_strtolower($data, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
            case 'numeric'  : return preg_replace('/[^0-9.]/', '', $data);
            case 'mobile'   : return preg_replace('/\D/', '', $data);
            case 'alnum'    : return preg_replace('/[^a-zA-Z0-9]/', '', $data);
            case 'password' :
                return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.@$!%*?&])[A-Za-z\d.@$!%*?&]{8,}$/', $data) 
                       ? $data : '';
            case 'date'     :
                try {
                    $dt = new \DateTime($data, new \DateTimeZone('UTC'));
                    return $dt->format('Y-m-d'); // standardized UTC date
                } catch (\Exception $e) {
                    return ''; // invalid input
                }
            case 'string'   : return (string)$data;
            default         : return $data;
        }
    }

    /*
     * Normalize an array of parameter values if requested
     */
    private function normalizeParameters(array $parameterValues, ?array $normalize = null): array {
        if ($normalize === null || empty($normalize)) {
            return $parameterValues; // no normalization applied
        }

        foreach ($parameterValues as $i => $value) {
            $case = $normalize[$i] ?? '';
            $parameterValues[$i] = $this->normalize($value, $case);
        }
        return $parameterValues;
    }

    /*
     * Run a prepared statement
     */
    public function run(string $sql, array $parameterValues = [], ?array $normalize = null): bool|PDOStatement {
        $parameterValues = $this->normalizeParameters($parameterValues, $normalize);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parameterValues);
        return $stmt; // can still be used for fetch
    }

    /*
     * Fetch single row, all rows, or a column
     */
    public function fetch(string $sql, array $parameterValues = [], ?array $normalize = null, string $fetchType = 'all') {
        $stmt = $this->run($sql, $parameterValues, $normalize);

        switch ($fetchType) {
            case "row":
                return $stmt->fetch(PDO::FETCH_ASSOC);
            case "col":
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
            case "all":
            default:
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /*
     * Exec for INSERT, UPDATE, DELETE
     */
    public function exec(string $sql, array $parameterValues = [], ?array $normalize = null): array {
        $stmt = $this->run($sql, $parameterValues, $normalize);
        return [
            'affected' => $stmt->rowCount(),
            'insertId' => $this->pdo->lastInsertId()
        ];
    }
}
