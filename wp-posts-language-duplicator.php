<?php
/**
 * WP posts language duplicator
 *
 * @package       WPPOSTSLAN
 * @author        Omar Habeh
 * @license       gplv2
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   WP posts language duplicator
 * Plugin URI:    https://letket.com
 * Description:   a WP plugin that duplicates all posts, categories, custom fields & templates for the post for all other languages set.
 * Version:       1.0.0
 * Author:        Omar Habeh
 * Author URI:    https://letket.com
 * Text Domain:   wp-posts-language-duplicator
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with WP posts language duplicator. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
// Plugin name
define( 'WPPOSTSLAN_NAME',			'WP posts language duplicator' );

// Plugin version
define( 'WPPOSTSLAN_VERSION',		'1.0.0' );

// Plugin Root File
define( 'WPPOSTSLAN_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'WPPOSTSLAN_PLUGIN_BASE',	plugin_basename( WPPOSTSLAN_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'WPPOSTSLAN_PLUGIN_DIR',	plugin_dir_path( WPPOSTSLAN_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'WPPOSTSLAN_PLUGIN_URL',	plugin_dir_url( WPPOSTSLAN_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once WPPOSTSLAN_PLUGIN_DIR . 'core/class-wp-posts-language-duplicator.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Omar Habeh
 * @since   1.0.0
 * @return  object|Wp_Posts_Language_Duplicator
 */
function WPPOSTSLAN() {
	return Wp_Posts_Language_Duplicator::instance();
}

WPPOSTSLAN();
