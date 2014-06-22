<?php
/** Status container
 * @package Enter
 * @subpackages Validator
 */
class Enter_Validator_Status
{
    const LOGIN_NONE   = "Not tried (none)";
    const LOGIN_OK     = "Alles klar (OK)";
    const LOGIN_FAILED = "failure (failed)";
    const LOGIN_ERROR  = "Shit happened (error)";

    public $validator = null;
    public $user = null;
    public $code = self::LOGIN_NONE;
    public $description = '';      // extra code info
    public $setCookie = false;  // let our cookie validator take over from here
    public $redirectUrl = null; // where to go after validation

    public function __construct($validator)
    {
        $this->validator = $validator;
    }

    /** Method wrapper for templates
     * @return bool
     */
    public function isNone()
    {
        return $this->code === self::LOGIN_NONE;
    }

    /** Method wrapper for templates
     * @return bool
     */
    public function isOk()
    {
        return $this->code === self::LOGIN_OK;
    }

    /** Method wrapper for templates
     * @return bool
     */
    public function isFailed()
    {
        return $this->code === self::LOGIN_FAILED;
    }

    /** Method wrapper for templates
     * @return bool
     */
    public function isError()
    {
        return $this->code === self::LOGIN_ERROR;
    }
}
?>
