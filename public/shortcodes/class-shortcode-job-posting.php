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
class Shortcode_Job_Posting implements Shortcode_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'ld_job';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle( $attributes = null ) {
        $map = [
            'description' => '_ld_job_description',
            'title' => '_ld_job_title',
            'employment_type' => 'job_employment_type',
            'company_name' => 'company_name',
            'date_posted' => 'job_date_posted',
            'valid_through' => 'job_valid_through',
        ];

        $attributes = shortcode_atts( array(
            'field' => '',
        ), $attributes );

        if ( $attributes['field'] && isset( $map[ $attributes['field'] ] ) ) {
            $field = $map[ $attributes['field'] ];
            $meta = get_post_meta( get_the_ID(), $field, true );

            if ( $meta ) {
                return $meta;
            }
        }
    }

}