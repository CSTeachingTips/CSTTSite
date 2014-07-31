/**
 * @file
 * Handles AJAX fetching of views, including filter submission and response.
 */
(function ($) {

// This makes sure, that the selected class is properly set on the links when
// using the options select-as-links.
Drupal.behaviors.MefibsBEFForm = {
  attach: function(context, settings) {
    $(context).each(function(index, el) {
      $('.bef-select-as-links', el).each(function() {
        if (!$(this).find('select').length) {
          return;
        }
        var selected = $(this).find('select').val().toLowerCase().replace(/_/g, '-').replace(/ /g, '-');
        if (typeof selected == 'undefined') {
          return;
        }
        var select_id = $(this).find('select').attr('id').toLowerCase().replace(/_/g, '-').replace(/ /g, '-');
        $(this).find('.form-item').removeClass('selected');
        $(this).find('#' + select_id + '-' + selected).addClass('selected');
      });
    });

    // Support for sliders in mefibs blocks.
    if (settings.mefibs) {
      if (typeof Drupal.settings.better_exposed_filters.slider_options != 'undefined') {
        var new_sliders = [];        
        $.each(settings.mefibs.forms, function(block_id, mefibs) {
          $.each(settings.better_exposed_filters.slider_options, function(element_id, slider) {

            var expected_id = ("views-exposed-form-" + mefibs.view_name + "-" + mefibs.view_display_id).replace(/_/g, '-');
            var element_wrapper = ('edit-' + element_id + '-wrapper').replace(/_/g, '-');
            var original_element = ('#' + element_wrapper + ' > .views-widget > div');
            var original_slider = ('#' + element_wrapper + ' .bef-slider').replace(/_/g, '-');

            if ($.inArray(element_id, mefibs.elements) == -1) {
              // Remove the old slider from the form.
              if ($(original_slider, original_element).length) {
                $(original_slider, original_element).slider('destroy');
                $(element_wrapper, original_element).remove();
              }
              return;
            }

            if (expected_id != slider.viewId) {
              return;
            }

            var new_slider = $.extend({}, slider);

            if (block_id != 'default') {
              new_slider.id = mefibs.form_prefix + '-' + new_slider.id;
              new_slider.viewId = ("views-exposed-form-" + mefibs.view_name + "-" + mefibs.view_display_id + "-" + mefibs.form_prefix).replace(/_/g, '-');
            }

            new_sliders.push(new_slider);

            // Remove the old slider from the form.
            if (block_id != 'default' && $(original_slider, context).length) {
              $(original_slider, context).slider('destroy');
              $(element_wrapper, context).remove();
            }
            delete Drupal.settings.better_exposed_filters.slider_options[element_id];
          });
        });
        $.each(new_sliders, function(i, slider) {
          Drupal.settings.better_exposed_filters.slider_options[slider.id] = slider;
        });
        Drupal.behaviors.better_exposed_filters_slider.attach(context, Drupal.settings);
      }
    }
  }
};

// Is there any way that we can be sure, that better_exposed_filters has
// already run? Anyway, there are problems with ajax links in the current
// stabe releases of BEF. We fix this for the moment by taking code from
// https://drupal.org/node/1268150#comment-8467807
Drupal.behaviors.MefibsBEFFormSelectAsLinks = {
  attach: function(context, settings) {
    $('.bef-select-as-links', context).once(function() {
      var $widgets = $(this).find('.views-exposed-widgets');
      // Hide the actual form elements from the user.
      $widgets.find('.bef-select-as-links select').hide();
      $(this).find('a').click(function(event) {
        var $wrapper = $(this).parents('.bef-select-as-links');
        var $options = $wrapper.find('select option');
        // We have to prevent the page load triggered by the links.
        event.preventDefault();
        event.stopPropagation();
        // Un select old select value.
        $wrapper.find('select option').removeAttr('selected');

        // Set the corresponding option inside the select element as selected
        var link_text = $(this).text();
        $selected = $options.filter(function() {
          return $(this).text() == link_text;
        });
        $selected.attr('selected', 'selected');
        $wrapper.find('.bef-new-value').val($selected.val());
        $wrapper.find('a').removeClass('active');
        $(this).addClass('active');
        // Submit the form.
        $wrapper.parents('form').find('.views-submit-button input[type=submit]').click();
      });
    });
  }
};

})(jQuery);
