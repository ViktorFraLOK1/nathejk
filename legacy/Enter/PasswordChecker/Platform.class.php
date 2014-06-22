<?php

/**
 * @package Enter
 * @subpackage PasswordChecker
 */
class Enter_PasswordChecker_Platform extends Enter_PasswordChecker
{
    private $host;

    /**
     * @param  string  the password checker URI
     */
    protected function __construct($uri)
    {
        $parts = parse_url($uri);
        if (!isset($parts['host'])) {
            trigger_error('Host not specified', E_USER_WARNING);
            return;
        }
        $this->host = $parts['host'];
    }

    public function authenticate($username, $password)
    {
        $url = "http://" . $this->host . "/misc/authenticate.php" .
            "?username=" . rawurlencode($username) . 
            "&password=" . rawurlencode($password);
        $response = file_get_contents($url);

        return substr($response, 0, 1) == '+';
    }
}

?>
