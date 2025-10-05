<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCVM_Plugin {
	private static $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		require_once WCVM_DIR . 'includes/class-wcvm-assets.php';
		require_once WCVM_DIR . 'includes/class-wcvm-admin.php';
		require_once WCVM_DIR . 'includes/class-wcvm-frontend.php';
		require_once WCVM_DIR . 'includes/class-wcvm-ajax.php';
		require_once WCVM_DIR . 'includes/class-wcvm-template.php';

		WCVM_Assets::init();
		WCVM_Admin::init();
		WCVM_Frontend::init();
		WCVM_Ajax::init();
	}
}
