<?php
/**
 * A default user which implements all our interfaces.
 * @package Enter
 */

class Enter_DefaultUser extends Enter_User
{
    /** These virtual columns are needed for the interfaces implementet by this class
     * @return array of column names
     */
    public function getVirtualColumnNames()
    {
        return array_unique(array_merge(
            parent::getVirtualColumnNames(), 
            self::getDebatVirtualColumnNames(),
            self::getOpropVirtualColumnNames()
        ));
    }

    /** These virtual columns are needed for a debat user
     * @return array of column names
     */
    private static function getDebatVirtualColumnNames()
    {
        global $CUSTOMER;
        //FIXME: Change this to look for customer access to til debat product when possible
        if (in_array($CUSTOMER->id, array(4, 14, 33))) { // peytz, ing, borsen
            return array('debatIsBanned', 
                        'debatAboutMe',
                        'debatHomepage',
                        'debatMsm',
                        'debatSort',
                        'debatNumPerPage',
                        'debatShowOnline',
                        'debatShowEmail',
                        'debatSignature'
                   );
        }
        return array();
    }
    
    /** These virtual columns are needed for a oprop user
     * @return array of column names
     */
    private static function getOpropVirtualColumnNames()
    {
        return array('unsubscribeurl');
    }

    /** Get list of columnNames that are suitable for displaying
     *  Look for VIP setting and if not found return all
     * @return array of column names
     */
    public function getDisplayColumnNames() 
    {
        $columnNames = Enter_Config::getValueByName("displayColumnNames");
        if ($columnNames) {
            return explode(",", $columnNames);
        }
        return array_diff(
                $this->getColumnNames(),
                self::getDebatVirtualColumnNames(),
                self::getOpropVirtualColumnNames(),
                array($this->getVirtualColumnsColumnName(),
                      'customerId',
                      'uniqueEmail',
                      'password',
                      ));
    }

    /** Stuff from Oprop_User interface
    */

    public function getUnsubscribeUrlByMailinglist($mailinglist, $ttl = 86400)
    {
        // use old hard-coded column -- if it exists
        if (!in_array('unsubscribeurl', $this->getColumnNames())) {
            return '';
        }
        if (!Pasta_Url::isAbsolute($this->unsubscribeurl)) {
            return '';
        }
        return $this->unsubscribeurl;
    }

    /**
     * @return array
     * @deprecated
     */
    public static function getUserIdsByMailinglist($mailinglist)
    {
        $group = $mailinglist->getSubscriberGroup();
        return $group ? $group->getUserIds() : array();
    }

    /**
     * @return int
     * @deprecated
     */
    public static function getUserCountByMailinglist($mailinglist)
    {
        $group = $mailinglist->getSubscriberGroup();
        return $group ? $group->userCount : 0;
    }

    /**
     * @param Oprop_Mailinglist
     * @return bool
     * @deprecated
     */
    public function getIsSubscribedByMailinglist($mailinglist)
    {
        $group = $mailinglist->getSubscriberGroup();
        return $group ? $group->containsUser($this) : false;
    }

    /**
     * @param Oprop_Mailinglist
     * @return bool
     * @deprecated
     */
    public function subscribeByMailinglist($mailinglist)
    {
        $group = $mailinglist->getSubscriberGroup();
        return $group ? $group->addUser($this) : false;
    }

    /**
     * @param Oprop_Mailinglist
     * @return bool
     * @deprecated
     */
    public function unsubscribeByMailinglist($mailinglist)
    {
        $group = $mailinglist->getSubscriberGroup();
        return $group ? $group->removeUser($this) : false;
    }

    /** Unubscribe a list of users from a mailinglist. Used by the autounsubscribe
     *  function in oprop/backend/unsubBounces.php
     *  Returns an array of user objects that has been unsubscribed
     * @param array of User Objects
     * @param Oprop_Mailinglist
     * @return array of User Objects
     * @deprecated
     */
    public static function unsubscribeByUsersAndMailinglist($users, $mailinglist) 
    {
        foreach ($users as $i => $user) {
            if (!$user->unsubscribeByMailinglist($mailinglist)) {
                unset($users[$i]);
            }
        }
        return $users;
    }


    /** username = email
     * override 
     */
    function save()
    {
        if (get_class($this) == 'Enter_DefaultUser') {
            // these are always real columns
            if (in_array('email', $this->getChangedColumns())) {
                $this->setUsername($this->getEmail());
            }
        }
        return parent::save();
    }

    /**
     * @param int
     * @return Enter_User|null
     */
    public static function getById($id)
    {
        global $CUSTOMER;
        $className = self::getUserClassName();
        $user = parent::getObjectById($className, $id);
        if ($user && $user->customerId != $CUSTOMER->id) {
            trigger_error('User id=' . $id . ' belongs to customer id=' .
                $user->customerId . ', but $CUSTOMER->id is ' . $CUSTOMER->id);
            return null;
        }
        return $user;
    }

    /**
     * @param string an email address
     * @param mixed  an indicator
     * @return Enter_User|null
     */
    public static function getByEmail($email, $indicator = null)
    {
        // So far only on dev
        if (isset($_SERVER['PEYTZ_DEV']) && !self::emailIsUnique()) {
            trigger_error('Email is not unique for this customer');
        }
        if (strpos($email, '@') === false) {
            return NULL;
        }
        $user = self::getUserObject();
        $user->setEmail($email, $indicator);
        return $user->findOne(false);
    }

    /** Stuff from Debat_User interface
    */

    public static function getByUserAgentOrIP($userAgent, $IP)
    {
        return false;
    }

    public function isBanned()
    {
        return $this->getDebatIsBanned();
    }
    
    public function ban()
    {
        $this->setDebatIsBanned(1);
        return $this->save();
    }
    
    public function unban()
    {
        $this->setDebatIsBanned(0);
        return $this->save();
    }

    public static function getAllBanned() 
    {
        $user = new self;
        $user->setDebatIsBanned(1);
        return $user->findAll();
    }
    
    public function isOnline()
    {
        if ($isOnline = Debat_IsOnline::getByUser($this)) {
            return $isOnline->isOnline();
        } else {
            return false;
        }
    }

    public function getDebatName()
    {
        return $this->getName();
    }

    public function getDebatAvatarUrl()
    {
        if ($avatar = Kollage_Content::getByPath('/debat/avatar/' . $this->getId())) {
            return $avatar->getUrl();
        } else {
            return false;
        }
    }

    public function isForumModerator($forum)
    {
        if ($RIGHTS = Debat_Rights::getByUser($this)) {
            return $RIGHTS->isForumModerator($forum);
        } else {
            return false;
        }
    }

    public function getScoreInPercent()
    {
        $ratings = new Debat_Rating;
        $ratings->setAuthorUserId($this->getId());
        $scores = array();
        foreach ($ratings->findAll() as $rating) {
            $scores[] = $rating->getScore() / $rating->getMaxScore();
        }
        if (!count($scores)) {
            return 0;
        }
        return 100 * array_sum($scores) / count($scores);
    }
}
?>
