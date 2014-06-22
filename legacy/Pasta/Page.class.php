<?php
require_once 'Pasta/smarty.inc.php';

/**
 * @package Pasta
 */

class Pasta_Page {

    protected $smarty;
    protected $title;

    protected $errors;
    protected $warnings;
    protected $successes;

    public $headerTemplatePath = null;
    public $footerTemplatePath = null;
    public $headerFooter = true; // set to false to avoid header and footer

    public function __construct()
    {
        $this->smarty = getSmarty();
        $this->smarty->registerFilter('pre', array('Pasta_Page', 'smartySafeLeftBracket'));

        $this->headerTemplatePath = 'header.tpl';
        $this->footerTemplatePath = 'footer.tpl';
        
        $this->errors = array();
        $this->warnings = array();
        $this->successes = array();
    }


    /**
     * allow javascript without using {ldelim}
     * @param  string
     * @return string
     */
    public static function smartySafeLeftBracket($tpl_source, $smarty)
    {
        // Do not interpret { in e.g. CSS and Javascript as a Smarty tag,
        // but escape it as {ldelim}, except in {literal}- and {php}-tags
        $parts = preg_split('@({/?literal}|{/?php})@', $tpl_source, -1, PREG_SPLIT_DELIM_CAPTURE);

        $source = '';
        $isLiteral = false;
        foreach ($parts as $part) {
            if ($isLiteral) {
                $source .= $part;
            } else {
                $source .= preg_replace('/{(?=\s)/', '{ldelim}', $part);
            }
            $isLiteral = ($part == '{literal}' || $part == '{php}');
        }

        return $source;
    }


    /**
     * Returns a Smarty resource name including resouce type, i.e. starting
     * with either "file:" or "kollage:".
     *
     * Usage:
     * getTemplatePath()             - look for .tpl in same directory as PHP
     *                                 file, or in same location in template
     *                                 directory
     * getTemplatePath('x.tpl')      - look for .tpl relative to the directory
     *                                 of PHP file, or in template directory
     *                                 relative to the root of the template
     *                                 directory (the latter is deprecated)
     * getTemplatePath('/x.tpl')     - not supported!
     * getTemplatePath('file:x.tpl') - returns 'file:x.tpl'
     * getTemplatePath('kollage:/foo/bar') - returns "kollage:/foo/bar"
     *
     * @param  string  relative resouce name
     * @return string  absolute smarty resouce name
     */
    public function getTemplatePath($templatePath = false)
    {
        if (strpos($templatePath, ':') !== false) {
            return $templatePath;
        }

        if (substr($templatePath, 0, 1) == '/') {
            trigger_error('Absolute paths not supported (yet) (brok dig til Schmidt, hvis dette er en fejl)', E_USER_WARNING);
        }

        if (!$templatePath) {
            $path = substr($_SERVER['SCRIPT_FILENAME'], 0, -4) . '.tpl';
            if (file_exists($path)) {
                return 'file:' . $path;
            }
        } else {
            $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $templatePath;
            if (file_exists($path)) {
                return 'file:' . $path;
            }
        }

        // The rest is legacy support - bug 2356

        // Path of PHP file relative to document root, i.e. /bar/index.php
        // for foo/backend/bar/index.php
        if (!$templatePath) {
            $phpPath = substr($_SERVER['SCRIPT_FILENAME'], strlen($_SERVER['DOCUMENT_ROOT']));
            $templatePath = ltrim(str_replace('.php', '.tpl', $phpPath), '/');
        }

        // Check foo/templatesFrontend or foo/templatesBackend
        $baseDir = dirname($_SERVER['DOCUMENT_ROOT']) . '/templates' . ucfirst(basename($_SERVER['DOCUMENT_ROOT'])) . '/';
        if (!is_dir($baseDir)) {
            $baseDir = dirname($_SERVER['DOCUMENT_ROOT']) . '/templates/';
        }
$baseDir = __DIR__ . '/../../sites/natpas.nathejk.dk/templates/';
        return 'file:' . $baseDir . $templatePath;
    }

