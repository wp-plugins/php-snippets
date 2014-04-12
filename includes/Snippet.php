<?php
/**
 * This "abstracting" class is the dynamic handler for all PHP Snippets
 * The reason we have this class is so we can instantiate it and call 
 * arbitrary methods on it (corresponding to Snippet names).  We use the 
 * magic __call() function to handle the requests.
 *
 * @package php-snippets
 */

namespace PhpSnippets;
class Snippet {

	public static $snippets = array();

	/**
	 * This is the function that dynamically handles all shortcodes.
	 *
	 * @param string $name
	 * @param mixed $args
	 */
	public static function __callStatic($name, $args) {

		// get the file by name
		if (isset(self::$snippets[$name]) && file_exists(self::$snippets[$name])) {

			// TODO: make all arguments avail. to a single var, e.g $scriptProperties, 
			// for snippets that have a variable # of inputs
			if (isset($args[0]) && is_array($args[0])) {
				extract($args[0]);
			}
			// surrounded by [tag]content[/tag]
			if (isset($args[1])) {
				$content = $args[1];
			}
			ob_start();
			include self::$snippets[$name];
			$content = ob_get_clean();
			return $content;
		}
		else {
			return sprintf(__('PHP Snippet does not exist: %s', 'php_snippets'), "<code>$name</code>");
		}
	}
	
	/**
	 * Defines a snippet by mapping a tag (shortcode) to a file
	 *
	 * @param string $tag identifier (the shortcode base), e.g. 'my_shortcode'
	 * @param string $fullpath to the file to be included.
	 */
	public static function map($tag,$fullpath) {
        self::$snippets[$tag] = $fullpath;
	}
}
/*EOF*/
