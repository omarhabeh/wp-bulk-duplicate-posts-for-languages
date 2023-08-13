<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Wp_Posts_Language_Duplicator' ) ) :

	/**
	 * Main Wp_Posts_Language_Duplicator Class.
	 *
	 * @package		WPPOSTSLAN
	 * @subpackage	Classes/Wp_Posts_Language_Duplicator
	 * @since		1.0.0
	 * @author		Omar Habeh
	 */
	final class Wp_Posts_Language_Duplicator {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Wp_Posts_Language_Duplicator
		 */
		private static $instance;

		/**
		 * WPPOSTSLAN helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Wp_Posts_Language_Duplicator_Helpers
		 */
		public $helpers;

		/**
		 * WPPOSTSLAN settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Wp_Posts_Language_Duplicator_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'wp-posts-language-duplicator' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'wp-posts-language-duplicator' ), '1.0.0' );
		}

		/**
		 * Main Wp_Posts_Language_Duplicator Instance.
		 *
		 * Insures that only one instance of Wp_Posts_Language_Duplicator exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Wp_Posts_Language_Duplicator	The one true Wp_Posts_Language_Duplicator
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Wp_Posts_Language_Duplicator ) ) {
				self::$instance					= new Wp_Posts_Language_Duplicator;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->enqueue();
				self::$instance->helpers		= new Wp_Posts_Language_Duplicator_Helpers();
				self::$instance->settings		= new Wp_Posts_Language_Duplicator_Settings();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'WPPOSTSLAN/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once WPPOSTSLAN_PLUGIN_DIR . 'core/includes/classes/class-wp-posts-language-duplicator-settings.php';
			require_once WPPOSTSLAN_PLUGIN_DIR . 'core/includes/classes/class-wp-posts-language-duplicator-table.php';
			require_once WPPOSTSLAN_PLUGIN_DIR . 'core/includes/classes/class-wp-posts-language-duplicator-helpers.php';
		}


		private function enqueue(){
			wp_enqueue_style( 'WPPOSTLAN_STYLE', plugin_dir_url( __FILE__ ) . 'includes/assets/css/backend-style.css', false, '1.0', 'all' ); 
			wp_enqueue_script( 'WPPOSTLAN_SCRIPT', plugin_dir_url( __FILE__ ) . 'includes/assets/js/backend-scripts.js', array('jquery'), '1.0.0', true );
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'wp-posts-language-duplicator', FALSE, dirname( plugin_basename( WPPOSTSLAN_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.