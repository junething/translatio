<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/tmysoft/tmy-wordpress
 * @since             1.0.0
 * @package           TMY_G11n
 *
 * @wordpress-plugin
 * Plugin Name:       TMY Globalization
 * Plugin URI:        https://github.com/tmysoft/tmy-wordpress
 * Description:       Translating your application into other languages.
 * Version:           1.9.0
 * Author:            Yu Shao
 * Author URI:        https://github.com/tmysoft
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tmy-g11n
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TMY_G11N_VERSION', '1.9.0' );
define( 'WP_TMY_G11N_DEBUG', false );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tmy-g11n-activator.php
 */
function activate_tmy_g11n( $network_wide ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tmy-g11n-activator.php';
	TMY_G11n_Activator::activate( $network_wide );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_tmy_g11n() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tmy-g11n-deactivator.php';
	TMY_G11n_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tmy_g11n' );
register_deactivation_hook( __FILE__, 'deactivate_tmy_g11n' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

require plugin_dir_path( __FILE__ ) . 'includes/class-tmy-g11n-widget.php';

require plugin_dir_path( __FILE__ ) . 'includes/class-tmy-g11n.php';

require plugin_dir_path( __FILE__ ) . 'includes/tmy-g11n-plugin-globals.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tmy_g11n() {

	$plugin = new TMY_G11n();
	$plugin->run();

}
run_tmy_g11n();
