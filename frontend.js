jQuery(document).ready(function() {
  if (jQuery("#whats-new-content").length > 0) {
 
    jQuery("#aw-whats-new-submit").click(function() {
      var submit_butt = jQuery(this);

      submit_butt.hide();
      submit_butt.attr("disabled", "disabled");
      jQuery("#whats-new-textarea").css("opacity", "0.3");
      if (
        !jQuery("#whats-new-content")
          .find(".url_preview_loading")
          .get(0)
      )
        jQuery("#whats-new-content").append(
          "<div class='url_preview_loading'></div>"
        );

      jQuery(document).ajaxComplete(function(e, xhr, options) {
        submit_butt.show();
        submit_butt.removeAttr("disabled");
        jQuery("#whats-new-content")
          .find(".url_preview_loading")
          .remove();
        jQuery("#whats-new-textarea").css("opacity", "1");
        jQuery(e.currentTarget).unbind("ajaxComplete");
      });
    });
  }
});
