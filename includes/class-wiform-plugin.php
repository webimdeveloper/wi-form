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
		register_setting( 'wiform_options', 'wiform_buc_uzs', [
			'type'              => 'number',
			'sanitize_callback' => 'absint',
			'default'           => 412000,
		] );

		register_setting( 'wiform_options', 'wiform_usd_to_uzs', [
			'type'              => 'number',
			'sanitize_callback' => function( $val ) { return (float) $val; },
			'default'           => 12000,
		] );

		register_setting( 'wiform_options', 'wiform_service_fee', [
			'type'              => 'number',
			'sanitize_callback' => 'absint',
			'default'           => 2407618,
		] );
		register_setting( 'wiform_options', 'wiform_vat_rate', [
			'type'              => 'number',
			'sanitize_callback' => function( $val ) { return (float) $val; },
			'default'           => 0.12,
		] );
		register_setting( 'wiform_options', 'wiform_search_word_base', [ 'type' => 'number', 'sanitize_callback' => 'absint', 'default' => 1500000 ] );
		register_setting( 'wiform_options', 'wiform_search_word_extra', [ 'type' => 'number', 'sanitize_callback' => 'absint', 'default' => 500000 ] );
		register_setting( 'wiform_options', 'wiform_search_fig_base', [ 'type' => 'number', 'sanitize_callback' => 'absint', 'default' => 2000000 ] );
		register_setting( 'wiform_options', 'wiform_search_fig_extra', [ 'type' => 'number', 'sanitize_callback' => 'absint', 'default' => 700000 ] );
		register_setting( 'wiform_options', 'wiform_search_comb_base', [ 'type' => 'number', 'sanitize_callback' => 'absint', 'default' => 2500000 ] );
		register_setting( 'wiform_options', 'wiform_search_comb_extra', [ 'type' => 'number', 'sanitize_callback' => 'absint', 'default' => 800000 ] );
		register_setting( 'wiform_options', 'wiform_accel_state_word_base_buc', [ 'type' => 'number', 'sanitize_callback' => function( $val ) { return (float) $val; }, 'default' => 8 ] );
		register_setting( 'wiform_options', 'wiform_accel_state_fig_base_buc', [ 'type' => 'number', 'sanitize_callback' => function( $val ) { return (float) $val; }, 'default' => 10 ] );
		register_setting( 'wiform_options', 'wiform_accel_state_comb_base_buc', [ 'type' => 'number', 'sanitize_callback' => function( $val ) { return (float) $val; }, 'default' => 12 ] );
		register_setting( 'wiform_options', 'wiform_accel_state_extra_buc', [ 'type' => 'number', 'sanitize_callback' => function( $val ) { return (float) $val; }, 'default' => 1 ] );
		register_setting( 'wiform_options', 'wiform_accel_service_cost_uzs', [ 'type' => 'number', 'sanitize_callback' => 'absint', 'default' => 1200000 ] );

		add_settings_section(
			'wiform_main_section',
			__( 'Currency & Fee Settings', 'wiform' ),
			null,
			'wiform'
		);

		add_settings_field(
			'wiform_buc_uzs',
			__( 'BUC Value (UZS)', 'wiform' ),
			[ $this, 'render_buc_uzs_field' ],
			'wiform',
			'wiform_main_section'
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
			__( 'Service Fee (UZS)', 'wiform' ),
			[ $this, 'render_service_fee_field' ],
			'wiform',
			'wiform_main_section'
		);
		add_settings_field( 'wiform_vat_rate', __( 'VAT Rate', 'wiform' ), [ $this, 'render_vat_rate_field' ], 'wiform', 'wiform_main_section' );

		add_settings_section( 'wiform_search_section', __( 'Trademark Clearance Search (UZS)', 'wiform' ), null, 'wiform' );
		$this->add_admin_numeric_field( 'wiform_search_word_base', __( 'Word: Base', 'wiform' ), 'render_search_word_base_field', 'wiform_search_section' );
		$this->add_admin_numeric_field( 'wiform_search_word_extra', __( 'Word: Extra class', 'wiform' ), 'render_search_word_extra_field', 'wiform_search_section' );
		$this->add_admin_numeric_field( 'wiform_search_fig_base', __( 'Figurative: Base', 'wiform' ), 'render_search_fig_base_field', 'wiform_search_section' );
		$this->add_admin_numeric_field( 'wiform_search_fig_extra', __( 'Figurative: Extra class', 'wiform' ), 'render_search_fig_extra_field', 'wiform_search_section' );
		$this->add_admin_numeric_field( 'wiform_search_comb_base', __( 'Combined: Base', 'wiform' ), 'render_search_comb_base_field', 'wiform_search_section' );
		$this->add_admin_numeric_field( 'wiform_search_comb_extra', __( 'Combined: Extra class', 'wiform' ), 'render_search_comb_extra_field', 'wiform_search_section' );

		add_settings_section( 'wiform_accel_section', __( 'Accelerated Examination', 'wiform' ), null, 'wiform' );
		$this->add_admin_numeric_field( 'wiform_accel_state_word_base_buc', __( 'State fee BUC (Word): Base', 'wiform' ), 'render_accel_state_word_base_buc_field', 'wiform_accel_section', '0.01' );
		$this->add_admin_numeric_field( 'wiform_accel_state_fig_base_buc', __( 'State fee BUC (Figurative): Base', 'wiform' ), 'render_accel_state_fig_base_buc_field', 'wiform_accel_section', '0.01' );
		$this->add_admin_numeric_field( 'wiform_accel_state_comb_base_buc', __( 'State fee BUC (Combined): Base', 'wiform' ), 'render_accel_state_comb_base_buc_field', 'wiform_accel_section', '0.01' );
		$this->add_admin_numeric_field( 'wiform_accel_state_extra_buc', __( 'State fee BUC: Extra class', 'wiform' ), 'render_accel_state_extra_buc_field', 'wiform_accel_section', '0.01' );
		$this->add_admin_numeric_field( 'wiform_accel_service_cost_uzs', __( 'Service cost (UZS)', 'wiform' ), 'render_accel_service_cost_uzs_field', 'wiform_accel_section' );
	}

	private function add_admin_numeric_field( string $id, string $title, string $callback, string $section, string $step = '1' ): void {
		add_settings_field( $id, $title, [ $this, $callback ], 'wiform', $section, [ 'step' => $step ] );
	}

	public function render_buc_uzs_field(): void {
		$value = get_option( 'wiform_buc_uzs', 412000 );
		?>
		<input type="number" name="wiform_buc_uzs" value="<?php echo esc_attr( $value ); ?>" class="regular-text">
		<p class="description"><?php esc_html_e( 'Base Unit Calculation value in UZS.', 'wiform' ); ?></p>
		<?php
	}

	public function render_usd_to_uzs_field(): void {
		$value = get_option( 'wiform_usd_to_uzs', 12000 );
		?>
		<input type="number" step="0.01" name="wiform_usd_to_uzs" value="<?php echo esc_attr( $value ); ?>" class="regular-text">
		<p class="description"><?php esc_html_e( 'Current exchange rate from 1 USD to UZS.', 'wiform' ); ?></p>
		<?php
	}

	public function render_service_fee_field(): void {
		$value = get_option( 'wiform_service_fee', 2407618 );
		?>
		<input type="number" name="wiform_service_fee" value="<?php echo esc_attr( $value ); ?>" class="regular-text">
		<p class="description"><?php esc_html_e( 'Base service fee in UZS per trademark.', 'wiform' ); ?></p>
		<?php
	}

	public function render_vat_rate_field(): void {
		$value = get_option( 'wiform_vat_rate', 0.12 );
		?>
		<input type="number" step="0.01" name="wiform_vat_rate" value="<?php echo esc_attr( $value ); ?>" class="regular-text">
		<p class="description"><?php esc_html_e( 'VAT rate as decimal, for example 0.12.', 'wiform' ); ?></p>
		<?php
	}

	private function render_generic_number_field( string $name, $default, string $description, string $step = '1' ): void {
		$value = get_option( $name, $default );
		?>
		<input type="number" step="<?php echo esc_attr( $step ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text">
		<p class="description"><?php echo esc_html( $description ); ?></p>
		<?php
	}

	public function render_search_word_base_field(): void { $this->render_generic_number_field( 'wiform_search_word_base', 1500000, __( 'UZS base service fee for word trademark search.', 'wiform' ) ); }
	public function render_search_word_extra_field(): void { $this->render_generic_number_field( 'wiform_search_word_extra', 500000, __( 'UZS fee for each extra class (word).', 'wiform' ) ); }
	public function render_search_fig_base_field(): void { $this->render_generic_number_field( 'wiform_search_fig_base', 2000000, __( 'UZS base service fee for figurative trademark search.', 'wiform' ) ); }
	public function render_search_fig_extra_field(): void { $this->render_generic_number_field( 'wiform_search_fig_extra', 700000, __( 'UZS fee for each extra class (figurative).', 'wiform' ) ); }
	public function render_search_comb_base_field(): void { $this->render_generic_number_field( 'wiform_search_comb_base', 2500000, __( 'UZS base service fee for combined trademark search.', 'wiform' ) ); }
	public function render_search_comb_extra_field(): void { $this->render_generic_number_field( 'wiform_search_comb_extra', 800000, __( 'UZS fee for each extra class (combined).', 'wiform' ) ); }

	public function render_accel_state_word_base_buc_field(): void { $this->render_generic_number_field( 'wiform_accel_state_word_base_buc', 8, __( 'Base BUC state fee for word trademark.', 'wiform' ), '0.01' ); }
	public function render_accel_state_fig_base_buc_field(): void { $this->render_generic_number_field( 'wiform_accel_state_fig_base_buc', 10, __( 'Base BUC state fee for figurative trademark.', 'wiform' ), '0.01' ); }
	public function render_accel_state_comb_base_buc_field(): void { $this->render_generic_number_field( 'wiform_accel_state_comb_base_buc', 12, __( 'Base BUC state fee for combined trademark.', 'wiform' ), '0.01' ); }
	public function render_accel_state_extra_buc_field(): void { $this->render_generic_number_field( 'wiform_accel_state_extra_buc', 1, __( 'Extra class BUC fee used for all trademark types.', 'wiform' ), '0.01' ); }
	public function render_accel_service_cost_uzs_field(): void { $this->render_generic_number_field( 'wiform_accel_service_cost_uzs', 1200000, __( 'Fixed service fee in UZS for accelerated examination.', 'wiform' ) ); }

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
		$service_fee = (float) get_option( 'wiform_service_fee', 2407618 );
		$buc_uzs     = (int) get_option( 'wiform_buc_uzs', 412000 );
		$usd_to_uzs  = (float) get_option( 'wiform_usd_to_uzs', 12000 );
		$vat_rate    = (float) get_option( 'wiform_vat_rate', 0.12 );

		$defaults = [
			'buc_uzs'    => $buc_uzs,
			'usd_to_uzs' => $usd_to_uzs,
			'company'    => [
				'submit_first'       => 6.0,
				'submit_additional'  => 1.0,
				'cert_first'         => 11.6,
				'cert_additional'    => 4.0,
				'service_fee_uzs'    => $service_fee,
			],
			'private'    => [
				'submit_first'       => 4.0,
				'submit_additional'  => 0.5,
				'cert_first'         => 6.8,
				'cert_additional'    => 1.0,

				'service_fee_uzs'    => $service_fee,
			],
			'options'    => [
				'vat_rate' => $vat_rate,
				'search'   => [
					'word'     => [ 'base' => (float) get_option( 'wiform_search_word_base', 1500000 ), 'extra' => (float) get_option( 'wiform_search_word_extra', 500000 ) ],
					'fig'      => [ 'base' => (float) get_option( 'wiform_search_fig_base', 2000000 ), 'extra' => (float) get_option( 'wiform_search_fig_extra', 700000 ) ],
					'combined' => [ 'base' => (float) get_option( 'wiform_search_comb_base', 2500000 ), 'extra' => (float) get_option( 'wiform_search_comb_extra', 800000 ) ],
				],
				'accel'    => [
					'state_base_buc' => [
						'word'     => (float) get_option( 'wiform_accel_state_word_base_buc', 8 ),
						'fig'      => (float) get_option( 'wiform_accel_state_fig_base_buc', 10 ),
						'combined' => (float) get_option( 'wiform_accel_state_comb_base_buc', 12 ),
					],
					'state_extra_buc' => (float) get_option( 'wiform_accel_state_extra_buc', 1 ),
					'service_uzs'     => (float) get_option( 'wiform_accel_service_cost_uzs', 1200000 ),
				],
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
			'trademark_clearance_search' => 'Trademark clearance search',
			'accelerated_examination'    => 'Accelerated examination',
			'trademark_type'             => 'Trademark type',
			'word_trademark'             => 'Word trademark',
			'figurative_trademark'       => 'Figurative trademark',
			'combined_trademark'         => 'Combined trademark',
			'search_total'               => 'Trademark clearance search',
			'accelerated_total'          => 'Accelerated examination',
			'total_usd_equivalent'       => 'Total (USD equivalent)',
		];
	}
}