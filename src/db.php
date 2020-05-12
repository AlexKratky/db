<?php
/**
 * @name db.php
 * @link https://alexkratky.com                         Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/db/              Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2020 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to work with database. Part of panx-framework.
 */
declare (strict_types = 1);

namespace AlexKratky;

use \PDO;

 class db {
    /**
     * @var PDO $conn The connection to db.
     */
    private static $conn;
    private static $debug;
    private static $executed_queries = array();
    public static $id;

    /**
     * @var array $settings The connection's settings.
     */
    private static $settings = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    );

    /**
     * Create connection to database.
     * @param string $host The hostname of DB server.
     * @param string $user The username of DB.
     * @param string $pass The password of DB user.
     * @param string $db The database name.
     * @return PDO The new connection.
     */
    public static function connect($host, $user, $pass, $db)
    {
        if (!isset(self::$conn))
        {
            self::$conn = @new PDO(
                "mysql:host=$host;dbname=$db",
                $user,
                $pass,
                self::$settings
            );
        }
        return self::$conn;
    }

    /**
     * Execute query on DB.
     * @param string $sql The query. Use ? for parameters.
     * @param array $params The array of parameters.
     */
    public static function query(string $sql, array $params = array())
    {
        self::saveQuery(array(
            $sql,
            $params
        ));
        $query = self::$conn->prepare($sql);
        $query->execute($params);
        $id = self::$conn->lastInsertId();
        self::$id = $id;
        return $id;
    }

    /**
     * Execute query on DB and fetch the result.
     * @param string $sql The query. Use ? for parameters.
     * @param array $params The array of parameters.
     */
    public static function select(string $sql, array $params = array()) {
        self::saveQuery(array(
            $sql,
            $params
        ));
        $q = self::$conn->prepare($sql);
        $q->execute($params);
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

    /**
     * Execute query on DB and fetch all rows of result.
     * @param string $sql The query. Use ? for parameters.
     * @param array $params The array of parameters.
     */
    public static function multipleSelect(string $sql, array $params = array()) {
        self::saveQuery(array(
            $sql,
            $params
        ));
        $q = self::$conn->prepare($sql);
        $q->execute($params);
        $data = $q->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    /**
     * Returns the number provided by COUNT query.
     * @param string $sql The query. Use ? for parameters.
     * @param array $params The array of parameters.
     */
    public static function count(string $sql, array $params = array()) {
        self::saveQuery(array(
            $sql,
            $params
        ));
        $q = self::$conn->prepare($sql);
        $q->execute($params);
        $data = $q->fetch();
        return $data[0];
    }

    /**
     * Enables or disables debug mode.
     * @param bool $debug
     */
    public function setDebug(bool $debug = true): void {
        self::$debug = $debug;
    }

    /**
     * Saves executed query if debug mode is enabled.
     * @param array $query
     */
    private function saveQuery(array $query) {
        if(self::$debug) {
            array_push(self::$executed_queries, $query);
        }
    }

    /**
     * @return array Returns array of executed queries. If debug mode is disabled, then return an empty array.
     */
    public function getQueries(): array {
        return self::$executed_queries;
    }

}
