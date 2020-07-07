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
class Action_Start_Indexing implements Action_Interface {

    /**
     * The table name for indexing.
     *
     * @var string
     */
    protected $table_name;

    /**
     * The template UUID.
     *
     * @var string
     */
    protected $template_uuid;

    protected $request;

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'location_domination_start_indexing';
    }

    /**
     * @return mixed
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest( $request ) {
        $this->request = $request;
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle() {
        if ( ! $this->request ) {
            return;
        }
//        if ( wp_verify_nonce( $_REQUEST[ '_nonce' ], 'location-domination-start-indexing' ) === false ) {
//            return wp_send_json( [ 'success' => false, 'message' => 'Your session has expired.' ] );
//        }
        require_once( __DIR__ . '/../../includes/class-location-domination-activator.php' );

        $country = (int) esc_attr( $this->request[ 'country' ] );
        $this->table_name = Location_Domination_Activator::getTableName();
        $this->template_uuid = esc_attr( $this->request[ 'uuid' ] );

        if ( $country === 236 ) {
            // United States
            $states = $this->get_states();

            if ( count( $states ) > 1 ) {
                $this->insert_index_page( 'United States', sprintf( 'scope="states" country="%s"', 'United States' ), 'country', 'United States' );
            }

            foreach ( $states as $record ) {
                $this->insert_index_page( $record->state, sprintf( 'scope="counties" state="%s"', $record->state ), 'state', $record->state );
            }

            $counties = $this->get_counties();

            foreach ( $counties as $record ) {
                $this->insert_index_page( $record->county, sprintf( 'scope="cities" state="%s" county="%s"', $record->state, $record->county ), 'county', sprintf( '%s, %s', $record->state, $record->county ) );
            }
        } else {
            // Regions
            $regions = $this->get_regions();

            foreach ( $regions as $record ) {
                $this->insert_index_page( $record->region, sprintf( 'region="%s"', $record->region ), 'region', sprintf( '%s, %s', $record->country, $record->region ) );
            }
        }
    }

    protected function insert_index_page( $name, $scope, $context, $context_value ) {
        $post_id = wp_insert_post([
            'post_title' => $name,
            'post_type' => $this->template_uuid,
            'post_status' => 'publish',
            'post_content' => "[internal_links $scope]",
        ]);

        if ( ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, $context, $context_value );
        }

        return false;
    }

    protected function get_regions() {
        global $wpdb;

        $table_name = $this->table_name;

        $query = $wpdb->prepare( "SELECT region FROM ${table_name} WHERE post_type = '%s' GROUP BY region", $this->template_uuid );

        return $wpdb->get_results( $query );
    }

    protected function get_states() {
        global $wpdb;

        $table_name = $this->table_name;

        $query = $wpdb->prepare( "SELECT state FROM ${table_name} WHERE post_type = '%s' GROUP BY state", $this->template_uuid );

        return $wpdb->get_results( $query );
    }

    protected function get_counties() {
        global $wpdb;

        $table_name = $this->table_name;

        $query = $wpdb->prepare( "SELECT state, county FROM ${table_name} WHERE post_type = '%s' GROUP BY state, county", $this->template_uuid );

        return $wpdb->get_results( $query );
    }

}
