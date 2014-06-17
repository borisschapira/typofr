<?php

/**
 * Plugin Name: TypoFR
 * Description: a plugin for french typography management, powered by JoliTypo
 *
 * Plugin URI: https://github.com/borisschapira/typofr
 * Version: 0.5
 *
 * Author: Boris Schapira
 * Author URI: http://borisschapira.com
 * License: MIT
 * @package typofr
 */

/**
 * The instantiated version of this plugin's class
 */
include 'vendor/autoload.php';

use JoliTypo\Fixer;

$GLOBALS['typofr'] = new typofr;

/**
 * TypoFR
 *
 * @package   typofr
 * @link      http://wordpress.org/extend/plugins/typofr/
 * @license   https://raw.githubusercontent.com/borisschapira/typofr/master/LICENSE MIT
 * @author    Boris Schapira <borisschapira@gmail.com>
 * @copyright Boris Schapira, 2014
 *
 */
class typofr
{
    /**
     * This plugin's identifier
     */
    const ID = 'typofr';

    /**
     * This plugin's name
     */
    const NAME = 'TypoFR';

    /**
     * This plugin's version
     */
    const VERSION = '0.2';

    /**
     * This plugin's table name prefix
     * @var string
     */
    protected $prefix = 'typofr_';


    /**
     * Has the internationalization text domain been loaded?
     * @var bool
     */
    protected $loaded_textdomain = false;

    /**
     * This plugin's options
     *
     * Options from the database are merged on top of the default options.
     *
     * @see typofr::set_options()  to obtain the saved
     *      settings
     * @var array
     */
    protected $options = array();

    /**
     * This plugin's default options
     * @var array
     */
    protected $options_default = array(
        'deactivate_deletes_data' => 1,
        'debug_in_console' => 0,
        'is_enable_title_fix' => 1,
        'is_enable_content_fix' => 1,
        'is_enable_excerpt_fix' => 1,
        'fix_ellipsis' => 1,
        'fix_dimension' => 1,
        'fix_dash' => 1,
        'fix_french_quotes' => 1,
        'fix_french_no_breakspace' => 1,
        'fix_curly_quote' => 1,
        'fix_hyphen' => 1,
        'fix_trademark' => 1
    );

    /**
     * Our option name for storing the plugin's settings
     * @var string
     */
    protected $option_name;

    /**
     * Name, with $table_prefix, of the table tracking login failures
     * @var string
     */
    protected $table_login;

    /**
     * Our usermeta key for tracking when a user logged in
     * @var string
     */
    protected $umk_login_time;

    /**
     * Declares the WordPress action and filter callbacks
     *
     * @return \typofr
     * @uses typofr::initialize()  to set the object's
     *       properties
     */
    public function __construct()
    {
        $this->initialize();

        /** @noinspection PhpUndefinedFunctionInspection */
        if (is_admin()) {

            require_once dirname(__FILE__) . '/admin.php';
            $admin = new typofr_admin;


            $admin_menu = 'admin_menu';
            $admin_notices = 'admin_notices';
            $plugin_action_links = 'plugin_action_links_typofr/typofr.php';


            add_action($admin_menu, array(&$admin, 'admin_menu'));
            add_action('admin_init', array(&$admin, 'admin_init'));
            add_filter($plugin_action_links, array(&$admin, 'plugin_action_links'));

            register_activation_hook(__FILE__, array(&$admin, 'activate'));
            if ($this->options['deactivate_deletes_data']) {
                register_deactivation_hook(__FILE__, array(&$admin, 'deactivate'));
            }
        }
    }

    /**
     * Sets the object's properties and options
     *
     * This is separated out from the constructor to avoid undesirable
     * recursion.  The constructor sometimes instantiates the admin class,
     * which is a child of this class.  So this method permits both the
     * parent and child classes access to the settings and properties.
     *
     * @return void
     *
     * @uses typofr::set_options()  to replace the default
     *       options with those stored in the database
     */
    protected function initialize()
    {
        global $wpdb;

        $this->option_name = self::ID . '-options';
        $this->set_options();

        if ($this->options['is_enable_title_fix']) {
            add_filter('the_title', array(&$this, 'fixTextContent'));
        }
        if ($this->options['is_enable_content_fix']) {
            add_filter('the_content', array(&$this, 'fixTextContent'));
        }

        if (is_admin()) {
            $this->load_plugin_textdomain();
        }
    }

