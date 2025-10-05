<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCVM_Admin {
	public static function init(): void {
		add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
		add_action( 'admin_menu', [ __CLASS__, 'menu' ] );
	}

	public static function menu(): void {
		add_submenu_page(
			'woocommerce',
			__( 'Variant Modal', 'wc-variant-modal' ),
			__( 'Variant Modal', 'wc-variant-modal' ),
			'manage_woocommerce',
			'wcvm-settings',
			[ __CLASS__, 'render_page' ]
		);
	}

	public static function register_settings(): void {
		register_setting( 'wcvm_settings_group', 'wcvm_settings', [ __CLASS__, 'sanitize' ] );

		add_settings_section( 'wcvm_general', __( 'General', 'wc-variant-modal' ), '__return_false', 'wcvm_settings' );
		add_settings_field( 'enabled', __( 'Enable', 'wc-variant-modal' ), [ __CLASS__, 'checkbox' ], 'wcvm_settings', 'wcvm_general', [ 'id' => 'enabled' ] );
		add_settings_field( 'enable_on_archives', __( 'Enable on archives', 'wc-variant-modal' ), [ __CLASS__, 'checkbox' ], 'wcvm_settings', 'wcvm_general', [ 'id' => 'enable_on_archives', 'desc' => __( 'Shop, category, tags, related, upsells, etc.', 'wc-variant-modal' ) ] );
		add_settings_field( 'button_text', __( 'Archive button text', 'wc-variant-modal' ), [ __CLASS__, 'text' ], 'wcvm_settings', 'wcvm_general', [ 'id' => 'button_text' ] );

		add_settings_section( 'wcvm_behavior', __( 'Behavior', 'wc-variant-modal' ), '__return_false', 'wcvm_settings' );
		add_settings_field( 'show_quantity', __( 'Show quantity', 'wc-variant-modal' ), [ __CLASS__, 'checkbox' ], 'wcvm_settings', 'wcvm_behavior', [ 'id' => 'show_quantity' ] );
		add_settings_field( 'show_stock', __( 'Show stock status', 'wc-variant-modal' ), [ __CLASS__, 'checkbox' ], 'wcvm_settings', 'wcvm_behavior', [ 'id' => 'show_stock' ] );
		add_settings_field( 'show_sku', __( 'Show SKU', 'wc-variant-modal' ), [ __CLASS__, 'checkbox' ], 'wcvm_settings', 'wcvm_behavior', [ 'id' => 'show_sku' ] );
		add_settings_field( 'close_on_esc', __( 'Close on ESC', 'wc-variant-modal' ), [ __CLASS__, 'checkbox' ], 'wcvm_settings', 'wcvm_behavior', [ 'id' => 'close_on_esc' ] );
		add_settings_field( 'close_on_backdrop', __( 'Close on backdrop click', 'wc-variant-modal' ), [ __CLASS__, 'checkbox' ], 'wcvm_settings', 'wcvm_behavior', [ 'id' => 'close_on_backdrop' ] );
		add_settings_field( 'lock_scroll', __( 'Lock page scroll when open', 'wc-variant-modal' ), [ __CLASS__, 'checkbox' ], 'wcvm_settings', 'wcvm_behavior', [ 'id' => 'lock_scroll' ] );

		add_settings_section( 'wcvm_style', __( 'Style', 'wc-variant-modal' ), '__return_false', 'wcvm_settings' );
		add_settings_field( 'primary_color', __( 'Primary color', 'wc-variant-modal' ), [ __CLASS__, 'color' ], 'wcvm_settings', 'wcvm_style', [ 'id' => 'primary_color' ] );
		add_settings_field( 'accent_color', __( 'Accent color', 'wc-variant-modal' ), [ __CLASS__, 'color' ], 'wcvm_settings', 'wcvm_style', [ 'id' => 'accent_color' ] );
		add_settings_field( 'text_color', __( 'Text color', 'wc-variant-modal' ), [ __CLASS__, 'color' ], 'wcvm_settings', 'wcvm_style', [ 'id' => 'text_color' ] );
		add_settings_field( 'modal_bg', __( 'Modal background', 'wc-variant-modal' ), [ __CLASS__, 'color' ], 'wcvm_settings', 'wcvm_style', [ 'id' => 'modal_bg' ] );
		add_settings_field( 'overlay_color', __( 'Overlay color', 'wc-variant-modal' ), [ __CLASS__, 'color' ], 'wcvm_settings', 'wcvm_style', [ 'id' => 'overlay_color' ] );
		add_settings_field( 'overlay_opacity', __( 'Overlay opacity (0–1)', 'wc-variant-modal' ), [ __CLASS__, 'text' ], 'wcvm_settings', 'wcvm_style', [ 'id' => 'overlay_opacity' ] );
		add_settings_field( 'border_radius', __( 'Corner radius (px)', 'wc-variant-modal' ), [ __CLASS__, 'text' ], 'wcvm_settings', 'wcvm_style', [ 'id' => 'border_radius' ] );
		add_settings_field( 'custom_css', __( 'Custom CSS', 'wc-variant-modal' ), [ __CLASS__, 'textarea' ], 'wcvm_settings', 'wcvm_style', [ 'id' => 'custom_css' ] );
	}

	public static function sanitize( $input ): array {
		$output = [];

		$keys_bool = [ 'enabled','enable_on_archives','close_on_esc','close_on_backdrop','lock_scroll','show_quantity','show_stock','show_sku' ];
		foreach ( $keys_bool as $key ) {
			$output[ $key ] = isset( $input[ $key ] ) ? 1 : 0;
		}

		$keys_text = [ 'button_text','primary_color','accent_color','text_color','modal_bg','overlay_color','overlay_opacity','border_radius','custom_css' ];
		foreach ( $keys_text as $key ) {
			$val = isset( $input[ $key ] ) ? (string) $input[ $key ] : '';
			if ( in_array( $key, [ 'primary_color','accent_color','text_color','modal_bg','overlay_color' ], true ) ) {
				$val = sanitize_hex_color( $val );
			} elseif ( 'custom_css' === $key ) {
				$val = wp_kses_post( $val );
			} else {
				$val = sanitize_text_field( $val );
			}
			$output[ $key ] = $val;
		}

		return $output;
	}

	public static function render_page(): void {
		$settings = wcvm_get_settings();
		?>
		<div class="wrap wcvm-wrap">
			<h1><?php esc_html_e( 'Variant Modal', 'wc-variant-modal' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'wcvm_settings_group' );
				do_settings_sections( 'wcvm_settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	// Field renderers:

	public static function checkbox( array $args ): void {
		$settings = wcvm_get_settings();
		$id       = esc_attr( $args['id'] );
		$desc     = isset( $args['desc'] ) ? esc_html( $args['desc'] ) : '';
		?>
		<label>
			<input type="checkbox" name="wcvm_settings[<?php echo $id; ?>]" value="1" <?php checked( 1, (int) $settings[ $id ] ); ?> />
			<?php if ( $desc ) : ?><span class="description"><?php echo $desc; ?></span><?php endif; ?>
		</label>
		<?php
	}

	public static function text( array $args ): void {
		$settings = wcvm_get_settings();
		$id       = esc_attr( $args['id'] );
		?>
		<input type="text" class="regular-text" name="wcvm_settings[<?php echo $id; ?>]" value="<?php echo esc_attr( $settings[ $id ] ); ?>" />
		<?php
	}

	public static function textarea( array $args ): void {
		$settings = wcvm_get_settings();
		$id       = esc_attr( $args['id'] );
		?>
		<textarea class="large-text code" rows="6" name="wcvm_settings[<?php echo $id; ?>]"><?php echo esc_textarea( $settings[ $id ] ); ?></textarea>
		<?php
	}

	public static function color( array $args ): void {
		$settings = wcvm_get_settings();
		$id       = esc_attr( $args['id'] );
		?>
		<input type="text" class="wcvm-color" data-default-color="<?php echo esc_attr( $settings[ $id ] ); ?>" name="wcvm_settings[<?php echo $id; ?>]" value="<?php echo esc_attr( $settings[ $id ] ); ?>" />
		<?php
	}
}
