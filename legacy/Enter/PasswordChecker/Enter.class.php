<?php

/**
 * @package Enter
 * @subpackage PasswordChecker
 */
class Enter_PasswordChecker_Enter extends Enter_PasswordChecker
{
    public function authenticate($username, $password)
    {
        // Enter_User::getByUsername() may be overridden, so call the method
        // on the proper subclass.
        $userClassName = Enter_Config::getValueByName("userClass");
        $method = array($userClassName, 'getByUsername');
        $user = call_user_func($method, $username);
        // check for trailing space
        if (!$user && substr($username, -1) == ' ') {
            $user = call_user_func($method, rtrim($username));
            if ($user) {
                trigger_error('Username with trailing spaces: "' . $username . '"');
            }
        }
        if (!$user) {
            return false;
        }

        $passwordOk = $user->checkPassword($password);
        // check for trailing space
        if (!$passwordOk && substr($password, -1) == ' ') {
            $passwordOk = $user->checkPassword(rtrim($password));
            if ($passwordOk) {
                trigger_error('Username with password with trailing spaces: "' . $username . '"');
            }
        }
        return $passwordOk;
    }
}

?>