    /*
     * ===== ACTION & FILTER CALLBACK METHODS =====
     */

    /**
     * Fixes a text content, applying modules
     *
     * @param $text the text to fix
     *
     * @return string the fixed text
     */
    public function fixTextContent($text)
    {
        // Should the plugin store log in the console ?
        $doDebug = $this->options['debug_in_console'];
        $logs = array();

        // What fixes should the plugin apply ?
        static $fixOptions;
        if (!isset($fixOptions)) {
            $fixOptions = array();
            foreach ($this->options as $key => $value) {
                $exp_key = explode('_', $key);
                if ($exp_key[0] == 'fix') {
                    $fixOptions[$key] = $value;
                }
            }
            array_push($logs, sprintf('Fixers : %s', implode(',', $fixOptions)));
        }

        // Should this function call initialize the fixe ?
        static $enableFixer;
        if (!isset($enableFixer)) {
            $enableFixer = count(array_filter($fixOptions));
        }

        // If no fix is activated, simply returns the original text
        if (!$enableFixer) {
            return $text;
        }

        // Else, initialize the Jolitypo fixer with the good options
        static $fixer;
        if (!isset($fixer)) {
            $fixer = $this->createFixer($fixOptions);
            //var_dump($fixer->fix('λ'));
        }

        array_push($logs, sprintf('Original text : %s', $text));
        $fixed = $fixer->fix($text);
        array_push($logs, sprintf('Fixed text : %s', $fixed));

        $logs = array_map(
            function ($t) {
                return trim(preg_replace('/\s+/', ' ', $t));
            },
            $logs
        );
        $scriptLogs = $doDebug ? "<script>console && console.log('" . implode("\\n", $logs) . "')</script>" : '';

        return $fixed . $scriptLogs;
    }

    /*
     * ===== INTERNAL METHODS ====
     */

    /**
     * Creates the JoliTypo Fixer
     *
     * @param $fixOptions
     *
     * @return Fixer
     */
    protected function createFixer($fixOptions)
    {
        $fixers = array();
        if($fixOptions['fix_ellipsis'])
            array_push($fixers, 'Ellipsis');
        if($fixOptions['fix_dimension'])
            array_push($fixers, 'Dimension');
        if($fixOptions['fix_dash'])
            array_push($fixers, 'Dash');
        if($fixOptions['fix_french_quotes'])
            array_push($fixers, 'FrenchQuotes');
        if($fixOptions['fix_french_no_breakspace'])
            array_push($fixers, 'FrenchNoBreakSpace');
        if($fixOptions['fix_curly_quote'])
            array_push($fixers, 'CurlyQuote');
        if($fixOptions['fix_hyphen'])
            array_push($fixers, 'Hyphen');
        if($fixOptions['fix_trademark'])
            array_push($fixers, 'Trademark');

        //var_dump($fixers);
        $fixers = [
            "Ellipsis",
            "Dimension",
            "Dash",
            "FrenchQuotes",
            "FrenchNoBreakSpace",
            "CurlyQuote",
            "Hyphen",
            "Trademark"];
        $fixer = new Fixer($fixers);
        $fixer->setLocale('fr_FR'); // Needed by the Hyphen Fixer

        return $fixer;
    }

    /**
     * Sanitizes output via htmlspecialchars() using UTF-8 encoding
     *
     * Makes this program's native text and translated/localized strings
     * safe for displaying in browsers.
     *
     * @param string $in the string to sanitize
     *
     * @return string  the sanitized string
     */
    protected function hsc_utf8($in)
    {
        return htmlspecialchars($in, ENT_QUOTES, 'UTF-8');
    }

    /**
     * A centralized way to load the plugin's textdomain for
     * internationalization
     * @return void
     */
    protected function load_plugin_textdomain()
    {
        if (!$this->loaded_textdomain) {
            load_plugin_textdomain(self::ID, false, self::ID . '/languages');
            $this->loaded_textdomain = true;
        }
    }

    /**
     * Replaces the default option values with those stored in the database
     * @uses login_security_solution::$options  to hold the data
     */
    protected function set_options()
    {
        $options = get_option($this->option_name);

        if (!is_array($options)) {
            $options = array();
        }
        $this->options = array_merge($this->options_default, $options);
    }
}

