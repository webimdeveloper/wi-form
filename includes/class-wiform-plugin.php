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
			add_action( 'admin_init', [ $this, 'register_settings' ] );
		}
		
		add_action( 'init', [ $this, 'register_polylang_strings' ] );

		// ---------------------------
		// I18N
		// ---------------------------
		load_plugin_textdomain( 'wiform', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

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
		register_setting( 'wiform_options', 'wiform_usd_to_uzs', [
			'type'              => 'number',
			'sanitize_callback' => 'absint',
			'default'           => 12000,
		] );

		register_setting( 'wiform_options', 'wiform_service_fee', [
			'type'              => 'number',
			'sanitize_callback' => 'absint',
			'default'           => 200,
		] );

		add_settings_section(
			'wiform_main_section',
			__( 'Currency Settings', 'wiform' ),
			null,
			'wiform'
		);

		add_settings_field(
			'wiform_usd_to_uzs',
			__( 'USD to UZS Rate', 'wiform' ),
			[ $this, 'render_usd_to_uzs_field' ],
			'wiform',
			'wiform_main_section'
		);

		add_settings_field(
			'wiform_service_fee',
			__( 'Service Fee (USD)', 'wiform' ),
			[ $this, 'render_service_fee_field' ],
			'wiform',
			'wiform_main_section'
		);
	}

	public function render_usd_to_uzs_field(): void {
		$value = get_option( 'wiform_usd_to_uzs', 12000 );
		?>
		<input type="number" name="wiform_usd_to_uzs" value="<?php echo esc_attr( $value ); ?>" class="regular-text">
		<p class="description"><?php esc_html_e( 'Current exchange rate from 1 USD to UZS.', 'wiform' ); ?></p>
		<?php
	}

	public function render_service_fee_field(): void {
		$value = get_option( 'wiform_service_fee', 200 );
		?>
		<input type="number" name="wiform_service_fee" value="<?php echo esc_attr( $value ); ?>" class="regular-text">
		<p class="description"><?php esc_html_e( 'Base service fee in USD per trademark.', 'wiform' ); ?></p>
		<?php
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
			<h1><?php esc_html_e( 'WiForm Calculator Settings', 'wiform' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'wiform_options' );
				do_settings_sections( 'wiform' );
				submit_button();
				?>
			</form>
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
		$service_fee = (float) get_option( 'wiform_service_fee', 200 );

		$defaults = [
			'buc_to_uzs' => 412000,
			'usd_to_uzs' => (int) get_option( 'wiform_usd_to_uzs', 12000 ),
			'company'    => [
				'submit_first'       => 6.0,
				'submit_additional'  => 1.0,
				'cert_first'         => 11.6,
				'cert_additional'    => 4.0,
				'service_per_tm_usd' => $service_fee,
			],
			'private'    => [
				'submit_first'       => 4.0,
				'submit_additional'  => 0.5,
				'cert_first'         => 6.8,
				'cert_additional'    => 1.0,

				'service_per_tm_usd' => $service_fee,
			],
			'labels'     => $this->get_frontend_labels(),
		];

		// Allow shortcode attributes: 'redirectUrl' and an optional JSON 'settings' to override defaults.
		// Note: WP lowercases all shortcode attributes, so we must match 'redirecturl'.
		$atts = shortcode_atts(
			[
				'redirecturl' => '',
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
		if ( ! empty( $atts['redirecturl'] ) ) {
			$settings['redirectUrl'] = esc_url_raw( $atts['redirecturl'] );
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

	public function register_polylang_strings(): void {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}

		foreach ( $this->get_raw_labels() as $key => $label ) {
			pll_register_string( $key, $label, 'wiform', false );
		}
	}

	public function get_frontend_labels(): array {
		$raw    = $this->get_raw_labels();
		$labels = [];

		foreach ( $raw as $key => $text ) {
			if ( function_exists( 'pll__' ) ) {
				$labels[ $key ] = pll__( $text );
			} else {
				$labels[ $key ] = __( $text, 'wiform' );
			}
		}

		return $labels;
	}

	private function get_raw_labels(): array {
		return [
			'choose_applicant_type' => 'Choose applicant type',
			'legal_entity'          => 'Legal entity',
			'individual'            => 'Individual',
			'specify_details'       => 'Specify details',
			'trademarks'            => 'Trademarks',
			'total_classes'         => 'Total classes',
			'state_fee_filing'      => 'State fee for filing',
			'state_fee_cert'        => 'State fee for TM certificate',
			'total_state_fee'       => 'Total state fee',
			'service'               => 'Service',
			'total'                 => 'Total',
			'request_proposal'      => 'Request proposal',
			'applicant_type'        => 'Applicant type',
			'add_another_trademark' => 'Add another trademark',
			'number_of_classes'     => 'Number of classes',
			'back'                  => '← Back',
			'note_text'             => 'The stated price is for reference only and does not guarantee the final cost. The final price will be agreed upon and approved separately.',
			'calculate'             => 'Calculate',
			'trademark'             => 'Trademark',
		];
	}
}