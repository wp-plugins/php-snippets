<?php
/**
 * This "abstracting" class is the dynamic handler for all PHP Snippets
 * The reason we have this class is so we can instantiate it and call 
 * arbitrary methods on it (corresponding to Snippet names).  We use the 
 * magic __call() function to handle the requests.
 *
 * @package php-snippets
 */


class PHP_Snippet {

	public $snippets = array();

	/**
	 * This is the function that dynamically handles all shortcodes.
	 *
	 * @param string $name
	 * @param mixed $args
	 */
	public function __call($name, $args) {

		// get the file by name
		if (isset($this->snippets[$name]['path']) && file_exists($this->snippets[$name]['path'])) {

			// TODO: does basename.defaults.php exist?  If yes, include it and use shortcode_atts()
			// TODO: make all arguments avail. to a single var, e.g $scriptProperties, for snippets that have a variable # of inputs
			if (isset($args[0]) && is_array($args[0])) {
				extract($args[0]);
			}
			// surrounded by [tag]content[/tag]
			if (isset($args[1])) {
				$content = $args[1];
			}
			ob_start();
			include $this->snippets[$name]['path'];
			$content = ob_get_clean();
			return $content;
		}
		else {
			return sprintf(__('PHP Snippet does not exist: %s', 'php_snippets'), "<code>$name</code>");
		}
	}

	/**
	 * Register all shortcodes.  A shortcode is registered for each valid snippet file.
	 */
	public function __construct() {
		// Register the shortcodes
		$this->snippets = (array) PHP_Snippet_Functions::get_snippets(); //array(); // get snippets
		foreach ($this->snippets as $name => $data) {
			if (!in_array($name, PHP_Snippet_Functions::$existing_shortcodes)) {
				
				$php_license = PHP_License::edd_check_license();
				if($php_license == 'valid') {
					add_shortcode( $name , array($this, $name));
				}
				
			}
			else {
				$msg = sprintf(__('The name %s is already taken by an existing shortcode. Please re-name your file.', 'php_snippets'), "<em>$name</em>");
				PHP_Snippet_Functions::register_warning($msg);
			}

		}
	}
}
/*EOF*/
