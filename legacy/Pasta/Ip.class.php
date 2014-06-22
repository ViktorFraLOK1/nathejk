<?php
/** 
 * IP address utility functions.
 * @package Pasta
 */

class Pasta_Ip
{
    private $_ipA, $_ipB, $_ipC, $_ipD;
    private $_isValid = false;

    /**
     *
     */
    public function __construct($ipAddress)
    {
        // explode it
        if (preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)$/', $ipAddress, $parts)) {
            $this->_ipA = $parts[1];
            $this->_ipB = $parts[2];
            $this->_ipC = $parts[3];
            $this->_ipD = $parts[4];
            $this->_isValid = ip2long($this->getAddress()) !== false;
        }
    }

    /**
     * Validity
     * @return bool
     */
    public function isValid()
    {
        return $this->_isValid;
    }

    /** 
     * Factory to find the public IP by looking at server variables.
     * @return Pasta_Ip 
     */
    public static function getByRemoteAddress()
    {
        $pastaIp = new self($_SERVER['REMOTE_ADDR']);
        if (!$pastaIp->isPublic()) {
            // squid uses HTTP_X_FORWARDED_FOR
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                // it's a comma separated list of ip's. Use first that is public.
                foreach (array_reverse(explode(",", $_SERVER['HTTP_X_FORWARDED_FOR'])) as $forward) {
                    $forward = new Pasta_Ip(trim($forward));
                    if ($forward->isPublic()) {
                        return $forward;
                    }
                }
            }
        }

        // give up
        return $pastaIp;
    }

    /**
     * Return the IPv4 address
     * @return string
     */
    public function getAddress()
    {
        return $this->_ipA
            . '.' . $this->_ipB
            . '.' . $this->_ipC
            . '.' . $this->_ipD;
    }

    /** Return whether an IP address is in a public scope.
     * @return bool
     */
    public function isPublic()
    {
        // 127.0.0.0   - 127.255.255.255
        if ($this->_ipA == 127) {
            return false;
        }
        // 10.0.0.0    - 10.255.255.255
        if ($this->_ipA== 10) {
            return false;
        }
        // 172.16.0.0  - 172.31.255.255
        if ($this->_ipA == 172 && $this->_ipB >= 16 && $this->_ipB <= 31) {
            return false;
        }
        // 192.168.0.0 - 192.168.255.255
        if ($this->_ipA == 192 && $this->_ipB == 168) {
            return false;
        }
        // 169.254.0.0 - 169.254.255.255
        if ($this->_ipA == 169 && $this->_ipB == 254) {
            return false;
        }

        // all is well
        return true;
    }

}
?>
