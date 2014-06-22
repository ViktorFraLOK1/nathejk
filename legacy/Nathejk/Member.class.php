<?php

abstract class Nathejk_Member extends Pasta_TableRow
{
    public function getClassNameByRow($row)
    {
        $query = new Nathejk_Klan;
        $query->id = $row['teamId'];
        $team = $query->findOne();
        if (!$team) return 'Nathejk_Guide';

        switch ($team->typeName) {
            case 'patrulje' :
                return 'Nathejk_Spejder';
            
            case 'start' :
                return 'Nathejk_Start';

            case 'slut' :
                return 'Nathejk_Slut';

            case 'lok' :
            case 'klan' :
                return 'Nathejk_Senior';

            case 'post' :
            case 'guide' :
            default : 
                return 'Nathejk_Guide';
        }
    }

    public function getTeam()
    {
        $query = new Nathejk_Klan;
        $query->id = $this->teamId;
        return $query->findOne();
    }

    public function getPhoto()
    {
        $query = new Nathejk_Photo;
        $query->id = $this->photoId;
        return $query->findOne();
    }

    public function getPhotoUrl()
    {
        return "/photo/{$this->team->id}:{$this->team->checksum}:{$this->id}";
    }

    public function getInfo()
    {
        return array(
            'Navn' => $this->title,
            'Adresse' => $this->address,
            'Postnr.' => $this->postalCode,
        );
    }
    
    public function scan(Nathejk_Team $team, $location)
    {
        foreach ($team->teams as $t) {
            $this->scan($t, $location);
        }
        $capture = new Nathejk_CheckIn;
        $capture->teamId = $team->id;
        $capture->memberId = $this->id;
        $capture->location = $location;
        $capture->createdUts = time();
        $capture->typeName = 'qr';
        $capture->isCaught = $this->isBandit() ? 1 : 0;
        return $capture->save();
        //$team = $capture->team;
    }

    public function getStatus()
    {
        if ($this->deletedUts) {
            return 'deleted';
        }
        if ($this->discontinuedUts) {
            return 'discontinued';
        }
        if ($this->pausedUts) {
            return 'paused';
        }
        return 'active';
    }

    public function setStatus($status)
    {
        switch ($status) {
        case 'deleted' :
            if (!$this->deletedUts) $this->deletedUts = time();
            break;
        case 'discontinued' :
            $this->deletedUts = 0;
            if (!$this->discontinuedUts) $this->discontinuedUts = time();
            break;
        case 'paused' :
            $this->deletedUts = 0;
            $this->discontinuedUts = 0;
            if (!$this->pausedUts) $this->pausedUts = time();
            break;
        case 'active' :
            $this->deletedUts = 0;
            $this->discontinuedUts = 0;
            $this->pausedUts = 0;
            break;
        }
    }
    public function isBandit()
    {
        return false;
    }
}

?>
