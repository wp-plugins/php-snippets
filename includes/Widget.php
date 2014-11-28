<?php
/**
 * This widget allows the user to choose a local file (i.e. a PHP Snippet)
 * be used to generate a widget.
 */
namespace PhpSnippets; 
class Widget extends \WP_Widget {

	public $name;
	public $description;
	public $control_options = array(
		'title' => 'Posts'
	);
	public static $dirs = array();
	public static $ext;
	public function __construct() {
		$this->name = __('PHP Snippet', 'php_snippets');
		$this->description = __('Choose a PHP Snippet that will generate the content for this widget.', 'php_snippets');
		$widget_options = array(
			'classname' => __CLASS__,
			'description' => $this->description,
		);

		// Insanity: WP has slowly allowed for compatibility with namespaces here...
		parent::__construct('phpsnippets_widget', $this->name, $widget_options, $this->control_options);
		//parent::__construct( 'baseID', 'name' );

//		parent::__construct(__CLASS__, $this->name, $widget_options, $this->control_options);
		//parent::__construct('PhpSnippets\\Widget', $this->name, $widget_options, $this->control_options);
	}

    /**
	 * OLD: this used to be necessary because WP didn't handle namespaces correctly.
     * Generates a unique id for a field instance (because there may be many widgets).
     * Make corrections: WP doesn't expect a classname with a backslash (namespace)
     * jQuery isn't able to target a field id if the identifier has invalid characters.
     */
//    public function get_field_id($id) {
//        $id = parent::get_field_id($id);
//        return str_replace('\\', '_', $id);
//    }
//    public function get_field_name($name) {
//        $name = parent::get_field_id($name);
//        return str_replace('\\', '_', $name);
//    }
//
    
	/**
	 * Create only form elements.
	 */
	public function form($instance) {

		$snippet_options = '<option></option>'; // empty first value
		foreach (self::$dirs as $d => $exists) {
            $snippets = (array) Base::get_snippets($d,self::$ext);
    		foreach ($snippets as $s) {
				if (isset($instance['snippet']) && $instance['snippet'] == $s)
				{
					$snippet_options .= sprintf('<option value="%s" selected="selected">%s</option>',
						$s,Base::get_shortname($s,self::$ext));
				}
				else
				{
					$snippet_options .= sprintf('<option value="%s">%s</option>',$s,Base::get_shortname($s,self::$ext));
				}
    		}
		}
		
		if (!isset($instance['title'])) {
			$instance['title'] = ''; 	// default value
		}
		if (!isset($instance['snippet'])) {
			$instance['snippet'] = ''; 	// default value
		}
		if (!isset($instance['content'])) {
			$instance['content'] = ''; 	// default value
		}
		
		print '<p>'.$this->description
			. '</p>
			<label class="cctm_label" for="'.$this->get_field_id('title').'">'.__('Title', 'php_snippets').'</label>
			<input type="text" name="'.$this->get_field_name('title').'" id="'.$this->get_field_id('title').'" value="'.$instance['title'].'" />
			
			<label for="" class="php_snippets_label">'.__('Snippet', 'php_snippets').'</label>
			<select name="'.$this->get_field_name('snippet').'" id="'.$this->get_field_id('snippet').'" onchange="javascript:add_php_snippet(this.value,\''.$this->get_field_id('content').'\');">'
				.$snippet_options.
			'</select>
			
			<br/>
			
			<label class="php_snippets_label" for="'.$this->get_field_id('content').'">'.__('Widget Content', 'php_snippets').'</label>
			<p>'.__('Modify your shortcode below', 'php_snippets').'</p>
			
			<textarea name="'.$this->get_field_name('content').'" id="'.$this->get_field_id('content').'" rows="3" cols="30">'.$instance['content'].'</textarea>
			';
	}

	/**
	 * This generates the widget content on the front-end. 
	 */
	public function widget($args, $instance) {
		//error_log('xyzzy');
		//print '<h2>xyzzy</h2>'; exit;
/*
		if (!isset($instance['content']) || empty($instance['content'])) {
			return; // don't do anything unless we got some content
		}
		
*/
		$output = $args['before_widget']
			. $args['before_title']
			. $instance['title']
			. $args['after_title']
			. do_shortcode($instance['content'])
			. $args['after_widget'];
		
		print $output;
	}

	//! Static
	public static function register_this_widget() {
		//print __CLASS__; exit;
        register_widget(__CLASS__);
        //register_widget('PhpSnippets\\Widget');
	}
	
	public static function setDirs($dirs) {
	   self::$dirs = $dirs;
	}
	public static function setExt($ext) {
	   self::$ext = $ext;
	}
}

/*EOF*/