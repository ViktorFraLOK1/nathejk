<?php

require_once 'Pasta/Debug.class.php';
require_once 'Pasta/TableRow.class.php';
require_once 'Vip/Product.class.php';

/*
Test cases:

$foo = 'not an object';
$foo->bar;

$error = new Pasta_Debug_Error();
$error->trigger('$error->trigger()', E_USER_WARNING);

$error = new Pasta_Debug_Error(true);
if (time() % 2) {
    $error->trigger('$error->trigger() w/reset', E_USER_WARNING);
} else {
    $error->reset();
}

trigger_error('trigger_error()');

fopen();

echo $undefinedVariable;

// Should display additional info in green text
$db = $error->getPearDb();
$db->getOne('SELECT 1 2');

throw new Exception('throw new Exception');
*/

/**
 * @package Pasta
 * @subpackage Debug
 */
class Pasta_Debug_Error extends Pasta_TableRow
{
    const STATUS_TYPE_NEW        = 0;
    const STATUS_TYPE_BUGZILLA   = 1;
    const STATUS_TYPE_FIXED_CVS  = 2;
    const STATUS_TYPE_FIXED_PROD = 3;
    const STATUS_TYPE_SEEN       = 9;

    private $_stackTrace = null;
    private $_globals = null;

    /**
     * @param  mixed  If false, md5Key is generated automatically in handle().
     *                if true, md5Key is initialized based on file and line
     *                number of the code calling the constructor.
     *                Otherwise, md5key is calculated as the MD5 checksum of
     *                this argument.
     */
    public function __construct($key = false)
    {
        if ($key === true) {
            list($frame) = debug_backtrace();
            if (!isset($frame['file'], $frame['line'])) {
                trigger_error('Could not find calling file/line');
                return;
            }
            $key = $frame['file'] . $frame['line'];
        }
        if ($key) {
            $this->md5Key = md5($key);
        }
    }

    public function getPearDb($direction = PASTA_DB_QUERY_READ)
    {
        $db = parent::getPearDb($direction);
        if (method_exists($db, 'setLogMode')) {
            $db->setLogMode(PASTA_DEBUG_DB_LOG_IGNORE);
        }
        return $db;
    }

    /**
     * Returns a string description reflecting the error number.
     * @return string
     * @see  getErrorNumber()
     */
    function getErrorType()
    {
        switch ($this->getErrorNumber()) {
            case E_PARSE:
                return 'Parse error';
            case E_ERROR:
                return 'Error';
            case E_RECOVERABLE_ERROR:
                return 'Recoverable error';
            case E_USER_ERROR:
                return 'User error';
            case E_WARNING:
                return 'Warning';
            case E_USER_WARNING:
                return 'User warning';
            case E_NOTICE:
                return 'Notice';
            case E_USER_NOTICE:
                return 'User notice';
            case E_STRICT:
                return 'Strict warning';
            default:
                return 'Unknown error type (' . $this->getErrorNumber() . ')';
        }
    }

    /**
     * Returns a short code reflecting the error number.
     * @return string  a one- or two-letter string
     * @see  getErrorNumber()
     */
    function getErrorTypeCode()
    {
        return strtoupper(preg_replace('/(\b\w)\w+\s?/', '\1', 
                                       $this->getErrorType()));
    }

    /**
     * Returns an integer representing the relative importance of each error
     * number. Highest number is most important, 0 is least important.
     * @param   int  E_USER_NOTICE, E_WARNING etc.
     * @return  int
     */
    public static function getImportanceByErrorNumber($errorNumber)
    {
        // Error numbers sorted by importance
        static $numbers = array(E_PARSE, E_ERROR, E_RECOVERABLE_ERROR,
            E_USER_ERROR, E_WARNING, E_USER_WARNING, E_NOTICE, E_USER_NOTICE,
            E_STRICT);

        $i = array_search($errorNumber, $numbers);
        if ($i === false) {
            trigger_error('Unknown error type (' . $errorNumber . ')');
            return 0;
        } else {
            return $i;
        }
    }

