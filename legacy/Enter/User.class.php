<?php

/**
 * A user
 * @package Enter
 */
class Enter_User extends Pasta_TableRow implements Enter_Validator_User
{
    const SESSION_COOKIE_NAME = 'PeytzEnterSession';

    private $validatorUri;

    /** get name of current customers user class
     * @return string
     */
    public static function getUserClassName()
    {
        return Enter_Config::getValueByName("userClass");
    }

    /** get user object of current customers user class
     *  with customerId eq current customerId.
     * @return object
     */
    public static function getUserObject()
    {
        global $CUSTOMER;
        $classname = self::getUserClassName();
        $obj = new $classname;
        $obj->customerId = $CUSTOMER->id;
        return $obj;
    }

    /**
     * overrides method from Pasta_TableRow
     * @param   array           database row
     * @return  string          classname
     */
    protected function getClassNameByRow($row)
    {
        return self::getUserClassName();
    }

    /** override
     * Use config settings for column names info for simple user classes.
     * List our columns
     * @return array of string
     */
    public function getVirtualColumnNames()
    {
        static $colList;
        if (!isset($colList)) {
            $colList = str_replace(" ", "", @Enter_Config::getValueByName("userClassVirtualColumnNames"));
        }
        return $colList ? explode(",", $colList) : array();
    }

    /** factory
     * @param int
     * @return Enter_User|null
     */
    public static function getById($id)
    {
        $className = self::getUserClassName();
        $args = func_get_args();
        return call_user_func_array(array($className, __FUNCTION__), $args);
    }

    /** factory
     * @param int
     * @return Enter_User|null
     */
    public static function getByEmail($email)
    {
        $className = self::getUserClassName();
        $args = func_get_args();
        return call_user_func_array(array($className, __FUNCTION__), $args);
    }

    /**
     * return user from validator; validate auth info and find user
     * @param string class name
     * @return Enter_User|null
     * @deprecated use Enter_Validator::getCurrentUser() instead.
     */
    public static function getByValidators()
    {
        if (isset($_SERVER['PEYTZ_DEV'])) {
            trigger_error('Deprecated; use Enter_Validator::getCurrentUser()');
        }
        $user = Enter_Validator::getCurrentUser();
        // Enter_Validator::getCurrentUser() returns null, not false
        return $user ? $user : false;
    }

    /**
     * find from cookie
     * @return Enter_User|null
     */
    public static function getByCookieArray($array)
    {
        $u = self::getUserObject();
        $u->id = $array['id'];
        if ($user = $u->findOne()) {
            // check for session
            if (!isset($_COOKIE[self::SESSION_COOKIE_NAME])) {
                $user->lastLogin = time();
                $user->save();
                $_COOKIE[self::SESSION_COOKIE_NAME] = $user->lastLogin;
                setCookie(self::SESSION_COOKIE_NAME, $user->lastLogin, 0, '/');
            }
        }
        return $user;
    }

    /** set the cookie
     * @return bool
     */
    public function setCookie($remember = false)
    {
        $validator = Enter_Validator::getByScheme('cookie');
        if (!$validator) {
            trigger_error('No cookie validator configured');
            return false;
        } else {
            $validator->setCookie($this, $remember);
        }
    }

    /** clear the cookie
     * @return bool
     * @deprecated
     */
    public static function unsetCookie()
    {
        $validator = Enter_Validator::getByScheme('cookie');
        if (!$validator) {
            trigger_error('No cookie validator configured');
            return false;
        } else {
            $validator->unsetCookie();
        }
    }

    /**
     * Sets the URI used to instantiate this instance.
     * @return  string  a validator URI
     */
    public function setValidatorUri($uri)
    {
        $this->validatorUri = $uri;
    }

    /** Get the info. to put in the session cookie
     * @return array
     */
    public function getCookieArray()
    {
        return array('id' => $this->id);
    }

