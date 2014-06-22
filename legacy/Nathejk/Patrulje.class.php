<?php

class Nathejk_Patrulje extends Nathejk_Team
{
    public function __construct()
    {
        $this->typeName = 'patrulje';
    }

    public function getAllArrivalTitles()
    {
        return array(
            'tog1942' => 'Vi ankommer med toget kl. 19:42',
            'tog2012' => 'Vi ankommer med toget kl. 20:12',
            'tog2130' => 'Vi ankommer med toget kl. 21:30',
            'bil' => 'Vi bliver kørt',
            'andet' => 'Andet',
        );
    }

    public static function getByIdAndChecksum($id, $cs)
    {
        $query = new self;
        $query->id = $id;
        $query->deletedUts = 0;
        if ($team = $query->findOne()) {
            if ($team->checksum == $cs) {
                return $team;
            }
        }
        return null;
    }

    public function sendInvite()
    {
        //$url = "http://{$_SERVER['HTTP_HOST']}/spejder?id=$this->id&cs=$this->checksum";
        $message = "For at starte tilmeldingen skal du følge nedenstående link\n\n{$this->frontendUrl}\n\nMed venlig hilsen\nNathejk";
        
        return $this->sendMail('Spejdertilmelding', $message);
    }

    public function sendConfirm()
    {
        $query = new Nathejk_MailTemplate;
        $query->id = 6;
        $template = $query->findOne();
    
        return $this->sendMail($template->subject, $template->body);
    }

    public function getFrontendUrl()
    {
        return "http://{$_SERVER['HTTP_HOST']}/spejder/$this->id:$this->checksum";
    }

    public function getMemberClassName()
    {
        return 'Nathejk_Spejder';
    }

    public function getMemberPrice()
    {
        return 150;
    }

    public function getMinMemberCount()
    {
        return 3;
    }
    public function getMaxMemberCount()
    {
        return 7;
    }
/*    public function getMemberCount()
    {
        return count($this->members);
    }
*/
    public function getCatchCount()
    {
        $query = new Nathejk_CheckIn;
        $query->teamId = $this->id;
        $query->addWhereSql("typeName != 'qr-fail'");
        //$query->columnIn('typeName', array('qr', 'sms', 'web'));
        $query->isCaught = 1;
        return $query->countAll();
    }
    public function getContactCount()
    {
        $query = new Nathejk_CheckIn;
        $query->teamId = $this->id;
        $query->addWhereSql("typeName != 'qr-fail'");
        return $query->countAll();
    }
    public function getNoticeText()
    {
        if ($this->teams) {
            $activeCount = $this->activeMemberCount;
            $armNumbers = array();
            foreach ($this->teams as $team) {
                $activeCount += $team->activeMemberCount;
                $armNumbers[] = $team->armNumber;
            }
            return "Patruljen er slået sammen med " . implode(' og ', $armNumbers) . " - de skal være i alt $activeCount spejdere";
        } else if ($this->activeMemberCount != $this->startMemberCount) {
            return "Patruljen er reduceret til {$this->activeMemberCount} spejdere";
        }
        return '';
    }

    public function getTeamNumber()
    {
        if (!$number = $this->getColumn('teamNumber')) {
            return '';
        }
        return $number;
    }

    public function getArmNumber()
    {
        if (intval($this->teamNumber) > 0) {
            return "{$this->teamNumber}-{$this->startMemberCount}";
        }
        return '';
    }

    public function save()
    {
        if (!$this->teamNumber && $this->signupStatusTypeName == Nathejk_Team::STATUS_PAID) {
            $this->teamNumber = $this->getNextId('teamNumber' . date('Y'));
        }
        return parent::save();
    }
    public function getIsReadyToStart()
    {
        if ($this->getUnpaidPrice > 0) {
            return false;
        }
        foreach ($this->members as $member) {
            if (!$member->contactSms) {
                return false;
            }
        }
        return true;
    }
}

?>
