<?php

class Nathejk_Agenda extends Pasta_TableRow
{
    public function getTeamById($teamId)
    {
        $query = new Nathejk_Klan;
        $query->id = $teamId;
        $team = $query->findOne();
        if ($team) {
            return $team;
        }
        $query = new Nathejk_Patrulje;
        $query->id = $teamId;
        $team = $query->findOne();
        return $team;
    }

    public function getSeniorMembers()
    {
        $query = new Nathejk_Klan;
        $query->typeName = 'klan';
        $klanIds = $query->findColumn('id');
        $query = new Nathejk_Senior;
        $query->columnIn('teamId', $klanIds);
        return $query->findAll();
    }

    public function getUnmarkedPhotos()
    {
        $query = new Nathejk_Photo;
        $query->memberId = 0;
        $query->teamId = 0;
        $query->deleteUts = 0;
        return $query->findAll();
    }

    public function getActiveTeamIds()
    {
        $query = new Nathejk_Patrulje;
        $query->addWhereSql('startUts > 0');
        $query->parentTeamId = 0;
        $query->finishUts = 0;
        $query->deletedUts = 0;
        return $query->findColumn('id');
    }
    public function getActiveTeams()
    {
        $query = new Nathejk_Patrulje;
        $query->columnIn('id', $this->activeTeamIds);
        $query->setOrderBySql('teamNumber');
        return $query->findAll();
    }
    public function getBingoTeams()
    {
        $bingo = array();
        foreach ($this->activeTeams as $team) {
            if ($team->catchCount == 0) {
                $bingo[] = $team;
            }
        }
        return $bingo;
    }
    public function getPausedMembers()
    {
        $query = new Nathejk_Spejder;
        $query->columnIn('teamId', $this->activeTeamIds);
        $query->addWhereSql('pausedUts > 0');
        $query->discontinuedUts = 0;
        $query->deletedUts = 0;
        $query->setOrderBySql('pausedUts');
        return $query->findAll();
    }

    public function getDiscontinuedMembers()
    {
        $query = new Nathejk_Spejder;
        $query->columnIn('teamId', $this->activeTeamIds);
        $query->addWhereSql('discontinuedUts > 0');
        $query->deletedUts = 0;
        $query->setOrderBySql('discontinuedUts');
        return $query->findAll();
    }

    public function getTeamsNotSeenOn($post)
    {
        $q = new Nathejk_Post;
        $q->typeName = $post;
        $teamIds = $q->findColumn('id');
        $q = new Nathejk_Senior;
        $q->columnIn('teamId', $teamIds);
        $memberIds = $q->findColumn('id');
        $q = new Nathejk_CheckIn;
        $q->columnIn('memberId', $memberIds);
        $caughtTeamIds = $q->findColumn('teamId', $memberIds);
        $notFoundIds = array_diff($this->activeTeamIds, $caughtTeamIds);
        $q = new Nathejk_Patrulje;
        $q->columnIn('id', $notFoundIds);
        $q->setOrderBySql('teamNumber');
        return $q->findAll();
    }

    public function isOpenForSeniorSignup()
    {
        return $this->signupSeniorOpen && ($this->maxSeniorMemberCount > count($this->seniorMembers));
    }
}

?>
