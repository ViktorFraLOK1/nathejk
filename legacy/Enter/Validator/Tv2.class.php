<?php

/**
 * @package Enter
 * @subpackage Validator
 */
class Enter_Validator_Tv2 extends Enter_Validator
{
    /**
     * @var string
     */
    protected $partnerUsername = '';

    /**
     * @var string
     */
    protected $partnerKey = '';

    /**
     * @var Tv2_LoginProxy
     */
    protected $_loginProxy = null;

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
        
        if (isset($query['partnerUsername'])) {
            $this->partnerUsername = trim($query['partnerUsername']);
        }
        
        if (isset($query['partnerKey'])) {
            $this->partnerKey = trim($query['partnerKey']);
        }
    }

    /**
     * @return Tv2_Login_Proxy
     */
    public function getLoginProxy()
    {
        if ($this->_loginProxy === null) {
            $this->_loginProxy = new Tv2_LoginProxy($this->partnerUsername, $this->partnerKey);
        }
        
        return $this->_loginProxy;
    }

    /**
     * 
     * @return Tv2_LoginProxy_ChecksumCookie
     */
    public function getChecksumCookie()
    {
        return new Tv2_LoginProxy_ChecksumCookie('tv2login_checksum', 'rickroll'); 
    }

    /**
     * @param  string class name of user to create
     * @return Enter_Validator_Status
     */
    public function getStatus($userClassName)
    {
        $status = new Enter_Validator_Status($this);
        
        $loginProxy = $this->getLoginProxy();
        $checksumCookie = $this->getChecksumCookie();
        
        // If the user is logging out, we destroy all
        // cookies associated with the login system
        if (isset($_REQUEST[ENTER_VALIDATOR_LOGOUT])) {
            $loginProxy->unsetCookie();
            $checksumCookie->unsetCookie();
            return $status;
        }
        
        // No tv2 login proxy cookie means that we can skip the rest
        if (!$loginProxy->getHasCookie()) {
            return $status;
        }
        
        // To avoid hammering the login proxy we first
        // attempt to authenticate with with our own
        // checksum cookie
        if ($checksumCookie->getHasCookieData()) {
            
            // Check that the cookie is valid
            if ($checksumCookie->getIsChecksumValidByUserData($loginProxy->getCookie())) {
                
                // Retrive the external association for the user
                global $CUSTOMER;
                $tv2UserId   = $checksumCookie->getUserId();
                $association = Enter_User_ExternalAssociation::getByCustomerAndForeignSourceAndForeignKey($CUSTOMER, Enter_User_ExternalAssociation::FOREIGN_SOURCE_TV2, $tv2UserId);
                $user        = $association ? call_user_func(array($userClassName, 'getById'), $association->userId) : null;
                
                // If we found a user over the external association
                // everything is peachy
                if ($user) {
                    $status->user = $user;
                    $status->code = Enter_Validator_Status::LOGIN_OK;
                    return $status;
                }
                
            }
            
            // If we reach this point the checksum cookie must be invalid
            $checksumCookie->unsetCookie();
            
        }
        
        // Negotiate with the login proxy
        $loginProxy->authenticate();
        
        // Was the user authenticated?
        if (!$loginProxy->getIsAuthenticated()) {
            return $status;
        }
        
        // Retrive the external association for the user
        global $CUSTOMER;
        $association = Enter_User_ExternalAssociation::getByCustomerAndForeignSourceAndForeignKey($CUSTOMER, Enter_User_ExternalAssociation::FOREIGN_SOURCE_TV2, $loginProxy->getUserId());
        if (!$association) {
            return $status;
        }
        
        // Try and load the user based on the associated user id
        $status->user = call_user_func(array($userClassName, 'getById'), $association->userId);
        if ($status->user) {
            $checksumCookie->setCookieDataByUserIdAndUserData($loginProxy->getUserId(), $loginProxy->getCookie());
            $status->code = Enter_Validator_Status::LOGIN_OK;
        }
        
        return $status;
    }
}
