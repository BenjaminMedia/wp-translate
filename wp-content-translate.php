<?php
/**
 * Plugin Name: WP Content Translate
 * Version: 2.1.1
 * Plugin URI: https://github.com/BenjaminMedia/wp-translate
 * Description: Mark translation ready content with this smart plugin!
 * Author: Bonnier - Michael Sørensen
 * License: GPL v3
 */

namespace WpContentTranslate;

if (!defined('ABSPATH')) {
    exit;
}

require __DIR__ .'/autoload.php';

class Plugin
{
    /**
     * Text domain for translators
     */
    const TEXT_DOMAIN = 'wp-content-translate';
    const CLASS_DIR = 'src';

    /**
     * @var object Instance of this class.
     */
    private static $instance;

    //private $settings;

    /**
     * @var string Filename of this class.
     */
    public $file;

    /**
     * @var string Basename of this class.
     */
    public $basename;

    /**
     * @var string Plugins directory for this plugin.
     */
    public $plugin_dir;

    /**
     * @var Object
     */
    public $scripts;

    /**
     * @var string Plugins url for this plugin.
     */
    public $pluginUrl;

    /**
     * Do not load this more than once.
     */
    private function __construct()
    {
        // Set plugin file variables
        $this->file = __FILE__;
        $this->basename = plugin_basename($this->file);
        $this->plugin_dir = plugin_dir_path($this->file);
        $this->plugin_url = plugin_dir_url($this->file);
        // Load textdomain
        load_plugin_textdomain(self::TEXT_DOMAIN, false, dirname($this->basename) . '/languages');

        //$this->settings = new SettingsPage();
    }

    /**
     * Returns the instance of this class.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
            self::$instance->columns();
            global $wpTranslate;
            $wpTranslate = self::$instance;

            /**
             * Run after the plugin has been loaded.
             */
            do_action('wp_translate_loaded');
        }

        return self::$instance;
    }

    function post_types() {
        $post_types = array_filter(get_post_types(['public' => true]), function ($postType) {
            return !in_array($postType, ['attachment']);
        });

        // Add filter to allow adding extra post types that are registered at a later point
        return apply_filters('wp_translate_post_types', $post_types);
    }

    private function columns()
    {
        $columns = new Columns();
        $columns->registerColumns();
        TranslationMetaBox::register();
    }

    private function bootstrap()
    {
        //$bootstrap = new Bootstrap();
        //$bootstrap->bootstrap();
    }
}

/**
 * @return Plugin $instance returns an instance of the plugin
 */
function instance()
{
    return Plugin::instance();
}

function translation_deadline_column_head($defaults)
{
    $defaults['translation_deadline'] = 'Translation Deadline';
    return $defaults;
}

function translation_deadline_column_content($column_name, $post_ID)
{
    if('translation_deadline' === $column_name) {
        $deadline = get_post_meta($post_ID, 'translation_deadline', true);
        if ($deadline) {
            echo date('F j, Y', strtotime($deadline));
            return;
        }
        echo '--';
    }
}

function translation_deadline_orderby($query) {
    if(!is_admin()) {
        return;
    }

    $orderby = $query->get('orderby');
    if('translation_deadline' == $orderby) {
        $query->set('meta_key', 'translation_deadline');
        $query->set('orderby', 'meta_value');
        $query->set('meta_type', 'DATE');
    }
}

add_filter('manage_posts_columns', __NAMESPACE__ . '\translation_deadline_column_head');
add_action('manage_posts_custom_column', __NAMESPACE__ . '\translation_deadline_column_content', 10, 2);
add_action('pre_get_posts', __NAMESPACE__ . '\translation_deadline_orderby');

add_action('after_setup_theme', __NAMESPACE__ . '\instance', 0);
