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
interface Metabox_Interface {

    /**
     * The name of the action.
     *
     * @return string
     * @since 2.0.0
     * @access public
     */
    public function get_key();

    /**
     * The title of the metabox.
     *
     * @return string
     * @since 2.0.0
     * @access public
     */
    public function get_title();

    /**
     * The screen to register the metabox on.
     *
     * @return string
     * @since 2.0.0
     * @access public
     */
    public function get_screen();

    /**
     * The context to register the metabox.
     *
     * @return string
     * @since 2.0.0
     * @access public
     */
    public function get_context();

    /**
     * Handle the action.
     *
     * @return mixed
     * @since 2.0.0
     * @access public
     */
    public function handle();

}