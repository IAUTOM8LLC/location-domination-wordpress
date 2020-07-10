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
class Action_End_Queue implements Action_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'location_domination_end_queue';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle() {
        $template_id = (int) $_REQUEST[ 'templateId' ];
        $option = get_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY . '_' . $template_id );

        // Build index pages
        $create_indexes = get_field( 'create_index_pages', $template_id );

        if ( $create_indexes ) {
            $indexer = new Action_Start_Indexing();
            $indexer->setRequest( $option->request );
            $indexer->handle();
        }

        Location_Domination_Admin::clear_permalinks_queued();

        if ( class_exists( 'Elementor\\Plugin' ) ) {
            // If using Elementor, re-generate CSS
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }

        // Delete queue transient
        delete_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY . '_' . $template_id );

        return wp_send_json([ 'success' => true ] );
    }

}
