(function($) {
Drupal.behaviors.scrolltoanchors = {
  attach: function(context, settings) {
    $(document).ready(function(){
      function validateSelector(a) {
        return /^#[a-z]{1}[a-z0-9_-]*$/i.test(a);
      }
      function scrollToDestination(a,b) {
        if (a > b) {
          destination = b;
        } else {
          destination = a;
        }
        $('html,body').animate({ scrollTop: destination }, 800, 'swing');
      }
      $('a[href^="#"]').click(function(event) {
        event.preventDefault();
        var hrefValue = $(this).attr('href');
        var strippedHref = hrefValue.replace('#','');
        var heightDifference = $(document).height() - $(window).height();
        if (validateSelector(hrefValue)) {
          if ($(hrefValue).length > 0) {
            var linkOffset = $(this.hash).offset().top;
            scrollToDestination(linkOffset, heightDifference);
          }
          else if ($('a[name=' + strippedHref + ']').length > 0) {
            var linkOffset = $('a[name=' + strippedHref + ']').offset().top;
            scrollToDestination(linkOffset, heightDifference);
          }
        }
      });
    });
  }
};
}(jQuery));
