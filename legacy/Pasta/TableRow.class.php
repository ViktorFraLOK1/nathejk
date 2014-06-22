<?php

require_once 'Pasta/db.inc.php';

/**
 * Helper class for simple getX / setX constructions based on table rows.
 *  - Put the data row in $this->_row
 *  - Name all db columns with camel case e.g. "columnName"
 *
 * @package Pasta
 */
class Pasta_TableRow
{
    const NOLL = "NULL (This string is the Pasta_TableRow 'signal' for null, not 0 (zero), false or the empty string)";

    const SEVERITY_NONE    =     0;
    const SEVERITY_LOW     =   400;
    const SEVERITY_MEDIUM  =   600;
    const SEVERITY_HIGH    =   800;
    const SEVERITY_FATAL   =  1000;

    const NOT_CACHED = 'notcached';

    private $_row = array(); // the table rows
    private $_exists = false; // does the row exists in the table

    /**
     * @var  array  one-dimensional array of column names
     */
    private $_changedColumns = array();

    /**
     * @var  array append values to the where part
     */
    private $_customWhereSql = array();

    /**
     * The column names in each table.
     * @var  array  associative array of arrays ("dbId.table" => array(col1, col2, ...))
     */
    private static $_columnNames = array();


    /**
     * Manually specified WHERE clause (without "WHERE")
     * @var  array  associative array (table => sql)
     */
    private $_whereSql = array();

    /**
     * Manually specified ORDER BY
     * @var  string  the ORDER BY part of an SQL SELECT query (without "ORDER BY")
     */
    private $_orderBySql = false;

    /**
     * @var  array  (offset, limit)
     */
    private $_limit = false;

    private $_errorString = "";
    private $_severity = false;
    private $_errorSeverityThreshold = self::SEVERITY_NONE;
    private $_errorName;
    private $_errorParameters;

    private static $objectCache = array();
    private static $tableColumnCache = array();

    const TABLEINFO_MODE = DB_TABLEINFO_ORDERTABLE;

    public static function clearTableColumnCache()
    {
        self::$tableColumnCache = array();
    }

    /**
     * Returns the database id used by this class.
     * @return  string
     */
    function getDbId()
    {
        return Pasta_Db_ConnectionPool::getDbIdByClassName(get_class($this));
    }

    /**
     * Do not use. Use Pasta_Db_ConnectionPool instead.
     * @deprecated
     */
    static function getDbIdByClassName($className)
    {
        return Pasta_Db_ConnectionPool::getDbIdByClassName($className);
    }

    /**
     * Returns the pear database obj. which contains the tables of this
     * object.
     * @param  string  a PASTA_DB_QUERY_* constant
     * @return  DB  a PEAR DB object
     */
    function getPearDb($direction = PASTA_DB_QUERY_READ)
    {
        $dbId = $this->getDbId();
        return self::getPearDbByDbIdAndDirection($dbId, $direction);
    }

    /**
     * Do not use. Use Pasta_Db_ConnectionPool instead.
     * @deprecated
     */
    static function getPearDbByClassName($className, $direction = PASTA_DB_QUERY_READ)
    {
        return Pasta_Db_ConnectionPool::getPearDbByClassName($className, $direction);
    }

    /**
     * Do not use. Use Pasta_Db_ConnectionPool instead.
     * @deprecated
     */
    static function getPearDbByDbIdAndDirection($dbId, $direction = PASTA_DB_QUERY_READ)
    {
        return Pasta_Db_ConnectionPool::getPearDbByDbIdAndDirection($dbId, $direction);
    }

    /**
     * Do not use. Use Pasta_Db_ConnectionPool instead.
     * @deprecated
     */
    protected static function getDbClassNameByClassName($className)
    {
        return Pasta_Db_ConnectionPool::getDbClassNameByClassName($className);
    }

    /**
     * @return  string
     */
    protected function getCacheKey()
    {
        $key = '';
        foreach ($this->getKeyColumns() as $column) {
            $key .= $this->getColumn($column) . '-';
        }
        return substr($key, 0, -1);
    }

    /**
     * @param  bool  if true the object is inserted into the cache, otherwise
     *               it is only updates if it is already cached.
     */
    protected function updateCache($insert = false)
    {
        static $count = 0;
        $class = strtolower(get_class($this));
        $key = $this->getCacheKey();
        if ($key &&
            isset(self::$objectCache[$class]) &&
            array_key_exists($key, self::$objectCache[$class]) ||
            $insert && $count++ < 500) {

            self::$objectCache[$class][$key] = $this->exists()
                ? clone $this : null;
        }
    }

    /**
     * Returns the object with the specified key from the cache.
     * @return  mixed  either a subclass of Pasta_TableRow, or
     *                 null if the database does not contain in object with
     *                 the specified key does not exist in the,
     *                 database, or self::NOT_CACHED if info about the
     *                 specified key is not present in the cache
     */
    private static function getCachedObject($class, $key)
    {
        global $_PASTA_TABLEROW_CACHE;
        $class = strtolower($class);
        if (isset(self::$objectCache[$class]) &&
            array_key_exists($key, self::$objectCache[$class])) {

            return self::$objectCache[$class][$key];
        } else {
            return self::NOT_CACHED;
        }
    }

    /**
     * Get obj. by type and id
     * @param   string  the table containing the id column
     * @param   int     the value of the id column
     * @return  Pasta_TableRow  an instance of the specified class, or null
     */
    public static function getObjectById($class, $id)
    {
        if ($id < 1) {
            return false;
        }

        // Assume that the value of getCacheKey() is $id. This is true if the
        // primary key is 'id' and getCacheKey() is not overridden by a
        // subclass.
        $object = Pasta_TableRow::getCachedObject($class, $id);
        if ($object === self::NOT_CACHED) {
            $object = new $class;
            if (!is_subclass_of($object, __CLASS__)) {
                trigger_error("$class is not a subclass of " . __CLASS__);
                return null;
            }

            $object->setColumn('id', $id);
            $object = $object->findOne();
        }

        return $object ? $object->getMutation() : null;
    }

