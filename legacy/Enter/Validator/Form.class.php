<?php
/** validate user by IP
 */
class Enter_Validator_Form extends Enter_Validator
{
    /**
     * @param  string
     */
    protected function __construct($uri)
    {
        $parts = parse_url($uri);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
    }

    /** Even more info
     * @param string class name of user to create
     * @return Enter_Validator_Status
     */
    public function getStatus($userClassName)
    {
        $status = new Enter_Validator_Status($this);
        if (isset($_REQUEST[ENTER_VALIDATOR_FORM_USERNAME], 
                $_REQUEST[ENTER_VALIDATOR_FORM_PASSWORD]) 
            && $_REQUEST[ENTER_VALIDATOR_FORM_USERNAME]) {

            $username = $_REQUEST[ENTER_VALIDATOR_FORM_USERNAME];
            $password = $_REQUEST[ENTER_VALIDATOR_FORM_PASSWORD];
            try {
                $check = Enter_PasswordChecker::checkPassword($username, $password);
                if ($check) {
                    // Call Foo_User::getByUsername($username)
                    $method = array($userClassName, 'getByUsername');
                    $status->user = call_user_func($method, $username);
                    if ($status->user) {
                        $status->code = Enter_Validator_Status::LOGIN_OK;
                        $status->setCookie = true;
                    }
                } else {
                    $status->code = Enter_Validator_Status::LOGIN_FAILED;
                }
            } catch (Enter_PasswordChecker_Exception $exception) {
                $status->code = Enter_Validator_Status::LOGIN_ERROR;
                $status->description = $exception->getMessage();
            }
        }
        return $status;
    }
}
?>
