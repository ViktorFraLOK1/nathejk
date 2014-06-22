<?php

abstract class Nathejk_Team extends Pasta_TableRow
{
    const STATUS_NEW  = 'NEW';
    const STATUS_HOLD = 'HOLD';
    const STATUS_PAY  = 'PAY';
    const STATUS_PAID = 'PAID';
    const STATUS_STOP = 'STOP';
    const STATUS_QUIT = 'QUIT';

    public function getAllSignupStatusTypes()
    {
        return array(
            self::STATUS_NEW  => "tilmelding påbegyndt",
            self::STATUS_HOLD => "på venteliste",
            self::STATUS_PAY  => "afventer betaling",
            self::STATUS_PAID => "tilmeldt",
            self::STATUS_STOP => "afmeldt af Nathejk",
            self::STATUS_QUIT => "afmeldt selv",
        );
    }

    public function save()
    {
        if (!isset($this->allSignupStatusTypes[$this->signupStatusTypeName])) {
            $this->signupStatusTypeName = self::STATUS_NEW;
        }
        if (intval($this->getColumn('memberCount')) && $this->signupStatusTypeName == Nathejk_Team::STATUS_NEW) {
            $this->signupStatusTypeName = Nathejk_Team::STATUS_PAY;
        }
        return parent::save();
    }
    
    public function getAgenda()
    {
        $query = new Nathejk_Agenda;
        return $query->findOne();
    }

    /**
     * A heelper function for display functions
     * @param string $columnName
     * @param string $context
     */
    public function getDisplayColumn($columnName, $context = 'search')
    {
        // See if a display method has been created for the column
        $displayMethod = 'getDisplay' . ucfirst($columnName);
        if (method_exists($this, $displayMethod)) {
            return $this->$displayMethod();
        }

        // If the column is a calculated a get method for it should exists
        $getMethod = 'get' . ucfirst($columnName);
        if (method_exists($this, $getMethod)) {
            return $this->$getMethod();
        }

        // It could be a object itself
        if ($this->columnExists($columnName)) {
            return $this->getColumn($columnName);
        }

        return '';
    }

    public function getClassNameByRow($row)
    {
        switch ($row['typeName']) {
            case 'patrulje' :
                return 'Nathejk_Patrulje';
            
            case 'post' :
            case 'post1' :
            case 'post2' :
            case 'oplev' :
            case 'start' :
            case 'slut' :
                return 'Nathejk_Post';

            case 'lok' :
                return 'Nathejk_Lok';

            case 'klan' :
            case 'super' :
            default : 
                return 'Nathejk_Klan';
        }
    }

    public function getNextId($sequence = '', $firstId = 1)
    {
        if ($sequence == $this->getDefaultTable()) {
            $id = parent::getNextId($sequence, max(date('Y') * 1000, $firstId));
            return $id;
        }
        return parent::getNextId($sequence, $firstId);
    }

    public function getChecksum()
    {
        return substr(md5($this->id . '**@'), -5);
    }

    public function getPhoto()
    {
        $q = new Nathejk_Photo;
        $q->id = $this->photoId;
        return $q->findOne();
    }

    public function getParentTeam()
    {
        $className = get_class($this);
        $q = new $className;
        $q->id = $this->parentTeamId;
        return $q->findOne();
    }

    public function getUrl()
    {
        $id = $this->parentTeamId ? $this->parentTeamId : $this->id;
        return "/team.php?id=$id";
    }

    public function getAllKorps()
    {
        return array(
            'dds' => "Det Danske Spejderkorps",
            'kfum' => "KFUM-Spejderne",
            'kfuk' => "De grønne pigespejdere",
            'dbs' => "Danske Baptisters Spejderkorps",
            'dgs' => "De Gule Spejdere",
            'dss' => "Dansk Spejderkorps Sydslesvig",
            'fdf' => "FDF / FPF",
            'andet' => "Andet",
        );
    }
    public function getAllLoks()
    {
        return array();
    }

    public function getAllArrivalTitles()
    {
        return array();
    }
    public function getKorpsTitle()
    {
        if (!array_key_exists($this->korps, $this->allKorps)) {
            return '-';
        }
        return $this->allKorps[$this->korps];
    }
    
    public function getActiveMembers()
    {
        $query = new $this->memberClassName;
        $query->teamId = $this->id;
        $query->pausedUts = 0;
        $query->discontinuedUts = 0;
        $query->deletedUts = 0;
        return $query->findAll();
    }
    public function getActiveMemberCount()
    {
        return count($this->activeMembers);
    }

    public function getStartMemberCount()
    {
        return count($this->members);
    }
    public function getMemberCount() {
        return max($this->minMemberCount, intval($this->getColumn('memberCount')));
    }
    public function getRequestedMemberCount() {
        return intval($this->getColumn('memberCount'));
    }

    public function getHostname()
    {
        return gethostbyaddr($this->ip);
    }
    

    public function getTotalPrice()
    {
        return $this->memberCount * $this->memberPrice;
    }
    abstract public function getMemberPrice();
    abstract public function getFrontendUrl();
    abstract public function getMemberClassName();