    /**
     * @param   string
     * @param   string
     * @return  Enter_User|null
     */
    public static function getBySslIssuerAndSubject($subject, $issuer)
    {
        $user = self::getUserObject();
        $user->sslSubject = $subject;
        $user->sslIssuer = $issuer;
        return $user->findOne(false);
    }

    /**
     * Returns whether the same email address may be used by two users on the
     * current customer. Based on the userClassEmailIsUnique setting.
     * When changing userClassEmailIsUnique from 0 to 1, run "UPDATE user SET
     * uniqueEmail = email WHERE customerId = ?" before and after changing the
     * setting.
     * @return  bool
     */
    public static function emailIsUnique()
    {
        return (bool) Enter_Config::getValueByName('userClassEmailIsUnique');
    }

    /**
     *
     * @param string
     * @return  Enter_User|null
     */
    public static function getByUsername($username)
    {
        $user = self::getUserObject();
        $user->username = $username;
        return $user->findOne(false);
    }

    /**
     *
     * @param string
     * @return  Enter_User|null
     */
    public static function getByForeignId($foreignId)
    {
        $user = self::getUserObject();
        $user->foreignId = $foreignId;
        return $user->findOne(false);
    }

    /**
     * @return  array
     */
    public function getExternalAssociations()
    {
        $q = new Enter_User_ExternalAssociation;
        $q->userId = $this->id;
        return $q->findAll();
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getColumn('email');
    }

    /**
     * @param  string
     */
    public function setEmail($value)
    {
        $value = trim($value); // leading and trailing white space seen in the wild
        if (substr($value, -1) == '.' &&
            Pasta_Mail_Address::isEmailValid(substr($value, 0, -1), $skipDns = true)) {

            // Strip trailing . (apparently a common error)
            $value = substr($value, 0, -1);
        }
        $this->setColumn('email', $value);
    }

    /**
     * @param  string
     */
    public function setForeignId($value)
    {
        // Make sure empty values are converted to NULL due to uniqueness
        $this->setColumn('foreignId', $value ? $value : self::NOLL);
    }

    /**
     * @param  string
     */
    public function setUsername($value)
    {
        // Make sure empty values are converted to NULL due to uniqueness
        $this->setColumn('username', $value ? $value : self::NOLL);
    }

    /**
     * passwords are special columns
     * @return string
     */
    public function getPassword()
    {
        $crypted = $this->getColumn('password');
        $key = Enter_Config::getValueByName("passwordKey");

        $db = self::getPearDbByClassName(__CLASS__);
        $q = "SELECT DECODE('" . $db->escapeSimple($crypted) . "', '" . $db->escapeSimple($key) . "')";

        return $db->getOne($q);
    }

    /**
     * passwords are special columns
     * @param string
     */
    public function setPassword($value)
    {
        $key = Enter_Config::getValueByName("passwordKey");

        $db = self::getPearDbByClassName(__CLASS__);
        $q = "SELECT ENCODE('" . $db->escapeSimple($value) . "', '" . $db->escapeSimple($key) . "')";

        $crypted = $db->getOne($q);

        $this->setColumn('password', $crypted);
    }

    /** Send welcome mail to user
     * @return bool
     */
    public function sendWelcomeMail()
    {
        return $this->sendMailByTemplates(
            Enter_Config::getValueByName('templateUserWelcomeMailSubject'),
            Enter_Config::getValueByName('templateUserWelcomeMailHtml'),
            Enter_Config::getValueByName('templateUserWelcomeMailText')
            );
    }
    /** Send goodbye mail to user
     * @return bool
     */
    public function sendGoodbyeMail()
    {
        return $this->sendMailByTemplates(
            Enter_Config::getValueByName('templateUserGoodbyeMailSubject'),
            Enter_Config::getValueByName('templateUserGoodbyeMailHtml'),
            Enter_Config::getValueByName('templateUserGoodbyeMailText')
            );
    }
    /** Send lost password mail to user
     * @return bool
     */
    public function sendLostPasswordMail()
    {
        return $this->sendMailByTemplates(
            Enter_Config::getValueByName('templateUserLostPasswordMailSubject'),
            Enter_Config::getValueByName('templateUserLostPasswordMailHtml'),
            Enter_Config::getValueByName('templateUserLostPasswordMailText')
            );
    }

