<?php
/**
 * HTTP stuff
 * @package Pasta
 */

require_once('HTTP.php');
class Pasta_Http extends HTTP
{
    /**
     * Indicate an infinite TTL in self::setProxyLifetime($ttl).
     */
    const TTL_FOREVER = 630720000; // 20 years

    /**
     * @var HTTP response code
     */
    private static $statusCode = 200;

    /**
     * Returns the HTTP response code set using Pasta_Http::sendResponse().
     * If the status code is set using a direct call to header(), it will not
     * be detected.
     * @return int  a HTTP response code
     */
    public static function getStatusCode()
    {
        return self::$statusCode;
    }

    /**
     * @return array  associate header array
     */
    public static function getResponseHeaders()
    {
        // FIXME: Due to a bug in PHP 5.2.0, headers set using header() are not
        // returned by apache_response_headers() before headers have been sent.
        if (function_exists('apache_response_headers') && headers_sent()) {
            return array_change_key_case(apache_response_headers(), CASE_LOWER);
        } else {
            $headers = array();
            foreach (headers_list() as $header) {
                list($name, $value) = explode(':', $header, 2);
                $name  = strtolower($name);
                $value = trim($value);
                if (isset($headers[$name])) {
                    $headers[$name] .= ', ' . $value;
                } else {
                    $headers[$name] = $value;
                }
            }
            return $headers;
        }
    }

    /** another redirector
     * @param string
     * @param bool
     * @return bool (if not exiting);
     */
    public static function locationHeader($url, $keepGoing = false)
    {
        require_once 'Net/URL2.php';
        $url = Net_URL2::getRequested()->resolve($url)->getURL();

        header('Location: '. $url);
        header('X-Peytz-Going-on: '. ($keepGoing ? "yep" : "nope"));

        if (isset($_SERVER['PEYTZ_DEV'])) {
            $trace = debug_backtrace();
            if (isset($trace[1]['file'], $trace[1]['line'])) {
                header('X-Pasta-HTTP-called-from: '.
                       $trace[1]['file'] . ':' . $trace[1]['line']);
            }
        }

        $url = htmlspecialchars($url);
        $html = "The document has moved to <a href=\"$url\">$url</a>.";
        if ($keepGoing) {
            // Make sure location header is flushed to browser
            $html .= str_pad(' ', 4096);
            // Tell browser to not send more requests on the current connection.
            // Otherwise the next request may be sent to the same Apache child,
            // and that child is usually buzy finishing the current request.
            header('Connection: close');
        }
        self::sendResponse(302, 'Found', $html);

        if (!$keepGoing) {
            exit;
        }

        // Make sure location header is flushed to browser
        ignore_user_abort(true);
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }

    /**
     * Sends a Location header and terminates the script.
     * @param  string  the URL to redirect to
     */
    public static function exitWithRedirect($url)
    {
        self::locationHeader($url, false);
    }

    /**
     * Sends a Location header without terminating the script.
     * @param  string  the URL to redirect to
     */
    public static function redirectWithoutExit($url)
    {
        self::locationHeader($url, true);
    }

