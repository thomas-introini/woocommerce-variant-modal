<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCVM_Frontend {
	public static function init(): void {
		add_filter( 'woocommerce_loop_add_to_cart_link', [ __CLASS__, 'tweak_loop_button' ], 10, 3 );
		add_action( 'wp_footer', [ __CLASS__, 'render_shell' ] );
	}

	/**
	 * Add our data attributes/classes to the archive "Select options" button for variable products.
	 * We keep the original href as a progressive enhancement fallback.
	 */
	public static function tweak_loop_button( string $button, WC_Product $product, array $args ): string {
		$settings = wcvm_get_settings();

		if ( ! wcvm_bool( $settings['enabled'] ) || ! wcvm_bool( $settings['enable_on_archives'] ) ) {
			return $button;
		}
		if ( ! $product || 'variable' !== $product->get_type() ) {
			return $button;
		}

		// Replace button text if configured.
		if ( ! empty( $settings['button_text'] ) ) {
			$button = preg_replace(
				'#>(.*?)</a>#',
				'>' . esc_html( $settings['button_text'] ) . '</a>',
				$button
			);
		}

		// Inject attributes for JS hook + accessibility.
		$button = preg_replace(
			'/<a /',
			sprintf(
				'<a data-wcvm="1" data-product_id="%d" class="wcvm-open-modal %s" role="button" aria-haspopup="dialog" ',
				$product->get_id(),
				'button' // keep theme button styles
			),
			$button,
			1
		);

		return $button;
	}

	/**
	 * Once per page: render the empty modal shell that JS will populate.
	 */
	public static function render_shell(): void {
		$settings = wcvm_get_settings();
		if ( ! wcvm_bool( $settings['enabled'] ) ) {
			return;
		}
		?>
		<div id="wcvm-overlay" class="wcvm-overlay" hidden></div>
		<div id="wcvm-dialog" class="wcvm-dialog" role="dialog" aria-modal="true" aria-labelledby="wcvm-title" hidden>
			<div>
				<div class="wcvm-dialog__header">
					<h3 id="wcvm-title"><?php echo esc_html( $settings['button_text'] ?: __( 'Scegli le opzioni', 'wc-variant-modal' ) ); ?></h3>
					<button type="button" class="wcvm-close" aria-label="<?php esc_attr_e( 'Chiudi', 'wc-variant-modal' ); ?>">&times;</button>
				</div>
				<div class="wcvm-dialog__body" id="wcvm-content" tabindex="-1">
					<!-- AJAX content goes here -->
				</div>
			</div>
		</div>
		<?php
	}
}
