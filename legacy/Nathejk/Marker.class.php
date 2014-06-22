<?php

class Nathejk_Marker extends Pasta_TableRow
{
    public static function getColorNames()
    {
        return array(
            'purple' => '8c4eb8', 
            'pink' => 'c259b6', 
            'lightred' => 'f34648', 
            'darkred' => 'c03639', 
            'lightblue' => '3875d7', 
            'darkblue' => '265bb2', 
            'cyan' => '5ec8bd', 
            'darkgreen' => '128e4e', 
            'lightgreen' => '67c547', 
            'yellow' => 'ffc01f', 
            'oramge' => 'ff8922', 
            'brown' => '9d7050', 
            'grey' => 'a8a8a8', 
            'white' => 'ffffff', 
            'black' => '000000',
        );
    }
    public static function getIconNames()
    {
        return array('home-2', 'regroup', 'radio-station-2', 'scoutgroup', 'pirates', 'start-race-2');
    }

    public function getIconUrl()
    {
        return '/gmap/icons/demo/' . $this->iconName . '.png';
    }
}

?>