    /**
     * @param  int
     * @return int
     * @see getImportanceByErrorNumber()
     */
    public function getImportance()
    {
        return self::getImportanceByErrorNumber($this->getErrorNumber());
    }

    /**
     * Returns whether this error has existed for too long without being
     * touched by the responsible person.
     * @return  bool
     */
    public function isOverdue()
    {
        if ($this->getStatusType() != self::STATUS_TYPE_NEW &&
            $this->getErrorNumber() == E_USER_NOTICE) {

            return false;
        }
        // Note: There is no support for national Holidays
        $expireUts = $this->firstOccurrence + 86400;
        if (idate('w', $this->firstOccurrence) == 6) {
            // Saturday
            $expireUts += 2 * 86400;
        } elseif (idate('w', $this->firstOccurrence) == 0) {
            // Sunday
            $expireUts += 86400;
        }
        return time() > $expireUts;
    }

    /**
     * Triggers the current instance. This is similar to calling trigger_error().
     * @param  string  an error message
     * @param  int     an E_USER_ constant
     */
    public function trigger($message, $errorNumber = E_USER_NOTICE)
    {
        $this->message     = $message;
        $this->errorNumber = $errorNumber;
        $this->stackTrace  = debug_backtrace();
        $this->handle(true);
    }

    /**
     * Resets recentOccurrenceCount.
     * @param  bool  true to reset (everything is ok), and false to trigger
     */
    public function reset()
    {
        if (!$this->md5Key) {
            trigger_error('$error->md5Key not set - '
                          . 'use "new Pasta_Debug_Error(true)" '
                          . 'or "new Pasta_Debug_Error(\'global unique key\')"',
                          E_USER_WARNING);
            return;
        }
        $error = self::getByMd5Key($this->md5Key);
        if ($error) {
            $error->recentOccurrenceCount = 0;
            $error->resetUts = time();
            $error->save();
        }
    }

