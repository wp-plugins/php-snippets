<?php
/**
 * This widget allows the user to choose a PHP Snippet to power a widget.
 *
 */
namespace PhpSnippets; 
class Widget extends \WP_Widget {

	public $name;
	public $description;
	public $control_options = array(
		'title' => 'Posts'
	);
	
	public function __construct() {
		$this->name = __('PHP Snippet', 'php_snippets');
		$this->description = __('Choose a PHP Snippet that will generate the content for this widget.', 'php_snippets');
		$widget_options = array(
			'classname' => __CLASS__,
			'description' => $this->description,
		);
		
		parent::__construct(__CLASS__, $this->name, $widget_options, $this->control_options);

		// We only need the additional functionality for the back-end.
		// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=331
		//if( is_admin() && is_active_widget( false, false, $this->id_base, true )) {	
		if( is_admin() && 'widgets.php' == substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1)) {
			wp_enqueue_script('thickbox');
			wp_register_script('PHP_Snippet_Widget', PHP_SNIPPETS_URL.'/js/widget.js', array('jquery'));
			wp_enqueue_script('PHP_Snippet_Widget');
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Create only form elements.
	 */
	public function form($instance) {
		
		$snippets = Functions::get_snippets(true);

//		print_r($snippets); return;
		$snippet_options = '<option></option>';
		foreach($snippets as $s => $info) {
			$snippet_options .= sprintf('<option value="%s">%s</option>',$info['path'],$s);
		}
		
		$args_str = ''; // formatted args for the user to look at so they remember what they searched for.
		
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
			<select name="'.$this->get_field_name('snippet').'" id="'.$this->get_field_id('snippet').'" onchange="add_php_snippet(this.value,\''.$this->get_field_id('content').'\');">'
				.$snippet_options.
			'</select>
			
			<br/>
			
			<label class="php_snippets_label" for="'.$this->get_field_id('content').'">'.__('Widget Content', 'php_snippets').'</label>
			<p>'.__('Modify your shortcode below', 'php_snippets').'</p>
			
			<textarea name="'.$this->get_field_name('content').'" id="'.$this->get_field_id('content').'" rows="3" cols="30">'.$instance['content'].'</textarea>
			';
	}
	
	//------------------------------------------------------------------------------
	/**
	 * This generates the widget content on the front-end. 
	 */
	function widget($args, $instance) {
	
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

	//------------------------------------------------------------------------------
	//! Static
	public static function register_this_widget() {
		$php_license = License::check();
		if($php_license == 'valid') {
			register_widget(__CLASS__);
		}
	}
}

/*EOF*/