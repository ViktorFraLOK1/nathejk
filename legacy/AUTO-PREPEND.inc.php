<?php
/**
 * @package Nathejk
 */

define('VIP_PRODUCT_NAME', 'nathejk');
require_once 'Pasta/errorHandling.inc.php';
require_once 'Pasta/autoload.inc.php';
require_once 'Pasta/smarty.inc.php';
require_once 'Pasta/db.inc.php';

/* Set locale to Dutch */
setlocale(LC_TIME, 'da_DK');
$CUSTOMER = Vip_Customer::getByName('nathejk');
$PRODUCT = $CUSTOMER->getProductByName(VIP_PRODUCT_NAME);

    header("Content-Type: text/html; charset=utf-8");

    $USER = Enter_Validator::getCurrentUser();

    if (!$USER && !strpos($_SERVER['PHP_SELF'], 'natpas.pdf')) {
        if (getenv('login') == 1 && substr($_SERVER['PHP_SELF'], -9) != 'login.php') {
            Pasta_Http::exitWithRedirect('/login.php?goto=' . urlencode($_SERVER['REQUEST_URI']));
        }

    }

$query = new Nathejk_Agenda;
$Agenda = $query->findOne();
unset($query);

function e($string) {
    return htmlentities($string, ENT_COMPAT, 'UTF-8');
    return htmlentities($string, ENT_COMPAT | ENT_HTML5, 'UTF-8');
}

?>