    /**
     * Saves current instance to database, sends emails etc. This is called
     * from the errorHandling.inc.php and backend/cron/checkErrorLog.php.
     * @param   bool  true if the error has just occurred in the current
     *                request; false if the error was read from an error log.
     */
    public function handle($hasJustOccurred)
    {
        // Number of errors in this request.
        static $errorCount = 0, $maxImportance = 0;

        $now = time();
        $isDisplayed = false;

        // We silently ignore errors about calling header() too late, if the
        // reason for the error is that we have already displayed an error.
        if ($errorCount > 0 &&
            $hasJustOccurred &&
            strpos($this->message, 'headers already sent')) {

            return;
        }

        // Check whether the error should be displayed - if we are on
        // a production server, it probably shouldn't, unless the user
        // is a "good guy" (and not a cron job).
        $isCron = isset($_SERVER['PEYTZ_CLI']) && !isset($_ENV['TERM']);
        if ($hasJustOccurred
            && !$isCron
            && (ini_get('display_errors') == 1 || isset($_SERVER['ipPeytz']))) {

            $allowJavascript = Pasta_Debug::getLogFormat() == Pasta_Debug::LOG_FORMAT_HTML;
            Pasta_Debug::log($this->getHtmlString($allowJavascript, true),
                             Pasta_Debug::LOG_TYPE_ERROR,
                             true);
            $isDisplayed = true;
        }

        // We throttle per 900 seconds - in case everything is broken, we don't
        // want to log every request.
        $throttleFile = '/tmp/pastaErrorHandling-' . ($now - $now % 900);

        if ($hasJustOccurred) {
            $errorCount++;
            $importance = $this->importance;
            // Is this error the most important in this request?
            $isMostImportant = $importance > $maxImportance;
            $maxImportance = max($importance, $maxImportance);
        } else {
            $isMostImportant = true;
        }

        // Only log the first few errors per request, but always log an error that
        // is more important than those previously logged in this request (this
        // prevents that notices hide warnings).
        // Throttle the maximum numbers of errors per period.
        $logToDatabase =
            (!isset($_SERVER['PEYTZ_DEV']) || isset($_SERVER['PEYTZ_TEST'])) &&
            ($errorCount < 5 || $isMostImportant) &&
            @filesize($throttleFile) < 50;

        $error = null;
        if ($logToDatabase) {
            file_put_contents($throttleFile, 'x', FILE_APPEND);
            // When using peytzPhp, the user is not necessarily www-data.
            @chmod($throttleFile, 0666);

            // Create MD5 key based if not explicitly set. If message contains
            // multiple lines, use only the first for generating the key.
            if (!$this->md5Key) {
                $this->md5Key = md5(
                    $this->filename .
                    $this->lineNumber .
                    preg_replace("/\n.*/", '', $this->message));
            }

            try {
                $error = self::getByMd5Key($this->md5Key);
            } catch (Pasta_DbException $e) {
                // Database is inaccessible, so don't save this to the database.
                $logToDatabase = false;
            }
        }

        if (!$error) {
            $error = $this;
            $error->recentOccurrenceCount = 1;
            $error->totalOccurrenceCount  = 1;
            if ($hasJustOccurred) {
                $error->firstOccurrence   = $now;
                $error->lastOccurrence    = $now;
            } elseif (!$error->firstOccurrence) {
                // $error->lastOccurrence is set in backend/cron/checkErrorLog.php.
                $error->firstOccurrence = $error->lastOccurrence;
            }
            if (!$error->productId) {
                if ($hasJustOccurred && defined('VIP_PRODUCT_NAME')) {
                    $productName = VIP_PRODUCT_NAME;
                } else {
                    $productName = $error->findProductName();
                }
                if ($productName) {
                    $product = Vip_Product::getByName($productName);
                    $error->productId = $product ? $product->id : 0;
                }
            }
            if ($hasJustOccurred) {
                $globals = Pasta_Debug::getGlobals();
                if (isset($_SERVER['PEYTZ_CLI'])) {
                    $globals['_SERVER']['PEYTZ_CLI_COMMAND'] = exec('ps -o command= -p ' . getmypid());
                }
                $error->globals = $globals;
            }
        } else {
            $error->recentOccurrenceCount++;
            $error->totalOccurrenceCount++;
            // $this->lastOccurrence is set in backend/cron/checkErrorLog.php.
            $error->lastOccurrence = $hasJustOccurred ? $now : $this->lastOccurrence;
        }

        if ($logToDatabase) {
            $ok = $error->save();
            // If the same error occurs several times in parallel, only send
            // mail for the one that was saved in the database.
            if (!$ok && !$error->exists() && self::getByMd5Key($error->md5Key)) {
                $logToDatabase = false;
            }
        }

        // Send email if this is the first time we see this error, unless we
        // are in a storm (max 10 mails per period).
        if ($logToDatabase &&
            !$isDisplayed &&
            $isMostImportant &&
            $error->recentOccurrenceCount == 1 &&
            @filesize($throttleFile) < 10) {

            $error->sendEmail();
        }

        // It is the responsibility of the error handler to die on fatal errors
        if ($hasJustOccurred &&
            ($error->getErrorNumber() & (E_ERROR | E_RECOVERABLE_ERROR | E_USER_ERROR))) {
            if (!headers_sent() && !isset($_SERVER['PEYTZ_CLI'])) {
                Pasta_Http::exitWithInternalServerError();
            } else {
                if (!$isCron) {
                    print("\n\nInternal server error\nexit(75)\n");
                }
                // When pasta/backend/cron/incoming.php is run as a Postfix
                // transport, 75 (EX_TEMPFAIL) tells Postfix to retry later
                // rather than consider the delivery failed.
                exit(75);
            }
        }
    }