    /** Sends mail to user.
     * @param  string
     * @param  string
     * @param  string
     * @return bool
     */
    public function sendMailByTemplates($subjectTemplatePath, $htmlTemplatePath, $textTemplatePath)
    {
        $mail = new Pasta_Mail();

        $subjectTemplate = Kollage_Content::getByPath($subjectTemplatePath);
        $htmlTemplate    = Kollage_Content::getByPath($htmlTemplatePath);
        $textTemplate    = Kollage_Content::getByPath($textTemplatePath);

        if (!$htmlTemplate || !$textTemplate) {
            trigger_error('Html/text template missing', E_USER_WARNING);
            return false;
        }

        if ($subjectTemplate) {
            $mail->setSubjectTemplate($subjectTemplate);
        }
        $mail->setHtmlTemplate($htmlTemplate);
        $mail->setTextTemplate($textTemplate);

        // FIXME: Convert to 'user' (this is set automatically in Pasta_Mail)
        $mail->assign('User', $this);

        $from = Enter_Config::getValueByName('fromEmail');
        // FIXME kun i en kort overgangsfase
        if (preg_match('/^"?([^"]*)"? <(.*)>/', $from, $reg)) {
            $mail->setFrom($reg[2], $reg[1]);
        } else {
            $name  = @Enter_Config::getValueByName('fromName');
            $mail->setFrom($from, $name ? $name : null);
        }
        $status = $mail->sendToUser($this);
        if (!$status == Pasta_Mail::SENT) {
            trigger_error("Send failed", E_USER_NOTICE);
        }
        return $status == Pasta_Mail::SENT;
    }

    /** Get number of users
     * @return int
     */
    public static function getCount()
    {
        global $CUSTOMER;
        // Don't use Pasta_TableRow to avoid JOIN'ing with customer-specific
        // tables.
        $db = self::getPearDbByClassName(__CLASS__);
        $sql = 'SELECT COUNT(*) FROM user WHERE customerId = ' . $CUSTOMER->id;
        return $db->getOne($sql);
    }

    /** Convert array of email adr to an array of id's
     * @param array of string
     * @return array of int
     */
    public static function getIdsByEmails($emails)
    {
        $user = new self();
        $user->customerId = $GLOBALS['CUSTOMER']->id;
        $user->columnIn('email', $emails);
        return $user->findColumn('id');
    }

    /** build an url which automagically logges in this user
     * @param string url
     * @param int ttl
     */
    public function getAutoLoginUrl($url, $ttl = 86400)
    {
        if ($validator = Enter_Validator::getByScheme('autologinurl')) {
            return $validator->getAutologinUrl($this, $url, $ttl);
        }
        return $url;
    }

    /**
     * @return  bool
     */
    function isValid()
    {
        // Hack: Skip DNS in backend - assume that backend users know what they
        // are doing, and avoid possibly slow DNS look-ups on batch imports
        if ($this->email &&
            $this->email != Pasta_TableRow::NOLL &&
            !Pasta_Mail_Address::isEmailValid($this->email, isset($_SERVER['PEYTZ_BACKEND']))) {

            $this->setErrorString('Invalid email address');
            return false;
        }
        return parent::isValid();
    }

