<?php
/**
 * Library of static functions used by this plugin.
 
 Data Structure for $snippets
 
 Array(
 	[shortname] => Array(
 		[file] => /full/path/to/shortname.snippet.php
 		[description] => An optional description for the Snippet
 		[controller] => /full/path/to/optional/shortname.controller.php
 	),
 )
 
 *
 */
namespace PhpSnippets; 
class Base {

	
	/**
	 * The key in wp_postmeta
	 */
	const db_key = 'php_snippets';
	
	/**
	 * Used to store warnings that are displayed to admin users.
	 */ 
	public static $warnings = array();
	

	/**
	 * All directories
	 */
	public static $directories = array();

    /**
     * Any [+placeholder+] you want to use in your directory
     * names, e.g. [+ABSPATH+]
     */
    public static $placeholders = array();

    /**
     * Register the shortcode using WP's add_shortcode function
     *
     * @param string $fullpath /full/path/to/file.php
     * @param string $ext file extension. This is stripped to calculate the shortcode
     * @return void
     */
    public static function add_shortcode($fullpath,$ext) {
        $tag = self::get_shortname($fullpath,$ext);
        Snippet::map($tag,$fullpath);

		// success
        add_shortcode($tag, 'Phpsnippets\Snippet::'.$tag);    
    }

    /**
     * Check the file for basic syntax errors
     *
     * @param string $fullpath to file
     * @return mixed boolean false on no errors, string error message on error
     */
    public static function has_bad_syntax($fullpath) {
        $fullpath = escapeshellarg($fullpath);
        $msg = `php -d error_reporting=1 -l $fullpath`;
        // No syntax errors detected in _____
        if (strpos($msg, 'No syntax errors detected') === 0) {
            return false;
        }
        return $msg;
    }
    
	/**
	 * Verify a given directory
	 *
	 * @param	string	$dir full path to directory to check.
	 * @return	boolean	true on success
	 */
	public static function dir_exists($dir) {
			if(trim($dir) == '') {
				return;
			}

			$dir = self::parse($dir,self::$placeholders);
			if (!file_exists($dir)) {
				return false;
			}
			if (!is_dir($dir)) {
				return false;		
			}

		return true;
	}
	
	/**
	 * get snippets on the specified directory
	 * @param string $dir /full/path/to/dir
	 * @param string $ext file extension to match.
	 * @return array simple array containing fullpaths to files
	 */
	public static function get_snippets($dir,$ext) {
		$snippets = array();
		$rawfiles = @scandir($dir); 
		$pattern = '/' . preg_quote($ext) . '$/';
		unset($rawfiles[0]);
		unset($rawfiles[1]);

		if(!empty($rawfiles)) {
			foreach ($rawfiles as $f) {
				if(!is_dir($dir.'/'.$f)) {
					if ( preg_match($pattern, $f) && strpos($f, $ext) ) {
						$shortname = self::get_shortname($f,$ext);
						$snippets[$shortname] = rtrim($dir,'/').'/'.$f;
					}			
				}
				
			}
		}
		return $snippets;
	}

	/**
	 * Get a list of all directories where the user said to look for snippets.
	 * We flag each directory as to whether or not it exists.
	 * @param array $dirs data from database
	 * @param boolean $include_built_in if true, include the built-in snippets dir in the list
	 * @return array associative array of full-path-to-dir => true/false.  True if the dir exits. False if it does not
	 */
	public static function get_dirs($dirs,$include_built_in) {
		if($include_built_in) {
			$dirs[] = PHP_SNIPPETS_PATH .'/snippets';
		}
		if(!empty($dirs)) {
			foreach($dirs as $dir){
				$dir = self::parse($dir, self::$placeholders); // resolve ABSPATH
				self::$directories[$dir] = self::dir_exists($dir) ;
			}
		}

		return self::$directories;
	}

	/**
	 * Show all Directories on the Add Directory modal
	 * @param string dir
	 * @return array of directories
	 */
	public static function list_dirs($dir='') {
		$directories = array();
		if($dir == '') {
			return array();
		}

		$rawfiles = @scandir($dir); 
		unset($rawfiles[0]);
		if(!empty($rawfiles)) {
			foreach ($rawfiles as $f) {
				// Check immediate sub-dirs
				if (is_dir($dir.'/'.$f)) { 
					$directories[] =$dir .'/'. $f;
				}
			}
		}
		
		return $directories;
	}
	
	/**
	 *
	 *
	 */
    public static function set_placeholder($key,$value) {
        self::$placeholders[$key] = $value;
    }

	/**
	 * Given a snippet's info and its shortname, generate a shortcode to be pasted
	 * into the text editor.
	 *
	 * @param array  $info
	 * @param string $shortname
	 * return string
	 *
	 * @return string
	 */
    public static function get_shortcode($info,$shortname) {

        // @shortcode defined verbatim
        if(isset($info['shortcode']) && !empty($info['shortcode'])) {
            return $info['shortcode'];
        }

        // no parameters to help us out
        if (!isset($info['params']) || empty($info['params'])) {
            if (isset($info['content'])) {
                return '['.$shortname.']'.$info['content'].'[/'.$shortname.']';
            }
            return "[$shortname]";
        };

        // Dynamic shortcode calculation
        $shortcode = '['.$shortname;
        foreach($info['params'] as $varname => $value) {
            $shortcode.= ' '.trim(htmlspecialchars($varname)).'="'.trim(htmlspecialchars($value)).'"';
        }
        $shortcode .= ']';
        if (isset($info['content']) && $info['content'] !== false) {
            return $shortcode .= $info['content']. '[/'.$shortname.']';
        }
        return $shortcode;
        
        
    }

