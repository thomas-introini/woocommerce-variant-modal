<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return merged settings with defaults.
 */
function wcvm_get_settings(): array {
	$defaults = [
		'enabled'             => 1,
		'enable_on_archives'  => 1,
		'close_on_esc'        => 1,
		'close_on_backdrop'   => 1,
		'lock_scroll'         => 1,
		'show_quantity'       => 1,
		'show_stock'          => 1,
		'show_sku'            => 0,
		// Leave empty to preserve WooCommerce's original link text; UI will fallback.
		'button_text'         => '',

		'primary_color'       => '#1a73e8',
		'accent_color'        => '#0b57d0',
		'text_color'          => '#111827',
		'modal_bg'            => '#ffffff',
		'overlay_color'       => '#000000',
		'overlay_opacity'     => '0.55',
		'border_radius'       => '14',

		'custom_css'          => '',
	];

	$settings = get_option( 'wcvm_settings', [] );
	$settings = wp_parse_args( (array) $settings, $defaults );

	// Sanitize numeric strings used in CSS.
	$settings['border_radius']   = preg_replace( '/[^0-9.]/', '', (string) $settings['border_radius'] );
	$settings['overlay_opacity'] = min( 1, max( 0, floatval( $settings['overlay_opacity'] ) ) );

	return $settings;
}

/**
 * Quick bool check helper.
 */
function wcvm_bool( $val ): bool {
	return (bool) ( $val === '1' || $val === 1 || $val === true );
}
