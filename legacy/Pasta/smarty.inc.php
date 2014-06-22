<?php

/**
 * @package Pasta
 */

/**
 * Parent base dir for Smarty's temporary files (with trailing slash).
 */
define('PASTA_SMARTY_TEMP_BASE_DIR', '/var/tmp/smarty/');

/**
 * Current base dir.
 * When moving code into production, this file should be touched. This will
 * simulate a Smarty clear. $smarty->clear_compiled_tpl() cannot be run on a
 * live system - http://www.phpinsider.com/smarty-forum/viewtopic.php?t=5822
 */
define('PASTA_SMARTY_CURRENT_TEMP_DIR', PASTA_SMARTY_TEMP_BASE_DIR . filemtime(__FILE__) . '/');

/**
 * Returns a Smarty instance with default settings for template and compile
 * directories.
 * If the query string contains SMARTY_CLEAR, the compiled templates are
 * deleted.
 * @return  Smarty
 */
function getSmarty($cacheName = null)
{
    global $CUSTOMER;

    // Smarty is not E_STRICT compatible
    $oldLevel = error_reporting();
    error_reporting($oldLevel & E_ALL);

    if (isset($_SERVER['PEYTZ_DEV'])) {
        // Look in /var/home/ib/www/smarty
        $smartyDir = $_SERVER['PEYTZ_PRODUCTS_DIR'] . 'smarty/';
        if (!is_dir($smartyDir)) {
            // Look in /var/www/test.peytz.dk/smarty
            $smartyDir = $_SERVER['PEYTZ_PRODUCTS_FALLBACK_DIR'] . 'smarty/';
        }
    } else {
        // In production, look in /var/www/virtual/smarty
        $smartyDir = dirname(__FILE__) . '/../../smarty/';
        $smartyDir = 'Smarty/';
    }
    require_once $smartyDir . 'Smarty.class.php';

    if ($oldLevel & E_STRICT) {
        require_once $smartyDir . 'Smarty_Compiler.class.php';
    }
    error_reporting($oldLevel);

    $smarty = new Smarty();

    $siteBase = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
    $siteBase = __DIR__ . '/../../sites/natpas.nathejk.dk';

    // Choose subdir based on document root. Change e.g.
    // /var/www/foobar/frontend to foobar_frontend, and
    // /home/dr/foobar/backend to dr_foobar_backend.
    // This logic should be in sync with Pasta_Debug_Error::getProductName().
    $subdir = str_replace(array('/home/', '/var/', '/www/'), '/', $siteBase);
    $subdir = str_replace('/', '_', substr($subdir, 1));
    if ($cacheName) {
        $subdir .= '_' . $cacheName;
    } elseif (!empty($CUSTOMER)) {
        // Include customer name because Kollage paths are unique per customer
        $subdir .= '_' . $CUSTOMER->name;
    }

    $smarty->_dir_perms = 0755;
    $smarty->compile_dir = PASTA_SMARTY_CURRENT_TEMP_DIR . 'templates_c/' . $subdir;
    $smarty->cache_dir   = PASTA_SMARTY_CURRENT_TEMP_DIR . 'cache/' . $subdir;
    $smarty->template_dir = $siteBase . '/templates';

    // A prefilter is always used before a template is written, so
    // use this to check that compile_dir and cache_dir exist
    $smarty->registerFilter('pre', 'pasta_smarty_prefilter');
    $smarty->use_sub_dirs = true; //assumes safe_mode is off

    //If PRODUCT is set, we export it as a smarty var.
    if (isset($GLOBALS['PRODUCT'])) {
        $smarty->assign('PRODUCT', $GLOBALS['PRODUCT']);
    }

    $isDev = isset($_SERVER['PEYTZ_DEV']);
    $smarty->compile_check = true;
    $smarty->force_compile = false;
    $smarty->debugging_ctrl =
        ($isDev || isset($_SERVER['ipPeytz'])) ? 'URL' : 'NONE';

    // Beware of functions that allow callbacks, e.g. preg_replace.
    // Make short lines so that CVS diffs are easy-to-read.
    // Avoid functions that already are Smarty modifiers, e.g. urlencode().
    static $harmlessFunctions = array(
        'array_slice', 'array_sum', 'array_unshift', 'array_shift', 'array_push', 'array_pop', 'array_combine', 'array_merge', 'array_diff',
        'array_intersect', 'array_keys', 'array_unique', 'arsort', 'asort', 'ksort', 'krsort', 'array_count_values',
        'count', 'dirname', 'basename', 'explode', 'implode', 'get_class', 'gmstrftime', 'in_array',
        'intval', 'is_a', 'is_array', 'base64_encode', 'base64_decode',
        'isset', 'ltrim', 'max', 'min', 'microtime', 'number_format', 'preg_match', 'round',
        'rtrim', 'sizeof', 'stripos', 'strtotime', 'strlen', 'strpos', 'strrpos', 'str_split', 'strrev',
        'str_pad', 'strstr', 'strrchr', 'strtolower', 'strtoupper', 'rand', 'mt_rand',
        'substr', 'time', 'trim', 'ucfirst', 'urlencode', 'urldecode', 'utf8_decode', 'uniqid', 'utf8_encode', 'ucwords',
        'var_dump', 'html_entity_decode', 'shuffle', 'array_reverse',
        'sqrt', 'serialize', 'unserialize', 'floor', 'ceil', 'abs', 'md5',
        'date', 'array_push', 'array_values','rsort', 'money_format',
        // Non-functions (used by IF_FUNCS)
        'true', 'false',
        );
        /*
    $smarty->security = true;
    // Allow inclusion of templates in other projects
    // FIXME: Prohibit inclusion on PHP files etc.
    if (isset($_SERVER['PEYTZ_DEV'])) {
        $smarty->secure_dir = array(
            $_SERVER['PEYTZ_PRODUCTS_DIR'],
            $_SERVER['PEYTZ_PRODUCTS_FALLBACK_DIR']);
    } else {
        $smarty->secure_dir = array(realpath($siteBase . '/..'));
    }
    // Kollage_Content_SmartyTemplate currently requires that PHP_TAGS is true
    $smarty->security_settings['PHP_TAGS'] = true;
    $smarty->security_settings['INCLUDE_ANY'] = false;
    $smarty->security_settings['ALLOW_CONSTANTS'] = true;
    $smarty->security_settings['MODIFIER_FUNCS'] = $harmlessFunctions;
    $smarty->security_settings['IF_FUNCS'] = $harmlessFunctions;
*/
    if (isset($_GET['SMARTY_CLEAR'])) {
        if ($isDev || isset($_SERVER['ipPeytz'])) {
            $smarty->clear_compiled_tpl();
        } else {
            print "SMARTY_CLEAR not allowed from $_SERVER[REMOTE_ADDR]\n";
        }
    }

    return $smarty;
}

function pasta_smarty_prefilter($tpl_source, $smarty)
{
    if (!is_dir($smarty->compile_dir)) {
        // Try mkdir() a few times because of http://bugs.php.net/35326
        for ($i = 0; $i < 3 && !is_dir($smarty->compile_dir); $i++) {
            $ok = @mkdir($smarty->compile_dir, 0755, true);
            if (!$ok) {
                // Sleep between 1 ms and 10 ms
                usleep(rand(1000, 10000));
            }
        }
        if (!is_dir($smarty->compile_dir)) {
            trigger_error('Could not create compile dir:' . $smarty->compile_dir,
                          E_USER_WARNING);
        }
    }
    /*
     * Smarty::_compile_source currently does not set cache_dir on the
     * smarty object passed to prefilters
    if (!is_dir($smarty->cache_dir)) {
        mkdir($smarty->cache_dir, 0755, true);
    }
    */

    return $tpl_source;
}

?>
