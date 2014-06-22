<?php

/**
 * Validates a user using his SSL client certificate.
 *
 * The Apache configuration for the login page should look like this:
 * SSLVerifyClient optional
 * SetEnv ENTER_VALIDATOR_SSL 1
 *
 * @package Enter
 * @subpackage Validator
 */
class Enter_Validator_Ssl extends Enter_Validator
{
    /** Even more info
     * @param string class name of user to create
     * @return Enter_Validator_Status
     */
    public function getStatus($userClassName)
    {
        // ENTER_VALIDATOR_SSL is necessary, because SSL_CLIENT_S_DN may be
        // set even on pages without "SSLVerifyClient optional". It is unknown
        // whether this is a browser bug or an Apache bug.
        $status = new Enter_Validator_Status($this);
        if (isset(
            $_SERVER['HTTPS'],
            $_SERVER['SSL_CLIENT_S_DN'],
            $_SERVER['SSL_SERVER_I_DN'],
            $_SERVER['ENTER_VALIDATOR_SSL'])) {

            // Call Foo_User::getBySslIssuerAndSubject($issuer, $subj)
            $method = array($userClassName, 'getBySslIssuerAndSubject');
            $user = call_user_func($method,
                $_SERVER['SSL_CLIENT_I_DN'],
                $_SERVER['SSL_CLIENT_S_DN']);
            if ($user) {
                $status->code = Enter_Validator_Status::LOGIN_OK;
                $status->user = $user;
                $status->setCookie = true;
            } else {
                $status->code = Enter_Validator_Status::LOGIN_FAILED;
                $status->codeText = 'Unknown user';
            }
        }
        return $status;
    }
}

?>
