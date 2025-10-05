<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCVM_Template {
	/**
	 * Output the modal inner content: standard Woo variations form, price area, buttons.
	 */
	public static function modal_content( WC_Product_Variable $product ): void {
		$settings              = wcvm_get_settings();
		$attributes            = $product->get_variation_attributes();
		$available_variations  = $product->get_available_variations(); // Preload to enable instant matching in JS.
		$selected_attributes   = $product->get_default_attributes();

		// Security nonce for add to cart.
		$nonce = wp_create_nonce( 'wcvm_add_to_cart' );
		?>
		<div class="wcvm-product-head">
			<?php if ( wcvm_bool( $settings['show_sku'] ) && $product->get_sku() ) : ?>
				<span class="wcvm-sku"><?php echo esc_html__( 'SKU', 'wc-variant-modal' ); ?>: <?php echo esc_html( $product->get_sku() ); ?></span>
			<?php endif; ?>
		</div>

		<form class="variations_form cart wcvm-form"
			  method="post"
			  data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
			  data-product_variations="<?php echo esc_attr( wp_json_encode( $available_variations ) ); ?>">

			<table class="variations" cellspacing="0" role="presentation">
				<tbody>
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<tr>
						<th class="label">
							<label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>">
								<?php echo wc_attribute_label( $attribute_name ); ?>
							</label>
						</th>
						<td class="value">
							<?php
							wc_dropdown_variation_attribute_options( [
								'options'   => $options,
								'attribute' => $attribute_name,
								'product'   => $product,
								'selected'  => isset( $selected_attributes[ sanitize_title( $attribute_name ) ] ) ? $selected_attributes[ sanitize_title( $attribute_name ) ] : '',
							] );
							?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<div class="wcvm-controls">
				<?php if ( wcvm_bool( $settings['show_quantity'] ) ) : ?>
					<?php woocommerce_quantity_input( [ 'min_value' => 1, 'input_value' => 1 ] ); ?>
				<?php endif; ?>

				<a class="reset_variations" href="#" style="display:none;"><?php esc_html_e( 'Ripristina', 'wc-variant-modal' ); ?></a>
			</div>

			<div class="single_variation_wrap">
				<?php
				/**
				 * Standard Woo hooks output:
				 * - .single_variation (price + stock)
				 * - hidden inputs for variation_id + attributes
				 * - submit button
				 */
				do_action( 'woocommerce_before_single_variation' );
				?>
				<div class="variations_button">
					<input type="hidden" name="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>" />
					<input type="hidden" name="variation_id" class="variation_id" value="0" />
					<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />
					<input type="hidden" name="wcvm_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
					<button type="submit" class="single_add_to_cart_button button alt">
						<?php esc_html_e( 'Aggiungi al carrello', 'wc-variant-modal' ); ?>
					</button>
				</div>
				<div class="single_variation"></div>
				<?php
				do_action( 'woocommerce_after_single_variation' );
				?>
			</div>

			<?php if ( wcvm_bool( $settings['show_stock'] ) ) : ?>
				<div class="wcvm-stock" aria-live="polite"></div>
			<?php endif; ?>
		</form>
		<?php
	}
}
