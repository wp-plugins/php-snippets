/**
 *
 * @param	string	snippet_path
 * @param	string	target
 * @return	dynamically writes shortcode to the Widget area.
 */
function add_php_snippet(snippet_path,target){

	// Prepare the AJAX query
	var data = {
	        "action" : 'get_snippet_shortcode',
	        "get_snippet_shortcode_nonce" : php_snippets.ajax_nonce,
	        "snippet_path" : snippet_path
	    };

	jQuery.post(
	    php_snippets.ajax_url,
	    data,
	    function( response ) {
	    	// Write the response to the div
			jQuery('#'+target).html(response);
	    }
	);
	
}
