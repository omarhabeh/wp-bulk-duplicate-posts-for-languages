<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Wp_Posts_Language_Duplicator_Settings
 *
 * This class contains all of the plugin settings.
 * Here you can configure the whole plugin data.
 *
 * @package		WPPOSTSLAN
 * @subpackage	Classes/Wp_Posts_Language_Duplicator_Settings
 * @author		Omar Habeh
 * @since		1.0.0
 */
class Wp_Posts_Language_Duplicator_Settings{

	/**
	 * The plugin name
	 *
	 * @var		string
	 * @since   1.0.0
	 */
	private $plugin_name;

	/**
	 * Our Wp_Posts_Language_Duplicator_Settings constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->plugin_name = WPPOSTSLAN_NAME;
		add_action('admin_menu', [$this, 'add_admin_submenu']);
		add_action('admin_init', [$this, 'register_admin_fields']);
		// add_action('wp_ajax_process_duplication',[$this,'ajax']);
	}


	/**
	 * Return the plugin name
	 *
	 * @access	public
	 * @since	1.0.0
	 * @return	string The plugin name
	 */
	public function get_plugin_name(){
		return apply_filters( 'WPPOSTSLAN/settings/get_plugin_name', $this->plugin_name );
	}

	/**
	 * Creates admin sub-menu under settings menu 
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	public function add_admin_submenu() {
        add_submenu_page(
            'options-general.php',
            'WP posts languages duplicator',      
            'WP posts languages duplicator',      
            'manage_options',      
            'wp-posts-language-duplicator',      
        	[$this, 'admin_page']
        );
    }

	/**
	 * shows the admin setting page
	 *
	 * @access	public
	 * @since	1.0.0
	 */
    public function admin_page() { ?>
		<div class="wrap">
			
			<h1>WP posts language duplicator</h1>

			<form method="post" action="options.php">
				<?php
					/* calling form settings */
					settings_fields('form_settings'); 
					do_settings_sections('form_settings');
					submit_button(); 
				?>
			</form>
			
			<?php 
				/* calling table settings */
				settings_fields('table_settings'); 
				do_settings_sections('table_settings');
			?>

		</div>
	<?php }

	/**
	 * register the admin fields that shows on the page
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	public function register_admin_fields(){

		/* form settings that includes sections & fields of the form */
		register_setting('form_settings', 'translation_plugin_options');
		register_setting('form_settings', 'post_type_options');
		register_setting('form_settings', 'list_languages_options');
		register_setting('form_settings', 'duplicated_post_statuses');
		
		/* table settings includes the call for tha table */
		register_setting('table_settings', 'post-list-section');
		
		add_settings_section(
			'general_settings',
			'General Settings',    
			[$this,'discriptions_section'],
			'form_settings' 
		);

		add_settings_field(  
			'translation_plugin_options',  
			'Which translation plugin you use',  
			[$this,'plugin_options'],  
			'form_settings',  
			'general_settings'  
		);
		
		add_settings_field(  
			'post_type_options',  
			'Post types to be translated',  
			[$this,'allowed_post_types'],  
			'form_settings',  
			'general_settings'  
		);
		
		add_settings_field(  
			'list_languages_options',  
			'Languages that will be duplicated to',  
			[$this,'list_languages_options'],  
			'form_settings',  
			'general_settings'  
		);
		
		add_settings_field(  
			'duplicated_post_statuses',  
			'Post statuses that will be duplicated',  
			[$this,'duplicated_post_statuses'],  
			'form_settings',  
			'general_settings'  
		);
		
		if(get_option( 'translation_plugin_options' ) && get_option( 'post_type_options' )){
			add_settings_section(
				'post-list-section',
				'Posts/Pages that will be translated',    
				[$this,'list_posts_to_be_translated'],
				'table_settings' 
			);
		}
	}

	/**
	 * Callback function to display section description
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	public function discriptions_section() {
		
		echo 'Configure general settings for the plugin.';
		echo '<p>this version only supports polylang duplications</p>';
		echo '<p>ONLY RUN THE PROCESS AFTER CHECKING THE TABLE OF POSTS/PAGES</p>';
	}

	/**
	 * Callback function to display section translation plugins
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	public function plugin_options() {

		$options = get_option( 'translation_plugin_options' );
		
		$html = '<input type="radio" id="checkbox" name="translation_plugin_options" value="polylang" ' . checked( 'polylang',$options, false )  . '/>';
		$html .= '<label for="checkbox">Polylang</label>';
		
		$html .= '<input type="radio" id="checkbox" name="translation_plugin_options" disabled value="wpml" ' . checked( 'wpml',$options, false ) . '/>';
		$html .= '<label for="checkbox">WPML</label>';
	
		echo $html;
	
	}

	/**
	 * Callback function to show allowed post types options
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	public function allowed_post_types(){
		
		$options = get_option( 'post_type_options' ) ?: [];
		$html = '';

		foreach ($options as $key => $post_type) {
			$html .= '<input type="checkbox" id="post_type_' . $key . '" name="post_type_options[]" value="' . $post_type . '" ' . checked( in_array($post_type, $options), true, false ) . '/>';
			$html .= '<label for="post_type_' . $key . '">' . $post_type . '</label>';
		}
		
		echo $html;
	}

	/**
	 * Callback function to show list of language options
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	public function list_languages_options(){
		
		$languages = pll_languages_list();
		$main_language = pll_default_language();
		$options = get_option( 'list_languages_options' ) ?: [];
		
		$html = '';
		foreach ($languages as $key => $lang) {
			
			/* exclude main language from list */
			if($lang != $main_language){
				$html .= '<input type="checkbox"'.($main_language == $lang ? 'disabled':'').' id="list_languages_options_' . $key . '" name="list_languages_options[]" value="' . $lang . '" ' . checked( in_array($lang, $options), true, false ) . '/>';
				$html .= '<label for="list_languages_options_' . $key . '">' . $lang . '</label>';
			}
		}
		
		echo $html;
	}
	
	/**
	 * Callback function to show list of post statuses allowed
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	public function duplicated_post_statuses(){

		$options = get_option( 'duplicated_post_statuses' );
		
		$html = '<select name="duplicated_post_statuses">
		<option '.($options == "all" ? "selected":"").' value="all">All</option>
		<option '.($options == "publish" ? "selected":"").' value="publish">Only Published</option>
		<option '.($options == "draft" ? "selected":"").' value="draft">Only Draft</option>
		</select>';

		echo $html;
	}

	/**
	 * Callback function to show the table of posts that match description of the form
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	public function list_posts_to_be_translated(){
		$posts_table = new Wp_Posts_Language_Duplicator_Table();

		echo '<form method="post" class="table-form">';
		$posts_table->prepare_items();
		$posts_table->display();
		echo '<input type="submit" name="bulk-duplicate" class="button button-primary action" value="Duplicate">';
		echo '</form>';

		// echo '<div id="loader" class="loader"></div>';
	}

	// public function ajax(){	
	// 	echo json_encode($_POST);
	// 	exit;
	// }
}