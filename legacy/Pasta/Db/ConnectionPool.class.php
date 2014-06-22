<?php

require_once 'Pasta/db.inc.php';

class Pasta_Db_ConnectionPool
{
    private static $TABLE_ROW_CLASS_NAME = 'Pasta_TableRow';
    private static $connectionCache = array();

    /**
     * Clears the connection cache. This should only be used during testing.
     */
    public static function clearConnectionCache()
    {
        self::$connectionCache = array();
    }

    /**
     * Returns the database id used by the specified class. The class need not
     * be a subclass of Pasta_TableRow.
     * @param   string
     * @return  string  a database id
     */
    public static function getDbIdByClassName($className)
    {
        if (!isset(self::$connectionCache[$className])) {
            if (!class_exists($className)) {
                trigger_error($className . " does not exist");
                return false;
            }
            $class = new ReflectionClass($className);
            if ($class->isSubclassOf(self::$TABLE_ROW_CLASS_NAME)) {
                $method = $class->getMethod('getDbId');
                if ($method->getDeclaringClass()->getName() != self::$TABLE_ROW_CLASS_NAME) {
                    //we cannot use invoke(), until getDbId is declared static
                    //$method->invoke(null, $table);
                    $staticMethod = array($method->getDeclaringClass()->getName(),
                                          $method->getName());
                    return call_user_func($staticMethod);
                }
            }
            $dbClassName = self::getDbClassNameByClassName($className);
            self::$connectionCache[$className] = strtolower(substr($dbClassName, 0, strpos($dbClassName, '_')));
        }
        return self::$connectionCache[$className];
    }

    /**
     * Returns a PEAR DB instance for the database used by the specified class
     * for the specified direction. The class need not be a subclass of
     * Pasta_TableRow.
     * @param  string
     * @param  string  PASTA_DB_QUERY_READ or PASTA_DB_QUERY_WRITE
     * @return  DB     a PEAR DB object
     */
    public static function getPearDbByClassName($className, $direction = PASTA_DB_QUERY_READ)
    {
        $dbId = self::getDbIdByClassName($className);
        return self::getPearDbByDbIdAndDirection($dbId, $direction);
    }

    /**
     * Returns a PEAR database for the specified database id and direction.
     * object.
     * @param   string  a database id
     * @return  DB      a PEAR DB object
     */
    public static function getPearDbByDbIdAndDirection($dbId, $direction = PASTA_DB_QUERY_READ)
    {
        return call_user_func($GLOBALS['getPearDbByDbIdAndDirection'], $dbId, $direction);
    }

    /**
     * Returns the name of the class that is used for automatically finding
     * the default table name and database id. This is either the specified
     * class itself or a parent class but not Pasta_TableRow itself.
     * or
     * @param   string  a class name
     * @return  string  a class name
     */
    public static function getDbClassNameByClassName($className)
    {
        // the method works even for classes that do not inherit from TableRow
        if (!is_subclass_of($className, self::$TABLE_ROW_CLASS_NAME)) {
            return $className;
        }
        for ($defaultClassName = $className; $defaultClassName; $defaultClassName = $parentName) {
            $parentName = get_parent_class($defaultClassName);
            if ($parentName == self::$TABLE_ROW_CLASS_NAME) {
                break;
            }
        }
        return $defaultClassName;
    }

    /**
     * Returns a MongoDB connection object
     * @param   string  a collection name
     * @return  object  a MongoDB connection object
     */
    public static function getMongoDbConnection($direction = PASTA_DB_QUERY_READ)
    {
        $mongoHost = parse_url($_SERVER['PEYTZ_DSN_OPROP_MONGO'], PHP_URL_HOST);
        $mongoPath = explode('/', parse_url($_SERVER['PEYTZ_DSN_OPROP_MONGO'], PHP_URL_PATH));
        $mongoOptions = array("persist" => "peytz");
        if (count($mongoPath) >= 2) {
            $mongoDatabase = $mongoPath[1];
        }
        if (count($mongoPath) >= 3 && !empty($mongoPath[2])) {
            $mongoOptions["replicaSet"] = $mongoPath[2];
        }
        if (empty($mongoDatabase)) {
            trigger_error("Mongo collection name missing");
            return false;
        }
        $connection = new Mongo($mongoHost, $mongoOptions);
        $db = $connection->$mongoDatabase;
        return $db;
    }

    /**
     * Returns a MongoDB collection object for the specified database.
     * @param   string  a collection name
     * @return  object  a MongoDB collection object
     */
    public static function getMongoDbByCollection($dbCollection, $direction = PASTA_DB_QUERY_READ)
    {
        $db = $self->getMongoDbConnection();
        return $db->$dbCollection;
    }

}
