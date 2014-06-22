<?php

/**
 * @package Enter
 * @subpackage PasswordChecker
 * @abstract
 */
abstract class Enter_PasswordChecker
{
    /**
     * @var  array  array of URI strings
     */
    private static $passwordCheckerUris;

    /**
     * @param  string  the password checker URI
     */
    protected function __construct($uri)
    {
    }

    /**
     * @param   string
     * @return  Enter_PasswordChecker
     */
    public static function getByUri($uri)
    {
        $parts = parse_url($uri);
        switch (strtolower($parts['scheme'])) {
            case 'enter';
                $className = 'Enter_PasswordChecker_Enter';
                break;
            case 'pop3';
                $className = 'Enter_PasswordChecker_Pop3';
                break;
            case 'imaps';
                $className = 'Enter_PasswordChecker_Imaps';
                break;
            case 'httpauth';
                $className = 'Enter_PasswordChecker_HttpAuth';
                break;
            case 'platform';
                $className = 'Enter_PasswordChecker_Platform';
                break;
            case 'external': // hook for external validator
                $className = $parts['host'];
                break;
            default:
                trigger_error('Unknown scheme ' . $parts['scheme'],
                    E_USER_WARNING);
                 return null;
        }
        if (!class_exists($className) || !is_subclass_of($className, 'Enter_PasswordChecker')) {
            trigger_error("Invalid password checker class: $className",
                          E_USER_WARNING);
            return null;
        }
        $auth = new $className($uri);

        return $auth;
    }

    /**
     * Checks the specified username and password against the current
     * password checkers. Both arguments are trimmed for whitespace before
     * being sent to the password checker.
     * @param   string
     * @param   string
     * @return  bool
     */
    public static function checkPassword($username, $password) 
    {
        $passwordCheckerUris = self::getPasswordCheckerUris();
        if (!$passwordCheckerUris) {
            trigger_error('No password checkers configured');
        }
        foreach ($passwordCheckerUris as $uri) {
            $auth = self::getByUri($uri);
            if ($auth->authenticate($username, $password)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return  array  an array of URI strings
     */
    public static function getPasswordCheckerUris()
    {
        if (!isset(self::$passwordCheckerUris)) {
            $s = Enter_Config::getValueByName('passwordCheckers');
            self::$passwordCheckerUris = $s ? explode(',', $s) : array();
        }
        return self::$passwordCheckerUris;
    }

    /**
     * @param  array  an array of URI strings
     */
    public static function setPasswordCheckerUris($uris)
    {
        if (!is_array($uris)) {
            trigger_error('Argument is not an array', E_USER_WARNING);
            return;
        }
        self::$passwordCheckerUris = $uris;
    }

    /**
     * Checks the specified username and password. Both arguments are assumed
     * to be trimmed for whitespace in advance.
     * @param  string
     * @param  string
     * @return  bool or (temporary) error string
     */
    public abstract function authenticate($username, $password);
}

?>
