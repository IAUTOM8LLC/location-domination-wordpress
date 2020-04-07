<?php

/*
Plugin Name: Location Domination
Plugin URI: #
Description: An iAutoM8 plugin designed to make mass page generating easy!
Version: 1.50
Author: iAutoM8
Author URI: https://i-autom8.com
License: GPL2
*/

if ( ! defined('WPINC') ){
	die;
}

define( 'LOCATION_DOMINATION_VER', 1.50 );

/*
 * Include the core classes
 */

define( 'MPBUILDER_DATA_PATH', __DIR__ . '/data' );
define( 'LOCATION_DOMINATION_ROOT_DIR', __DIR__ );

require __DIR__ . '/includes/puc/plugin-update-checker.php';

$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/IAUTOM8LLC/location-domination-wordpress/',
    __FILE__, //Full path to the main plugin file or functions.php.
    'location-domaination'
);

$myUpdateChecker->setBranch('stable' );

require_once plugin_dir_path(__FILE__ ) . '/includes/mpbuilder-main.php';

function run_main_class() {
	$mpbuilder = new mpbuilder_main();
	$mpbuilder->run();
}

run_main_class();

/**
 * Admin Queries
 */
require_once __DIR__ . '/admin/queries/mpb-queries.php';
require_once __DIR__ . '/admin/queries/create-tables.php';

function activate() {
    flush_rewrite_rules();

    $create = new create_tables();
    $create->create_all_cities_table();

    $mpb_queries = new mpb_queries();
    $mpb_queries->insert_cities();
}

register_activation_hook( __FILE__, 'activate' );