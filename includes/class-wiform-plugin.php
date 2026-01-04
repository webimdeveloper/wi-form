<?php

/**
 * Main plugin class for WiForm.
 *
 * Responsible for:
 * - Wiring admin and frontend hooks.
 * - Initialising admin menu.
 * - Registering shortcode and frontend assets.
 */
class WiForm_Plugin {

	/**
	 * Register all core hooks.
	 */
	public function register(): void {

		// ---------------------------
		// ADMIN AREA HOOKS
		// ---------------------------
		if ( is_admin() ) {
			add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
			add_action( 'admin_init', [ $this, 'register_settings' ] ); // placeholder for future settings.
		}

		// ---------------------------
		// FRONTEND HOOKS
		// ---------------------------
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );

		// Shortcode for Trademark Calculator.
		add_shortcode( 'wi_form_trademark', [ $this, 'render_trademark_shortcode' ] );
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
	 * Placeholder for settings (will implement later).
	 */
	public function register_settings(): void {
		// Here we will later add settings for BUC/UZS, USD/UZS, company/private coefficients.
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
			<p><?php esc_html_e( 'This is the main admin screen. The form builder UI will go here later.', 'wiform' ); ?></p>
			<p><?php esc_html_e( 'Trademark calculator settings will be added soon.', 'wiform' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Register frontend CSS/JS.
	 */
	public function register_assets(): void {
		$style_path = WIFORM_PATH . 'assets/dist/wiform-frontend.css';
		$style_url  = WIFORM_URL . 'assets/dist/wiform-frontend.css';
		$style_ver  = file_exists( $style_path ) ? filemtime( $style_path ) : '0.1.0';

		$script_path = WIFORM_PATH . 'assets/dist/wiform-frontend.js';
		$script_url  = WIFORM_URL . 'assets/dist/wiform-frontend.js';
		$script_ver  = file_exists( $script_path ) ? filemtime( $script_path ) : '0.1.0';

		wp_register_style(
			'wiform-frontend',
			$style_url,
			[],
			$style_ver
		);

		wp_register_script(
			'wiform-frontend',
			$script_url,
			[],
			$script_ver,
			true
		);
	}

	/**
	 * Shortcode callback: [wi_form_trademark]
	 */
	public function render_trademark_shortcode( $atts = [], $content = '' ): string {

		// Load assets only when shortcode is used.
		wp_enqueue_style( 'wiform-frontend' );
		wp_enqueue_script( 'wiform-frontend' );

		// Default settings (later we will override from admin settings).
		$defaults = [
			'buc_to_uzs' => 412000,
			'usd_to_uzs' => 12000,
			'company'    => [
				'submit_first'       => 6.0,
				'submit_additional'  => 1.0,
				'cert_first'         => 11.6,
				'cert_additional'    => 4.0,
				'service_per_tm_usd' => 200.0,
			],
			'private'    => [
				'submit_first'       => 4.0,
				'submit_additional'  => 0.5,
				'cert_first'         => 6.8,
				'cert_additional'    => 1.0,
				'service_per_tm_usd' => 200.0,
			],
		];

		// Allow shortcode attributes: 'redirectUrl' and an optional JSON 'settings' to override defaults.
		$atts = shortcode_atts(
			[
				'redirectUrl' => '',
				'settings'    => '',
			],
			$atts,
			'wi_form_trademark'
		);

		$settings = $defaults;

		// If the user passed a JSON settings payload via shortcode attribute, merge it.
		if ( ! empty( $atts['settings'] ) ) {
			$decoded = json_decode( wp_unslash( $atts['settings'] ), true );
			if ( is_array( $decoded ) ) {
				$settings = array_replace_recursive( $settings, $decoded );
			}
		}

		// redirectUrl may be provided as an attribute; otherwise use a sensible default.
		if ( ! empty( $atts['redirectUrl'] ) ) {
			$settings['redirectUrl'] = esc_url_raw( $atts['redirectUrl'] );
		} else {
			$settings['redirectUrl'] = '/contacts';
		}

		// Make settings available to JS as window.wiformTrademarkSettings
		wp_localize_script( 'wiform-frontend', 'wiformTrademarkSettings', $settings );

		$config_json = wp_json_encode( $settings );
		$instance_id = wp_unique_id( 'wiform-' );

		ob_start();
		?>

		<div
			id="<?php echo esc_attr( $instance_id ); ?>"
			class="wiform-root"
			data-wiform-id="<?php echo esc_attr( $instance_id ); ?>"
			data-wiform-config="<?php echo esc_attr( $config_json ); ?>"
			data-wiform-calculator="trademark"
		>
			<noscript><?php esc_html_e( 'Please enable JavaScript to use the WiForm calculator.', 'wiform' ); ?></noscript>
		</div>

		<?php
		return ob_get_clean();
	}
}