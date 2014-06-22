<?php

class Nathejk_Post extends Nathejk_Team
{
    public function getMemberIds()
    {
        $query = new Nathejk_Senior;
        $query->teamId = $this->id;
        return $query->findColumn('id');
    }

    public function getCatchCount()
    {
        $query = new Nathejk_CheckIn;
        $query->columnIn('memberId', $this->memberIds);
        //$query->columnIn('typeName', array('qr', 'sms', 'web'));
        return $query->countAll();
        
    }

    public function getContactedTeams()
    {
        $query = new Nathejk_CheckIn;
        $query->columnIn('memberId', $this->memberIds);
        //$query->columnIn('typeName', array('qr', 'sms', 'web'));
        $teamIds = $query->findColumn('teamId');
        $query = new Nathejk_Patrulje;
        $query->columnIn('id', $teamIds);
        return $query->findAll();
    }
    public function getBeforeTeams()
    {
        $query = new Nathejk_Patrulje;
        $query->typeName = 'patrulje';
        $query->deletedUts = 0;
        $query->signupStatusTypeName = 'PAID';

        return $query->countAll() - $this->catchCount;

        $query = new Nathejk_CheckIn;
        $query->columnIn('memberId', $this->memberIds);
        //$query->columnIn('typeName', array('qr', 'sms', 'web'));
        return $query->countAll();
    }
    public function getBeforeCount()
    {
        $query = new Nathejk_Patrulje;
        $query->typeName = 'patrulje';
        $query->deletedUts = 0;
        $query->signupStatusTypeName = 'PAID';
        return $query->countAll() - $this->catchCount;

        $query = new Nathejk_CheckIn;
        $query->columnIn('memberId', $this->memberIds);
        //$query->columnIn('typeName', array('qr', 'sms', 'web'));
        return $query->countAll();
    }

    public function getInsideCount()
    {
        $query = new Nathejk_CheckIn;
        $query->columnIn('memberId', $this->memberIds);
        $query->outUts = 0;
        return $query->countAll();
        
    }

    public function getAfterCount()
    {
        $query = new Nathejk_CheckIn;
        $query->columnIn('memberId', $this->memberIds);
        $query->addWhereSql('outUts > 0');
        return $query->countAll();
    }

    public function getTotalPrice()
    {}
    public function getFrontendUrl()
    {}
    public function getMemberClassName()
    {
        return 'Nathejk_Senior';
    }
    public function getMemberPrice()
    {
        return 0;
    }
}

?>
