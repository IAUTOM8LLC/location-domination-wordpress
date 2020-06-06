<?php
    global $post;

    $previous_request_meta = get_post_meta( $post->ID, 'location_domination_post_request', true );
    $previous_request =  $previous_request_meta ? json_encode( $previous_request_meta ) : 'null';
?>
<div class="location-domination-vendor" id="location-domination-settings">
    <post-builder :previous-request="<?php echo htmlentities($previous_request); ?>" nonce="<?php echo wp_create_nonce( 'location-domination-start-queue' ); ?>" ajax-url="<?php echo admin_url('admin-post.php'); ?>" template-id="<?php echo $post->ID ?>" />
</div>