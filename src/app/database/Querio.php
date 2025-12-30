<?php

namespace src\app\database;

use PDO;
use PDOException;
use Ramsey\Uuid\Rfc4122\UuidV4;

use src\exceptions\pdo\ColumnDoesntHaveADefaultValueException;
use src\exceptions\pdo\ColumnNotFoundException;
use src\exceptions\pdo\DuplicateEntryException;
use src\exceptions\pdo\IntegerValueException;
use src\exceptions\pdo\TableOrViewNotFoundException;
use stdClass;

class Querio
{
    protected static PDO $db;
    protected static string $table;
    protected static string $dbType;
    protected static string $queryString;
    /** @var array<string, mixed> */
    protected static array $bind = [];
    protected static bool $selectIsOne = false;

    public string $uuid;

    protected static function initDb(): void
    {
        static::$db = Database::get(static::$dbType);
    }

    protected static function resetQuery(): void
    {
        static::$queryString = '';
        static::$bind = [];
        static::$selectIsOne = false;
    }

    protected static function getTable(): string
    {
        return static::$table ?? strtolower((new \ReflectionClass(static::class))->getShortName()) . 's';
    }

    /**
     * @param array $data
     * @return static
     */
    public static function insert(array $data): static
    {
        static::initDb();
        static::resetQuery();
        static::$queryString = "INSERT INTO " . static::getTable();
        return static::values($data);
    }

    /**
     * @param array<string, mixed> $binds
     * @return static
     */
    protected static function values(array $binds = []): static
    {
        static::$bind = $binds;
        $keys = implode(", ", array_keys($binds));
        $keysUsingInBind = ":" . implode(", :", array_keys($binds));
        static::$queryString .= " ({$keys}) VALUES ({$keysUsingInBind})";
        // dd($binds, static::$queryString);
        return new static();
    }

    /**
     * @param string $column
     * @param string $operation
     * @param string|int|float|null $value
     * @return static
     */
    public static function where(string $column, string $operation, string|int|float|null $value): static
    {
        $columnWithoutTable = $column;
        if (str_contains($column, '.'))
            [$table, $columnWithoutTable] = explode(".", $column);

        static::$queryString .= " WHERE {$column} {$operation} :{$columnWithoutTable}";
        static::$bind[$columnWithoutTable] = $value;
        return new static();
    }


    /**
     * @param string $column
     * @param string|int|float|null $value
     * @return static
     */
    public static function whereEquals(string $column, string|int|float|null $value): static
    {
        $columnWithoutTable = $column;
        if (str_contains($column, '.'))
            [$table, $columnWithoutTable] = explode(".", $column);

        static::$queryString .= " WHERE {$column} = :{$columnWithoutTable}";
        static::$bind[$columnWithoutTable] = $value;
        return new static();
    }

    /**
     * @param string $column
     * @param string $operation
     * @param mixed $value
     * @return static
     */
    public static function andWhere(string $column, string $operation, mixed $value): static
    {
        $columnWithoutTable = $column;
        if (str_contains($column, '.'))
            [$table, $columnWithoutTable] = explode(".", $column);

        static::$queryString .= " AND {$column} {$operation} :{$columnWithoutTable}";
        static::$bind[$columnWithoutTable] = $value;
        return new static();
    }

    /**
     * @param string $column
     * @param string $operation
     * @param mixed $value
     * @return static
     */
    public static function orWhere(string $column, string $operation, mixed $value): static
    {
        $columnWithoutTable = $column;
        if (str_contains($column, '.'))
            [$table, $columnWithoutTable] = explode(".", $column);

        static::$queryString .= " OR {$column} {$operation} :{$column}";
        static::$bind[$column] = $value;
        return new static();
    }

