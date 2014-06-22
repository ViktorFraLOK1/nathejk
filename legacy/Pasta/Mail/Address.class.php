<?php
/** 
 * Handle email related stuff like hard bounce quarantines.
 *
 * Simply double the quarantine time each time defaulting to 1 day
 * To skip email on the list:
 *   if (!Pasta_Mail_Address::isEmailInQuarantine($email)) { go on as usual }
 * To report a hard bounce:
 *   Pasta_Mail_Address::handleEmailEvent($email, 
 *  Pasta_Mail_Address::EVENT_BOUNCE_HARD, $uts, 'ouch')
 * To report something good :-) :
 *   Pasta_Mail_Address::handleEmailEvent($email, 
 *  Pasta_Mail_Address::EVENT_CLICK, $uts, 'hurra')
 *
 * @package Pasta
 * @subpackage Mail
 */
class Pasta_Mail_Address extends Pasta_TableRow
{
    const QUARANTINE_MIN = 86400; // 1 day

    /* events */
    const EVENT_BOUNCE_HARD = 'bounced hard';
    const EVENT_IMAGE       = 'fetched image';
    const EVENT_CLICK       = 'clicked link';
    const EVENT_COMPLAINT   = 'complained';
    const EVENT_SEND        = 'tried to send to';

    // regexp to check email for idn expansion
    const IDN_EMAIL_REGEXP = '/^(.+)@(.*[^a-z0-9\.\-]+.*)$/i';

    /**
     * TableRow override.
     * Makes this class's data facility local.
     * @return  string
     */
    function getDbId()
    {
        return 'pasta_local';
    }

    /** factory
     * @param   int
     * @return  Pasta_Mail_Adddress
     */
    public static function getById($id)
    {
        return parent::getObjectById(__CLASS__, $id);
    }

    /**
     * factory -- creates entry if email is not known (and valid)
     * @param string
     * @return Pasta_Mail_Address
     */
    public static function getByEmail($email)
    {
        if (!$email || $email == self::NOLL) {
            return null;
        }
        $adrs = new self;
        $adrs->email = $email;
        $adr = $adrs->findOne();
        if (!$adr) {
            $adr = new self;
            $adr->email = $email;
            $adr->createUts = $_SERVER['REQUEST_TIME'];
            if (!$adr->save()) {
                return null;
            }
        }
        return $adr;
    }

    /** 
     * override to ensure email validity
     * @return bool
     */
    public function isValid()
    {
        if (!self::isEmailValid($this->email, $skipDns = true)) {
            $this->setErrorString('Malformed email');
            return false;
        }
        return parent::isValid();
    }

    /**
     * Wrap events
     * @param string event name
     * @param int    the time when the mail initiating the event was sent
     * @param string event details
     */
    public function handleEvent($eventName, $uts, $eventDetails = '')
    {
        switch ($eventName) {
        case self::EVENT_IMAGE:
        case self::EVENT_CLICK:
        case self::EVENT_COMPLAINT: // even a complaint is a sign of life ;-)
            // only update max. once a day
            if ($this->aliveUts < $uts - 86400) {
                $this->aliveUts = $uts;
                // We don't reset the quarantine, because Hotmail is known to
                // generate artificial clicks in bounced mails.
                $this->save();
            }
            break;
        case self::EVENT_BOUNCE_HARD:
            if ($this->quarantineExpireUts < $uts) { // only update when expired
                $this->extendQuarantine($eventDetails);
                $this->save();
            }
            break;
        case self::EVENT_SEND:
            if ($this->sendUts < $uts - 7 * 86400) { // only update max. once a week (see backend/cron/dequarantine.php)
                $this->sendUts = $uts;
                $this->save();
            }
            break;
        }
    }

    /** Static wrapper
     * @param string email
     * @param string event name
     * @param int    the time when the mail initiating the event was sent
     * @param string event details
     */
    public static function handleEmailEvent($email, $eventName, $uts, $eventDetails)
    {
        if ($mailAddress = self::getByEmail($email)) {
            $mailAddress->handleEvent($eventName, $uts, $eventDetails);
        }
    }

    /**
     * Make email unsendable for a period
     * @param string
     * @param string
     * @deprecated use handleEvent
     */
    public static function putInQuarantine($email, $reason = '')
    {
        $adr = self::getByEmail($email);
        if ($adr && !$adr->getIsInQuarantine()) {
            $adr->extendQuarantine($reason);
            $adr->save();
        }
    }