    /**
     * @return  Vip_User  a user, or null if the product is unknown
     */
    public function getPrimaryContactUser()
    {
        static $users = array();
        $product = $this->getProduct();
        if (!$product) {
            return null;
        }
        $emails = explode(',', $product->getNotifyEmails());
        $email = $emails[0];
        if (!array_key_exists($email, $users)) {
            $user = new Vip_User();
            $user->email = $email;
            $users[$email] = $user->findOne();
        }
        return $users[$email];
    }

    /**
     * Returns an HTML representation of this error.
     * @param  bool  true if the HTML may contain Javascript
     * @param  bool  true if the output should be "compact"
     */
    public function getHtmlString($allowJavascript = true, $compact = true)
    {
        $s = "<div style='text-align: left'>";
        $s .= "<p><span style='font-weight: bold'>" .
            $this->getErrorType() . "</span>: ";
        $s .= htmlspecialchars($this->getMessage());
        $s .= "</p>\r\n";

        // If this error was originally a DB_Error, trigger_error() was called
        // in PEAR_Error::PEAR_Error(). The detailed SQL error message is
        // specified as the fourth argument to this constructor.
        $trace = $this->getStackTrace();
        if (isset($trace[1]['class'], $trace[1]['function'], $trace[1]['args']) &&
            $trace[1]['class'] == 'PEAR_Error' &&
            $trace[1]['function'] == 'PEAR_Error') {

            $text = $trace[1]['args'][4];
            if (preg_match('/^(.*)\[nativecode=[0-9]+ \*\* (.*)\]$/s', $text, $reg)) {
                $sql = $reg[1];
                $dbMsg = $reg[2];

                $s .= "<p style='color: green'>" . htmlspecialchars($dbMsg) . "</p>\r\n";
                $s .= "<p style='white-space: pre'>" .
                    htmlspecialchars($sql) . "</p>\r\n";
            }
        }
        $s .= Pasta_Debug::stackTraceToHtml($this->getStackTrace(), $allowJavascript);
        if (!$compact) {
            $s .= Pasta_Debug::variablesToHtml($this->getGlobals());
        }
        $s .= "</div>";

        return $s;
    }