	/**
	 * Given the /full/path/to/snippet.php, read a description out of the header,
	 * and read a sample shortcode.
	 *
	 * @param    string $path to file
	 * @param           array
	 *
	 * @return array
	 */
	public static function get_snippet_info($path) {
		$info = array();
		if (file_exists($path)) {
			$info['path'] 		= $path;
			$info['desc'] 		= '';
			$info['shortcode'] 	= ''; // verbatim e.g. [my_snippet] or [hey]you[/hey]
//			$info['tag']        = ''; // trimmed e.g. my_snippet
			$info['params']     = array();
			$info['errors']     = false; // should be false if syntax is ok.
			$info['content']    = false;

			$contents = file_get_contents($path);
			
			// Legacy:
			// Get description
			preg_match('/^Description:(.*)$/m', $contents, $matches);
			if (isset($matches[1])) {
				$info['desc'] = $matches[1];
			}
			// Get shortcode
			preg_match('/^Shortcode:(.*)$/m', $contents, $matches);
			if (isset($matches[1])) {
				$info['shortcode'] = $matches[1];
			}
			
			// Allow the user to skip this in case the environment doesn't support it
			if (!defined('PHP_SNIPPETS_SKIP_SYNTAX_CHECK')) {
			     $info['errors'] = self::has_bad_syntax($path);
			}

            // Docblocks
            $dox_start = preg_quote('/*','#');
            $dox_end = preg_quote('*/','#');
            
        
            preg_match("#$dox_start(.*)$dox_end#msU", $contents, $matches);
        
            if (!isset($matches[1])) {
                    return false; // No doc block found!
            }
            
            // Get the docblock                
            $dox = $matches[1];
            
            // Loop over each line in the comment block
            foreach (preg_split('/((\r?\n)|(\r\n?))/', $dox) as $line) {
                preg_match('/^\s*\**\s*@description(.*)$/',$line,$m);
                if (isset($m[1])) {
                    $info['desc'] = trim(ltrim(trim($m[1]),':'));
                }
                preg_match('/^\s*\**\s*@content(.*)$/',$line,$m);
                if (isset($m[1])) {
                    $info['content'] = trim(ltrim(trim($m[1]),':'));
                }
                preg_match('/^\s*\**\s*@shortcode(.*)$/',$line,$m);
                if (isset($m[1])) {
                    $info['shortcode'] = trim(ltrim(trim($m[1]),':'));
                }
                preg_match('/^\s*\**\s*@param(.*)$/',$line,$m);
                if (isset($m[1])) {
                    $m[1] = trim($m[1]);
                    strtok($m[1], ' '); // first word: throw-away type
                    $varname = strtok(' '); // second word: the varname
                    $varname = ltrim($varname,'$');
                    // todo: look for a default value
                    // $param_desc = 
                    $info['params'][$varname] = '';
                    preg_match('/\(default:(.*)\)$/i',$m[1],$m2);
                    if (isset($m2[1])) {
                        $info['params'][$varname] = trim($m2[1]);   
                    }
                }                
            }
		}
		
		return $info;
	}

	/**
	 * E.g. convert '/path/to/something.snippet.php' to 'something'
	 * This is the tag by which we can identify the snippet.
	 *
	 * @param string full path to file
	 * @param string extension
	 *
	 * @return mixed|string
	 */
    public static function get_shortname($file,$ext) {
        $file = basename($file);
		$x = strpos($file,'.'); // to the first dot
		return substr($file,0,$x);
    }

	/**
	 * Designed to safely retrieve scalar elements out of a hash. Don't use this
	 * if you have a more deeply nested object (e.g. an array of arrays).
	 *
	 * @param array   $hash    an associative array, e.g. array('animal' => 'Cat');
	 * @param string  $key     the key to search for in that array, e.g. 'animal'
	 * @param mixed   $default (optional) : value to return if the value is not set. Default=''
	 * @return mixed
	 */

	public static function get_value($hash, $key, $default='') {
		if ( !isset($hash[$key]) ) {
			return $default;
		}
		else {
			if ( is_array($hash[$key]) ) {
				return $hash[$key];
			}
			else {
				return $hash[$key];
			}
		}
	}

	/**
	 * Load up a PHP file into a string via an include statement. MVC type usage here.
	 *
	 * @param string  $filename (relative to the views/ directory)
	 * @param array   $data (optional) associative array of data
	 * @return string the parsed contents of that file
	 */
	public static function load_view($filename, $data=array()) {

		$path = PHP_SNIPPETS_PATH . '/templates/';

		if (is_file($path.$filename)) {
			ob_start();
			include $path.$filename;
			return ob_get_clean();
		}
		die('View file does not exist: ' .$path.$filename);
	}


	/**
	 * @param	string	$msg	 the localized error message.
	 */
	public static function register_warning($msg) {
		self::$warnings[$msg] = 1; // ensure warnings are not duplicated
	}


	/**
	 * Parse a string with replacements
	 *
	 * @param $str
	 * @param $placeholders
	 *
	 * @return string $dirname
	 */
	public static function parse($str,$placeholders) {
        foreach ($placeholders as $k => $v) {
            $str = str_replace('[+'.$k.'+]', $v, $str);
        }
        return $str;
	}
	
}
/*EOF*/