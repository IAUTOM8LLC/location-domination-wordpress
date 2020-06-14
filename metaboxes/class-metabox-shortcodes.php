<?php

/**
 * @link       https://i-autom8.com
 * @since      1.0.0
 *
 * @package    Location_Domination
 * @subpackage Location_Domination/admin
 */

/**
 * @package    Location_Domination
 * @subpackage Location_Domination/admin
 * @author     iAutoM8 LLC <support@i-autom8.com>
 */
class Metabox_Shortcodes implements Metabox_Interface {

    /**
     * The contents of the metabox.
     *
     * @return string
     */
    public function handle() {
        include_once( __DIR__ . '/views/shortcodes.php' );
    }

    /**
     * The context to register the metabox.
     *
     * @return string
     * @since 2.0.0
     * @access public
     */
    public function get_context() {
        return 'side';
    }

    /**
     * The name of the action.
     *
     * @return string
     * @since 2.0.0
     * @access public
     */
    public function get_key() {
        return 'shortcode_display';
    }

    /**
     * The screen to register the metabox on.
     *
     * @return string
     * @since 2.0.0
     * @access public
     */
    public function get_screen() {
        return 'mptemplates';
    }

    /**
     * The title of the metabox.
     *
     * @return string
     * @since 2.0.0
     * @access public
     */
    public function get_title() {
        return 'Location Domination Shortcodes';
    }
}