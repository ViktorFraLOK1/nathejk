<?php

// deprecated
define('PASTA_DEBUG_LOG_TYPE_ERROR', 'error');
define('PASTA_DEBUG_LOG_TYPE_DB',    'db');
define('PASTA_DEBUG_LOG_TYPE_MISC',  'misc');

// deprecated
define('PASTA_DEBUG_LOG_ACTION_IGNORE',          'ignore');
define('PASTA_DEBUG_LOG_ACTION_INLINE',          'inline');
define('PASTA_DEBUG_LOG_ACTION_APPEND',          'append');

// deprecated
define('PASTA_DEBUG_LOG_FORMAT_HTML',       'html');
define('PASTA_DEBUG_LOG_FORMAT_JAVASCRIPT', 'js');
define('PASTA_DEBUG_LOG_FORMAT_CSS',        'css');
define('PASTA_DEBUG_LOG_FORMAT_XML',        'xml');
define('PASTA_DEBUG_LOG_FORMAT_TEXT',       'txt');

/**
 * @package     Pasta
 * @subpackage  Debug
 */
abstract class Pasta_Debug
{
    const LOG_TYPE_ERROR            = 'error';
    const LOG_TYPE_DB               = 'db';
    const LOG_TYPE_MISC             = 'misc';

    const LOG_FORMAT_HTML           = 'html';
    const LOG_FORMAT_JAVASCRIPT     = 'js';
    const LOG_FORMAT_CSS            = 'css';
    const LOG_FORMAT_XML            = 'xml';
    const LOG_FORMAT_TEXT           = 'txt';

    const LOG_ACTION_IGNORE         = 'ignore';
    const LOG_ACTION_INLINE         = 'inline';
    const LOG_ACTION_APPEND         = 'append';
    const LOG_ACTION_FILE           = 'file';
    /**
     * Log messages to standard error. This should only be used when running
     * from the command line (otherwise the messages end up as big multi-line
     * entries in Apache's error log).
     */
    const LOG_ACTION_STDERR         = 'stderr';

    /**
     * Actions for specified log types.
     * @var  array  associative array, (LOG_TYPE_xxx => LOG_ACTION_xxx)
     */
    private static $logAction = array(
        self::LOG_TYPE_ERROR => self::LOG_ACTION_INLINE,
        self::LOG_TYPE_DB    => self::LOG_ACTION_IGNORE,
        self::LOG_TYPE_MISC  => self::LOG_ACTION_IGNORE,
    );

    private static $logAppend = array();

    private static $logFormat;

    /**
     * Formats a variable as HTML and returns it as a string.
     * @param mixed
     */
    public static function variablesToHtml($variables)
    {
        $s = "<ul>";
        foreach ($variables as $key => $value) {
            //convert value to PHP code
            $s2 = "\$$key = " . print_r($value, true);

            $s2 = preg_replace("/(Array|Object)\n *\(/", "\$1 (", $s2);
            $s2 = preg_replace("/\(\n *\) *\n/", "()", $s2);

            $s2 = preg_replace('/(mysqli?:\/\/[^:]+):[^@]+@/', '\1:****@', $s2);

            $s .= "<li style='white-space: pre'>" . htmlspecialchars($s2) . "</li>";
        }
        $s .= "</ul>\r\n";
        return $s;
    }

