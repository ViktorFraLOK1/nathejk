<?php

class Nathejk_Lok extends Nathejk_Team
{
    public function getMemberIds()
    {
        $query = new Nathejk_Lok;
        $query->lokNumber = $this->lokNumber;
        $query->deletedUts = 0;
        $query->columnIn('typeName', array('lok', 'klan', 'super'));
        $teamIds = $query->findColumn('id');

        $query = new Nathejk_Senior;
        $query->columnIn('teamId', $teamIds);
        $query->deletedUts = 0;
        return $query->findColumn('id');
    }

    public function getTeams()
    {
        $q = new Nathejk_Klan;
        $q->typeName = 'klan';
        $q->lokNumber = $this->lokNumber;
        return $q->findAll();
    }

    public function igetActiveMembers()
    {
        $query = new Nathejk_Lok;
        $query->lokNumber = $this->lokNumber;
        $query->deletedUts = 0;
        $query->columnIn('typeName', array('lok', 'klan', 'super'));
        $teamIds = $query->findColumn('id');

        $query = new Nathejk_Senior;
        $query->columnIn('teamId', $teamIds);
        $query->deletedUts = 0;
        $query->setOrderBySql('number, teamId, id');
        return $query->findAll();


        $query = new $this->memberClassName;
        $query->teamId = $this->id;
        if ($onlyActive) {
            //$query->pausedUts = 0;
            //$query->discontinuedUts = 0;
            $query->deletedUts = 0;
        }
        $query->setOrderBySql('id');
        return $query->findAll();
    }

    public function getMemberCount()
    {
        return count($this->memberIds);
    }
    public function getCatchCount()
    {
        $query = new Nathejk_CheckIn;
        $query->columnIn('memberId', $this->memberIds);
        return $query->countAll();
        
    }

    public function getMemberPrice()
    {
        return 0;
    }
    public function getTotalPrice()
    {}
    public function getFrontendUrl()
    {}
    public function getMemberClassName()
    {
        return 'Nathejk_Senior';
    }
    
    public function getMaxMemberCount()
    {
        return 28;
    }
}

?>
