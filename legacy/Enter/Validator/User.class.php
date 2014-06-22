<?php

/**
 * Classes that implement this interface can be validated using the validators
 * in Enter_Validator subpackage.
 * @package  Enter
 * @subpackage  Validator
 */
interface Enter_Validator_User
{
    /**
     * Returns the information that should be stored in the login cookie. This
     * information is used by getByCookieArray() to look up the user in later
     * requests. This method may also contain other information that should be
     * stored in the cookie, e.g. for efficiency reason.
     * @return  array  an associate array of (string => string) pairs
     */
    function getCookieArray();

    /**
     * Returns the user indicated by the specified cookie array.
     * @return  Enter_Validator_User  an Enter_Validator_User, or null
     */
    static function getByCookieArray($array);

    /**
     * Returns the user with the specified username. The implementing class
     * may choose to use other unique properties as a username, e.g. email
     * address, phone number etc.
     * @param   string  a username
     * @return  Enter_Validator_User  an Enter_Validator_User, or null
     */
    static function getByUsername($username);

    /**
     * Returns the user with the specified SSL certificate.
     * @param  string  the issuer, SSL_CLIENT_I_DN
     * @param  string  the subject, SSL_CLIENT_S_DN
     * @return  Enter_Validator_User  an Enter_Validator_User, or null
     */
    static function getBySslIssuerAndSubject($issuer, $subject);

    /**
     * Sets the URI used to instantiate this instance.
     * @return  string  a validator URI
     */
    public function setValidatorUri($urn);
}

?>
