<?php

class Nathejk_CheckIn extends Pasta_TableRow
{

    public function getTeam()
    {
        $query = new Nathejk_Patrulje;
        $query->id = $this->teamId;
        return $query->findOne();
    }

    public function getMember()
    {
        $query = new Nathejk_Senior;
        $query->id = $this->memberId;
        return $query->findOne();
    }
}
