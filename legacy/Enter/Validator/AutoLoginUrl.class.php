<?php

/**
 * Validate user using credentials in the query string.
 * @package Enter
 * @subpackage Validator
 */
class Enter_Validator_AutoLoginUrl extends Enter_Validator
{
    private $hosts = false;
    private $parameterName = 'enterAutologin';
    private $redirect = true;
    private $key;

    protected function __construct($uri)
    {
        $parts = parse_url($uri);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        if (!isset($query['key'])) {
            trigger_error('key not specified in autologin:// URI');
            return;
        }
        $this->key = $query['key'];
        if (!isset($query['hosts'])) {
            trigger_error('hosts not specified in autologin:// URI');
            return;
        }
        $this->hosts = $query['hosts'] == '*' ? false : explode(',', $query['hosts']);

        if (isset($query['parameterName'])) {
            $this->parameterName = $query['parameterName'];
        }
        if (isset($query['redirect'])) {
            $this->redirect = (bool) $query['redirect'];
        }
    }

    /**
     * @return  Enter_Validator_User
     */
    public function getUser()
    {
        $status = $this->getStatus(Enter_Validator::getUserClassName());
        return $status->user;
    }

    /** Even more info
     * @param string class name of user to create
     * @return Enter_Validator_Status
     */
    public function getStatus($userClassName)
    {
        $status = new Enter_Validator_Status($this);
        if (isset($_REQUEST[$this->parameterName])) {
            $cookie = Pasta_Cookie::getByDataAndKey($_REQUEST[$this->parameterName],
                                                    $this->key);
            if ($cookie) {
                // Call Foo_User::getById($cookie->parts['id'])
                $method = array(Enter_Validator::getUserClassName(), 'getById');
                $status->user = call_user_func($method, $cookie->parts['id']);
            }
            if ($status->user) {
                $status->code = Enter_Validator_Status::LOGIN_OK;
                $status->setCookie = true;
                if ($this->redirect) {
                    $url = Pasta_Http::absoluteURI();

                    // SCRIPT_URI can be wrong and there for Pear_HTTP will attach a wrong port this removes the wrong
                    // port.
                    if (preg_match('/^(http\:\/\/.*)(\:443)(\/.*)$/', $url, $matches)) {
                        $url = $matches[1] . $matches[3];
                    }

                    // Strip autologin parameter from URL                    
                    $status->redirectUrl = preg_replace('@.' . $this->parameterName . '=[^&]+@', '', $url);
                }
            } else {
                $status->code = Enter_Validator_Status::LOGIN_FAILED;
                $status->codeText = 'Unknown user';
            }
        }

        return $status;
    }

    /**
     * Returns an URL that automagically logs in this user. Credentials are
     * added to the query string of the specified URL.
     * @param  string  URL
     * @param  int     TTL in seconds
     */
    public function getAutoLoginUrl(Enter_Validator_User $user, $url, $ttl = 86400)
    {
        $urlParts = parse_url($url);
        if ($this->hosts === false || (isset($urlParts['host']) 
                && in_array($urlParts['host'], $this->hosts))) {

            $data = Pasta_Cookie::getCookieDataByArray(
                array('id' => $user->getId()),
                $this->key,
                $ttl > 0 ? $ttl : 86400);
            $url .= (isset($urlParts['query']) ? '&' : '?') .
                $this->parameterName . '=' . urlencode($data);
        }
        
        return $url;
    }
}

?>
