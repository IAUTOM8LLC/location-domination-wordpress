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
class Shortcode_MetaCity implements Shortcode_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
    	return 'citymeta';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle( $attributes = null ) {
        $excluded_list = [
            'id','city','city_id','city_ascii','city_alt','state_id','state_name','county_fips','county_name','county_fips_all','county_name_all'
        ];
    	if (isset($attributes['name'])) {
    		$meta_key = sanitize_text_field($attributes['name']);
            if (in_array($meta_key,$excluded_list) ) {
                return '[citymeta name="' . $meta_key . '"]';
            }
    		$meta_data = json_decode(get_post_meta( get_the_ID(), '_city_meta', true ),1);
        	return isset($meta_data[$meta_key]) ? $meta_data[$meta_key] : '';
    	} else {
            return '[citymeta]';
        }

    	return '';
    }

}
