<?php
/**
 * This "abstracting" class is the dynamic handler for all PHP Snippets
 *
 *
 *
 * @package
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
			$file = $this->snippets[$name];
			include $this->snippets[$name];
			return;
		}
		else {
			die('file does not exist: ' . $name);
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