    /**
     * Returns all objects of the specified type.
     * @param   string  a class name of an subclass of Pasta_TableRow
     * @param   string  an ORDER BY string (excluding "ORDER BY")
     * @return  array   an array of instances of the specified class
     */
    static function getAllObjects($class, $orderBySql = null)
    {
        $object = new $class();
        if (!is_subclass_of($object, __CLASS__)) {
            trigger_error("$class is not a subclass of " . __CLASS__);
        }
        if ($orderBySql) {
            $object->setOrderBySql($orderBySql);
        }
        return $object->findAll();
    }

    /**
     * Returns the next key from the specified sequence. A table with the
     * following definition must exist in the database returned by
     * $this->getPearDb($sequence):
     *
     *   CREATE TABLE `sequence` (
     *     `sequence` VARCHAR(255) NOT NULL,
     *     `id` BIGINT(20) UNSIGNED NOT NULL,
     *     PRIMARY KEY (`sequence`)
     *   )
     *
     * @param  string  the name of the sequence (usually the name of the table
     *                 where it is used)
     * @param  int  the minimum number returned
     * @return int or false
     */
    function getNextId($sequence = "", $firstId = 1)
    {
        // default table to class name
        if (empty($sequence)) {
            $sequence = $this->getDefaultTable();
        }

        $db = $this->getPearDb(PASTA_DB_QUERY_WRITE);
        if (!$db) {
            return 0;
        }

        $ok = false;
        //don't use PEAR's default sequence emulation for MySql, because
        //it creates way to many tables
        if ($db->phptype == 'mysql' || $db->phptype == 'mysqli') {
            $sql = "SELECT id FROM sequence
                WHERE sequence = '" . $db->escapeSimple($sequence) . "'";
            $id = $db->getOne($sql);
            if (is_null($id)) {
                $id = $firstId;
                $sql = "INSERT INTO sequence (sequence, id)
                    VALUES ('" . $db->escapeSimple($sequence) . "', $id)";
                @$r = $db->simpleQuery($sql);
                // If there was an error, it might be because another thread
                // made the INSERT before we did. We just mute the error,
                // because if it fails, we try again below and here we generate
                // an error upon failure.
                $ok = !DB::isError($r);
            }
            $id = max($id, $firstId);

            while (!$ok) {
                $id++;
                $sql = "UPDATE sequence SET id = $id
                         WHERE sequence = '" . $db->escapeSimple($sequence) . "'
                           AND id <= $id - 1";
                $r = $db->simpleQuery($sql);
                if (DB::isError($r)) {
                    break;
                }
                $ok = ($db->affectedRows() > 0);
            }
        } else {
            $id = $db->nextId($sequence);
        }

        if (!$ok) {
            trigger_error('Could not get next id: ' . $this->getErrorString(),
                E_USER_WARNING);
            $id = false;
        }
        return $id;
    }

    /**
     * Does the row exists?
     * @return bool
     */
    function exists()
    {
        return $this->getExists();
    }

    /**
     * Does the row exists?
     * @return bool
     */
    function getExists()
    {
        return $this->_exists;
    }

    /**
     * Does the row exists -- change value?
     * @param bool
     */
    function setExists($value)
    {
        $this->_exists = (bool) $value;
    }

    /**
     * Returns whether the current data in this instance is valid, sane and
     * otherwise ready to be saved to the database.
     * Subclasses should override this and add their own validation logic. If
     * false is returned, an explanation and the severity of the problem should
     * be specified using setErrorString(). If several problems exists, the
     * least severe should be reported (though this is not a strict
     * requirement).
     * Errors with severity <= the current error severity threshold should be
     * ignored, i.e. they should not cause the method to return false.
     *
     * @return  bool
     * @see  setErrorString()
     * @see  getErrorSeverityThreshold()
     */
    function isValid()
    {
        return true;
    }

    /**
     * @param   string          column name
     * @return  boolean         column exists
     */
    public function columnExists($column)
    {
        return in_array($column, $this->getColumnNames());
    }

    /**
     * Return column value.
     * @param string column name
     * @return mixed or null
     */
    function getColumn($column)
    {
        // check for column validity
        if (isset($_SERVER['PEYTZ_DEV']) && !in_array($column, $this->getColumnNames())) {
            trigger_error("Invalid column '$column'", E_USER_WARNING);
        }

        // can we return a value?
        if (!isset($this->_row[$column])) {
            return null;
        }
        return $this->_row[$column];
    }

    /**
     * Update column value.
     * @param string column name
     * @param mixed column value
     */
    function setColumn($column, $value)
    {
        // we need a value
        if (is_null($value)) {
            trigger_error("Value parameter is null (column = $column).");
            return;
        }

        // check for column validity
        if (isset($_SERVER['PEYTZ_DEV']) && !in_array($column, $this->getColumnNames())) {
            trigger_error("Invalid column '$column'", E_USER_WARNING);
        }


        if (is_integer($value)) {
            $value = "$value";
        }
        // add to list of changed columns
        if ( (!isset($this->_row[$column]) || $this->_row[$column] !== $value)
            && !$this->isColumnChanged($column)) {

            $this->_changedColumns[] = $column;
        }

        // set value
        $this->_row[$column] = $value;

    }