    public function initialize()
    {
        global $USER, $CUSTOMER, $PRODUCT;

        $this->headerTemplatePath = $this->getTemplatePath($this->headerTemplatePath);
        $this->footerTemplatePath = $this->getTemplatePath($this->footerTemplatePath);

        // Global variables
        $this->assign('CUSTOMER', $CUSTOMER);
        $this->assign('PRODUCT', $PRODUCT);
        $this->assign('USER', $USER);
    }

    /**
     * @param  string template path, or part of, see getTemplatePath()
     * @return string
     */
    public function fetch($templatePath = false)
    {
        $this->initialize();
        $result = '';
        if ($this->headerFooter) {
            $result .= $this->smarty->fetch($this->headerTemplatePath);
        }
        $result .= $this->smarty->fetch($this->getTemplatePath($templatePath));
        if ($this->headerFooter) {
            $result .= $this->smarty->fetch($this->footerTemplatePath);
        }
        return $result;
    }

    /**
     * @param  string template path, or part of, see fetch()
     */
    public function display($templatePath = false)
    {
        $this->initialize();
        if ($this->headerFooter) {
            $this->smarty->display($this->headerTemplatePath);
        }
        $this->smarty->display($this->getTemplatePath($templatePath));
        if ($this->headerFooter) {
            $this->smarty->display($this->footerTemplatePath);
        }
    }

    /**
     * Assigns a value to the underlying Smarty template. Works like
     * Smarty::assign(), i.e. the arguments can either be one associative
     * array or a name/value pair.
     * @param  mixed  a variable name or an associative array
     * @param  mixed  the variable value (should only be specified if the
     *                first argument is a string)
     * @see  Smarty::assign()
     */
    function assign($nameOrArray, $value = null)
    {
        $this->smarty->assign($nameOrArray, $value);
    }

    public function setTitle($title)
    {
        $this->title = $title;
        $this->assign('pageTitle', $title);
    }

    /**
     * Sets an error message that should be displayed on the page.
     * @param  string  an plain-text string (no HTML)
     */
    public function setError($msg = null)
    {
        $this->errors[] = $msg;
        $this->assign('pageError', implode("\n", $this->errors));
    }

    /**
     * Sets a warning message that should be displayed on the page.
     * @param  string  an plain-text string (no HTML)
     */
    public function setWarning($msg = null)
    {
        $this->warnings[] = $msg;
        $this->assign('pageWarning', implode("\n", $this->warnings));
    }

    /**
     * Sets a success message that should be displayed on the page.
     * @param  string  an plain-text string (no HTML)
     */
    public function setSuccess($msg = null)
    {
        $this->successes[] = $msg;
        $this->assign('pageSuccess', implode("\n", $this->successes));
    }

    /**
     * Returns the errors.
     * @return string
     */
    public function getError()
    {
        return (isset($_REQUEST['pageError']) ? $_REQUEST['pageError'] . "\n" : '') . $this->smarty->get_template_vars('pageError');
    }

    /**
     * Returns the warnings.
     * @return string
     */
    public function getWarning()
    {
        return (isset($_REQUEST['pageWarning']) ? $_REQUEST['pageWarning'] . "\n" : '') . $this->smarty->get_template_vars('pageWarning');
    }

    /**
     * Returns the successes.
     * @return string
     */
    public function getSuccess()
    {
        return (isset($_REQUEST['pageSuccess']) ? $_REQUEST['pageSuccess'] . "\n" : '') . $this->smarty->get_template_vars('pageSuccess');
    }

    /**
     * @return  smarty
     */
    function getSmarty()
    {
        if (!isset($this->smarty)) {
            $this->smarty = getSmarty();
        }
        return $this->smarty;
    }

    /**
     * @param  smarty
     */
    function setSmarty($smarty)
    {
        $this->smarty = $smarty;
    }
}

?>
