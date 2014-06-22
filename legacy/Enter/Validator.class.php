<?php

define('ENTER_VALIDATOR_FORM_USERNAME', 'enterValidatorFormUsername');
define('ENTER_VALIDATOR_FORM_PASSWORD', 'enterValidatorFormPassword');
define('ENTER_VALIDATOR_FORM_REMEMBER', 'enterValidatorFormRemember');
define('ENTER_VALIDATOR_LOGOUT',        'enterValidatorLogout');

/**
 * A validator takes info to identify a user and validates it
 * @package Enter
 * @subpackage Validator
 */
abstract class Enter_Validator
{
    /**
     * @var  string  a class name
     */
    private static $userClassName;

    /**
     * @var  array  array of URI strings
     */
    private static $validatorUris;

    /**
     * @var  Enter_Validator_Status
     */
    private static $validatorStatus = null;

    /**
     * @var  string
     */
    private $errorString = false;

    /**
     * Validators should be instantiated using getByUri().
     * @param  string  the URI corresponding to this instance
     */
    protected function __construct($uri)
    {
    }

    /**
     * Returns the name of current user class.
     * @return  string  a class name
     */
    public static function getUserClassName()
    {
        if (!isset(self::$userClassName)) {
            self::$userClassName = Enter_Config::getValueByName('userClass');
        }
        return self::$userClassName;
    }

    /**
     * Sets the name of the current user class.
     * @param string
     */
    public static function setUserClassName($className)
    {
        self::$userClassName = $className;
    }

    /**
     * @return  array  an array of URI strings
     */
    public static function getValidatorUris()
    {
        if (!isset(self::$validatorUris)) {
            $s = Enter_Config::getValueByName('validators');
            self::$validatorUris = $s ? explode(',', $s) : array();
        }
        return self::$validatorUris;
    }

    /**
     * @param  array  an array of URI strings
     */
    public static function setValidatorUris($uris)
    {
        if (!is_array($uris)) {
            trigger_error('Argument is not an array', E_USER_WARNING);
            return;
        }
        self::$validatorUris = $uris;
    }

    /**
     * Last non-trivial status
     * @return  Enter_Validator_Status
     */
    public static function getValidatorStatus()
    {
        return self::$validatorStatus;
    }

    /**
     * @return  array  an array of URI strings
     */
    public static function getPasswordCheckerUris()
    {
        return Enter_PasswordChecker::getPasswordCheckerUris();
    }

    /**
     * @param  array  an array of URI strings
     */
    public static function setPasswordCheckerUris($uris)
    {
        return Enter_PasswordChecker::setPasswordCheckerUris($uris);
    }

    /**
     * Returns the first validator of the specified scheme.
     * @param   string  the scheme part of the URI, e.g. "cookie"
     * @return  Enter_Validator
     */
    public static function getByScheme($scheme)
    {
        foreach (self::getValidatorUris() as $uri) {
            if (strpos($uri, $scheme . '://') === 0) {
                return self::getByUri($uri);
            }
        }
        return false;
    }

    /**
     * @return  Enter_Validator_User
     */
    public static function getCurrentUser()
    {
        $user = null;
        $userClassName = self::getUserClassName();

        $validatorUris = self::getValidatorUris();
        if (!$validatorUris) {
            trigger_error('No validators configured');
            return null;
        }
        $setCookie = false;
        $redirectUrl = false;
        // try validators until user is found...
        foreach ($validatorUris as $validatorUri) {
            if ($validator = self::getByUri($validatorUri)) {
                if (method_exists($validator, 'getStatus')) {
                    $status = $validator->getStatus($userClassName);
                    if ($status->code === Enter_Validator_Status::LOGIN_OK) {
                        $user = $status->user;
                        $setCookie = $status->setCookie;
                        $redirectUrl = $status->redirectUrl ? $status->redirectUrl : null;
                    }
                    if ($status->code !== Enter_Validator_Status::LOGIN_NONE) {
                        // save status and get out
                        self::$validatorStatus = $status;
                        if ($user) {
                            $user->setValidatorUri($validatorUri);
                        }
                        break; // from foreach()
                    }
                } else {
                    // deprecated old way
                    $user = $validator->getUser();
                    if ($user) {
                        break; // from foreach()
                    }
                }
            } else {
                trigger_error('Unknown validator: ' . $validatorUri,
                              E_USER_WARNING);
            }
        }

        if ($setCookie) {
            $cv = self::getByScheme('cookie');
            if (!$cv) {
                trigger_error('Cookie validator not found');
            } elseif ($user) {
                $cv->setCookie($user, isset($_REQUEST[ENTER_VALIDATOR_FORM_REMEMBER]));
            } else {
                $cv->unsetCookie();
            }
            if ($redirectUrl) {
                Pasta_Http::exitWithRedirect($redirectUrl);
            }
        }
        return $user;
    }

    /**
     * @param   string
     * @return  Enter_Validator
     */
    static function getByUri($uri)
    {
        $parts = parse_url($uri);
        switch (strtolower($parts['scheme'])) {
            case 'cookie';
                $className = 'Enter_Validator_Cookie';
                break;
            case 'facebook';
                $className = 'Enter_Validator_Facebook';
                break;
            case 'tv2';
                $className = 'Enter_Validator_Tv2';
                break;
            case 'form';
                $className = 'Enter_Validator_Form';
                break;
            case 'ssl';
                $className = 'Enter_Validator_Ssl';
                break;
            case 'ip';
                $className = 'Enter_Validator_Ip';
                break;
            case 'httpauth';
                $className = 'Enter_Validator_HttpAuth';
                break;
            case 'autologinurl';
                $className = 'Enter_Validator_AutoLoginUrl';
                break;
            case 'external';
                $className = isset($parts['host']) ? $parts['host'] : false;
                break;
            default:
                trigger_error('Unknown scheme ' . $parts['scheme'],
                              E_USER_WARNING);
                return null;
        }

        if (!is_subclass_of($className, 'Enter_Validator')) {
            trigger_error('Invalid validator class: ' . $className,
                          E_USER_WARNING);
            return null;
        }

        $validator = new $className($uri);

        return $validator;
    }

    /**
     * Returns the current user determined by the current validator, or null if
     * no user was not found.
     * @return Enter_Validator_User
     * @deprecated Use getStatus
     */
    public function getUser()
    {
        if (isset($_SERVER['PEYTZ_DEV'])) {
            trigger_error('Deprecated: implement getStatus instead');
        }
        if (method_exists($this, 'getStatus')) {
            $status = $this->getStatus(self::getUserClassName());
            return $status->user;
        }
        return null;
    }

    /** Even more info
     * @param string class name of user to create
     * @return Enter_Validator_Status
     */
    //public abstract function getStatus($userClassName);
}

?>
