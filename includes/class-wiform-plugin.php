<?php

/**
 * Main plugin class for WiForm.
 *
 * Responsible for:
 * - Wiring admin and frontend hooks.
 * - Initialising admin menu.
 */
class WiForm_Plugin {

	/**
	 * Register all core hooks.
	 */
	public function register(): void {
		// Admin area hooks.
		if ( is_admin() ) {
			add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
		}

		// Frontend hooks (shortcodes, assets) will go here later.
	}

	/**
	 * Register the main admin menu page.
	 */
	public function register_admin_menu(): void {
		add_menu_page(
			__( 'WiForm Calculator', 'wiform' ), // Page title
			__( 'WiForm', 'wiform' ),           // Menu label
			'manage_options',                   // Capability
			'wiform',                           // Menu slug
			[ $this, 'render_admin_page' ],     // Callback
			'dashicons-calculator',             // Icon
			26                                  // Position
		);
	}

	/**
	 * Render the main admin page.
	 */
	public function render_admin_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WiForm – Dashboard', 'wiform' ); ?></h1>
			<p><?php esc_html_e( 'This is the main admin screen. The form builder UI will go here 
later.', 'wiform' ); ?></p>
		</div>
		<?php
	}
}