    /**
     * Check browsers if-modified-since header and exit
     * with a 304 if nothing new is here.
     *
     * @param int UNIX timestamp
     */
    public static function handleLastModified($unixtime)
    {
        if (headers_sent()) {
            // It's too late do anything
            return;
        }
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSince = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            if ($ifModifiedSince >= $unixtime) {
                self::$statusCode = 304;
                header($_SERVER['SERVER_PROTOCOL'] . " 304 Not Modified");
                exit;
            }
        }
        header("Last-Modified: " . gmdate(DATE_RFC1123, $unixtime));
    }

    /**
     * Sends a 403 page and terminates the script.
     * @param  string  an HTML message to display on the error page.
     */
    public static function exitWithForbidden($html = false)
    {
        if (!$html) {
            $html = "You don't have permission to access the requested object.";
        }
        self::sendResponse(403, 'Forbidden', $html);
        exit;
    }

    /**
     * Sends a 404 page and terminates the script.
     * @param  string  an HTML message to display on the error page.
     */
    public static function exitWithNotFound($html = false)
    {
        if (!$html) {
            $html = htmlspecialchars(
                "The requested URL was not found on this server.");
        }
        self::sendResponse(404, 'Not Found', $html);
        exit;
    }

    /**
     * Sends a 500 page and terminates the script.
     * @param  string  an HTML message to display on the error page.
     */
    public static function exitWithInternalServerError($html = false)
    {
        if (!$html) {
            $html = "The server encountered an internal error and was
                unable to complete your request.";
        }
        self::sendResponse(500, 'Internal Server Error', $html);
        exit;
    }

    /**
     * Set the allowed lifetime in a proxy. Request is assumed not to be
     * user-specific, password protected etc..
     * @param  int  TTL in seconds, or self::TTL_FOREVER
     */
    public static function setProxyLifetime($ttl)
    {
        header('Cache-Control: public, s-maxage=' . $ttl);
        $headers = self::getResponseHeaders();
        if (isset($headers['expires']) || $ttl == self::TTL_FOREVER) {
            // For small TTL values, we should rather omit Expires due to this
            // bug in Squid, but PHP doesn't allow unsetting headers:
            // http://www.squid-cache.org/bugs/show_bug.cgi?id=7
            header('Expires: ' . gmdate(DATE_RFC1123, time() + $ttl));
        }
        if (isset($headers['vary'])) {
            // PHP doesn't allow unsetting headers, so replace header with
            // something that doesn't vary. Unfortunately this will prevent
            // IE from caching.
            // We block this header in Squid for certain URL patterns.
            header('Vary: Host');
        }
    }

    /**
     * Guesstimate a proxy lifetime based on the time of last modification.
     * @param  int  a Unix timestamp
     */
    public static function setProxyLifetimeByLastModifyUts($uts)
    {
        // Cache between 10 seconds (when age is < 10 minutes) and 5 minutes
        // (when age is > 5 hours)
        $seconds = max(10, min(300, round((time() - $uts) / 60)));
        self::setProxyLifetime($seconds);
    }

    /**
     * Set the standard bunch of no-cache headers
     */
    public static function setNoCacheHeaders()
    {
        if (headers_sent()) {
            // It's too late do anything
            return;
        }
        // Vary: Cookie causes problem when opening generated Excel files
        // directly in Excel with IE6 (Excel complains that temporary file does
        // not exist) and IE7 (when clicking on a link to a Word document in
        // Outlook 2007, the document doesn't open, but IE7 says "navigation
        // cancelled - bug 2982 comment 7).
        if (!isset($_SERVER['HTTP_USER_AGENT']) ||
            !preg_match('/MSIE [67]/', $_SERVER['HTTP_USER_AGENT'])) {

            header('Vary: Cookie', false);
        }
        header('Expires: Mon, 26 Jul 1970 05:00:00 GMT');
        header('Cache-Control: private, must-revalidate');
    }

    /**
     * Sends the specified HTTP status line and a small HTML page.
     * @param  int     an HTTP status code (1xx-5xx)
     * @param  string  the HTTP reason phrase (short status text)
     * @param  string  HTML string to display in body
     */
    private static function sendResponse($statusCode, $reason, $html)
    {
        self::$statusCode = $statusCode;
        header($_SERVER['SERVER_PROTOCOL'] . " $statusCode $reason");
        header('Content-Type: text/html');

        if (!$html) {
            $html = htmlspecialchars($reason);
        }

        $output = <<<E
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>$statusCode $reason</title>
</head><body>
<h1>$reason</h1>
<p>$html</p>
</body></html>
E;

        // Necessary for IE to redirect immediately when $keepGoing == true
        header('Content-Length: ' . strlen($output));
        print $output;
    }
}
?>