    /**
     * Sends an HTML representation of this error by email.
     * @param  string  the email address of the recipient
     */
    private function sendEmail()
    {
        require_once 'Mail.php';
        require_once 'Mail/mime.php';

        $mime = new Mail_mime("\n");

        if (strpos($this->getGlobal('_SERVER', 'HTTP_HOST'), '.virtual.')) {
            $bgcolor = '#ffffaa';
            $version = 'virtual';
        } elseif ($this->getGlobal('_SERVER', 'PEYTZ_TEST')) {
            $bgcolor = '#cceecc';
            $version = 'test';
        } elseif ($this->getGlobal('_SERVER', 'PEYTZ_DEV') ||
                  isset($_SERVER['PEYTZ_DEV'])) {
            $bgcolor = '#ccccff';
            $version = 'dev';
        } else {
            $bgcolor = 'white';
            $version = false;
        }

        $html = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
            <html><head><style type="text/css"> '.
            'th { text-align: left; padding: 0 .2em 0 0; } ' .
            'body { background-color: ' . $bgcolor . '} ' .
            '</style></head><body>';

        $html .= '<table cellspacing=0>';
        if ($command = $this->getGlobal('_SERVER', 'PEYTZ_CLI')) {
            $html .= "<tr><th>Host</th> " .
                "<td>" . htmlspecialchars($this->getGlobal('_SERVER', 'PEYTZ_HOSTNAME')) . "</td></tr>\n" .
                "<tr><th>Command</th> " .
                "<td>" . htmlspecialchars($this->getGlobal('_SERVER', 'PEYTZ_CLI_COMMAND')) . "</td></tr>\n";
        } else {
            $url = $this->getUrl();
            if ($url) {
                $html .= "<tr><th>URL</th> " .
                    "<td><a href='" . htmlspecialchars($this->getUrl()) . "'>" .
                    htmlspecialchars($this->getUrl()) . "</a></td></tr>\n";
            }

            $remoteAddr = $this->getGlobal('_SERVER', 'REMOTE_ADDR');
            // REMOTE_ADDR is unknown for segmentation faults.
            if ($remoteAddr && $remoteAddr != '127.0.0.1') {
                $hostname = gethostbyaddr($remoteAddr);
                $html .= "<tr><th>Client</th> <td>" .
                    $remoteAddr .
                    ($remoteAddr != $hostname ? ' (' . $hostname . ')' : '');
                $user = $this->getGlobal('USER');
                if ($user instanceof Vip_User) {
                    $html .= ', ' . $user->customer->name . '\\' . $user->username;
                }
                $html .= "</td></tr>\n";
            }
        }

        $infoUrl = $this->getInfoUrl();
        if ($infoUrl) {
            $infoUrlHtml = htmlspecialchars($infoUrl);
            $html .= "<tr><th>Status</th> " .
                "<td><iframe src='$infoUrlHtml&amp;show=status&amp;VIP_HTTP_AUTH' " .
                     "height='20' width='100%' frameborder='0' marginheight='0' " .
                     "marginwidth='0'></iframe></td></tr>";
            $html .= "<tr><th>Info</th> " .
                "<td><a href='$infoUrlHtml'>$infoUrlHtml</a></td></tr>";
        }

        $html .= "</table>";
        $html .= $this->getHtmlString(false);
        $html .= '</body></html>';

        $mime->setHTMLBody($html);

        $product = $this->getProduct();
        $subject = '[' . ($product ? $product->getName() : '?') . '] ' .
            ($version ? "[$version] " : '') .
            $this->getErrorType() . ': ' . $this->getMessage();

        $hdrs = array(
              'From'     => __CLASS__ . ' <blackhole@peytz.dk>',
              'Subject'  => $subject,
              'X-Mailer' => __FILE__,
              );

        $recipient = $product ? $product->getNotifyEmails() : 'tech@peytz.dk';
        if (isset($_SERVER['PEYTZ_DEV']) && !isset($_SERVER['PEYTZ_TEST'])) {
            // You may want to change this to your own email during development
            $recipient = 'blackhole@peytz.dk';
        }

        $body = $mime->get();
        $hdrs = $mime->headers($hdrs);

        $mail = Mail::factory('mail');
        $ok = $mail->send($recipient, $hdrs, $body);

        return $ok;
    }

    /**
     * Outputs a string representation this error to the browser.
     * @param  bool  true if the output should be "compact"
     */
    public function display($compact = true)
    {
        print $this->getHtmlString(true, $compact);
    }

    /**
     * Returns the url of the page where the error occured.
     * @return  mixed  a URL, or false if the URL is unknown
     */
    public function getUrl()
    {
        $server = $this->getGlobal('_SERVER');
        if (!isset($server['HTTP_HOST'])) {
            return false;
        }
        return (isset($server['HTTPS']) ? 'https' : 'http') . '://' .
            $server['HTTP_HOST'] . $server['REQUEST_URI'];
    }

    /**
     * Returns the URL of the pasta/backend/error/error.php where stacktrace
     * etc. from this error is displayed.
     * @return  string  a URL
     */
    public function getInfoUrl()
    {
        // XXX Replace getId() with exists(), when bug 1884 is fixed
        if (!isset($_SERVER['PEYTZ_VIP_DOMAIN']) || !$this->getId()) {
            return false;
        }
        // We could use Vip_Product to get the backend URL of Pasta, but we
        // hardcode it here so that the error handler doesn't rely on a lot of
        // code and databases.
        return 'https://' . $_SERVER['PEYTZ_VIP_DOMAIN'] .
            '/peytz/pasta/error/error.php?id=' . $this->getId();
    }

