<?php

/**
 * @package Enter
 * @subpackage PasswordChecker
 */
class Enter_PasswordChecker_Imaps extends Enter_PasswordChecker
{
    private $host;
    private $port = 993;
    private $usernameDomain = ''; // append to username 

    /**
     * @param  string  the password checker URI
     */
    protected function __construct($uri)
    {
        $parts = parse_url($uri);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        if (!isset($parts['host'])) {
            trigger_error('Host not specified', E_USER_WARNING);
            return;
        }
        $this->host = $parts['host'];
        if (isset($parts['port'])) {
            $this->port = intval($parts['port']);
        }
        if (isset($query['usernameDomain'])) {
            $this->usernameDomain = $query['usernameDomain'];
        }
    }

    public function authenticate($username, $password)
    {
        $mailbox = "{{$this->host}:{$this->port}/imap/ssl/readonly}INBOX";
        $login = $username . $this->usernameDomain;
        if ($mbox = @imap_open ($mailbox, $login, $password)) {
            imap_close($mbox);
            return true;
        }
        return false;
    }
}

?>