    /**
     * @param   array
     * @return  string
     */
    public static function stackTraceToHtml(array $trace, $allowJavascript)
    {
        $s = "<ul>";
        $skipFrames = 0;

        foreach ($trace as $i => &$frame) {
            if (!isset($frame['function'])) {
                continue;
            }

            // Generate  "function", "class->method" or "class::staticMetod".
            $frame['callableName'] = '';
            if (isset($frame['class'])) {
                $frame['callableName'] .= $frame['class'] . $frame['type'];
            }
            $frame['callableName'] .= $frame['function'];

            if (   $frame['callableName'] == 'trigger_error'
                || $frame['callableName'] == 'Pasta_Debug_Error->trigger'
                || $frame['callableName'] == 'Smarty->trigger_error'
                || $frame['callableName'] == 'Smarty_Compiler->_syntax_error'
                || $frame['function'] == 'raiseError'
                || $frame['function'] == 'mysqlRaiseError'
                ) {

                // Hide the name of functions used to trigger errors explicitly,
                // e.g. display
                //   User notice: Foo
                //    * in /var/www/virtual/foo/frontend/bar.php:17
                // rather than
                //   User notice: Foo
                //    * trigger_error("Foo") in /var/www/virtual/foo/frontend/bar.php:17
                unset($frame['function']);

                // Skip all levels below this, i.e. frames inside the error generator.
                $skipFrames = $i;
            }
        }

        $trace = array_slice($trace, $skipFrames);

        foreach ($trace as $i => &$frame) {
            static $codeIdCounter = 0;
            $codeId = 'code-' . $codeIdCounter++;

            $s .= "<li>    ";
            // Frames without a function name occur e.g. with E_NOTICEs about
            // undefined variables.
            if (isset($frame['function'])) {
                $s .= "at <span style='font-weight: bold; color: darkred'>"
                    . $frame['callableName'] . "</span>(";
                if (isset($frame['args'])) {
                    $j = 0;
                    foreach ($frame['args'] as $name => $value) {
                        if ($j++ > 0) {
                            $s .= ", ";
                        }
                        if (is_string($value)) {
                            if (strlen($value) > 500) {
                                $value = substr($value, 0, 300) . ' ... ' . substr($value, -50);
                            }
                            // escape special chars except '
                            $s .= '"' . htmlspecialchars(str_replace("\\'", "'", addslashes($value))) . '"';
                        } else if (is_numeric($value)) {
                            $s .= $value;
                        } else if (is_bool($value)) {
                            $s .= $value ? 'true' : 'false';
                        } else if (is_array($value)) {
                            $s .= 'array[' . sizeof($value) . ']';
                        } else {
                            $s .= gettype($value);
                        }
                    }
                }
                $s .= ") ";
            }
            if (isset($frame['file'])) {
                $s .= "in ";
                $showCode = file_exists($frame['file']);
                if ($allowJavascript && $showCode) {
                    $s .= "<a target='_self' " .
                        "href='javascript:var s=document.getElementById(\"$codeId\").style;void(s.display=(s.display==\"block\"?\"none\":\"block\"))'>";
                }
                $s .= "<span style='color: navy'>" . dirname($frame['file']) .
                    "/<span style='font-weight: bold'>" . basename($frame['file']) .
                    "</span></span>";
                $s .= ":<span style='color: green; font-weight: bold'>" .
                    "$frame[line]</span>";
                if ($allowJavascript && $showCode) {
                    $s .= "</a>";
                }
                $s .= "\r\n";
            } else {
                $showCode = false;
            }

            if ($showCode && ($i == 0 || $allowJavascript)) {
                $s .= "<pre style='" . ($allowJavascript ? "display: none;" : "") .
                    "margin-top: 0' id='$codeId'><code>";
                $lines = explode("\n", file_get_contents($frame['file']));
                for ($l = max(1, $frame['line'] - 4);
                     $l <= min(sizeof($lines), $frame['line'] + 3);
                     $l++) {

                    if ($l == $frame['line']) {
                        $s .= '<span style="background-color: #ff9">';
                    }
                    $s .= "    $l: " . htmlspecialchars(rtrim($lines[$l - 1])) . "\r\n";
                    if ($l == $frame['line']) {
                        $s .= '</span>';
                    }
                }
                $s .= "</code></pre>";
            }

            $s .= "</li>\r\n";
        }
        $s .= "</ul>\r\n";

        return $s;
    }