    /**
     * Returns the canonical hostname of server, where the error occured, i.e.
     * not the virtual hostname.
     * @return  string  a hostname (e.g. real23)
     */
    public function getHostname()
    {
        return $this->getGlobal('_SERVER', 'PEYTZ_HOSTNAME');
    }

    /**
     * Returns the current product based on $_SERVER[DOCUMENT_ROOT] or on the
     * name of the file where the error was triggered.
     * @return  mixed  a product name (e.g. voxpop or dr-derude), or false if
     *                 the product name is unknown
     */
    public function findProductName()
    {
        $paths = array(
            $this->getGlobal('_SERVER', 'DOCUMENT_ROOT'),
            $this->getFilename());
        foreach ($paths as $path) {
            // Detect /var/www/virtual/foo, /var/www/test.peytz.dk/foo
            // and /home/nn/www/foo
            if (preg_match('@^(/var/www/virtual|/var/www/test\.peytz\.dk|/home/[^/]+/www)/([^/]+)\b@', $path, $reg)) {
                return $reg[2];
            }
        }

        // Errors in Kollage templates - this should be in sync with
        // Pasta/smarty.inc.php
        if (preg_match('@/templates_c/[^_]+_([^_/]+)@', $this->getFilename(), $reg)) {
            return $reg[1];
        }

        return false;
    }

    /**
     * @return  Vip_Product  a product, or null if the product is unknown
     */
    public function getProduct()
    {
        return Vip_Product::getById($this->getProductId());
    }

    /**
     * @return  Vip_Customer  a customer, or null if the customer is unknown
     */
    public function getCustomer()
    {
        return $this->getGlobal('CUSTOMER');
    }

    /**
     * @return  Vip_User
     */
    public function getLastModifyUser()
    {
        return Vip_User::getById($this->getLastModifiedByUserId());
    }

    /**
     * Returns the available status types.
     * @return  array  ((int) statusType => (string) description)
     */
    public static function getStatusTypes()
    {
        return array(
            self::STATUS_TYPE_NEW        => 'ikke behandlet',
            self::STATUS_TYPE_BUGZILLA   => 'oprettet i Bugzilla',
            self::STATUS_TYPE_FIXED_CVS  => 'rettet i CVS',
            self::STATUS_TYPE_FIXED_PROD => 'rettet i produktion',
            self::STATUS_TYPE_SEEN       => 'behandlet',
            );
    }

    /**
     * @return  bool
     */
    public function save()
    {
        // Quickfix for bug 2543
        if (memory_get_usage() < 50 * 1024 * 1024) {
            if (isset($this->_globals)) {
                $this->setColumn('globals', serialize($this->_globals));
            }
            if (isset($this->_stackTrace)) {
                $this->setColumn('stackTrace', serialize($this->_stackTrace));
            }
        }
        return parent::save();
    }

    /**
     * @return  Pasta_Debug_Error
     */
    public static function getById($id)
    {
        return parent::getObjectById(__CLASS__, $id);
    }

    /**
     * @return  Pasta_Debug_Error
     */
    private static function getByMd5Key($md5key)
    {
        $error = new self();
        $error->md5Key = $md5key;
        return $error->findOne();
    }

    /**
     * @return  array  similar to the output of debug_backtrace()
     */
    public function getStackTrace()
    {
        if (isset($this->_stackTrace)) {
            return $this->_stackTrace;
        }
        $string = $this->getColumn('stackTrace');
        $this->_stackTrace = $string ? @unserialize($string) : false;

        // If there is no stack trace, reconstruct part of the first frame.
        if (!is_array($this->_stackTrace)) {
            $this->_stackTrace = array();
            if ($this->fileName) {
                $this->_stackTrace[] = array(
                    'line' => $this->lineNumber,
                    'file' => $this->fileName,
                );
            }
        }
        return $this->_stackTrace;
    }

