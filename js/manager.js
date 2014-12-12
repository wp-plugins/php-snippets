/*------------------------------------------------------------------------------
This is called by the TinyMCE button click.  Make sure this function name 
matched the one in editor_plugin.js!
------------------------------------------------------------------------------*/
function show_php_snippets() {
	// Make us a place for the thickbox
	jQuery('body').append('<div id="php_snippets_thickbox"></div>');

	// Prepare the AJAX query
	var data = {
	        "action" : 'list_snippets',
	        "list_snippets_nonce" : php_snippets.ajax_nonce
	    };
	
	jQuery.post(
	    php_snippets.ajax_url,
	    data,
	    function( response ) {
	    	// Write the response to the div
			jQuery('#php_snippets_thickbox').html(response);

			var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
			W = W - 80;
			H = H - 84;
			// then thickbox the div
			tb_show('', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=php_snippets_thickbox' );			
	    }
	);
	
}


/*------------------------------------------------------------------------------
Pastes the shortcode back into WP.
Copied from wp-admin/media-upload.js send_to_editor() function -- I couldn't 
find where that JS is queued up, so I just copied this one function.
------------------------------------------------------------------------------*/
function insert_shortcode(h) {
	var ed;
	if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden() ) {
		// restore caret position on IE
		if ( tinymce.isIE && ed.windowManager.insertimagebookmark )
			ed.selection.moveToBookmark(ed.windowManager.insertimagebookmark);

		if ( h.indexOf('[caption') === 0 ) {
			if ( ed.plugins.wpeditimage )
				h = ed.plugins.wpeditimage._do_shcode(h);
		} else if ( h.indexOf('[gallery') === 0 ) {
			if ( ed.plugins.wpgallery )
				h = ed.plugins.wpgallery._do_gallery(h);
		} else if ( h.indexOf('[embed') === 0 ) {
			if ( ed.plugins.wordpress )
				h = ed.plugins.wordpress._setEmbed(h);
		}

		ed.execCommand('mceInsertContent', false, h);

	} else if ( typeof edInsertContent == 'function' ) {
		edInsertContent(edCanvas, h);
	} else {
		jQuery( edCanvas ).val( jQuery( edCanvas ).val() + h );
	}

	tb_remove();
}


/*------------------------------------------------------------------------------
Add Snippet Directory (Multi dir feature)
------------------------------------------------------------------------------*/
function add_field_dir(e) {
	jQuery( "#dir_wrap" ).append( "<div class='dir_item'><input type='text' name='snippet_dirs[]' size='100' value=''/><span class='rm_dir'>x<span></div>" );
	e.preventDefault();
}

/*------------------------------------------------------------------------------
Remove Snippet Directory
------------------------------------------------------------------------------*/
jQuery(document).on('click','.rm_dir', function(){
    jQuery(this).parent().remove();
    event.preventDefault();
});

/*------------------------------------------------------------------------------
Show Snippets from phpsnippets setting page
NOTE: thickbox script and css must be loaded on the plugin or else modal will fail on firefox
------------------------------------------------------------------------------*/
function settings_snippets(e) {
	jQuery('body').append('<div id="snippets_list" style="display:none;"></div>');
        var data = {
          "action" : 'dir_snippets',
          "dir_snippets_nonce" : php_snippets.ajax_nonce
        };


        jQuery.post(
          php_snippets.ajax_url,
          data,
          function( response ) {
            // Write the response to the div
            console.log(response);
            jQuery('#snippets_list').html(response);

            var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
            W = W - 80;
            H = H - 120;
            // then thickbox the div
            tb_show('', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=snippets_list' );  
          }
        );
       e.preventDefault();
}

/*------------------------------------------------------------------------------
Open a modal with directory list, default to wp root path
NOTE: thickbox script and css must be loaded on the plugin or else modal will fail on firefox
------------------------------------------------------------------------------*/
function modal_directory(e) {
var content = '<div class="dir_item ">'+
          '<input type="text" name="snippet_dirs[]" class="snippet_dir" size="100" value=""><span class="rm_dir">x</span>'+
                  '</div>';
                  jQuery('#dir_wrap').append(content);
       e.preventDefault();
}

/*------------------------------------------------------------------------------
Refresh the content of the Thickbox, upong clicking a directory
NOTE: thickbox script and css must be loaded on the plugin or else modal will fail on firefox
------------------------------------------------------------------------------*/
jQuery(document).on('click','.refresh_dir', function(){
    var parent_dir = jQuery(this).data('parent_dir');
    var data = {
      "action" : 'list_directory',
      "list_directory_nonce" : php_snippets.ajax_nonce,
      'parent_dir' : parent_dir
    };
    jQuery.post(
      php_snippets.ajax_url,
      data,
      function( response ) {
        jQuery('#dir_modal_list').html(jQuery('.dir_modal_content',response));
      }
    );
    event.preventDefault();
});


/*------------------------------------------------------------------------------
Add a new Field element on the Settign Page and use the selected Directory 
as the field value
------------------------------------------------------------------------------*/
jQuery(document).on('click','.select_dir', function(){
	var selected = jQuery(this).data('sel_dir');
    tb_remove();
    jQuery( "#dir_wrap" ).append( "<div class='dir_item'><input type='text' class='snippet_dir' name='snippet_dirs[]' size='100' value='"+selected+"'/><span class='rm_dir'>x<span></div>" );
    event.preventDefault();
});

jQuery('#qt_content_PHPSnippets').on('click',function() {
  console.log('php snippets modal');
  event.preventDefault();
})