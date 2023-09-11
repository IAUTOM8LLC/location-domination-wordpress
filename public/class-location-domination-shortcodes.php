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
class Location_Domination_Shortcodes {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * An array representation of the shortcodes that we
     * provide.
     *
     * @var string[]
     * @since  2.0.0
     * @access protected
     */
    protected $shortcodes = [
        Shortcode_Map::class,
        Shortcode_Zips::class,
        Shortcode_City::class,
        Shortcode_State::class,
        Shortcode_County::class,
        Shortcode_Breadcrumbs::class,
        Shortcode_Internal_Links::class,
        Shortcode_Job_Posting::class,
        Shortcode_State_Abbreviation::class,
        Shortcode_Neighborhoods::class,
        Shortcode_RelatedCityPosts::class,
        Shortcode_RelatedCityNoLink::class,
        Shortcode_MetaCity::class,
    ];

    /**
     * An array of the loaded shortcodes that make use of the
     * Shortcode_Interface class.
     *
     * @var Shortcode_Interface[] $loaded_shortcodes
     * @since  2.0.0
     * @access protected
     */
    protected $loaded_shortcodes = [];

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     *
     * @since    1.0.0
     * @access   public
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        foreach ( $this->shortcodes as $shortcode ) {
            $this->loaded_shortcodes[ $shortcode ] = new $shortcode();

            if ( is_array( $this->loaded_shortcodes[ $shortcode ]->get_key() ) ) {
                foreach( $this->loaded_shortcodes[ $shortcode ]->get_key() as $key ) {
                    add_shortcode( $key, [
                        $this->loaded_shortcodes[ $shortcode ],
                        'handle'
                    ] );
                }
            } else {
                add_shortcode( $this->loaded_shortcodes[ $shortcode ]->get_key(), [
                    $this->loaded_shortcodes[ $shortcode ],
                    'handle'
                ] );
            }

        }
    }

    /**
     * @param $content
     *
     * @return string
     * @since  2.0.0
     * @access public
     */
    public function standardize_shortcodes( $content ) {
        foreach ( $this->loaded_shortcodes as $shortcode ) {
            if ( is_array( $shortcode->get_key() ) ) {
                foreach ( $shortcode->get_key() as $key ) {
                    $content = str_ireplace( $key, strtolower( $key ), $content );
                }
            } else {
                $content = str_ireplace( '[' . $shortcode->get_key(), '[' . strtolower( $shortcode->get_key() ), $content );
            }
        }

        return $content;
    }
}
