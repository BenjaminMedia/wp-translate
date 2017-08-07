<?php

namespace WpContentTranslate;

class Columns
{
    public function registerColumns()
    {
        $post_types = ['contenthub_composite'];

        foreach ($post_types as $post_type) {
            add_action('manage_' . $post_type . '_posts_custom_column', [$this, 'pll_before_post_column'], 8, 2);
            add_action('manage_' . $post_type . '_posts_custom_column', [$this, 'pll_after_post_column'], 12, 2);
        }

        add_action('load-edit.php', [$this, 'loadEdit']);
    }

    public function pll_before_post_column($column, $post_id)
    {
        if (false === strpos($column, 'language_')) {
            return;
        }

        $language = Pll()->model->get_language( substr( $column, 9 ) );
        $translation_id = pll_get_post($post_id, $language->slug);

        $meta_post_data = get_post_meta($translation_id, 'translation_state', true);

        if($meta_post_data === false || $meta_post_data === '') {
            return;
        }

        switch ($meta_post_data) {
            case 'progress':
                echo '<span class="wptranslate-state-progress">'.PHP_EOL;
                break;
            case 'translated':
                echo '<span class="wptranslate-state-translated">'.PHP_EOL;
                break;
            case 'ready':
                echo '<span class="wptranslate-state-ready">'.PHP_EOL;
                break;
        }
        return;
    }

    public function pll_after_post_column( $column, $post_id ) {
        if ( false === strpos( $column, 'language_' ) ) {
            return;
        }

        $language = Pll()->model->get_language( substr( $column, 9 ) );
        $translation_id = pll_get_post($post_id, $language->slug);

        $meta_post_data = get_post_meta($translation_id, 'translation_state', true);

        if($meta_post_data === false || $meta_post_data === '') {
            return;
        }

        echo '</span>'.PHP_EOL;
    }

    public function loadEdit()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueColumnsStyles']);
    }

    public function enqueueColumnsStyles()
    {
        wp_enqueue_style('wpcontenttranslate-metabox', \WpContentTranslate\instance()->plugin_url . 'assets/css/style.css');
    }
}