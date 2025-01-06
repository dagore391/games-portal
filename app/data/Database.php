<?php
namespace app\data;

use app\config\Constants;
use app\security\Logger;
use PDO;
use PDOException;

final class Database {
    private static $_dbInstance = null;
    private $_pdo;
    private $_count = 0;
    private $_error = false;
    private $_results = [];
    private $_query;
    private $_lastInsertedId = '0';

    private function __construct() {
        try {
            $this->_pdo = new PDO(
                'mysql:host=' . Constants::DB_HOST . ';dbname=' . Constants::DB_NAME . ';charset=utf8',
                Constants::DB_USER,
                Constants::DB_PASS,
                [
                    PDO::ATTR_AUTOCOMMIT => false,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => true
                ]
            );
        } catch(PDOException $exception) {
			Logger::logError($exception->getMessage(), $exception);
            die();
        }
    }

    public static function getInstance(): Database {
        if (!isset(self::$_dbInstance)) {
            self::$_dbInstance = new Database();
        }
        return self::$_dbInstance;
    }

    public function query(string $sql, array $params = [], array $paramTypes = [], int $fetchType = PDO::FETCH_OBJ): Database {
        $this->_error = false;
        try {
            $this->_query = $this->_pdo->prepare($sql);
            if ($this->_query) {
                foreach ($params as $idx => $param) {
                    $this->_query->bindValue($idx + 1, $param, $paramTypes[$idx] ?? PDO::PARAM_STR);
                }
                if ($this->_query->execute()) {
                    $this->_results = $this->_query->fetchAll($fetchType);
                    $this->_count = $this->_query->rowCount();
                    $this->_lastInsertedId = $this->_pdo->lastInsertId();
                } else {
                    $this->_error = true;
                }
            }
        } catch(PDOException $exception) {
			Logger::logError($exception->getMessage(), $exception);
            $this->_error = true;
        }
        return $this;
    }

    public function affectedRows(): bool {
        return $this->_query ? $this->_query->rowCount() : 0;
    }

    public function isError(): bool {
        return $this->_error;
    }

    public function first(): ?object {
        return $this->_results[0] ?? null;
    }

    public function firstSanitize(array $excludeFields = []): ?object {
        return $this->_results ? Sanitize::cleanObjectHtml($this->_results[0], $excludeFields) : null;
    }

    public function latestInsertedId(): string {
        return $this->_lastInsertedId;
    }

    public function results(): array {
        return $this->_results;
    }

    public function resultsSanitize(array $excludeFields = []): array {
        return array_map(fn($row) => Sanitize::cleanObjectHtml($row, $excludeFields), $this->_results);
    }

    public function count(): int {
        return $this->_count;
    }

    public function beginTransaction(): void {
        $this->_pdo->beginTransaction();
    }

    public function commit(): void {
        $this->_pdo->commit();
    }

    public function rollback(): void {
        $this->_pdo->rollback();
    }
}