    /**
     * @param string $column
     * @param array<string, mixed> $values
     * @return static
     */
    public static function andIn(string $column, array $values): static
    {
        $columnWithoutTable = $column;
        if (str_contains($column, '.'))
            [$table, $columnWithoutTable] = explode(".", $column);

        $params = [];
        foreach ($values as $key => $value) {
            $paramKey = "{$column}{$key}";
            $params[":{$paramKey}"] = $value;
            static::$bind[$paramKey] = $value;
        }
        $paramsIn = implode(", ", array_keys($params));

        static::$queryString .= " AND {$column} IN ({$paramsIn})";

        return new static();
    }

    /**
     * @param string $column
     * @param array<string, mixed> $values
     * @return static
     */
    public static function whereIn(string $column, array $values): static
    {
        $columnWithoutTable = $column;
        if (str_contains($column, '.'))
            [$table, $columnWithoutTable] = explode(".", $column);

        $params = [];
        foreach ($values as $key => $value) {
            $paramKey = "{$column}{$key}";
            $params[":{$paramKey}"] = $value;
            static::$bind[$paramKey] = $value;
        }
        $paramsIn = implode(", ", array_keys($params));

        static::$queryString .= " WHERE {$column} IN ({$paramsIn})";
        return new static();
    }

    /**
     * @param string $column
     * @return static
     */
    public static function whereIsNull(string $column): static
    {
        static::$queryString .= " WHERE {$column} IS NULL";
        return new static();
    }

    /**
     * @param string $column
     * @return static
     */
    public static function whereIsNotNull(string $column): static
    {
        static::$queryString .= " WHERE {$column} IS NOT NULL";
        return new static();
    }