    /**
     * @param  string  an error message in HTML format
     */
    private static function display($s, $isHtml = false)
    {
        if (!is_string($s)) {
            $isHtml = false;
            $s = var_export($s, true);
        }

        if ($isHtml) {
            $html = $s;
            $text = html_entity_decode(strip_tags($html));
        } else {
            $text = $s;
            $html = '<p>' . nl2br(htmlspecialchars($s)) . '</p>';
        }
        $text = preg_replace("/(^[\r\n]+|[\r\n]+$)/", '', $text);

        $logFormat = self::getLogFormat();
        switch ($logFormat) {
        case self::LOG_FORMAT_HTML:
            if (!headers_sent()) {
                header('Content-Type: text/html');
            }

            //escape if we were in the middle of a tag when the error occured
            print '<!-- "  \' -->';

            //escape other HTML tags
            print '</script></a></select>';
            print $html;
            break;

        case self::LOG_FORMAT_XML:
            $stripXmlProlog = false;
            if (!headers_sent()) {
                header('Content-Type: text/xml');
                // The < ?xml version="1.0"? > tag may only appear at the very
                // start of the output, so strip it if appears after this error
                if (!ob_get_length()) {
                    $stripXmlProlog = true;
                }
            }
            print "<!--\r\n";
            print str_replace('--', '- -', $text);
            print "\r\n-->\r\n";
            if ($stripXmlProlog) {
                ob_start(array(__CLASS__, 'stripXmlProlog'));
            }
            break;

        case self::LOG_FORMAT_JAVASCRIPT:
        case self::LOG_FORMAT_CSS:
            if (!headers_sent()) {
                if ($logFormat == self::LOG_FORMAT_JAVASCRIPT) {
                    header('Content-Type: text/javascript');
                } else {
                    header('Content-Type: text/css');
                }
            }
            print "\r\n/*\r\n";
            print str_replace('*/', '* /', $text);
            print "\r\n*/\r\n";
            break;

        case self::LOG_FORMAT_TEXT:
            if (!headers_sent()) {
                header('Content-Type: text/plain');
            }
            print "\r\n";
            print $text;
            print "\r\n";
            break;
        }
        flush();
    }

    /**
     * Output filter function - used by display() when log format is XML.
     * @param  string
     * @return string
     */
    public static function stripXmlProlog($s)
    {
        return preg_replace('/<\?xml.*\?>/Us', '', $s, 1);
    }

    /**
     * @param  string  a self::LOG_TYPE_* constant (ERROR, DB, MISC)
     * @param  string  a self::LOG_ACTION_* constant (IGNORE, INLINE, APPEND,
     *                 FILE, STDERR).
     */
    public static function setLogAction($type, $action = false)
    {
        if (!isset(self::$logAction[$type])) {
            trigger_error("Unknown log type: $type", E_USER_NOTICE);
            return;
        }
        self::$logAction[$type] = $action;
        if ($action == self::LOG_ACTION_APPEND) {
            self::registerShutdownFunction();
        }
    }

    /**
     * @return  string  a self::LOG_ACTION_* constant
     */
    public static function getLogActionByType($type)
    {
        if (!isset(self::$logAction[$type])) {
            trigger_error("Unknown log type: $type", E_USER_NOTICE);
            return self::LOG_ACTION_IGNORE;
        }
        return self::$logAction[$type];
    }

    /**
     * @param  string  a self::LOG_FORMAT_* constant
     */
    public static function setLogFormat($format)
    {
        self::$logFormat = $format;
    }

    /**
     * @return  string  a self::LOG_FORMAT_* constant
     */
    public static function getLogFormat()
    {
        if (!isset(self::$logFormat)) {
            // If it is SOAP, WebDAV or looks like XML
            if (isset($_SERVER['HTTP_SOAPACTION']) ||
                ob_get_level() > 0 && substr(ob_get_contents(), 0, 5) == '<?xml') {

                self::$logFormat = self::LOG_FORMAT_XML;

            } elseif (!isset($_SERVER['REMOTE_ADDR']) ||
                      !isset($_SERVER['HTTP_USER_AGENT']) ||
                      substr($_SERVER['HTTP_USER_AGENT'], 0, 5) == 'curl/' ||
                      substr($_SERVER['HTTP_USER_AGENT'], 0, 5) == 'Wget/' ||
                      isset($_SERVER['PEYTZ_CLI'])) {

                // We are not called from a webbrowser (e.g from the command line)
                self::$logFormat = self::LOG_FORMAT_TEXT;

            } else {
                $contentType = 'text/html';
                foreach (headers_list() as $header) {
                    if (preg_match('/^Content-Type\s*:\s*([^; ]+)/i', $header, $reg)) {
                        $contentType = strtolower($reg[1]);
                    }
                }

                if (strpos($contentType, 'text/html') !== false) {
                    self::$logFormat = self::LOG_FORMAT_HTML;
                } else if (strpos($contentType, 'xml') !== false) {
                    self::$logFormat = self::LOG_FORMAT_XML;
                } else if (strpos($contentType, 'javascript') !== false) {
                    self::$logFormat = self::LOG_FORMAT_JAVASCRIPT;
                } else if (strpos($contentType, 'css') !== false) {
                    self::$logFormat = self::LOG_FORMAT_CSS;
                } else {
                    self::$logFormat = self::LOG_FORMAT_TEXT;
                }
            }
        }
        return self::$logFormat;
    }

