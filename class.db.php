<?php


class DB {

	protected static $connection;

    static $db_host;
    static $db_user;
    static $db_pass;
    static $db_name;

    public static function init($sever){
        $info = parse_ini_file("config.ini",true);
        $db = $info[$sever];
        self::$db_host = $db['host'];
        self::$db_user = $db['user'];
        self::$db_pass = $db['pass'];
        self::$db_name = $db['name'];
    }

	public static function connect() {
        try {
            self::$connection = new PDO('mysql:host='.self::$db_host.';dbname='.self::$db_name, self::$db_user, self::$db_pass);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
	}

    public static function disconnect() {
        self::$connection=null;
    }

	public static function query($query, $value) {
        try {
            $stmt = self::$connection->prepare($query);
            $value == null ? $stmt->execute() : $stmt->execute($value);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rows;
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
	}

    public static function getResult($query, $value, $per_page) {
        try {
            $stmt = self::$connection->prepare($query);
            $value == null ? $stmt->execute() : $stmt->execute($value);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();
            return array('total'=>$count, 'per_page'=>$per_page, 'data'=>$rows );
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function execute($query, $value) {
        try {
            $stmt = self::$connection->prepare($query);
            return $value == null ? $stmt->execute() : $stmt->execute($value);
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function getRow($query, $value) {
        try {
            $stmt = self::$connection->prepare($query);
            $value == null ? $stmt->execute() : $stmt->execute($value);
            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
            return $rows;
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }



}