    /** 
     * Extend the quarantine period
     * @param string
     */
    protected function extendQuarantine($reason = '')
    {
        $now = $_SERVER['REQUEST_TIME'];

        if (!$this->quarantineCreateUts) { // first time around
            $this->quarantineCreateUts = $now;
        }
        // adjust expire uts. ceiling is php's max int
        $multiplier = pi(); // or whatever
        $timeSinceFirstQuarantine = $now - $this->quarantineCreateUts;
        if ($timeSinceFirstQuarantine >= floor(PHP_INT_MAX / $multiplier)) {
            $this->quarantineExpireUts = PHP_INT_MAX;
        } else {
            $this->quarantineExpireUts = $now + max(self::QUARANTINE_MIN, floor($multiplier * $timeSinceFirstQuarantine));
        }

        if ($this->quarantineHistory) {
            $this->quarantineHistory .= "\n";
        }
        $this->quarantineHistory .= "$now: $reason";
    }

    /**
     * Remove email address from blacklist
     * @param string
     * @deprecated use handleEvent
     */
    public static function releaseFromQuarantine($email)
    {
        if ($adr = self::getByEmail($email)) {
            $adr->quarantineCreateUts = 
            $adr->quarantineExpireUts = self::NOLL;
            $adr->save();
        }
    }

    /**
     * Get email quarantine status
     * @param string
     * @return boolean
     * @deprecated use isEmailInQuarantine
     */
    public static function isInQuarantine($email)
    {
        return self::isEmailInQuarantine($email);
    }

    /**
     * Get email quarantine status
     * @param string
     * @return boolean
     */
    public static function isEmailInQuarantine($email)
    {
        $adr = self::getByEmail($email);
        return $adr ? $adr->getIsInQuarantine() : false;
    }


    /** 
     * Get quarantine status
     * return bool
     */
    public function getIsInQuarantine()
    {
        return $this->quarantineExpireUts > $_SERVER['REQUEST_TIME'];
    }

    /** Mailbox part
     * @return string
     */
    public function getMailbox()
    {
        return self::getEmailMailbox($this->email);
    }

    /** Domain part
     * @return string
     */
    public function getDomain()
    {
        return self::getEmailDomain($this->email);
    }

    /** chech that email is likely to be valid
     * @param string email address
     * @param bool skip dns checks?
     * @return bool
     * @static
     */
    static function isEmailValid($email, $skipDns = false)
    {
        @list($mailbox, $host) = explode("@", $email);
        if (!isset($mailbox, $host)) {
            return false;
        }
        if (isset($_SERVER['PEYTZ_DEV']) && preg_match('/\.invalid$/', $email)) {
            return true;
        }
        // idn_to_ascii will segfault if hostname starts with a dot or contains two neighbouring dots
        if (strpos($host, '.') === 0 || strpos($host, '..') !== false) {
            return false;
        }
        $asciiHost = idn_to_ascii($host); // fixme: idn.default_charset must match internal/our charset
        $asciiEmail = join('@', array($mailbox, $asciiHost));
        require_once 'Mail/RFC822.php';
        $mailRFC822 = new Mail_RFC822;
        // isValidInetAddress() doesn't catch foobar-.tld and -foobar.tld, but
        // these are forbidden according to RFC 2821 sections 4.1.2 and 4.1.3.
        // Domains ending with a digit are syntactically valid, but Postfix
        // rejects them and no TLD ends with a digit, so they don't exist in
        // practice. Also, isValidInetAddress() allows whitespace.
        if (!$mailRFC822->isValidInetAddress($asciiEmail) ||
            preg_match('/^-|-$|\.-|-\.|\d$|\s/', $asciiHost)) {

            return false;
        }
        if ($skipDns) {
            return true;
        }
        // check that host has either A- or MX-rr
        if (!checkdnsrr($asciiHost, "MX") && !checkdnsrr($asciiHost, "A")) {
            return false;
        }
        // all seems ok
        return true;
    }


    /** return only the mailbox part of the email address
     * @param string
     * @return string
     */
    public static function getEmailMailbox($email)
    {
        return substr($email, 0, strpos($email, '@'));
    }

    /** return only the domain part of the email address
     * @param string
     * @return string
     */
    public static function getEmailDomain($email)
    {
        return substr($email, 1 + strpos($email, '@'));
    }




    /** Domain slice
     */
    public static function getAllByDomain($domain)
    {
        $o = new self;
        $o->columnLike('email', '%@' . $domain);
        $o->setOrderBySql('email');
        return $o->findAll();
    }

    /** 
     * tablrow override
     * @return string
     */
    public function getDefaultTable()
    {
        return 'mailAddress';
    }


}
?>
