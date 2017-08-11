<?php

namespace WpContentTranslate;


class ContenthubComposite
{
    public function registerMetabox() {
        //add_action('load-edit.php', [$this, 'loadEdit']);
        add_action('admin_enqueue_scripts', [$this, 'registerMetaboxJavascript']);
    }

    public function loadEdit()
    {
        add_action('admin_enqueue_scripts', [$this, 'registerMetaboxJavascript']);
    }

    public function registerMetaboxJavascript()
    {
        wp_enqueue_script('wpcontenttranslate-metabox', \WpContentTranslate\instance()->plugin_url . 'assets/js/wpcontenttranslate.js');
    }
}