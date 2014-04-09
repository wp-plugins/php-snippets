INIT = {
  add_dir : function() {
    $('#add_dir').on('click',function(e){
      $( "#dir_wrap" ).append( "<div class='dir_item'><input type='text' name='snippet_dirs[]' size='100' value=''/><span class='rm_dir'>x<span></div>" );
      e.preventDefault();
    });
  },

  rm_dir : function() {
     $(document).on('click','.rm_dir', function(){
        $(this).parent().remove();
        return false;
    });

  },

  show_all_snippets : function() {
    $('#show_all_snippets').on('click',function(e){
      $('body').append('<div id="snippets_list"></div>');
        var data = {
          "action" : 'test',
          "test_nonce" : php_snippets.ajax_nonce
        };


        $.post(
          php_snippets.ajax_url,
          data,
          function( response ) {
            // Write the response to the div
            $('#snippets_list').html(response);

            var width = $(window).width(), H = $(window).height(), W = ( 720 < width ) ? 720 : width;
            W = W - 80;
            H = H - 120;
            // then thickbox the div
            tb_show('', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=snippets_list' );     
          }
        );

        e.preventDefault();
    })
  }
}

$(document).ready(function(){
    INIT.add_dir();
    INIT.rm_dir();
    INIT.show_all_snippets();
});