    /**
     * Logs the specified message.
     * The message should be formatted as a block-level HTML element. In order
     * to look nice after a simple HTML->text conversion (HTML-tags stripped
     * and entities decoded), \r\n should be inserted appropriately. The
     * message should not begin or end with a \r\n sequence.
     */
    public static function log($s, $type = self::LOG_TYPE_MISC, $isHtml = false)
    {
        if (!is_string($s)) {
            $isHtml = false;
            $s = var_export($s, true);
        }

        switch (self::getLogActionByType($type)) {
        case self::LOG_ACTION_APPEND:
            self::$logAppend[] = '<p>' .
                ($isHtml ? $s : nl2br(htmlspecialchars($s))) . '</p>';
            self::registerShutdownFunction();
            break;
        case self::LOG_ACTION_INLINE:
            self::display($s, $isHtml);
            break;
        case self::LOG_ACTION_FILE:
            $file = '/tmp/Pasta_Debug_' . $_SERVER['HTTP_HOST'];
            $text = $isHtml ? html_entity_decode(strip_tags($s)) : $s;
            $text = str_replace("\r", '', $text) . "\n\n";
            file_put_contents($file, $text, FILE_APPEND);
            chmod($file, 0777);
            break;
        case self::LOG_ACTION_STDERR:
            $text = $isHtml ? html_entity_decode(strip_tags($s)) : $s;
            $text = str_replace("\r", '', $text) . "\n\n";
            fputs(STDERR, $text);
            break;
        case self::LOG_ACTION_IGNORE:
            break;
        default:
            trigger_error("Unknown log action: " . self::getLogActionByType($type));
        }
    }

    /**
     * Registers flushLog as shutdown function.
     */
    public static function registerShutdownFunction()
    {
        static $hasRegistered = false;
        if (!$hasRegistered) {
            register_shutdown_function(array('Pasta_Debug', 'flushLog'));
            $hasRegistered = true;
        }
    }

    /**
     * Displays any log entries logged with self::LOG_ACTION_APPEND.
     * This method may be called several times, but should at least be called
     * sometime after last the call to Pasta_Debug::log(), e.g. from the
     * bottom of the auto_append_file.
     */
    public static function flushLog()
    {
        $s = '';
        if (sizeof(self::$logAppend) > 0) {
            $s .= "<ul>";
            foreach (self::$logAppend as $html) {
                $s .= "<li>" . $html . "</li>\r\n";
            }
            $s .= "</ul>\r\n";

            //empty the log
            self::$logAppend = array();
        }

        if (is_callable(array('Pasta_Debug_Profiler', 'hasBeenStarted')) &&
            Pasta_Debug_Profiler::hasBeenStarted()) {

            Pasta_Debug_Profiler::stop(true);
            $s .= Pasta_Debug_Profiler::getHtmlString();
        }

        if ($s) {
            $s = "<div style='text-align: left'>" .
                 "\r\n<h1 style='font-size: 100%; padding: .5em 0; " .
                 "border-top: 1px solid black; margin-top: 1em'>" .
                 "Pasta_Debug appended log</h1>\r\n\r\n" . $s . "</div>";

            self::display($s, true);
        }
    }

    /**
     * Returns a copy of the current value of the $GLOBALS array with a few
     * duplicate values removed.
     * @return  array
     */
    public static function getGlobals()
    {
        // omit these because they are duplicated in other variables
        $omit = array('GLOBALS', 'HTTP_COOKIE_VARS', 'HTTP_GET_VARS',
            'HTTP_POST_VARS', 'HTTP_POST_FILES', 'HTTP_SERVER_VARS',
            'HTTP_ENV_VARS');
        // omit variables beginning with _, except these
        $include = array('_GET', '_POST', '_COOKIE', '_SERVER');
        $globals = array();
        foreach ($GLOBALS as $name => $value) {
            if (!in_array($name, $omit) &&
                (substr($name, 0, 1) != '_' || in_array($name, $include))) {

                $globals[$name] = $value;
            }
        }

        return $globals;
    }
}

?>
