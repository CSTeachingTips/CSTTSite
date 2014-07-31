jQuery(document).ready(function($) {
    // Check if the filter exists
    if($('.form-item-sort-by select').length){
        // Your change function
        $('.form-item-sort-by select').change(function() {
            // Submit the form
            $(this).parents('form').submit();
        });
    }
});