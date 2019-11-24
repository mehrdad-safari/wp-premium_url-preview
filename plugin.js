(function() {
  tinymce.PluginManager.add("at_preview", function(editor, url) {
    // Add a button that opens a window
    editor.addButton("at_preview_button_key", {
      title: "Insert URL",
      image: url + "/button.png",
      onclick: function() {
        // Open window
        editor.windowManager.open({
          title: "Ajaxtown - Preview Plugin",
          body: [
            {
              type: "textbox",
              name: "title",
              label: "Enter URL"
            }
          ],
          onsubmit: function(e) {
            // Insert content when the window form is submitted
            jQuery.ajax({
              url: url + "/class.linkpreview.php",
              data: "url=" + e.data.title + "&image_no=" + 1 + "&css=" + true,
              type: "get",
              success: function(html) {
                
                editor.insertContent(html);
              }
            });
          }
        });
      }
    });
  });
})();
