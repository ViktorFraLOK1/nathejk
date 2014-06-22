<?php

/**
 * This validator can be configured in vip settings
 * by adding it to validators under enter, eg:
 * 
 * facebook://na?appId={appID}&appSecret={appSecret}
 * 
 * It can be retrived once it is configured:
 * 
 * <code>
 *     
 *     // This requries the facebook validator to be configured
 *     $facebookValidator = Enter_Validator::getByScheme('facebook');
 *     
 *     // Create a facebook login url, request email permission and redirect to fb-login.php afterwards
 *     $facebookLoginUrl = $facebookValidator->getFacebookLoginUrl($SITE->getUrl() . 'fb-login.php', array('email'));
 *     
 *     // Get the current users profile data (the contents depends on the permissions)
 *     $facebookProfileData = $facebookValidator->getFacebookProfileData();
 *     
 *     try {
 *         // Requires the proper permissions
 *         $facebookValidator->getFacebook()->api('/me/feed', 'POST', array(
 *             'link' => 'www.example.com',
 *             'message' => 'Posting with the PHP SDK!'
 *         ));
 *     } catch(FacebookApiException $e) {
 *         // ....
 *     }
 * 
 * </code>
 * 
 * @package Enter
 * @subpackage Validator
 */
class Enter_Validator_Facebook extends Enter_Validator
{
    /**
     * @var string
     */
    protected $appId  = '';
    
    /**
     * @var string
     */
    protected $appSecret = '';
    
    /**
     * @var array of string
     */
    protected $appPermissions = array();
    
    /**
     * @var Pasta_Facebook
     */
    private $_facebook = null;
    
    /**
     * @param string
     */
    protected function __construct($uri)
    {
        $parts = parse_url($uri);
        $query = array();
        
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        
        if (isset($query['appId'])) {
            $this->appId = trim($query['appId']);
        }
        
        if (isset($query['appSecret'])) {
            $this->appSecret = trim($query['appSecret']);
        }
        
        if (isset($query['appPermissions'])) {
            $this->appPermissions = explode(';', $query['appPermissions']);
        }
    }
    
    /**
     * @return Pasta_Facebook
     */
    public function getFacebook()
    {
        if (!$this->_facebook) {
            $this->_facebook = new Pasta_Facebook(array(
                'appId'  => $this->appId,
                'secret' => $this->appSecret,
                'cookie' => true,
                'oauth'  => true,
            ));
        }
        
        return $this->_facebook;
    }
    
    /**
     * Get at facebook login url.
     * 
     * The $permission parameter can be used to 
     * override the default permissions configured 
     * for the validator using appPermissions.
     * 
     * For a list of facebook permissions see: 
     * http://developers.facebook.com/docs/authentication/permissions/
     * 
     * @param string $redirectUrl
     * @param array  $permissions
     */
    public function getFacebookLoginUrl($redirectUrl = '', $permissions = null)
    {
        if (!is_array($permissions)) {
            $permissions = $this->appPermissions;
        }
        
        $params = array();
        
        if (!empty($permissions)) {
            $params['scope'] = implode($permissions, ', ');
        }
        
        if (!empty($redirectUrl)) {
            $params['redirect_uri'] = $redirectUrl;
        }
        
        return $this->getFacebook()->getLoginUrl($params);
    }
    
    /**
     * @return array
     */
    public function getFacebookProfileData()
    {
        if ($user = $this->getFacebook()->getUser()) {
            try {
                return $this->getFacebook()->api('/me');
            } catch (FacebookApiException $e) {
            }
        }
        
        return array();
    }
    
    /**
     * @param  string class name of user to create
     * @return Enter_Validator_Status
     */
    public function getStatus($userClassName)
    {
         $status = new Enter_Validator_Status($this);
         
         // If the user is logging out, we destroy the
         // facebook session data regardless if it exists
         if (isset($_REQUEST[ENTER_VALIDATOR_LOGOUT])) {
             $this->getFacebook()->destroySession();
             return $status;
         }
         
         // If a facebook user id has been obtained from facebook 
         // attempt to match it to a user over the the external 
         // associations
         if ($facebookUserId = $this->getFacebook()->getUser()) {
             
             global $CUSTOMER;
             
             $externalAssoiciation = Enter_User_ExternalAssociation::getByCustomerAndForeignSourceAndForeignKey($CUSTOMER, Enter_User_ExternalAssociation::FOREIGN_SOURCE_FACEBOOK, $facebookUserId);
             if ($externalAssoiciation) {
                 $userId = $externalAssoiciation->userId;
                 $status->user = call_user_func(array($userClassName, 'getById'), $userId);
                 if ($status->user) {
                     $status->code = Enter_Validator_Status::LOGIN_OK;
                 }
             }
         }
         
         return $status;
    }
    
}
