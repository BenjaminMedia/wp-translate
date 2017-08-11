jQuery(document).ready(function($) {
    if($('#post_type').val() === 'contenthub_composite') {
        var translationDropdown = $('#acf-field_5940df2d4eff9');
        var publishButton = $('#publish');
        var wordpressPostData = $('#post_status');

        if(translationDropdown.val() !== 'translated') {
            if($(this).val() === 'translated') {
                publishButton.prop('disabled', false);
            } else {
                // console.log($('#post_status option[value="draft"]'));
                $('#post_status option[value="draft"]').prop('selected', true);
                $('#post-status-display').html('Draft');
                publishButton.prop('disabled', false);
            }
        }

        translationDropdown.change(function() {
            if($(this).val() === 'translated') {
                publishButton.prop('disabled', false);
            } else {
                // console.log($('#post_status option[value="draft"]'));
                $('#post_status option[value="draft"]').prop('selected', true);
                $('#post-status-display').html('Draft');
                publishButton.prop('disabled', false);
            }
        });
    }
});