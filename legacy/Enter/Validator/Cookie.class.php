<?php

/**
 * @package Enter
 * @subpackage Validator
 */
class Enter_Validator_Cookie extends Enter_Validator
{
    private $cookieName;
    private $domain = '';
    private $key;
    private $cookieRememberTtl = 2678400; // 31 days is default
    private $sslOnly = false;

    /**
     * @param  string
     */
    protected function __construct($uri)
    {
        $parts = parse_url($uri);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        if (!isset($query['key'])) {
            trigger_error('key not specified in cookie:// URI');
            return;
        }
        if (isset($query['cookieRememberTtl'])) {
            $this->cookieRememberTtl = $query['cookieRememberTtl'];
        }
        if (isset($query['sslOnly'])) {
            $this->sslOnly = (bool) $query['sslOnly'];
        }
        if (isset($query['domain'])) {
            $this->domain = $query['domain'];
            if (!preg_match('@' . str_replace('.', '\.', $this->domain) . '$@',
                    $_SERVER['HTTP_HOST'])) {
                trigger_error('Domain mismatch');
                $this->domain = '';
            }
        }
        if (isset($query['name'])) {
            $this->cookieName = $query['name'];
        } else {
            // Include domain in cookie name to prevent e.g. cookies set on
            // "peytz.dk" for VIP on prod to interfere with cookies set on
            // use "dev.peytz.dk" for VIP on dev
            $this->cookieName = 'enterValidatorCookie';
            if ($this->domain) {
                $this->cookieName .= '_' . strtr($this->domain, '.', '_');
            }
        }
        $this->key = $query['key'];
    }

    /**
     * @return  string
     */
    public function getCookieName()
    {
        return $this->cookieName;
    }

    /**
     * Extract info and find user
     * @return Enter_Validator_User
     */
    public function getUser()
    {
        Pasta_Http::setNoCacheHeaders();
        $user = null;
        if (isset($_REQUEST[ENTER_VALIDATOR_LOGOUT])) {
            $this->unsetCookie();
        } elseif (isset($_COOKIE[$this->cookieName])) {
            $cookie = Pasta_Cookie::getByDataAndKey($_COOKIE[$this->cookieName],
                                                    $this->key);
            if ($cookie) {
                // Call Foo_User::getByCookieArray($cookie->parts)
                $method = array(Enter_Validator::getUserClassName(), 'getByCookieArray');
                $user = call_user_func($method, $cookie->parts);
            }
        }
        return $user;
    }

    /** Even more info
     * @param string class name of user to create
     * @return Enter_Validator_Status
     */
    public function getStatus($userClassName)
    {
        $status = new Enter_Validator_Status($this);
        if ($user = $this->getUser()) {
            $status->code = Enter_Validator_Status::LOGIN_OK;
            $status->user = $user;
        }
        return $status;
    }

    /** set the cookie
     * @param  Enter_Validator_User
     * @param  bool  when true, maximum session length is $this->cookieRememberTtl
     *               (default is 31 days), otherwise it is 12 hours
     * @return string
     */
    public function getCookieValue(Enter_Validator_User $user, $remember = false)
    {
        if (!$user->getExists()) {
            trigger_error("user does not exist");
            return false;
        }
        // Session length: 31 days (as default), otherwise 12 hours
        $data = Pasta_Cookie::getCookieDataByArray($user->getCookieArray(),
            $this->key, $remember ? $this->cookieRememberTtl : 43200);
        return $data;
    }

    /** set the cookie
     * @param  Enter_Validator_User
     * @param  bool  when true, maximum session length is $this->cookieRememberTtl
     *               (default is 31 days), otherwise it is 12 hours
     * @return bool
     */
    public function setCookie(Enter_Validator_User $user, $remember = false)
    {
        $data = $this->getCookieValue($user, $remember);
        $_COOKIE[$this->cookieName] = $data; 
        header('X-Enter-Validator-Cookie: login');
        setcookie($this->cookieName,
                  $data,
                  $remember ? time() + $this->cookieRememberTtl : 0,
                  '/',
                  $this->domain,
                  $this->sslOnly,
                  true);
    }

    /**
     * Deletes the cookie and ends the current session.
     */
    public function unsetCookie()
    {
        unset($_COOKIE[$this->cookieName]);
        header('X-Enter-Validator-Cookie: logout');
        setcookie($this->cookieName,
                  '',
                  time() - 86400,
                  '/',
                  $this->domain,
                  $this->sslOnly,
                  true);
    }
}

?>