    public function getPayments()
    {
        $query = new Nathejk_Payment;
        $query->teamId = $this->id;
        $query->setOrderBySql('uts');
        return $query->findAll();
    }
    public function getPaidPrice()
    {
        $paid = 0;
        foreach ($this->payments as $payment) {
            $paid += $payment->amount;
        }
        return $paid;
    }  
    public function getUnpaidPrice()
    {
        return intval($this->totalPrice) - intval($this->paidPrice);
    }
    public function addPayment($paid, $uts, $memberCount = 0)
    {
        $payment = new Nathejk_Payment;
        $payment->teamId = $this->id;
        $payment->amount = $paid;
        //$payment->paidMemberCount = intval($payment->paidMemberCount) + $memberCount;
        $payment->uts = $uts;
        $payment->save();
        if ($this->unpaidPrice <= 0) {
            $this->signupStatusTypeName = Nathejk_Team::STATUS_PAID;
            $this->paidMemberCount = intval($this->paidMemberCount) + $memberCount;
            $this->save();
        }
    }
    public function getUnpaidMemberCount()
    {
        return $this->memberCount - $this->paidMemberCount;
    }

    public function getMemberById($id)
    {
        $query = new $this->memberClassName;
        $query->id = $id;
        $query->teamId = $this->id;
        return $query->findOne();
    }
    public function getMemberByIdOrNew($id)
    {
        if ($member = $this->getMemberById($id)) {
            return $member;
        }
        if (count($this->members) < $this->requestedMemberCount) {
            $member = new $this->memberClassName;
            $member->teamId = $this->id;
            $member->createdUts = time();
            return $member;
        }
        return null;
    }
    public function getMemberByIndex($i) {
        $members = $this->members;
        if (isset($members[$i])) return $members[$i];
        if ($i < $this->memberCount) return new $this->memberClassName;
        return null;
    }

    public function getMembersInfoText()
    {
        $plain = '';
        foreach ($this->members as $i => $member) {
            $plain .= "Deltager " . ($i + 1) . ":\n" . $member->infoText . "\n";
        }
        return $plain;
    }

    public function getTeams()
    {
        $q = new Nathejk_Patrulje;
        $q->parentTeamId = $this->id;
        return $q->findAll();
        return array();
    }
    public function getMembers($onlyActive = true)
    {
        $query = new $this->memberClassName;
        $query->teamId = $this->id;
        if ($onlyActive) {
            //$query->pausedUts = 0;
            //$query->discontinuedUts = 0;
            $query->deletedUts = 0;
        }
        $query->setOrderBySql('id');
        //$query->setLimit($this->memberCount);
        return $query->findAll();
    }

    public function getPausedMembers()
    {
        $query = new $this->memberClassName;
        $query->teamId = $this->id;
        $query->discontinuedUts = 0;
        $query->deletedUts = 0;
        $query->addWhereSql('pausedUts > 0');
        return $query->findAll();
    }

    public function getDiscontinuedMembers()
    {
        $query = new $this->memberClassName;
        $query->teamId = $this->id;
        $query->deletedUts = 0;
        $query->addWhereSql('discontinuedUts > 0');
        return $query->findAll();
    }

    public function getDeletedMembers()
    {
        $query = new $this->memberClassName;
        $query->teamId = $this->id;
        $query->addWhereSql('deletedUts > 0');
        return $query->findAll();
    }


    public function addMember($member) 
    {
        if (count($this->members) < $this->maxMemberCount) {
            //return false;
        }
        $member->teamId = $this->id;
        $member->createdUts = time();
        return $member->save();
    }
    public function deleteMembers(array $ids)
    {
        foreach ($ids as $id) {
            if ($member = $this->getMemberById($id)) {
                $member->delete();
            }
        }
    }

    public function delete()
    {
        if (!$this->deletedUts) {
            $this->deletedUts = time();
            return $this->save();
        }
        return false;
    }

    public function getCheckInByTypeName($typeName)
    {
        $query = new Nathejk_CheckIn;
        $query->teamId = $this->id;
        $query->typeName = $typeName;
        return $query->findOne();
    }

    public function getCheckIns()
    {
        $query = new Nathejk_CheckIn;
        $query->teamId = $this->id;
        $query->setOrderBySql('id DESC');
        return $query->findAll();
    }

    public function getMail()
    {
        $mail = new Nathejk_Mail;
        $mail->teamId = $this->id;
        return $mail;
    }

    public function getMails()
    {
        $query = new Nathejk_Mail;
        $query->teamId = $this->id;
        return $query->findAll();
    }

    public function sendMail($subject, $textBody, $sendToAll = false)
    {
        $mail = $this->mail;
        $mail->subject = $subject;
        $mail->body = $textBody;
        $rcpts = array("{$this->contactTitle} <{$this->contactMail}>");
        if ($sendToAll) {
            foreach ($this->members as $member) {
                if ($member->mail) {
                    $rcpts[] = "{$member->title} <{$member->mail}>";
                }
            }
        }
        $mail->rcpts = $rcpts;
        return $mail->send();
    }
    
    public function getPhoneVerifyCode()
    {
        $number = intval($this->createdUts) - intval($this->contactPhone);
        return substr($number, -4);
    }

    public function sendVerifyPhoneSMS()
    {
        // The neccesary variables are set. 
        $url = "http://www.cpsms.dk/sms/"; 
        $url .= "?message=" . urlencode("Din aktiveringskode til Nathejktilmeldingen er: " . $this->phoneVerifyCode); 
        $url .= "&recipient=45" . $this->contactPhone; // Recipient 
        $url .= "&username="; // Username 
        $url .= "&password="; // Password 
        $url .= "&from=" . urlencode("Nathejk"); // Sendername
        // The url is opened 
        $reply = file_get_contents($url);
    }
}

?>