    /** override from tablerow
     */
    function save()
    {
        if (self::emailIsUnique()) {
            if ($this->email) {
                $this->setColumn('uniqueEmail', $this->email);
            } else {
                $this->setColumn('uniqueEmail', Pasta_TableRow::NOLL);
            }
        }
        if (array_diff($this->getChangedColumns(),
                       array('created', 'lastLogin'))) {

            $this->changed = time();
        }
        if (!$this->created) {
            $this->created = time();
        }
        $ok = parent::save();
        if (!$ok) {
            // Find column that caused the error (until TableRow is able to
            // return a better error message)
            $uniqueColumnNames = array('username', 'foreignId');
            if (self::emailIsUnique()) {
                $uniqueColumnNames[] = 'email';
            }
            foreach ($uniqueColumnNames as $columnName) {
                $value = $this->getColumn($columnName);
                if ($value && $value != Pasta_TableRow::NOLL) {
                    $user = new self();
                    $user->customerId = $this->customerId;
                    $user->setColumn($columnName, $value);
                    // Don't use $this->exists() - this always returns false
                    // when save() has failed
                    if ($this->id) {
                        $user->addWhereSql('id != ' . $this->id);
                    }
                    if ($user->findOne()) {
                        $this->setErrorString(ucfirst($columnName) . ' already in use');
                        break;
                    }
                }
            }
        }
        return $ok;
    }

    /** take backup
     */
    public function delete()
    {
        return Pasta_TableRowBackup::saveInstance($this)
        && parent::delete();
    }

    /**
     * @return  array           array of Enter_Crowd
     */
    public function getAllCrowds()
    {
        $obj = new Enter_CrowdUser;
        $obj->setUserId($this->getId());

        $crowds = new Enter_Crowd;
        $crowds->columnIn('id', $obj->findColumn('crowdId'));

        return $crowds->findAll();

    }

    /**
     * @return  array           array of Enter_User
     */
    public function getAllUsersInAllCrowds()
    {
        $users = new Enter_User;
        $users->columnIn('id', $this->getAllUserIdsInAllCrowds());
        return $users->findAll();
    }

    /**
     * @return  array           array of Enter_User ids
     */
    public function getAllUserIdsInAllCrowds()
    {
        $userIds = array();
        foreach ($this->getAllCrowds() as $crowd) {
            $userIds = array_merge($userIds, $crowd->getAllUserIds());
        }
        return array_unique($userIds);
    }

    /**
     * Checks to see if a ColumName matches a regex pattern, if it matches the value will be threated as an Unix Timestamp
     * and be converted into a readable date.
     * If ColumValue is an Array it is json encoded and returned
     *
     * @param string $columnName
     * @return string json date or original value
     */
    public function getColumnAsDisplayString($columnName) {
        $value = $this->$columnName;

        if (is_array($value)) {
            return json_encode($this->$columnName);
        }

        if ((in_array($columnName, array('created', 'changed', 'lastLogin'))
             || preg_match('/Uts$/', $columnName))
            && intval($this->$columnName)) {

            return date("Y-m-d H:i:s", intval($this->$columnName));
        }

        return $value;
    }

    /** Check a password
     * @param string password
     * @return bool
     */
    public function checkPassword($password)
    {
        return $this->password === $password;
    }
    
    /**
     * Generates a random password
     *
     * @param integer $length
     * @param boolean $includeSpecialChars
     * @return string
     */
    public static function generatePassword($length = 8, $includeSpecialChars = false)
    {
        $chars = 'abcdefghijklnmpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        
        if ($includeSpecialChars) {
            $chars .= '!@#$%^&*_-+=';
        }
        
        $charsLength = strlen($chars) -1;
        $generatedPassword = '';
        for ($i = 0;$i < $length;$i++) {
            $generatedPassword .= $chars[mt_rand(0, $charsLength)];
        }
        
        return $generatedPassword;
    }