    /**
     * Sets the default value for the specified column.
     * @param string column name
     * @param mixed column value
     */
    public function setColumnDefault($column, $value)
    {
        // check for column validity
        if (isset($_SERVER['PEYTZ_DEV']) && !in_array($column, $this->getColumnNames())) {
            trigger_error("Invalid column '$column'", E_USER_WARNING);
            return;
        }

        if ($this->exists()) {
            trigger_error("Cannot set default values on existing rows", E_USER_WARNING);
            return;
        }

        if (!$this->isColumnChanged($column)) {
            $this->_row[$column] = $value;
        }
    }

    /**
     * Returns whether the specified column has changed since it was
     * created/retrieved from the database.
     * @param  string  column name
     * @return bool
     */
    function isColumnChanged($column)
    {
        return in_array($column, $this->_changedColumns);
    }

    /**
     * Returns whether any column in the specified table has changed since it
     * was created/retrieved from the database.
     * @return  bool
     */
    function isTableChanged($table)
    {
        return sizeof($this->getChangedColumnsByTable($table)) > 0;
    }

    /**
     * Returns the names of the columns (real and virtual) that have changed
     * since they were created/retrieved from the database.
     * @return  array   array of column names
     */
    function getChangedColumns()
    {
        return $this->_changedColumns;
    }

    /**
     * Returns the names of the columns from the specified table that have
     * changed since they were created/retrieved from the database.
     * @param   string  a table name
     * @return  array   array of column names
     */
    function getChangedColumnsByTable($table)
    {
        $tableColumnNames = $this->getColumnNamesByTable($table);
        $changedTableColumnNames = array_intersect($this->_changedColumns, $tableColumnNames);

        // If a virtual column has changed, add the virtual columns column name
        if (in_array($this->getVirtualColumnsColumnName(), $tableColumnNames) &&
            $this->getChangedVirtualColumns()) {

            $changedTableColumnNames[] = $this->getVirtualColumnsColumnName();
        }
        return $changedTableColumnNames;
    }

    /**
     * Returns the names of the virtualcolumns that have
     * changed since they were created/retrieved
     * from the database.
     * @return  array   array of column names
     */
    function getChangedVirtualColumns()
    {
        return array_intersect($this->_changedColumns, $this->getVirtualColumnNames());
    }

    /**
     * Returns whether any column has changed since it was created/retrieved
     * from the database.
     * @return  bool
     */
    function isChanged()
    {
        return sizeof($this->_changedColumns) > 0;
    }

    /**
     * Returns an SQL INSERT query for this row in the specified table.
     * Key values must be set
     * @param  string   a table name
     * @return  string  an SQL query or false
     */
    function getInsertSql($table)
    {
        if (!$this->isTableChanged($table)) {
            return false;
        }

        $db = $this->getPearDb(PASTA_DB_QUERY_WRITE);
        if (!$db) {
            return false;
        }

        // column list
        $columns = $this->getColumnNamesByTable($table);
        $sql = "INSERT INTO $table (";
        // value list
        $valueStr = "";
        foreach ($columns as $column) {
            $value = $this->getColumn($column);
            if (!is_null($value)) {
                if ($valueStr) { // insert separator
                    $valueStr .= ", ";
                    $sql .= ", ";
                }
                $sql .= $column;
                if ($value === Pasta_TableRow::NOLL) {
                    $valueStr .= "NULL";
                } else {
                    $valueStr .= "'" . $db->escapeSimple($value) . "'";
                }
            }
        }
        $sql .= ") VALUES (" . $valueStr . ")";

        return $sql;
    }

    /**
     * Returns an SQL UPDATE query for this row in the specified table.
     * @param string table name
     * @return string sql
     */
    function getUpdateSql($table)
    {
        if (!$this->isTableChanged($table)) {
            return false;
        }

        $db = $this->getPearDb(PASTA_DB_QUERY_WRITE);
        if (!$db) {
            return false;
        }
        $sql = "UPDATE $table SET ";
        $i = 0;
        foreach ($this->getChangedColumnsByTable($table) as $column) {
            $sql .= ($i++ > 0 ? ', ' : '') . $column .
                " = " . (
                    $this->getColumn($column) === Pasta_TableRow::NOLL ?
                    "NULL" :
                    "'" . $db->escapeSimple($this->getColumn($column)) . "'"
                );
        }

        $sql .= " WHERE " . $this->getWhereSql($table, true);

        return $sql;
    }

    /**
     * Returns an SQL DELETE query for this row in the specified table.
     *
     * @param  string   a table name
     * @return  string  an SQL query
     */
    function getDeleteSql($table)
    {
        // default table to class name
        if (empty($table)) {
            $table = $this->getDefaultTable();
        }

        //use LIMIT 1 as an extra precaution against catastrophes
        $sql = "DELETE FROM $table
            WHERE " . $this->getWhereSql($table, true) . "
            LIMIT 1";
         return $sql;
    }

    /**
     * Returns an SQL SELECT query (without a LIMIT part).
     * @param  bool    only include keys in WHERE part
     * @param  string  an SQL select expression that should be used instead of
     *                 $this->getSelectExpressionSql()
     * @return string
     */
    function getSelectSql($onlyByKeys = false, $selectExpressionSql = false)
    {
        if (!$onlyByKeys && $this->exists()) {
            // This is experimental - so far we don't return
            trigger_error('Cannot SELECT on an object that is already loaded',
                          E_USER_WARNING);
            // return false;  Indkommenteres når vi er sikre på, at det ikke ødelægger noget
        }
        $sql = 'SELECT ' .
            ($selectExpressionSql ? $selectExpressionSql : $this->getSelectExpressionSql()) .
            ' FROM ' . $this->getTableSql() . ' WHERE ';

        $i = 0;
        foreach ($this->getTables() as $table) {
            $sql .= ($i++ > 0 ? ' AND ' : '') .
                $this->getWhereSql($table, $onlyByKeys);
        }
        if ($this->_orderBySql) {
            $sql .= ' ORDER BY ' . $this->_orderBySql;
        }
        return $sql;
    }

