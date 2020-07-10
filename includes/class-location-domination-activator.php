<?php

/**
 * Fired during plugin activation
 *
 * @link       https://i-autom8.com
 * @since      1.0.0
 *
 * @package    Location_Domination
 * @subpackage Location_Domination/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Location_Domination
 * @subpackage Location_Domination/includes
 * @author     iAutoM8 LLC <support@i-autom8.com>
 */
class Location_Domination_Activator {

    static $INDEX_CREATE_TABLE_SQL = "CREATE TABLE `{prefix}locationdomination_index`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_type` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `post_id` int(10) UNSIGNED NULL DEFAULT NULL,
  `country` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `state` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `county` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `region` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `city` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `locked` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;";

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        maybe_create_table( self::getTableName(), self::getTableSql());
	}

    /**
     * Get the creation SQL for the table.
     *
     * @return string|string[]
     */
	public static function getTableSql() {
	    global $wpdb;

	    $sql = self::$INDEX_CREATE_TABLE_SQL;

	    return str_replace( '{prefix}', $wpdb->prefix, $sql );
    }

    public static function getTableName() {
	    global $wpdb;

	    return $wpdb->prefix . 'locationdomination_index';
    }

}
