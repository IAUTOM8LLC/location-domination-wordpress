<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://i-autom8.com
 * @since             1.0.0
 * @package           Location_Domination
 *
 * @wordpress-plugin
 * Plugin Name:       Location Domination
 * Plugin URI:        https://locationdomination.net
 * Description:       An iAutoM8 plugin designed to make mass page generating easy!
 * Version:           2.0.2
 * Author:            iAutoM8 LLC
 * Author URI:        https://i-autom8.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       location-domination
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( __DIR__ . '/vendor/autoload.php' );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LOCATION_DOMINATION_VERSION', '2.0.2' );

/**
 * The URL to interact with Location Domination.
 */

define( 'MAIN_URL', 'https://staging.locationdomination.net/' );
//define( 'MAIN_URL', 'https://locationdomination.net/' );


/**
 * The option key for storing the API token.
 *
 * @since 2.0.0
 */
define( 'LOCATION_DOMINATION_API_OPTION_KEY', 'mpb_api_key' );

/**
 * The option key for checking whether the website is connected.
 *
 * @since 2.0.0
 */
define( 'LOCATION_DOMINATION_API_CONNECTED_OPTION_KEY', 'ld_connected' );

/**
 * The option key for storing the location type.
 *
 * @since 2.0.0
 */
define( 'LOCATION_DOMINATION_LOCATION_TYPE_OPTION', 'mpb_location_type' );

/**
 * The custom post type name.
 *
 * @since 2.0.0
 */
define( 'LOCATION_DOMINATION_TEMPLATE_CPT', 'mptemplates' );

/**
 * The path that we will register ACF.
 *
 * @since 2.0.0
 */
define( 'LOCATION_DOMINATION_ACF_PATH', __DIR__ . '/includes/acf' );

/**
 * The URL in which we can access ACF.
 *
 * @since 2.0.0
 */
define( 'LOCATION_DOMINATION_ACF_URL', plugin_dir_url( __FILE__ ) . 'includes/acf/' );

/**
 * Bootstrapping helper functions + third-party files
 */
require_once( __DIR__ . '/includes/helpers.php' );
include_once( LOCATION_DOMINATION_ACF_PATH . '/acf.php' );
include_once( LOCATION_DOMINATION_ACF_PATH . '/fields.php' );

/**
 * Connect to the Plugin Update Checker to load
 * updates from the GitHub repository.
 */
$checker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/IAUTOM8LLC/location-domination-wordpress/',
    __FILE__,
    'location-domination'
);

$checker->setBranch('stable');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-location-domination-activator.php
 */
function activate_location_domination() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-location-domination-activator.php';
	Location_Domination_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-location-domination-deactivator.php
 */
function deactivate_location_domination() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-location-domination-deactivator.php';
	Location_Domination_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_location_domination' );
register_deactivation_hook( __FILE__, 'deactivate_location_domination' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-location-domination.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_location_domination() {
	$plugin = new Location_Domination();
	$plugin->run();
}

run_location_domination();