    /**
     * Sets the stack trace that was actual when the error occurred. The first
     * frame should be the offending line, i.e. frames that are added by the
     * error handling system should be shifted of the array in advance.
     * Likewise, if $stackTrace[0]['function'] is "trigger_error" or the error
     * handler, this must be unset() in advance.
     * @param  array  similar to the output of debug_backtrace()
     */
    public function setStackTrace(array $stackTrace)
    {
        // If error is triggered from global scope, we don't want to store the
        // globals array twice, so we unset args
        if (isset($stackTrace[0]['args'][4]['GLOBALS'])) {
            unset($stackTrace[0]['args'][4]);
        }
        if (!$this->getFilename() && isset($stackTrace[0]['file'])) {
            $this->setFilename($stackTrace[0]['file']);
        }
        if (!$this->getLineNumber() && isset($stackTrace[0]['line'])) {
            $this->setLineNumber($stackTrace[0]['line']);
        }
        // We currently don't use this, so remove it to save space
        foreach ($stackTrace as &$frame) {
            unset($frame['object']);
        }
        $this->_stackTrace = $stackTrace;
    }

    /**
     * Gets the variables in that were present in the global scope when the
     * error occurred.
     * @return  array
     */
    public function getGlobals()
    {
        if (isset($this->_globals)) {
            return $this->_globals;
        }
        $string = $this->getColumn('globals');
        $this->_globals = $string ? @unserialize($string) : false;
        if (!is_array($this->_globals)) {
            $this->_globals = array();
        }
        return $this->_globals;
    }

    /**
     * Sets the variables in that were present in the global scope when the
     * error occurred.
     * @param  array
     */
    public function setGlobals(array $globals)
    {
        $this->_globals = $globals;
    }

    /**
     * Returns the value of $GLOBALS[$name1][$name2][$name3][...]. Specify one
     * or more parameters.
     * @return mixed
     */
    public function getGlobal($name1, $name2 = false, $name3 = false /* etc. */)
    {
        $value = $this->getGlobals();
        for ($i = 0; $i < func_num_args(); $i++) {
            $name = func_get_arg($i);
            if (!isset($value[$name])) {
                return false;
            }
            $value = $value[$name];
        }
        return $value;
    }

    /**
     * Returns the column names in this table without querying the database.
     * This allows the error handler to work at least to some extent when the
     * database is down. Overrides Pasta_TableRow::getColumnNames().
     * @return array  array of column names
     */
    public function getColumnNames()
    {
        static $columnNames = array('id', 'md5Key', 'fileName', 'lineNumber',
            'errorNumber', 'message', 'productId', 'firstOccurrence',
            'resetUts', 'lastOccurrence',
            'recentOccurrenceCount', 'totalOccurrenceCount', 'stackTrace', 
            'globals', 'lastModifiedByUserId', 'statusType', 'statusText',
            );
        return $columnNames;
    }

    function getId()
    {
        return $this->getColumn('id');
    }

    function getFileName()
    {
        return $this->getColumn('fileName');
    }
    function setFileName($value)
    {
        $this->setColumn('fileName', $value);
    }

    function getLineNumber()
    {
        return $this->getColumn('lineNumber');
    }
    function setLineNumber($value)
    {
        $this->setColumn('lineNumber', $value);
    }

    function getMessage()
    {
        return $this->getColumn('message');
    }
    function setMessage($value)
    {
        $this->setColumn('message', $value);
    }

    function getErrorNumber()
    {
        return $this->getColumn('errorNumber');
    }
    function setErrorNumber($value)
    {
        $this->setColumn('errorNumber', $value);
    }

    function getFirstOccurrence()
    {
        return $this->getColumn('firstOccurrence');
    }
    function setFirstOccurrence($value)
    {
        $this->setColumn('firstOccurrence', $value);
    }

    function getLastOccurrence()
    {
        return $this->getColumn('lastOccurrence');
    }
    function setLastOccurrence($value)
    {
        $this->setColumn('lastOccurrence', $value);
    }
}

?>