    /**
     * Returns the table reference part of an SQL SELECT query, i.e. everything
     * between "FROM" and "WHERE". This may be a single table name or a number
     * of JOINs.
     * @return  string
     */
    function getTableSql()
    {
        return $this->getDefaultTable();
    }

    /**
     * Returns the select expression reference part of an SQL SELECT query,
     * i.e. everything between "SELECT" and "FROM". This may be e.g. "*" or a
     * comma-separated list of column names. This is usually necessary when
     * doing LEFT JOINs in getTableSql();
     * @return  string
     * @see getTableSql()
     */
    function getSelectExpressionSql()
    {
        return '*';
    }

    /**
     * Returns the WHERE part (excluding "WHERE") of an SQL query that looks
     * up this row based on its primary key.
     * @param  string  a table name
     * @param  bool    only include key columns
     * @return string
     */
    private function getWhereSql($table, $onlyByKeys)
    {
        if (isset($this->_whereSql[$table])) {
            return $this->_whereSql[$table];
        }

        if ($onlyByKeys) {
            $columns = $this->getKeyColumns();
        } else {
            $columns = $this->getChangedColumnsByTable($table);
            // Don't do exact matching on virtual columns column
            $i = array_search($this->getVirtualColumnsColumnName(), $columns);
            if ($i !== false) {
                unset($columns[$i]);
            }
        }
        $db = $this->getPearDb();
        if (!$db) {
            // return something that is always false so that we wont select or
            // update anything by mistake, if the db connection "returns" later
            // on in the script.
            return ' 1 = 2 ';
        }
        $originalCustomWhereSql = $this->_customWhereSql;
        foreach ($columns as $column) {
            $colVal = $this->getColumn($column);
            if (is_null($colVal) && $onlyByKeys) {
                trigger_error("Key column '$column' not set.");
            } else {
                $condition = " $table.$column ";
                if ($colVal === Pasta_TableRow::NOLL) {
                    $condition .= " IS NULL";
                } else {
                    $condition .= " = '" . $db->escapeSimple($colVal) . "' ";
                }
                $this->addWhereSql($condition, false);
            }
        }

        if (!$onlyByKeys) {
            foreach ($this->getChangedVirtualColumns() as $cCol) {
                $val = serialize($cCol) . serialize($this->_row[$cCol]);
                $val = str_replace(array('%', '_'), array('\%', '\_'), $val);
                $this->columnLike($this->getVirtualColumnsColumnName(), "%$val%");
            }
        }

        $whereSql = implode(' AND ', $this->_customWhereSql);
        $this->_customWhereSql = $originalCustomWhereSql; // reset to original value.

        return empty($whereSql) ? ' 1=1 ' : $whereSql;
    }

    /**
     * Sets the WHERE clause when using this instance as a template for
     * queries using findAll().
     * @param  string  a table name
     * @param  string  an SQL expression (without "WHERE")
     */
    function setWhereSql($table, $sql)
    {
        if (!in_array($table, $this->getTables())) {
            trigger_error("Invalid table '$table'", E_USER_WARNING);
            return;
        }
        $this->_whereSql[$table] = $sql;
    }

    /**
     * Sets the ORDER BY part of the SELECT query when using this instance as
     * a template for queries using findAll().
     * @param  string  an ORDER BY clause (without "ORDER BY")
     */
    function setOrderBySql($orderBy)
    {
        $this->_orderBySql = $orderBy;
    }

    /**
     * Gets the ORDER BY part of the SELECT query
     */
    function getOrderBySql()
    {
        return $this->_orderBySql;
    }

    /**
     * Sets the offset and count for the SELECT query when using this instance
     * as a template for queries using findAll(). When called with only one
     * argument, setLimit($x), this is equivalent to setLimit(0, $x).
     * @param  int  if the method is called with two arguments, this is the
     *              offset into the result set
     * @param  int  the maximum number of results
     */
    function setLimit($offset, $limit = null)
    {
        if (is_null($limit)) {
            $this->_limit = array(0, $offset);
        } else {
            $this->_limit = array($offset, $limit);
        }
    }

