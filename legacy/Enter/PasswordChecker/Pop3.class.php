<?php

/**
 * @package Enter
 * @subpackage PasswordChecker
 */
class Enter_PasswordChecker_Pop3 extends Enter_PasswordChecker
{
    private $host;
    private $port = 110;
    private $tls = false;
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
        if (isset($query['tls'])) {
            $this->tls = (bool) $query['tls'];
        }
        if (isset($query['usernameDomain'])) {
            $this->usernameDomain = $query['usernameDomain'];
        }
    }

    public function authenticate($username, $password)
    {
        $fp = fsockopen($this->host, $this->port);
        if (!$fp) {
            return false;
        }

        $error = false;
        $line = fgets($fp);
        if ($line[0] != '+') {
            trigger_error('Unexpected reply: '. $line, E_USER_WARNING);
            $error = true;
        }

        if ($this->tls) {
            fputs($fp, "STLS\r\n");
            fflush($fp);
            $line = fgets($fp);
            if ($line[0] != '+') {
                trigger_error('Unexpected reply: '. $line, E_USER_WARNING);
                $error = true;
            }
            stream_socket_enable_crypto($fp, true,
                                        STREAM_CRYPTO_METHOD_TLS_CLIENT);
        }

        if (!$error) {
            fputs($fp, "USER $username{$this->usernameDomain}\r\n");
            fflush($fp);
            $line = fgets($fp);
            if ($line[0] != '+') {
                // A negative reply may indicate an unknown username or that
                // TLS is required
                if (strpos($line, 'TLS')) {
                    trigger_error('Server said: '. $line, E_USER_WARNING);
                }
                $error = true;
            }
        }

        $valid = false;
        if (!$error) {
            fputs($fp, "PASS $password\r\n");
            fflush($fp);
            $line = fgets($fp);
            $valid = ($line[0] == '+');
        }

        fputs($fp, "QUIT\r\n");
        fclose($fp);

        return $valid;
    }
}

?>
