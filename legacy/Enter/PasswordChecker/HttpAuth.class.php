<?php

/**
 * Checks passwords against a URL protected with HTTP Basic authentication.
 *
 * Example URI:
 * httpauth://na?url=http%3A//example.org/foo.php%3Fbar%3D1
 *
 * @package Enter
 * @subpackage PasswordChecker
 */
class Enter_PasswordChecker_HttpAuth extends Enter_PasswordChecker
{
    private $url;

    /**
     * @param  string  the password checker URI
     */
    protected function __construct($uri)
    {
        $parts = parse_url($uri);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        if (!isset($query['url'])) {
            trigger_error('"url" not specified', E_USER_WARNING);
            return;
        }
        $this->url = $query['url'];
    }

    /**
     * @param   string
     * @param   string
     * @return  bool
     */
    public function authenticate($username, $password)
    {
        // Add username+password to URL: http://john:secret@example.com/foo.bar
        $i = strpos($this->url, '://') + 3;
        $url = substr($this->url, 0, $i) . rawurlencode($username) . ':' .
            rawurlencode($password) . '@' . substr($this->url, $i);

        // Try without username+password to make sure that the URL is actually
        // password protected - otherwise everybody would be let.
        $remoteFile = Pasta_RemoteFile::getByUrl($this->url);
        $remoteFile->setMinValidity(60);
        $ok = $remoteFile->fetchData();
        if ($ok) {
            trigger_error('URL is accessible even without password',
                          E_USER_WARNING);
            return false;
        }

        $remoteFile = new Pasta_RemoteFile();
        $remoteFile->setUrl($url);
        $ok = $remoteFile->fetchData();
        return (bool) $ok;
    }
}

?>
