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
interface Action_Interface {

    /**
     * The name of the action.
     *
     * @return string
     * @since 2.0.0
     * @access public
     */
    public function get_key();

    /**
     * Handle the action.
     *
     * @return mixed
     * @since 2.0.0
     * @access public
     */
    public function handle();

}