<?php
/**
 * cookie with timestamped and checksummed info in url form, e.g.
 * "id=234&name=Svend+Bent&_to=23543535&_cs=2o34lwerjwlrej"
 * Usage:
 * <code>
 *     $key = "ib";
 *     $parts = array("id" => 1234, "name" => "Svendemånd","firma" => "Peytz & Co.");
 *     $data = Pasta_Cookie::getCookieDataByArray($parts, $key, $ttl = 86400);
 *     setCookie("kagen", $data, time() + $ttl);
 *
 *     ...
 *
 *     $cookie = Pasta_Cookie::getByDataAndKey($_COOKIE['kagen'], $key);
 *     $id = $cookie->getPart("id");
 * </code>
 * @package  Pasta
 */
class Pasta_Cookie
{
    const VERSION = 2;
    // version 1 (before versions)
    const COOKIE_SEPARATOR = "&";
    const COOKIE_CHECKSUM = "_cs";
    const COOKIE_TIMEOUT = "_to"; // unix timestamp
    // version 2
    const COOKIE_VERSION = "_v";
    const COOKIE_ISSUETIME = "_it"; // unix timestamp
    const COOKIE_IP = "_ip"; // ip address

    /**
     * @param string
     * @return Pasta_Cookie
     */
    function __construct($data, $secretKey)
    {
        $this->cookieData = $data;
        $this->secretKey = $secretKey;
    }

    /**
     * @param string
     * @param string
     * @return Pasta_Cookie
     */
    static function getByDataAndKey($data, $secretKey)
    {
        $o = new self($data, $secretKey);
        return $o->validate() ? $o : false;
    }

    /** 
     * @return bool
     */
    function validate()
    {
        if (empty($this->cookieData) || empty($this->secretKey)) {
            return false;
        }

        $this->parts = self::getParts($this->cookieData);
        // we need a checksum
        if (!isset($this->parts[self::COOKIE_CHECKSUM])) {
            if (isset($_SERVER['PEYTZ_DEV'])) {
                trigger_error(self::COOKIE_CHECKSUM . " part is missing");
            }
            return false;
        }

        // generate checksum
        $data = "";
        foreach ($this->parts as $name => $value) {
            if ($name != self::COOKIE_CHECKSUM) {
                $data .= $name . $value;
            }
        }
        $checksum = md5($data . $this->secretKey);
        //var_dump($this->cookieData, $checksum);
        // checksums must match 
        if ($checksum !== $this->parts[self::COOKIE_CHECKSUM]) {
            return false;
        }

        // it is time
        $now = time();

        // timeout must be set and in the future
        if (!isset($this->parts[self::COOKIE_TIMEOUT])) {
            if (isset($_SERVER['PEYTZ_DEV'])) {
                trigger_error(self::COOKIE_TIMEOUT . " part is missing");
            }
            return false;
        }
        if ($now > $this->parts[self::COOKIE_TIMEOUT]) {
            return false;
        }

        
        // version extensions
        if (isset($this->parts[self::COOKIE_VERSION])) {
            switch ($this->parts[self::COOKIE_VERSION]) {
            case 4:
                // dont break :-)
            case 3:
                // dont break :-)
            case 2:
                // must be issued in the past :-)
                if (!isset($this->parts[self::COOKIE_ISSUETIME])) {
                    if (isset($_SERVER['PEYTZ_DEV'])) {
                        trigger_error(self::COOKIE_ISSUETIME . " part is missing (cookie version " . $this->parts[self::COOKIE_VERSION] . ")");
                    }
                    return false;
                }
                if ($now < $this->parts[self::COOKIE_ISSUETIME]) {
                    return false;
                }
                // must come from same ip
                if (!isset($this->parts[self::COOKIE_IP])) {
                    if (isset($_SERVER['PEYTZ_DEV'])) {
                        trigger_error(self::COOKIE_IP . " part is missing (cookie version " . $this->parts[self::COOKIE_VERSION] . ")");
                    }
                    return false;
                }
                /* seems unusable
                $ip = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
                if ($ip != $this->parts[self::COOKIE_IP]) {
                    // ip has changed
                    if ($now - $this->parts[self::COOKIE_IP] < 600) {
                        // less than 10 min. ago
                        trigger_error("IP address cookie mismatch (recent)");
                    } else {
                        // more
                        trigger_error("IP address cookie mismatch (old)");
                    }
                    // just notice for now, dont: return false;
                }
                */
                break;
            }
        }

        return true;
    }

    /** generate a cookie
     * @param array of parts
     * @param int time to live
     * @return string
     */
    static function getCookieDataByArray($parts, $key, $ttl = 84600)
    {
        $cookieData = "";
        $data = "";

        if (empty($key)) {
            if (isset($_SERVER['PEYTZ_DEV'])) {
                trigger_error("Empty secret key");
            }
            return false;
        }
        foreach ($parts as $name => $value) {
            if (!empty($name)) {
                $cookieData .= (empty($cookieData) ? "" : self::COOKIE_SEPARATOR) . urlencode($name) . '=' . urlencode($value);
                $data .= $name . $value;
            }
        }
        if (!$data) {
            if (isset($_SERVER['PEYTZ_DEV'])) {
                trigger_error("Empty cookie parts");
            }
            return false;
        }
        // set version
        $version = self::VERSION;
        $cookieData .= self::COOKIE_SEPARATOR . self::COOKIE_VERSION . "=" . $version;
        $data .= self::COOKIE_VERSION . $version;

        // set timeout
        $timeout = time() + $ttl;
        $cookieData .= self::COOKIE_SEPARATOR . self::COOKIE_TIMEOUT . "=" . $timeout;
        $data .= self::COOKIE_TIMEOUT . $timeout;

        // set issue time
        $issuetime = time();
        $cookieData .= self::COOKIE_SEPARATOR . self::COOKIE_ISSUETIME . "=" . $issuetime;
        $data .= self::COOKIE_ISSUETIME . $issuetime;

        // set ip address
        $ip = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR'])); // unsigned int
        $cookieData .= self::COOKIE_SEPARATOR . self::COOKIE_IP . "=" . $ip;
        $data .= self::COOKIE_IP . $ip;

        // append checksum
        $checksum = md5($data . $key);
        $cookieData .= self::COOKIE_SEPARATOR . self::COOKIE_CHECKSUM . "=" . $checksum;

        // try to validate
        $cookie = self::getByDataAndKey($cookieData, $key);

        return $cookie ? $cookieData : false;
    }

    /**
     * @param string
     * @return array
     */
    static function getParts($cookie)
    {
        // split to array
        parse_str($cookie, $parts);
        return $parts;
    }

    /** get a cookie part
     * @param string name
     * @return string value
     */
    function getPart($name)
    {
        return isset($this->parts[$name]) ? $this->parts[$name] : false;
    }
}
?>
