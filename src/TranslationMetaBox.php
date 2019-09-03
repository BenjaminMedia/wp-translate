<?php

namespace WpContentTranslate;

/**
 * Class PostMetaBox
 *
 * @package \Bonnier\WP\Cache\Admin
 */
class TranslationMetaBox
{
    const TRANSLATION_STATE = 'translation_state';
    const TRANSLATION_DEADLINE = 'translation_deadline';

    public static function register()
    {
        add_action('do_meta_boxes', function () {
            add_meta_box(
                'wp_content_translate',
                'Translation State',
                [__CLASS__, 'metaBoxContent'],
                collect(get_post_types(['public' => true]))->reject('attachment')->toArray(),
                'side'
            );
        });
        add_action('save_post', [__CLASS__, 'saveMetaBoxSettings']);
    }

    public static function metaBoxContent($post)
    {
        static::printTranslationState($post);
        static::printTranslationDeadline($post);
    }

    public static function saveMetaBoxSettings($postId)
    {
        if (isset($_POST[static::TRANSLATION_STATE])) {
            update_post_meta($postId, static::TRANSLATION_STATE, $_POST[static::TRANSLATION_STATE]);
        }
        if (isset($_POST[static::TRANSLATION_DEADLINE])) {
            $formattedDate = date('Ymd', strtotime($_POST[static::TRANSLATION_DEADLINE]));
            update_post_meta($postId,static::TRANSLATION_DEADLINE, $formattedDate);
        }
    }

    private static function printTranslationState($post)
    {
        $translationStates = [
            'Ready for translation' => 'ready',
            'In progress'           => 'progress',
            'Translated'            => 'translated',
        ];

        $postTranslationState = get_post_meta($post->ID, static::TRANSLATION_STATE, true);

        echo
            sprintf(
                '<p><strong>Translation Status</strong></p><select name="%s"><option value="" %s>- Select -</option>',
                static::TRANSLATION_STATE,
                $postTranslationState ? 'selected="selected"' : ''
            )
            .
            collect($translationStates)->map(function ($translationState, $label) use ($postTranslationState) {
                return sprintf(
                    '<option value="%s" %s>%s</option>',
                    $translationState,
                    $postTranslationState === $translationState ? 'selected="selected"' : '',
                    $label
                );
            })->implode('')
            .
            '</select><br>';
    }

    private static function printTranslationDeadline($post)
    {
        echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
             <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>';

        echo sprintf(
            '<p><strong>Translation Deadline</strong></p><input type="text" id="%s" name="%s" value="%s">',
            static::TRANSLATION_DEADLINE,
            static::TRANSLATION_DEADLINE,
            get_post_meta($post->ID, static::TRANSLATION_DEADLINE, true) ?: ''
        );

        echo sprintf(
            '<script type="text/javascript"> flatpickr("#%s", { dateFormat: "M d, Y" }); </script>',
            static::TRANSLATION_DEADLINE
        );
    }
}
