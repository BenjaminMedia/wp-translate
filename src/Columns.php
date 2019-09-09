<?php

namespace WpContentTranslate;

class Columns
{
    public function registerColumns()
    {
        foreach (Plugin::instance()->post_types() as $post_type) {
            add_filter('manage_edit-'. $post_type .'_sortable_columns', [$this, 'sortable_column']);
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

        $meta_post_data = get_post_meta($translation_id, TranslationMetaBox::TRANSLATION_STATE, true);

        if($meta_post_data === false || $meta_post_data === '') {
            return;
        }

        echo sprintf('<span class="wptranslate-state-%s">%s', $meta_post_data, PHP_EOL);
    }

    public function pll_after_post_column( $column, $post_id ) {
        if ( false === strpos( $column, 'language_' ) ) {
            return;
        }

        $language = Pll()->model->get_language( substr( $column, 9 ) );
        $translation_id = pll_get_post($post_id, $language->slug);

        $meta_post_data = get_post_meta($translation_id, TranslationMetaBox::TRANSLATION_STATE, true);

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

    public function sortable_column($columns) {
        $columns['translation_deadline'] = 'translation_deadline';
        return $columns;
    }
}
