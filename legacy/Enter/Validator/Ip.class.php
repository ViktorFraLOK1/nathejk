<?php
/** validate user by IP
 */
class Enter_Validator_Ip extends Enter_Validator
{
    private $username; // ip => username mappings

    /**
     * @param  string
     */
    protected function __construct($uri)
    {
        $parts = parse_url($uri);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        // ip://na?username[123.45.67.89]=foo&username[127.0.0.1]=cron
        if (!isset($query['username']) || !is_array($query['username'])) {
            trigger_error('username[] not specified in ip:// URI');
            break;
        }
        $this->username = $query['username'];
    }

    /** Even more info
     * @param string class name of user to create
     * @return Enter_Validator_Status
     */
    public function getStatus($userClassName)
    {
        $status = new Enter_Validator_Status($this);
        $status->setCookie = false;

        foreach ($this->username as $ip => $username) {
            if ($ip == $_SERVER['REMOTE_ADDR']) {
                // Call Foo_User::getByUsername($username)
                $method = array($userClassName, 'getByUsername');
                $user = call_user_func($method, $username);
            }
        }

        if (isset($user)) {
            if ($user) {
                $status->code = Enter_Validator_Status::LOGIN_OK;
                $status->user = $user;
            } else {
                $status->code = Enter_Validator_Status::LOGIN_FAILED;
                $status->codeText = 'Unknown user';
            }
        }
        return $status;
    }
}
?>
