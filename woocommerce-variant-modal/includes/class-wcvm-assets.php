<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCVM_Assets {
	public static function init(): void {
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_assets' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'frontend_assets' ] );
	}

	public static function admin_assets( $hook ): void {
		// Load assets only on our settings page.
		if ( false === strpos( $hook, 'wcvm-settings' ) ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'wcvm-admin', WCVM_URL . 'assets/css/admin.css', [], WCVM_VERSION );
		wp_enqueue_script( 'wcvm-admin', WCVM_URL . 'assets/js/admin.js', [ 'wp-color-picker' ], WCVM_VERSION, true );
	}

	public static function frontend_assets(): void {
		$settings = wcvm_get_settings();

		if ( ! wcvm_bool( $settings['enabled'] ) ) {
			return;
		}

		// Styles
		wp_enqueue_style( 'wcvm-frontend', WCVM_URL . 'assets/css/frontend.css', [], WCVM_VERSION );

		// Inline CSS variables from settings for theming.
		$vars  = ':root{';
		$vars .= '--wcvm-primary:' . esc_attr( $settings['primary_color'] ) . ';';
		$vars .= '--wcvm-accent:' . esc_attr( $settings['accent_color'] ) . ';';
		$vars .= '--wcvm-text:' . esc_attr( $settings['text_color'] ) . ';';
		$vars .= '--wcvm-bg:' . esc_attr( $settings['modal_bg'] ) . ';';
		$vars .= '--wcvm-overlay:' . esc_attr( $settings['overlay_color'] ) . ';';
		$vars .= '--wcvm-overlay-opacity:' . esc_attr( $settings['overlay_opacity'] ) . ';';
		$vars .= '--wcvm-radius:' . esc_attr( $settings['border_radius'] ) . 'px;';
		$vars .= '}';
		if ( ! empty( $settings['custom_css'] ) ) {
			$vars .= "\n" . trim( $settings['custom_css'] );
		}
		wp_add_inline_style( 'wcvm-frontend', $vars );

		// Scripts:
		// Use Woo's variation engine + add to cart params.
		if ( ! wp_script_is( 'wc-add-to-cart-variation', 'registered' ) ) {
			wp_register_script( 'wc-add-to-cart-variation', WC()->plugin_url() . '/assets/js/frontend/add-to-cart-variation.min.js', [ 'jquery', 'wp-util' ], WC()->version, true );
		}
		wp_enqueue_script( 'wc-add-to-cart-variation' );

		wp_enqueue_script( 'wcvm-frontend', WCVM_URL . 'assets/js/frontend.js', [ 'jquery', 'wp-util', 'wc-add-to-cart-variation' ], WCVM_VERSION, true );

		wp_localize_script( 'wcvm-frontend', 'WCVM', [
			'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
			'nonce'             => wp_create_nonce( 'wcvm' ),
			'texts'             => [
				'title'         => esc_html__( 'Scegli le opzioni', 'wc-variant-modal' ),
				'addToCart'     => esc_html__( 'Aggiungi al carrello', 'wc-variant-modal' ),
				'added'         => esc_html__( 'Aggiunto al carrello!', 'wc-variant-modal' ),
				'selectOptions' => esc_html__( 'Scegli le opzioni del prodotto.', 'wc-variant-modal' ),
				'close'         => esc_html__( 'Chiudi', 'wc-variant-modal' ),
				'reset'         => esc_html__( 'Ripristina', 'wc-variant-modal' ),
			],
			'behavior'         => [
				'closeOnEsc'      => wcvm_bool( $settings['close_on_esc'] ),
				'closeOnBackdrop' => wcvm_bool( $settings['close_on_backdrop'] ),
				'lockScroll'      => wcvm_bool( $settings['lock_scroll'] ),
			],
			'wcAjaxUrl'        => isset( $GLOBALS['wc_add_to_cart_params']['wc_ajax_url'] )
				? $GLOBALS['wc_add_to_cart_params']['wc_ajax_url']
				: add_query_arg( 'wc-ajax', '%%endpoint%%', home_url( '/' ) ),
			'showQty'          => wcvm_bool( $settings['show_quantity'] ),
			'showStock'        => wcvm_bool( $settings['show_stock'] ),
			'showSku'          => wcvm_bool( $settings['show_sku'] ),
		] );
	}
}
