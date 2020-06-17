<?php
    global $post, $wpdb;

    $index_table_name = $wpdb->prefix . LOCATION_DOMINATION_INDEX_DB_TABLE;

    $previous_request_meta = get_post_meta( $post->ID, 'location_domination_post_request', true );
    $previous_request =  $previous_request_meta ? json_encode( $previous_request_meta ) : 'null';
    $previous_request_transient = get_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY  . '_' . $post->ID );
    $previous_request_transient = json_encode( $previous_request_transient ) ?: 'null';

    $post_type = get_post_meta( $post->ID, '_uuid', true );
    $post_type_post_count = wp_count_posts( $post_type );
    $index_matches = $wpdb->get_var( "SELECT COUNT(ID) FROM $index_table_name WHERE post_type = '$post_type'" );
    $requires_indexing = (int) $post_type_post_count->publish !== $index_matches;
?>
<div class="location-domination-vendor" id="location-domination-settings">
    <post-builder :requires-indexing="<?php echo $requires_indexing ? 'false' : 'true'; ?>" :previous-request-transient="<?php echo htmlentities($previous_request_transient); ?>" :previous-request="<?php echo htmlentities($previous_request); ?>" nonce="<?php echo wp_create_nonce( 'location-domination-start-queue' ); ?>" ajax-url="<?php echo admin_url('admin-post.php'); ?>" template-id="<?php echo $post->ID ?>" />
</div>