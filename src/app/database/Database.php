<?php

namespace src\app\database;

use Exception;
use PDO;
use PDOException;


class Database
{
    protected static ?PDO $pdo = null;
    protected static ?string $typeConnection = null;
    protected static ?string $host = null;
    protected static ?string $dbname = null;
    protected static ?string $username = null;
    protected static ?string $password = null;
    protected static ?string $service = null;
    protected static ?string $server = null;

    static function config(string $type, string $host, string $dbname, string $username, string $password, ?string $service = null, ?string $server = null)
    {
        self::$typeConnection = $type;
        self::$host = $host;
        self::$dbname = $dbname;
        self::$username = $username;
        self::$password = $password;
        self::$service = $service;
        self::$server = $server;
    }

    static function get(string|null $dbType = null)
    {
        self::setConfig($dbType);
        return self::connect();
    }

    static function setConfig(string|null $dbType)
    {
        $file = "DB_FILE_" . strtoupper($dbType);
        $config = dbEnv($file);
        self::config(
            type: $config["DB_TYPE"],
            host: $config["DB_HOST"],
            username: $config["DB_USER"],
            password: $config["DB_PASSWORD"],
            dbname: $config["DB_NAME"],
            server: $config["DB_SERVER"] ?? null,
            service: $config["DB_PORT"] ?? null,
        );
        return new self;
    }

    static function instance(): PDO
    {
        return self::get();
    }

    /**
     * Method performs the database connection
     * @return PDO
     */
    protected static function connect(): PDO
    {
        try {
            self::$pdo = new PDO(self::dns(), self::$username, self::$password);
            return self::$pdo;
        } catch (PDOException $e) {
            dd("Erro ao conectar com o banco de dados: " . $e->getMessage());
        }
    }

    /**
     * Obtains the type of DNS according to the informed connection
     * 
     * @return string
     */
    protected static function dns(): string
    {
        switch (self::$typeConnection) {
            case 'mysql':
                return "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8mb4";
            case 'sqlsrv':
                return "sqlsrv:Server=" . self::$host . ";Database=" . self::$dbname . "";
            case 'informix':
                return "informix:host=" . self::$host . "; service=" . self::$service . "; database=" . self::$dbname . "; server=" . self::$server . "; protocol=olsoctcp";
            default:
                throw new Exception("Tipo de conexão inválido: " . self::$typeConnection . "");
        }
    }
}
