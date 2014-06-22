<?php

/**
 * Validate user using HTTP Basic authentication.
 * @package Enter
 * @subpackage Validator
 */
class Enter_Validator_HttpAuth extends Enter_Validator
{
    private $force = false;

    /**
     * @param  string
     */
    protected function __construct($uri)
    {
        global $CUSTOMER;
        $parts = parse_url($uri);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        $this->force = isset($query['force']) && $query['force'];
        $this->realm = isset($query['realm']) ? $query['realm'] :
            (isset($CUSTOMER) && $CUSTOMER ? $CUSTOMER->getName() : 'system');
    }

    /**
     * @return  Enter_Validator_User
     */
    public function getUser()
    {
        $user = null;
        if (isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) &&
            $_SERVER['PHP_AUTH_USER']) {

            // The Gnome WebDAV client urlencodes the username (e.g. if it
            // contains backslash)
            $username = urldecode($_SERVER['PHP_AUTH_USER']);
            $password = $_SERVER['PHP_AUTH_PW'];
            if (Enter_PasswordChecker::checkPassword($username, $password)) {
                // Call Foo_User::getByUsername($username)
                $method = array(Enter_Validator::getUserClassName(), 'getByUsername');
                $user = call_user_func($method, $username);
            }
        }
        if (!$user && $this->force) {
            self::requestAuthorization($this->realm);
        }
        return $user;
    }

    /**
     * Sends a 401 Unauthorized header.
     */
    public static function requestAuthorization($realm)
    {
        header('HTTP/1.0 401 Unauthorized');
        // Weird characters confuse certain clients
        $realm = preg_replace('@[^a-z0-9 -]@i', '', $realm);
        header('WWW-Authenticate: Basic realm="' . $realm . '"');
        print "401 Unauthorized\n";
        exit;
    }
}

?>
