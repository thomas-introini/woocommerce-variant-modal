<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCVM_Ajax {
	public static function init(): void {
		add_action( 'wp_ajax_wcvm_get_modal', [ __CLASS__, 'get_modal' ] );
		add_action( 'wp_ajax_nopriv_wcvm_get_modal', [ __CLASS__, 'get_modal' ] );
	}

	/**
	 * Return the modal HTML for a specific variable product.
	 * Expects: nonce, product_id (int)
	 */
	public static function get_modal(): void {
		check_ajax_referer( 'wcvm', 'nonce' );

		$product_id = isset( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : 0;
		$product    = wc_get_product( $product_id );

		if ( ! $product || 'variable' !== $product->get_type() ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Prodotto non valido.', 'wc-variant-modal' ) ], 400 );
		}

		ob_start();
		WCVM_Template::modal_content( $product );
		$html = ob_get_clean();

		wp_send_json_success( [
			'html' => $html,
			'title' => wp_kses_post( $product->get_name() ),
		] );
	}
}
