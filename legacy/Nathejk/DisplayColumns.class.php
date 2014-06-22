<?php

/**
 * @package Sbs
 */
class Nathejk_DisplayColumns
{
    protected static $views = array(
        'Spejder check-in' => array(
            'spejderCheckIn',
        ),
        'Senior' => array(
            'senior',
        ),
        'Kontrolposter' => array(
            'kontrol',
        ),
        'Simpel' => array(
            'basic',
        ),
        'Kontaktoplysninger' => array(
            'basic',
            'contact',
        ),
        'Tilmeldingsoplysninger' => array(
            'basic',
            'signup',
        ),
        'Betalingsoplysninger' => array(
            'basic',
            'paid',
        ),
        'Alle' => array(
            'all',
        ),
    );
    
    public static function getDefaultViewName()
    {
        return 'Simpel';
    }
    
    public static function getViewNames()
    {
        return array_keys(self::$views);
    }
    
    public static function getColumnNamesByViewName($viewName)
    {
        if (!isset(self::$views[$viewName])) {
            return false;
        }
        
        return self::getColumnNamesByGroups(self::$views[$viewName]);
    }
    
    public static function getColumnNamesByGroups($columnGroups)
    {
        $columns = array();
        
        if (is_string($columnGroups)) {
            $columnGroups = array($columnGroups);
        }
        
        foreach ($columnGroups as $columnGroup) {
            $methodName = 'get' . ucfirst($columnGroup) . 'Columns';
            if (method_exists(__CLASS__, $methodName)) {
                $columns = array_merge($columns, self::$methodName());
            } else {
                trigger_error('DisplayColumns does not define colunm group: ' . $columnGroup);
            }
            
        }
        
        return $columns;
    }
    
    public static function getAllColumns()
    {
        $all = array(
            'id' => 'Id',
            'teamNumber'   => 'Patruljenummer',
            'ligaNumber'   => 'Adv. spejderliganummer',
            'lokNumber'   => 'LOK-nummer',
            'title'         => 'Holdnavn',
            'gruppe'   => 'Gruppe og division',
            'korps'          => 'Korps',
            'contactTitle'         => 'Tilmelder navn',
            'contactAddress'         => 'Tilmelder adresse',
            'contactPostalCode'         => 'Tilmelder postnr.',
            'contactMail'   => 'Tilmelder e-mail',
            'contactPhone'          => 'Tilmelder telefonnummer',
            'createdUts'   => 'Tilmelding startet',
            'openedUts'       => 'Tilmelding sluttet',
            'memberCount'       => 'Ønskede antal medlemmer',
            'activeMemberCount'       => 'Indtastede medlemmer',
            'typeName' => 'Type',
            'catchCount' => 'Antal fangster',
            'unpaidPrice'               => 'Betalt',
            'photoId'               => 'Photo',
        );
        
        return $all;
    }
    
    public static function getKontrolColumns()
    {
        return array(
            'title'         => 'Klannavn',
            'beforeCount'   => 'Ikke igennem',
            'insideCount'          => 'På posten',
            'afterCount'          => 'Videre',
        );
    }
    public static function getBasicColumns()
    {
        return array(
            'id'   => 'Id',
            'teamNumber'   => 'Patruljenummer',
            'title'         => 'Klannavn',
            'gruppe'   => 'Gruppe og division',
            'korps'          => 'Korps',
        );
    }
    public static function getSpejderCheckInColumns()
    {
        return array(
            'teamNumber'   => 'Patruljenummer',
            'memberCount'       => 'Antal startet',
            'activeMemberCount'       => 'Antal aktive',
            'catchCount' => 'Antal fangster',
            'title'         => 'Patruljenavn',
            'gruppe'   => 'Gruppe og division',
            'photoId'          => 'Photo',
        );
    }
    public static function getSeniorColumns()
    {
        return array(
            'lokNumber'   => 'LOK',
            'catchCount' => 'Antal fangster',
            'title'         => 'Klannavn',
            'gruppe'   => 'Gruppe og division',
            'memberCount'       => 'Antal startet',
            'advSpejdNightCount'       => 'Spejdernes Lejr',
        );
    }
    
    public static function getContactColumns()
    {
        return array(
            'contactTitle'         => 'Tilmelder',
            'contactMail'   => 'E-mail',
            'contactPhone'          => 'Telefonnummer',
        );
    }
    
    public static function getSignupColumns()
    {
        return array(
            'createdUts'   => 'Tilmelding startet',
            'openedUts'       => 'Tilmelding sluttet',
            'memberCount'       => 'Antal seniorer',
            'activeMemberCount'       => 'Indtastede seniorer',
            'typeName' => 'Type',
        );
    }
    
    public static function getPaidColumns()
    {
        return array(
            'memberCount'       => 'Antal seniorer',
            'activeMemberCount'       => 'Indtastede seniorer',
            'unpaidPrice'               => 'Betalt',
            'checkedAtStart'               => 'Startet',
        );
    }
    
}

?>
