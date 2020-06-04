<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://i-autom8.com
 * @since      1.0.0
 *
 * @package    Location_Domination
 * @subpackage Location_Domination/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap" id="location-domination-app">
    <layout :connected="<?php echo get_option( LOCATION_DOMINATION_API_CONNECTED_OPTION_KEY ) === 'connected' ? 'true' : 'false'; ?>" nonce="<?php echo wp_create_nonce( 'location-domination-settings' ); ?>" ajax-url="<?php echo admin_url('admin-post.php'); ?>" version="<?php echo LOCATION_DOMINATION_VERSION; ?>" class="h-full" />
</div>