    /** Sends an xls or csv file to the client based on the list
     *  of $userIds given and $columns selected to print
     *
     * $userIds is a multidimensional structure:
     * <code>
     *  $userIds =
     *      array(  "userIds" =>
     *                  array(12345 => array( value1, value2, ...), ... ),
     *              "customColumnNames" =>
     *                  array("columnName1", "columnName2", ...)
     *           )
     * </code>
     *
     * "customColumnNames" and their corresponding values can be left out, like this:
     * <code>
     *  $userIds =
     *      array(  "userIds" => array(12345, 12346, ... ))
     * </code>
     *
     * For an example of usage see: /var/www/test.peytz.dk/chance/backend/lottery/participantsExcel.php
     *
     * @param array     userIds and extra columns
     * @param array     array of enter column names that should be included in the export (empty array = all columns)
     * @param string    format of the spreadsheet, 'xls' or 'csv', 'xls' is default
     * @param int       row limit, or false if there is no limit
     * @param string    optional return type, 'filename' (return path to physical file) or 'string' (return
     *                  the spreadsheet as a string) , false is default (return nothing).
     * @param string    escape characters for double quotes in .csv files, default is "" (two double quotes)
     * @return string
     */

    public static function exportByUserIds(
                             array $userIds,
                             array $columnNames = array(),
                             $formatName = 'xls',
                             $limitNumber = false,
                             $returnAsMethod = false,
                             $doubleQuoteEscapeString = '""'
                           ) {

        set_time_limit(0);

        //Dummy TableRow Object
        $dummyUser = self::getUserObject();

        //Dummy holder for the limitCount, used for terminating foreach when the limit is reached. 0 = No limit
        $limitCount = 0;

        //List of columns we should return
        $columnNames = (sizeof($columnNames) > 0) ? $columnNames : $dummyUser->displayColumnNames;
        $customColumnNames = isset($userIds['customColumnNames']) ? $userIds['customColumnNames'] : array();

        //Locks the script for use in 30 minutes or until the script is successfully executed.
        $semaphore = Pasta_Semaphore_Database::acquireByName("enter-export-" . $GLOBALS['CUSTOMER']->id . time(), 1800);
        if (!$semaphore) {
            //Script locked so terminate and print a nice fail message to the user.
            Pasta_Http::exitWithNotFound("Export in progress");
            return false;
        }

        // Send HTTP headers, column headers etc.
        switch ($formatName) {
        case 'xls':
            require_once 'Spreadsheet/Excel/Writer.php';
            // Number of rows per sheet including the header row.
            $xlsRowsPerSheet = 50000 + 1;
            $xlsSheetNumber = 1;
            // The column headers are written to row 0 after all sheets have
            // been created.
            $xlsRowNumber = 1;

            switch ($returnAsMethod) {
            case 'filename':
            case 'string':
                $filename = tempnam("/tmp/", "Enter_Users_");
                $xlsWorkbook = new Spreadsheet_Excel_Writer($filename);
                break;
            default:
                $xlsWorkbook = new Spreadsheet_Excel_Writer();
                $xlsWorkbook->send($GLOBALS['CUSTOMER']->name . '-users.xls');
                break;
            }

            $xlsWorksheet = $xlsWorkbook->addWorksheet("Users_{$xlsSheetNumber}");
            $xlsWorksheet->activate();
            break;
        case 'csv':
        default:
            switch ($returnAsMethod) {
            case 'filename':
                $filename = tempnam("/tmp/", "Enter_Users_");
                $fp = fopen($filename, 'w');
                break;
            case 'string':
                $fp = fopen('php://temp', 'w');
                break;
            default:
                $fp = fopen('php://output', 'w');
                header('Content-type: text/plain');
                header('Content-Disposition: attachment; filename="' . $GLOBALS['CUSTOMER']->name . '-users.csv"');
                break;
            }

            // Add header row
            foreach ($columnNames as $columnName) {
                fwrite($fp, '"' . str_replace('"', $doubleQuoteEscapeString, $columnName) . '";');
            }
            foreach ($customColumnNames as $columnName) {
                fwrite($fp, '"' . str_replace('"', $doubleQuoteEscapeString, $columnName) . '";');
            }

            fwrite($fp, "\r\n");
            break;
        }

        // Write rows.
        foreach ($userIds['userIds'] as $userId => $customColumnValues) {
            $user = Enter_User::getById($userId);

            // Check for customer ownership and if it have been deleted.
            if (!$user || $user->customerId != $GLOBALS['CUSTOMER']->id) {
                continue;
            }

            switch($formatName) {
            case 'xls':
                if ($xlsRowNumber >= $xlsRowsPerSheet) {
                    $xlsSheetNumber++;
                    $xlsWorksheet = $xlsWorkbook->addWorksheet("Users_{$xlsSheetNumber}");
                    $xlsRowNumber = 1;
                }
                $xlsColNumber = 0;
                foreach ($columnNames as $columnName) {
                    $xlsWorksheet->write($xlsRowNumber, $xlsColNumber++, $user->getColumnAsDisplayString($columnName));
                }
                foreach ($customColumnValues as $value) {
                    $xlsWorksheet->write($xlsRowNumber, $xlsColNumber++, $value);
                }
                $xlsRowNumber++;
                break;

            case 'csv':
            default:
                foreach ($columnNames as $columnName) {
                    fwrite($fp, '"' . str_replace('"', $doubleQuoteEscapeString, $user->getColumnAsDisplayString($columnName)) . '";');
                }
                foreach ($customColumnValues as $value) {
                    fwrite($fp, '"' . str_replace('"', $doubleQuoteEscapeString, $value) . '";');
                }
                fwrite($fp, "\r\n");
                break;
            }

            if ($limitNumber == $limitCount && $limitNumber > 0) {
                break;
            }
            $limitCount++;
        }

        $semaphore->release();

        // Get return value.
        switch($formatName) {
        case 'xls':
            foreach ($xlsWorkbook->worksheets() as $xlsWorksheet) {
                // Make header row fixed when scrolling in the spreadsheet.
                $xlsWorksheet->freezePanes(array(1, 0, 1, 0));
                $xlsWorksheet->repeatRows(0);

                // Add header row.
                $xlsColNumber = 0;
                foreach ($columnNames as $columnName) {
                    $xlsWorksheet->write(0, $xlsColNumber++, ucfirst($columnName));
                }
                foreach ($customColumnNames as $columnName) {
                    $xlsWorksheet->write(0, $xlsColNumber++, ucfirst($columnName));
                }
            }

            // Write data to file.
            $xlsWorkbook->close();

            switch($returnAsMethod) {
            case 'filename':
                chmod($filename, 0777);
                $return = $filename;
                break;
            case 'string':
                $return = file_get_contents($filename);
                break;
            default:
                $return = true;
                break;
            }
            break;

        case 'csv':
        default:
            switch($returnAsMethod) {
            case 'filename':
                chmod($filename, 0777);
                $return = $filename;
                break;
            case 'string':
                fseek($fp, 0);
                $return = stream_get_contents($fp);
                break;
            default:
                $return = true;
                break;
            }
            fclose($fp);
            break;
        }

        return $return;
    }
    
    /**
     * Get the user's groups
     * @return array of Enter_Group
     */
    public function getGroups()
    {
        $sql = sprintf('SELECT gu.groupId
                          FROM groupTable g
                    INNER JOIN groupUser gu ON g.id = gu.groupId
                           AND g.customerId = %d
                           AND gu.userId = %d', $this->getCustomerId(), $this->getId());
        
        $groupIds = $this->getPearDb()->getCol($sql);
        
        $groupQuery = new Enter_Group;
        $groupQuery->customerId = $this->customerId;
        $groupQuery->columnIn('id', $groupIds);

        return $groupQuery->findAll();
    }

    /**
     * Check for group membership by getting a single group
     * @param string
     * @return Enter_Group|null
     */
    public function getGroupByName($groupName)
    {
        $groupQuery = new Enter_Group;
        $groupQuery->customerId = $this->customerId;
        $groupQuery->name = $groupName;
        $group = $groupQuery->findOne();
        return $group && $group->containsUser($this) ? $group : null;
    }

}
?>
