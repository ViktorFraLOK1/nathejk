<?php

/** Stuff to do for all projects on startup
 * @package Pasta
 */

/**
 * Finds the file containing the specified class and includes it. No error is
 * triggered, if the class is not found. Unserializing an unknown class will
 * work, even though the class definition cannot be found.
 * @param  string
 */
function pasta_autoload($className)
{
    $tmp = explode('_', $className, 2);
    if (sizeof($tmp) != 2) {
        // We don't support autoloading of classes that do not contain _
        return;
    }

    // check for product name definition
    if (!defined('VIP_PRODUCT_NAME')) {
        if (isset($_SERVER['PEYTZ_DEV']) && !isset($_SERVER['PEYTZ_TEST'])) {
            trigger_error('VIP_PRODUCT_NAME not defined. Do that before autoload.inc.php is included/required.');
        }
        define('VIP_PRODUCT_NAME', 'UNKNOWN');
    }

    // Convert FooBar_Baz_Gazonk to
    // $projectName = foo-bar
    // $filename = Bar/Gazonk.class.php
    $fileName = strtr($tmp[1], '_', '/') . '.class.php';
    $productName = strtolower(preg_replace('/([a-z0-9])([A-Z])/', '\1-\2', $tmp[0]));

    // Where should we look for the class FooBar_Baz_Gazonk
    if ($productName == VIP_PRODUCT_NAME) {
        // Use /var/home/ib/www/foo-bar/include/Baz/Gazonk.class.php.
        // Look relative to document root to allow projects in non-standard
        // directories to include their own files, e.g.
        // /var/home/ib/www/foo-bar-2/include/Baz/Gazonk.class.php).
        $productDir = $_SERVER['DOCUMENT_ROOT'] . '/..';

    } elseif (isset($_SERVER['PEYTZ_DEV']) && !isset($_SERVER['PEYTZ_TEST'])) {
        // Use /var/home/ib/www/foo-bar/include/Baz/Gazonk.class.php - or
        // /var/www/test.peytz.dk/foo-bar/include/Baz/Gazonk.class.php if the
        // developer hasn't checked out the project.
        // Do not look relative to __FILE__, because __FILE__ may be in
        // PEYTZ_PRODUCTS_FALLBACK_DIR.
        $productDir = $_SERVER['PEYTZ_PRODUCTS_DIR'] . $productName;

        if (!is_dir($productDir)) {
            $productDir = $_SERVER['PEYTZ_PRODUCTS_FALLBACK_DIR'] .
                $productName;
        }
    } elseif (preg_match('/^(drupal|pressflow)\d/', VIP_PRODUCT_NAME)) {
        $productDir = $_SERVER['DOCUMENT_ROOT'] . '/../' . $productName;

    } else {
        // Look relative to current project, i.e. use
        // /var/www/virtual/foo-bar/include/Baz/Gazonk.class.php if
        // DOCUMENT_ROOT is e.g. /var/www/virtual/snaps/frontend
        $productDir = $_SERVER['DOCUMENT_ROOT'] . '/../../' . $productName;
    }

    $includeFile = realpath($productDir . '/include/' . $fileName);
    $includeFile = ucfirst($productName) . "/$fileName";
    if ($includeFile && $productName != 'smarty') {
        include $includeFile;
    }
}

spl_autoload_register('pasta_autoload');

// If we use a reverse proxy the IP address needs to be corrected.
$pastaIp = Pasta_Ip::getByRemoteAddress();
$_SERVER['REMOTE_ADDR'] = $pastaIp->getAddress();

$trustedAddresses = array(
    '80.199.116.190', // Rentemestervej, gateway
    '62.243.131.41',  // Rentemestervej, dev.peytz.dk
    '81.7.134.249',   // nagios.peytz.dk
    '127.0.0.1',      // cronjobs etc.
    );
if (in_array($_SERVER['REMOTE_ADDR'], $trustedAddresses)
    || preg_match('/^10\./', $_SERVER['REMOTE_ADDR'])) {

    $_SERVER['ipPeytz'] = '1';
}

// On dev, this is set already in /var/www/AUTO_PREPEND.inc
if (!isset($_SERVER['PASTA_REQUEST_START_UTS'])) {
    $_SERVER['PASTA_REQUEST_START_UTS'] = microtime(true);
}

?>