    /**
     * @return mixed
     */
    public static function finish($lvl = 0): mixed
    {
        static::initDb();

        try {
            $firstWord = strstr(static::$queryString, ' ', true);
            if (!is_string($firstWord)) {
                return null;
            }
            $operation = strtolower(trim($firstWord));
            $isSelect = $operation === 'select';

            if ($lvl === 1)
                dd(static::$queryString);
            if (!$isSelect) {
                $stmt = static::$db->prepare(static::$queryString);
                $r = $stmt->execute(static::$bind ?? []);
                if ($operation === 'insert')
                    return (object) array_merge(['id' => static::$db->lastInsertId()], static::$bind);
                else if ($operation === 'update')
                    return (object) static::$bind;
                return $r;
            } else {
                if (static::$selectIsOne)
                    static::limit();

                $stmt = static::$db->prepare(static::$queryString);
                $stmt->execute(static::$bind ?? []);
                $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);


                if (static::$selectIsOne) {
                    $found = $stmt->fetch();
                    if (!$found)
                        return null;
                    return $found;
                } else {
                    $found = $stmt->fetchAll();
                    if (!$found)
                        return null;
                    return $found;
                }
            }
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), "doesn't have a default value"))
                throw new ColumnDoesntHaveADefaultValueException(['message from pdo' => $e->errorInfo[2]]);
            else if (str_contains($e->getMessage(), 'Base table or view not found'))
                throw new TableOrViewNotFoundException(['message from pdo' => $e->errorInfo[2]]);
            else if (str_contains($e->getMessage(), 'Column not found'))
                throw new ColumnNotFoundException(['message from pdo' => $e->errorInfo[2]]);
            else if (str_contains($e->getMessage(), 'Incorrect integer value'))
                throw new IntegerValueException(['message from pdo' => $e->errorInfo[2]]);
            else if (str_contains($e->getMessage(), 'Duplicate entry'))
                throw new DuplicateEntryException(['message from pdo' => $e->errorInfo[2]]);
            return false;
        }
    }

    /**
     * @param array<int, string> $fields
     * @param bool $selectIsOne - false as default
     */
    public static function select(array $fields = [], bool $selectIsOne = false): static
    {
        static::initDb();
        static::resetQuery();

        $table = static::getTable();
        if (empty($fields))
            $fieldsInString = "{$table}.*";
        else
            $fieldsInString = implode(', ', $fields);

        static::$selectIsOne = $selectIsOne;
        static::$queryString = "SELECT {$fieldsInString} FROM {$table}";
        return new static();
    }

    /**
     * @param array<int, string> $fields
     * @return static
     */
    public static function selectOne(array $fields = []): static
    {
        return static::select($fields, true);
    }

    /**
     * @param int $limit - 1 as default
     */
    public static function limit(int $limit = 1): static
    {
        static::$queryString .= " LIMIT {$limit}";
        return new static();
    }

    /**
     * @param string $table
     * @param string $firstColumn
     * @param string $operation
     * @param string $secondColumn
     * @return static
     */
    public static function innerJoin(string $table, string $firstColumn, string $operation, string $secondColumn): static
    {
        $currentTable = static::getTable();
        static::$queryString .= " INNER JOIN {$table} ON {$currentTable}.{$firstColumn} {$operation} {$table}.{$secondColumn}";
        return new static();
    }

    /**
     * @param string $table
     * @param string $firstColumn
     * @param string $operation
     * @param string $secondColumn
     * @return static
     */
    public static function leftJoin(string $table, string $firstColumn, string $operation, string $secondColumn): static
    {
        $currentTable = static::getTable();
        static::$queryString .= " LEFT JOIN {$table} ON {$currentTable}.{$firstColumn} {$operation} {$table}.{$secondColumn}";
        return new static();
    }

    /**
     * @param string $table
     * @param string $firstColumn
     * @param string $operation
     * @param string $secondColumn
     * @return static
     */
    public static function rightJoin(string $table, string $firstColumn, string $operation, string $secondColumn): static
    {
        $currentTable = static::getTable();
        static::$queryString .= " RIGHT JOIN {$table} ON {$currentTable}.{$firstColumn} {$operation} {$table}.{$secondColumn}";
        return new static();
    }

    /**
     * @param string $table
     * @param string $firstColumn
     * @param string $operation
     * @param string $secondColumn
     * @return static
     */
    public static function fullJoin(string $table, string $firstColumn, string $operation, string $secondColumn): static
    {
        $currentTable = static::getTable();
        static::$queryString .= " FULL OUTER JOIN {$table} ON {$currentTable}.{$firstColumn} {$operation} {$table}.{$secondColumn}";
        return new static();
    }

    /**
     * @param string $table
     * @return static
     */
    public static function table(string $table): static
    {
        static::$table = $table;
        return new static();
    }

    /**
     * @param array<string, mixed> $data
     * @return static
     */
    public static function update(array $data): static
    {
        static::initDb();
        static::resetQuery();

        $table = static::getTable();
        static::$queryString = "UPDATE {$table} SET ";
        foreach ($data as $key => $value) {
            static::$queryString .= "{$key} = :{$key}, ";
        }
        static::$queryString = rtrim(static::$queryString, ", ");
        static::$bind = $data;
        return new static();
    }

    /**
     * @return static
     */
    public static function delete(): static
    {
        static::initDb();
        static::resetQuery();

        $table = static::getTable();
        static::$queryString = "DELETE FROM {$table}";
        return new static();
    }

    /**
     * @return static
     */
    public static function softDelete(): static
    {
        static::initDb();
        static::resetQuery();

        $table = static::getTable();
        static::$queryString = "UPDATE {$table} SET deleted_at = NOW()";
        return new static();
    }

    /**
     * @return ?PDO
     */
    public static function getPDO(): ?PDO
    {
        static::initDb();
        return static::$db;
    }

    public static function transactionBegin(): static
    {
        static::initDb();
        static::$db->beginTransaction();
        return new static();
    }

    public static function transactionCommit(): static
    {
        static::initDb();
        static::$db->commit();
        return new static();
    }

    public static function transactionRollback(): static
    {
        static::initDb();
        static::$db->rollback();
        return new static();
    }

    /**
     * @param int $offset
     */
    public static function offset(int $offset): static
    {
        static::$queryString .= " OFFSET {$offset}";
        return new static();
    }

    /**
     * @param string $column
     * @param string $type
     */
    public static function order(string $column, string $type): static
    {
        static::$queryString .= " ORDER BY {$column} {$type}";
        return new static();
    }

    public static function getPagination(int $itemsInPage = 5): stdClass
    {
        $stdclass = new stdClass();

        $raw = static::select()->finish();
        $pagina = (isset($_GET['page']) ? $_GET['page'] : 1) - 1;
        $offset = $pagina * $itemsInPage;
        $paginated = static::select()->order("id", "DESC")->limit($itemsInPage)->offset($offset)->finish();
        $quantitiesOfPages = ceil(count($raw ? $raw : []) / $itemsInPage);
        $links = pagination($quantitiesOfPages);

        $stdclass->raw = $raw;
        $stdclass->currentPage = $pagina + 1;
        $stdclass->offset = $offset;
        $stdclass->paginated = $paginated;
        $stdclass->quantitiesOfPages = $quantitiesOfPages;
        $stdclass->quantitiesPerPage = $itemsInPage;
        $stdclass->links = $links;

        return $stdclass;
    }

    // Functions ready for uses

    /**
     * @param array $data
     * @param bool $setUuid - Flag to ignore the field uuid
     * @return bool|array<string, mixed>|object
     */
    public static function create(array $data, bool $setUuid = true): bool|array|object
    {
        if ($setUuid)
            $data['uuid'] = UuidV4::uuid4()->toString();

        $data['created_at'] = date('Y-m-d H:i:s');
        return static::insert($data)->finish();
    }

    public static function getById(int $id): mixed
    {
        return static::getByColumn("id", $id);
    }

    public static function getByUuid(string $uuid): mixed
    {
        return static::getByColumn("uuid", $uuid);
    }

    public static function getByColumn(string $column, string $value, string $operation = "="): mixed
    {
        return static::selectOne()->where($column, $operation, $value)->finish();
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function deleteById(int $id): bool
    {
        return static::delete()->where('id', "=", $id)->finish();
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function deleteByColumn(string $column, string $operation, mixed $value): bool
    {
        return static::delete()->where($column, $operation, $value)->finish();
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public static function deleteByUuid(string $uuid): bool
    {
        return static::delete()->where('id', "=", $uuid)->finish();
    }

    public static function softDeleteById(int $id): bool
    {
        return static::softDelete()->where('id', "=", $id)->finish();
    }

    public static function softDeleteByUuid(string $uuid): bool
    {
        return static::softDelete()->where('uuid', "=", $uuid)->finish();
    }

    public static function updateById(int $id, array $data)
    {
        return static::update($data)->where("id", "=", $id)->finish();
    }

    public static function updateByColumn(string $column, mixed $value, array $data)
    {
        return static::update($data)->where($column, "=", $value)->finish();
    }

    public static function updateByUuid(string $uuid, array $data)
    {
        return static::update($data)->where("uuid", "=", $uuid)->finish(0);
    }

    /**
     * Save the record
     * @return bool|array<string, mixed>
     */
    public static function save(array $data): bool|array
    {
        if (isset($data['id'])) {
            return static::updateById($data['id'], $data);
        } else if (isset($data['uuid'])) {
            return static::updateByUuid($data['uuid'], $data);
        }
        return static::create($data);
    }

    /**
     * Find all records
     * @param array<int, string> $fields
     * @return array<int, object>
     */
    public static function getAll(array $fields = ['*']): array
    {
        return static::select($fields)->finish() ?: [];
    }


    /**
     * Find all records actives
     * @param array<int, string> $fields
     * @return array<int, object>
     */
    public static function getActives(array $fields = ['*']): array
    {
        return static::select($fields)->whereIsNull('deleted_at')->finish() ?: [];
    }


    function destroy()
    {
        return static::delete()->whereEquals('uuid', $this?->uuid)->finish();
    }

    function save_()
    {
        return static::update((array) $this)->whereEquals('uuid', $this?->uuid)->finish();
    }
}
