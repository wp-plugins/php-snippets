<?php
/**
 * This "abstracting" class is the dynamic handler for all PHP Snippets
 *
 *
 *
 * @package php-snippets
 */


class PHP_Snippet {

	public $snippets = array();

	/**
	 * This is the function that dynamically handles all shortcodes.
	 * @param string $name
	 * @param mixed $args
	 */
	public function __call($name, $args) {

		// get the file by name
		if (isset($this->snippets[$name]) && file_exists($this->snippets[$name])) {
	//		die(print_r($args, true));
			// TODO: does basename.defaults.php exist?  If yes, include it and use shortcode_atts()
			// TODO: make all arguments avail. to a single var, e.g $scriptProperties
			// TODO: pass [tag]surrounded[/tag] content to the snippet.
			if (isset($args[0])) {
				extract($args[0]);
			}
			// surrounded by [tag]content[/tag]
			if (isset($args[1])) {
				$content = $args[1];
			}
			ob_start();
			include $this->snippets[$name];
			$content = ob_get_clean();
			return $content;
		}
		else {
			return sprintf(__('PHP Snippet does not exist %s', 'php_snippets'), $name);
		}
	}

	/**
	 * 
	 */
	public function __construct() {
		// Register the shortcodes
		$this->snippets = (array) PHP_Snippet_Functions::get_snippets(); //array(); // get snippets
		foreach ($this->snippets as $name => $tmp) {
			if (!in_array($name, PHP_Snippet_Functions::$existing_shortcodes)) {
				add_shortcode( $name , array($this, $name));
			}
			else {
				$msg = sprintf(__('The name %s is already taken by an existing shortcode. Please re-name your file.', 'php_snippets'), "<em>$name</em>");
				PHP_Snippet_Functions::register_warning($msg);
			}

		}
	}
}
/*EOF*/