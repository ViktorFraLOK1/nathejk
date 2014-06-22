<?php

// PEAR DB is not E_STRICT compatible
$oldLevel = error_reporting();
error_reporting($oldLevel & E_ALL);
require_once 'DB.php';
require_once 'DB/mysql.php';
error_reporting($oldLevel);
#require_once 'Pasta/Db/ConnectionPool.class.php';

/**
 * Database selection logic
 * The DSN is set in apache's conf;
 *  - PEYTZ_DSN_<DBID> for read/write to same host
 * or
 *  - PEYTZ_DSN_<DBID>_READ for read connection
 *  - PEYTZ_DSN_<DBID>_WRITE for write connection
 * @package Pasta
 */

/**
 * DB Connection Retries
 */
define("PASTA_DB_CONNECTION_RETRIES_MAX", 3);
 
/**
 * Query directions
 */
define("PASTA_DB_QUERY_READ", "READ");
define("PASTA_DB_QUERY_WRITE", "WRITE");

/**
 * apache env preface
 */
define("PASTA_DB_APACHE_PREFACE", "PEYTZ_DSN_");

/**
 *
 */
$GLOBALS['getPearDbByDbIdAndDirection'] = "getPearDbByDbIdAndDirectionDefault";
$GLOBALS['getDsnByDbIdAndDirectionCache'] = array();

/**
 * Initialize default db connection
 * @param  string  database identifier
 * @param  string  a PASTA_DB_QUERY_xxx constant
 * @return string  a DSN e.g. "mysql://john:SecReT@myhost/mybase"
 * @throws Pasta_DbException
 */
function getDsnByDbIdAndDirection($dbId, $direction)
{
    return 'mysqli://nathejk:vabbes@localhost/nathejk';
    global $getDsnByDbIdAndDirectionCache, $CUSTOMER;

    // determine if customer has specialized dsn's - either a set of READ/WRITE or a general dsn
    $customerName = '';
    if ($CUSTOMER) {
        $customerBaseDsnName = PASTA_DB_APACHE_PREFACE . strtoupper($dbId . '_' . $CUSTOMER->name);
        if (isset($_SERVER[$customerBaseDsnName]) || 
            isset($_SERVER[$customerBaseDsnName . '_READ'], $_SERVER[$customerBaseDsnName . '_WRITE'])) {
            $customerName = strtoupper($CUSTOMER->name);
        }
    }
    if (isset($getDsnByDbIdAndDirectionCache[$dbId][$customerName][$direction])) {
        return $getDsnByDbIdAndDirectionCache[$dbId][$customerName][$direction];
    }

    // find apache dsn info
    $sName = PASTA_DB_APACHE_PREFACE . strtoupper($dbId) . (!empty($customerName) ? "_$customerName" : '');
    if (!empty($_SERVER[$sName])) {
        // general direction dsn found
        $dsn = $_SERVER[$sName];
    } else {
        $sName .= "_" . $direction;
        if (!empty($_SERVER[$sName])) {
            // specific direction dsn found
            $dsn = $_SERVER[$sName];
        }
    }
    if (empty($dsn)) {
        // look for default dsn
        $sName = PASTA_DB_APACHE_PREFACE . "DEFAULT";
        if (!empty($_SERVER[$sName])) {
            // general direction dsn found
            $dsn = $_SERVER[$sName];
        } else {
            $sName .= "_" . $direction;
            if (!empty($_SERVER[$sName])) {
                // specific direction dsn found
                $dsn = $_SERVER[$sName];
            } else {
                $dsn = false;
            }
        }
        $dsn = str_replace("DEFAULT", strtolower($dbId), $dsn);
    }

    if (!$dsn) {
        throw new Pasta_DbException("DSN '$dbId' not defined");
    }

    // If we have written to the database, then do the remaining reads in the
    // current request in the write database to make sure that the data we
    // have just written is available. In future requests, check that the
    // replication lag does not exceed the time passed since the last write.
    $cookieName = 'pastaDbWrite_' . $dbId;
    if ($direction == PASTA_DB_QUERY_WRITE) {
        // Make reads use the master for the rest of this request
        $getDsnByDbIdAndDirectionCache[$dbId][$customerName][PASTA_DB_QUERY_READ] = $dsn;

        // Make reads use the master for the next few requests
        $now = time();
        if (!headers_sent() && class_exists("Pasta_Replication")) {
            $_COOKIE[$cookieName] = $now;
            setcookie($cookieName, $now, time() + 1800, '/', '', false, true);
        }
    } elseif (isset($_COOKIE[$cookieName]) && class_exists("Pasta_Replication")) {
        // If the last write hasn't reached the slave, use the master for reading.
        // If the last write was within the last 5 seconds, don't bother looking
        // up the lag.
        // Extract another two seconds to compensate for rounding to whole seconds.
        if ($_COOKIE[$cookieName] > time() - 5 ||
            $_COOKIE[$cookieName] > time() - Pasta_Replication::getLagByDsn($dsn) - 2) {
            // Note: The recursive call updates the cookie once again, so if
            // the lag is considerable, the user may end up using only the
            // after master a write, if he keeps doing consecutive reads with
            // intervals shorter that the lag period. But this may actually be
            // a good thing because it gives the slave a better chance to catch
            // up.
            $dsn = getDsnByDbIdAndDirection($dbId, PASTA_DB_QUERY_WRITE);
            if (isset($_SERVER['ipPeytz']) && !headers_sent()) {
                header('X-Pasta-Db: Using master for reading');
            }
        } elseif (!headers_sent()) {
            // Slave is up-to-date - delete cookie
            setcookie($cookieName, '', time() - 86400, '/', '', false, true);
            unset($_COOKIE[$cookieName]);
        }
    }

    $getDsnByDbIdAndDirectionCache[$dbId][$customerName][$direction] = $dsn;

    return $dsn;
}

