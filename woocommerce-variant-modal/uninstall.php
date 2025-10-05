<?php
/**
 * Uninstall cleanup.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove plugin options.
delete_option( 'wcvm_settings' );