    /**
     * Saves the data in the current instance to the database by issuing an
     * INSERT query for the specified table.
     * @param   string  the name of the table to insert to
     * @return  bool
     */
    protected final function executeInsert($table)
    {
        if ($sql = $this->getInsertSql($table)) {
            $db = $this->getPearDb(PASTA_DB_QUERY_WRITE);
            if (!$db) {
                return false;
            }
            // A duplicate key error should not trigger an error
            $db->expectError(DB_ERROR_ALREADY_EXISTS);
            $rs = $db->query($sql);
            $db->popExpect();
            if (DB::isError($rs)) {
                $this->setErrorString($rs->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * Saves the data in the current instance to the database by issuing an
     * UPDATE query for the specified table.
     * @param   string  the name of the table to insert to
     * @return  bool
     */
    protected final function executeUpdate($table)
    {
        if ($sql = $this->getUpdateSql($table)) {
            $db = $this->getPearDb(PASTA_DB_QUERY_WRITE);
            if (!$db) {
                return false;
            }
            // A duplicate key error should not trigger an error
            $db->expectError(DB_ERROR_ALREADY_EXISTS);
            $rs = $db->query($sql);
            $db->popExpect();
            if (DB::isError($rs)) {
                $this->setErrorString($rs->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * Delete row(s)
     * @return bool
     */
    protected final function executeDelete($table)
    {
        if ($sql = $this->getDeleteSql($table)) {
            $db = $this->getPearDb(PASTA_DB_QUERY_WRITE);
            if (!$db) {
                return false;
            }
            $rs = $db->query($sql);
            if (DB::isError($rs)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Loads the data for the current instance from the database. The
     * properties constituting the key columns must be set. The caller may need
     *
     * @param   bool        use only keys as load criteria
     * @return  bool|null   true if found and loaded, null on error
     */
    function load($onlyByKeys = true)
    {
        // We do not fetch from cache here - perhaps we should?
        // In that case we should change Oprop_Newsletter::fire()

        $this->setExists(false);
        $db = $this->getPearDb(PASTA_DB_QUERY_READ);
        if (!$db) {
            return null;
        }

        $sql = $this->getSelectSql($onlyByKeys);
        $rs = $db->limitQuery($sql, 0, 1);
        if (DB::isError($rs)) {
            return null;
        }
        if (!is_object($rs)) {
            trigger_error('Bugzilla 1862: "' . substr($sql, 0, 50) . '" ' . var_export($rs, true));
            return null;
        }

        $row = $rs->fetchRow();
        if (!is_null($row)) {
            $this->setRow($row, false);
            $this->setExists(true);
        }

        // tableInfo is rather expensive, so only use it if necessary
        foreach ($this->getTables() as $table) {
            $columnNameKey = $this->getColumnNameKey($table);
            if (!isset(self::$_columnNames[$columnNameKey])) {
                $tableInfo = $db->tableInfo($rs, self::TABLEINFO_MODE);
                if (DB::isError($tableInfo)) {
                    throw new Pasta_Exception('Could not get table info.');
                }
                $this->initializeFromTableInfo($tableInfo);
            }
        }

        $rs->free();
        $this->updateCache(true);

        return $this->getExists();
    }

    /**
     * Saves the data in the current instance to the database. The
     * properties constituting the key columns must be set. If the key consists
     * of only one column called "id", and the column has not been set, a new
     * id is generated using the sequence with the same name as the default
     * table.
     * isValid() is called, and if it returns false, the save is aborted. This
     * depends on the error severity threshold.
     *
     * @return  bool
     * @see isValid()
     */
    function save()
    {
        if ($this->getErrorSeverityThreshold() >= self::SEVERITY_FATAL) {
            trigger_error('Cannot ignore fatal errors', E_USER_WARNING);
            return false;
        }

        if (!$this->isValid()) {
            if (!$this->getErrorString()) {
                trigger_error(get_class($this) . '::isValid() returned ' .
                              'false, but no error string was specified',
                              E_USER_WARNING);
                $this->setErrorString('Cannot save');
            }
            if ($this->getErrorSeverity() < $this->getErrorSeverityThreshold()) {
                trigger_error(get_class($this) . '::isValid() returned ' .
                              'false, but the error severity (' .
                              $this->getErrorSeverity() .
                              ') was lower than the specified threshold (' .
                              $this->getErrorSeverityThreshold() . ')',
                              E_USER_WARNING);
            }
            return false;
        }

        $keyColumns = $this->getKeyColumns();

        // automate calling of getNextId
        if (!$this->exists() &&
            sizeof($keyColumns) == 1 && $keyColumns[0] == 'id' &&
            is_null($this->getColumn('id'))) {

            $this->setColumn('id', $this->getNextId($this->getDefaultTable()));
        }

        // check key columns
        foreach ($keyColumns as $column) {
            if (is_null($this->getColumn($column))) {
                trigger_error("Key column '$column' not set.", E_USER_WARNING);
                // vi burde returnere falsk her, men vi tør ikke, før vi er sikre
                // på, at der ikke er noget kode, der forlader sig på den gamle
                // virkemåde (typisk hvis de bruger en auto-increment-kolonne)
            }
        }

        // re-pack virtual columns
        $vColNames = $this->getVirtualColumnNames();
        $cCols = array_intersect($this->_changedColumns, $vColNames); // changed virtual columns
        if (count($cCols)) {
            $vColColName = $this->getVirtualColumnsColumnName();
            $virtualColumns = array_key_exists($vColColName, $this->_row)
                ? @unserialize($this->_row[$vColColName])
                : array();
            foreach ($cCols as $cCol) {
                $virtualColumns[$cCol] = $this->_row[$cCol];
            }
            $this->setColumn($vColColName, serialize($virtualColumns));
        }

        $ok = true;
        foreach ($this->getTables() as $table) {
            if ($this->isTableChanged($table)) {
                if ($this->getExists()) {
                    $ok = $ok && $this->executeUpdate($table);
                } else {
                    $ok = $ok && $this->executeInsert($table);
                }
            }
        }
        if ($ok) {
            $this->_changedColumns = array();
            $this->setExists(true);
            $this->updateCache();
        }
        return $ok;
    }

    /**
     * Deletes the rows represented by the current instance from the database.
     * The properties constituting the key columns must be set.
     * @return  bool
     */
    function delete() {
        if (!$this->exists()) {
            trigger_error('Object does not exist in database', E_USER_NOTICE);
            return false;
        }

        $ok = true;
        foreach ($this->getTables() as $table) {
            $ok = $ok && $this->executeDelete($table);
        }
        if ($ok) {
            $this->setExists(false);
            $this->updateCache();
        }
        return $ok;
    }

    /**
     * Find the row with matching key values and set this obj. row to it
     * @param string table name
     * @return bool
     */
    function find($table = null, $onlyByKeys = true)
    {
        return $this->load($onlyByKeys);
    }

    /**
     * @return  Pasta_TableRow  a instance of a subclass of Pasta_TableRow, or null
     */
    function findOne() {
        $found = $this->load(false);
        return $found ? $this->getMutation() : null;
    }

    /**
     * Delete all objects of the specified type that have the same values
     * as those set on this instance.
     * @return  int  number of deleted rows
     */
    function deleteAll()
    {
        $deleted = 0;
        foreach ($this->findAll() as $obj) {
            if ($obj->delete()) {
                $deleted++;
            }
        }
        return $deleted;
    }

    /**
     * Returns all objects that have the same values as those set on this
     * instance.
     * @return  array  an array of instances of this class
     */
    function findAll()
    {
        $table = $this->getDefaultTable();
        $db = $this->getPearDb(PASTA_DB_QUERY_READ);
        if (!$db) {
            return array();
        }

        $objects = array();
        $sql = $this->getSelectSql(false);
        if ($this->_limit) {
            $rs = $db->limitQuery($sql, $this->_limit[0], $this->_limit[1]);
        } else {
            $rs = $db->query($sql);
        }
        if (DB::isError($rs)) {
            return array();
        }
        $tableInfo = $db->tableInfo($rs, self::TABLEINFO_MODE);
        if (DB::isError($tableInfo)) {
            throw new Pasta_Exception('Could not get table info.');
        }
        $this->initializeFromTableInfo($tableInfo);
        while ($row = $rs->fetchRow()) {
            $class = $this->getClassNameByRow($row);
            if (!class_exists($class) || !is_subclass_of($class, __CLASS__)) {
                trigger_error("$class is not a subclass of " . __CLASS__);
                continue;
            }
            $object = new $class();
            $object->setRow($row, false);
            $object->setExists(true);
            $object->updateCache(true);
            $objects[] = $object;
        }
        $rs->free();
        return $objects;
    }

    /**
     * Returns an indexed array of the values from the specified column from
     * all rows that have the same values as those set on this instance. The
     * array key is the column returned by getKeyColumns(). If the key consists
     * of multiple columns, an error is triggered.
     * @return  array
     */
    function findColumn($columnName)
    {
        $prefixedColumnName = $this->getPrefixedColumnNameByColumnName($columnName);
        if (!$prefixedColumnName) {
            trigger_error("Invalid column '$columnName'", E_USER_WARNING);
            return array();
        }
        $db = $this->getPearDb(PASTA_DB_QUERY_READ);
        if (!$db) {
            return array();
        }
        $sql = $this->getSelectSql(false, $prefixedColumnName);
        if ($this->_limit) {
            $sql .= ' LIMIT ' . $this->_limit[0] . ', ' . $this->_limit[1];
        }
        $r = $db->getCol($sql);
        return DB::isError($r) ? array() : $r;
    }

    /**
     * Returns count of all objects of the specified type that have the same values
     * as those set on this instance.
     * @return  int
     */
    function countAll()
    {
        $db = $this->getPearDb(PASTA_DB_QUERY_READ);
        if (!$db) {
            return null;
        }
        $sql = $this->getSelectSql(false, 'COUNT(*)');
        $r = $db->getOne($sql);
        return DB::isError($r) ? 0 : $r;
    }

    /** Fine tune searching...
     * @param  string  an SQL expression (without leading or trailing "AND")
     * @param  bool    add parantheses around the expression (necessary if the
     *                 expression contains "OR" et.al)
     */
    function addWhereSql($sql, $addParentheses = true)
    {
        if ($addParentheses) {
            $sql = '(' . $sql . ')';
        }
        $this->_customWhereSql[] = $sql;
    }

    /**
     * A helper function for AddWhereSql
     *
     * @param string $column columnname
     * @param array $values
     */
    public function columnNotIn($columnName, $values) {
        $prefixedColumnName = $this->getPrefixedColumnNameByColumnName($columnName);
        if (!$prefixedColumnName) {
            return;
        }
        $valuesIn = array();
        $db = $this->getPearDb();
        if (!$db) {
            return;
        }
        foreach ($values as $value) {
            $valuesIn[] = "'" . $db->escapeSimple($value) . "'";
        }
        $sql = $prefixedColumnName .
            " NOT IN (" . (count($valuesIn) ? implode(', ', $valuesIn) : 'NULL') . ")";
        $this->addWhereSql($sql, false);
    }

    /** A helper function for AddWhereSql
     * @param string columnname
     * @param array values
     */
    public function columnIn($columnName, $values)
    {
        $prefixedColumnName = $this->getPrefixedColumnNameByColumnName($columnName);
        if (!$prefixedColumnName) {
            return;
        }
        $valuesIn = array();
        $db = $this->getPearDb();
        if (!$db) {
            return;
        }
        foreach ($values as $value) {
            $valuesIn[] = "'" . $db->escapeSimple($value) . "'";
        }
        $sql = $prefixedColumnName .
            " IN (" . (count($valuesIn) ? implode(', ', $valuesIn) : 'NULL') . ")";
        $this->addWhereSql($sql, false);
    }

    /** A helper function for addWhereSql
     * @param string columnname
     * @param string like
     */
    public function columnLike($columnName, $like)
    {
        $prefixedColumnName = $this->getPrefixedColumnNameByColumnName($columnName);
        if (!$prefixedColumnName) {
            return;
        }
        $db = $this->getPearDb();
        if (!$db) {
            return;
        }
        $this->addWhereSql($prefixedColumnName . " LIKE '" . $db->escapeSimple($like) . "'", false);
    }

    /**
     * Returns an array of table names contained in the view represented by
     * this instance. This should be overridden by subclasses, unless the
     * table has the same name as the class.
     *
     * @return  array  an array of strings
     */
    function getTables()
    {
        return array($this->getDefaultTable());
    }

    /**
     * Returns the default table name that is used by other methods when a
     * table name is not passed as an argument.
     * @return  string
     */
    function getDefaultTable()
    {
        $className = self::getDbClassNameByClassName(get_class($this));
        $name = substr($className, strrpos($className, '_') + 1);
        $classNameParts = explode('_', $className);
        $productName = strtolower(array_shift($classNameParts));
        //keep interCaps, but lower-case the first letter, e.g.
        //"Bas_FooBar" is turned into "fooBar"
        return $productName . '_' . strtolower($name[0]) . substr($name, 1);
    }

    /**
     * Return the list of primary key columns. All tables in the view
     * represented by this instance should have the same primary key.
     * @return  array  array of column names
     */
    function getKeyColumns()
    {
        return array("id");
    }

    /**
     * Return the database row.
     * @return  array  associative array of (column => value) pairs
     */
    function getRow()
    {
        return $this->_row;
    }

    /**
     * Assign database row data.
     * @param  array  associative array of (column => value) pairs
     * @param  bool   if true, the row is assumed to have a for each table
     *                column names (this saves quite a bit CPU).
     */
    public function setRow(array $row, $fillRow = true)
    {
        // NOTE: This method is heavily optimized - make sure to profile any
        // changes
        if (!is_array($row)) {
            trigger_error('$row is not an array');
            return false;
        }

        // expand virtual values (real columns wins)
        $vColNames = $this->getVirtualColumnNames();
        if ($vColNames) { // yes, we use virtual columns
            $vColColName = $this->getVirtualColumnsColumnName();
            $virtualColumns = isset($row[$vColColName]) && $row[$vColColName]
                ? unserialize($row[$vColColName])
                : array();
            foreach ($vColNames as $vName) {
                if (!array_key_exists($vName, $row)) {
                    $row[$vName] = isset($virtualColumns[$vName])
                        ? $virtualColumns[$vName] : null;
                }
            }
        }

        if ($fillRow) {
            // ensure that all columns are there
            foreach (array_diff($this->getTableColumnNames(), array_keys($row)) as $colName) {
                $row[$colName] = null;
            }
        }

        // save state
        $this->_row = $row;
        $this->_changedColumns = array();
    }

    /**
     * Returns the proper subclass for the specified row. This is used when
     * several classes inherit from the same base class, and the specific class
     * to be used depends on the contents of the database row.
     * @param  array
     * @return  string  a class name, or false if the default should be used
     * @see     getMutation()
     */
    protected function getClassNameByRow($row)
    {
        return get_class($this);
    }

    /**
     * Public synonym for self::getClassNameByRow()
     * @param array
     * @return  string  a class name
     */
    public function getClassNameByRow2($row)
    {
        return $this->getClassNameByRow($row);
    }

    /**
     * Returns a new instance of Pasta_TableRow depending on the current
     * values. This is used to get an instance of the proper subclass, i.e. the
     * one returned by getClassNameByRow.
     * @return  Pasta_TableRow
     * @see     getClassNameByRow()
     */
    public function getMutation()
    {
        $class = $this->getClassNameByRow($this->_row);
        if ($class == get_class($this)) {
            $object = $this;
        } else {
            $object = new $class();
            if (!is_subclass_of($object, __CLASS__)) {
                trigger_error("$class is not a subclass of " . __CLASS__);
                return null;
            }
            $object->setRow($this->_row, false);
            $object->_exists = $this->_exists;
            $object->_changedColumns = $this->_changedColumns;
        }
        return $object;
    }

    /**
     * Returns the threshold for ignoring problems in isValid().
     * @return  int  a severity level
     */
    public function getErrorSeverityThreshold()
    {
        return $this->_errorSeverityThreshold;
    }

    /**
     * Sets the threshold for ignoring problems in isValid().
     * @param   int  return false if there are problems with severity >= this
     *               value, i.e. ignore problems with severity below this value
     */
    public function setErrorSeverityThreshold($threshold)
    {
        $this->_errorSeverityThreshold = $threshold;
    }

    /**
     * Returns a description of the last error that has occurred during
     * update(), delete() etc.
     * @return   string
     */
    public function getErrorString()
    {
        if (!$this->_errorString && isset($this->_errorName)) {
            // Legacy support
            foreach (array('en', 'da') as $languageCode) {
                $string = $this->getErrorStringByLanguageCode($languageCode);
                if ($string) {
                    return $string;
                }
            }
        }
        return $this->_errorString;
    }

    /**
     * Returns the severity of the error described by getErrorString().
     * @return   int   a severity level
     * @see  getErrorString()
     */
    public function getErrorSeverity()
    {
        return $this->_severity;
    }

    /**
     * Assign error string
     * @param  string  error message
     * @param  int     a severity level
     */
    protected function setErrorString($value, $severity = self::SEVERITY_FATAL)
    {
        $this->_errorString = $value;
        $this->_severity = $severity;
    }

    /**
     * Assign error string by text name.
     * @param  string  a name that maps to a translation text
     * @param  array   parameters that are substituted into the string
     * @see Pasta_Translation
     */
    protected function setErrorName($name, array $params = array())
    {
        $this->_errorName       = $name;
        $this->_errorParameters = $params;
    }

    /**
     * Copies error name or error string from the specified object to this
     * object.
     * @param  Pasta_TableRow  an object whose _errorName or _errorString
     *                         property has been set
     */
    protected function setErrorByObject(Pasta_TableRow $source)
    {
        if (isset($source->_errorName)) {
            $this->_errorName       = $source->_errorName;
            $this->_errorParameters = $source->_errorParameters;
        } else {
            // Legacy support
            $this->_errorString     = $source->_errorString;
        }
        $this->_severity = $source->_severity;
    }

    /**
     * Assign error string
     * @param  string  a language code
     * @return string
     */
    public function getErrorStringByLanguageCode($languageCode)
    {
        if (!isset($this->_errorName)) {
            // Legacy support
            return $this->_errorString;
        }
        $packageName = Vip_Product::getNameByClassName(get_class($this));
        $translation = Pasta_Translation::getByPackageNameAndLanguageCode($packageName, $languageCode);
        if (!$translation) {
            return false;
        }
        return $translation->getStringByName($this->_errorName, $this->_errorParameters);
    }

    /**
     * @param string
     * @return mixed
     */
    function __get($name)
    {
        // look for getX function
        $fName = 'get' . $name;
        if (method_exists($this, $fName)) {
            return $this->$fName();
        }
        return $this->getColumn($name);
    }

    /**
     * @param string
     * @param mixed
     * @return void
     */
    function __set($name, $value)
    {
        // look for setX function
        $fName = 'set' . $name;
        if (method_exists($this, $fName)) {
            $this->$fName($value);
        } else {
            $this->setColumn($name, $value);
        }
    }

    /**
     * @param  string
     * @param  array
     * @return mixed  if $method was getFoo, a value is returned, otherwise the
     *                return value is void
     */
    function __call($method, $args)
    {
        if (strlen($method) > 3) {
            $type = substr($method, 0, 3);
            $column = strtolower($method[3]) . substr($method, 4);
            if (!isset($_SERVER['PEYTZ_DEV']) ||
                in_array($column, $this->getColumnNames())) {

                if ($type == 'get') {
                    return $this->getColumn($column);
                } elseif ($type == 'set') {
                    if (sizeof($args) == 0) {
                        trigger_error("Missing argument 1 for " . get_class($this) .
                                      "::$method()", E_USER_WARNING);
                        return;
                    } else {
                        $this->setColumn($column, $args[0]);
                        return;
                    }
                }
            }
        }
        trigger_error("Call to undefined method " . get_class($this) .
                      "::$method()", E_USER_ERROR);
    }

    /**
     * @param  array
     */
    private function initializeFromTableInfo(array $tableInfo)
    {
        foreach ($tableInfo['ordertable'] as $table => $rowInfo) {
            $columnNameKey = $this->getColumnNameKey($table);
            self::$_columnNames[$columnNameKey] = array_keys($rowInfo);
        }
    }

    /**
     * return an array of all column names incl. virtual
     * @return array
     */
    public function getColumnNames()
    {
        return array_unique(array_merge($this->getTableColumnNames(), $this->getVirtualColumnNames()));
    }

    /** return the list of real column names
     @return array
     */
    public final function getTableColumnNames()
    {
        $cacheKey = get_class($this);
        if (!isset(self::$tableColumnCache[$cacheKey]) || $cacheKey == 'Pasta_SimpleTableRow') {
            self::$tableColumnCache[$cacheKey] = array();
            foreach ($this->getTables() as $table) {
                self::$tableColumnCache[$cacheKey] = array_merge(self::$tableColumnCache[$cacheKey], $this->getColumnNamesByTable($table));
            }
        }
        return self::$tableColumnCache[$cacheKey];
    }

    /**
     * return an array of column names in the table
     * @param string table name
     * @return array
     */
    public final function getColumnNamesByTable($table = null)
    {
        if (!$table) {
            $table = $this->getDefaultTable();
        }

        if (!in_array($table, $this->getTables())) {
            trigger_error("Invalid table '$table'", E_USER_WARNING);
            return;
        }

        $columnNameKey = $this->getColumnNameKey($table);
        if (!isset(self::$_columnNames[$columnNameKey])) {
            self::$_columnNames[$columnNameKey] = array();
            $db = $this->getPearDb();
            if (!$db) {
                return array();
            }

            $tableInfo = $db->tableInfo($table, self::TABLEINFO_MODE);
            if (DB::isError($tableInfo)) {
                throw new Pasta_Exception('Could not get table info.');
            }
            $this->initializeFromTableInfo($tableInfo);
        }

        return self::$_columnNames[$columnNameKey];
    }

    /**
     * Add table prefix to column name in order to prevent SQL errors when same
     * the column name appears in several JOIN'ed tables (usually "id"). The
     * table name used is the first table that occurs in getTables() that
     * contains a column with the specified name.
     * @param string  e.g. "id"
     * @param string  e.g. "footable.id"
     */
    private function getPrefixedColumnNameByColumnName($columnName)
    {
        // Deprecated: Support "tablename.columnname" as parameter.
        if (strpos($columnName, '.')) {
            // Strip table name.
            $columnName = preg_replace('/^.+\./', '', $columnName);
            trigger_error('Tablename prefix is disabled');
        }
        foreach ($this->getTables() as $tableName) {
            if (in_array($columnName, $this->getColumnNamesByTable($tableName))) {
                return $tableName . '.' . $columnName;
            }
        }
        trigger_error("Invalid column '$columnName'", E_USER_WARNING);
        return false;
    }

    private function getColumnNameKey($table)
    {
        return $this->getDbId() . '.' . $table;
    }

    /**
     * Return the name of the real column that holds the virtual columns
     * @return string
     */
    public function getVirtualColumnsColumnName($table = null)
    {
        return "foreignData";
    }

    /**
     * Get the list of virtual column names
     * @return array of string
     */
    protected function getVirtualColumnNames()
    {
        return array();
    }
}

?>