$GLOBALS['getPearDbByDbIdAndDirectionDefaultCache'] = array();

/**
 * @param  string database identifier
 * @param  string a PASTA_DB_QUERY_xxx constant
 * @return DB     a PEAR DB object
 * @throws Pasta_DbException
 */
function getPearDbByDbIdAndDirectionDefault($dbId, $direction)
{
    global $getPearDbByDbIdAndDirectionDefaultCache;
    $dsn = getDsnByDbIdAndDirection($dbId, $direction);

    if (isset($getPearDbByDbIdAndDirectionDefaultCache[$dsn])) {
        $db = $getPearDbByDbIdAndDirectionDefaultCache[$dsn];
        if ($direction == PASTA_DB_QUERY_WRITE) { // write wins
            $db->pastaDirection = $direction;
        }
        return $db;
    }

    $doDebug = isset($_SERVER['PEYTZ_DEV']) ||
        isset($_REQUEST['PASTA_DEBUG_DB']) && $_SERVER['REMOTE_ADDR'] == '80.199.116.190';
    if ($doDebug) {
        require_once 'Pasta/Debug/DB.class.php';
        $db = Pasta_Debug_DB::connect($dsn);

        if (isset($_REQUEST['PASTA_DEBUG_DB'])) {
            require_once 'Pasta/Debug.class.php';
            $action = $_REQUEST['PASTA_DEBUG_DB']
                ? $_REQUEST['PASTA_DEBUG_DB'] : Pasta_Debug::LOG_ACTION_APPEND;
            Pasta_Debug::setLogAction(Pasta_Debug::LOG_TYPE_DB, $action);
        }
    } else {
        $db = DB::connect($dsn);
    }

    // Retry on connection errors
    $dbConnectionRetries = 0;
    while (DB::isError($db) && $dbConnectionRetries < PASTA_DB_CONNECTION_RETRIES_MAX) {
        if (strpos($db->getUserinfo(), "Lost connection to MySQL server") !== false) {
            // Try just once more on a certain type of transient error that happens
            // once in a while during high load:
            // Lost connection to MySQL server at 'reading authorization packet'
            // Lost connection to MySQL server at 'reading initial communication packet'
            // Lost connection to MySQL server during query
            $dbConnectionRetries = PASTA_DB_CONNECTION_RETRIES_MAX;
            // This only has effect with the PEAR DB mysql adapter and a few
            // others, not including mysqli
            $dsn .= (strpos($dsn, '?') === false ? '?' : '&') . 'new_link=true';
        }
        // Count the number of retries
        $dbConnectionRetries++;
        // Sleep - but just a little bit
        usleep(rand(10, 250));
        // Retry connecting
        $db = DB::connect($dsn);
    }

    if (DB::isError($db)) {
        throw new Pasta_DbException($db->toString());
    } else {
        $db->setFetchMode(DB_FETCHMODE_ASSOC);
        $db->setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
        $db->pastaDirection = $direction;
    }

    if ($doDebug && isset($_GET['PASTA_DEBUG_DB_FLUSH'])) {
        $db->query('FLUSH TABLES');
        unset($_GET['PASTA_DEBUG_DB_FLUSH']);
    }

    $getPearDbByDbIdAndDirectionDefaultCache[$dsn] = $db;

    return $db;
}

?>
