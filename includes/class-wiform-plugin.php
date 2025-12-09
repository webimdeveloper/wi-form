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
		wp_register_style(
			'wiform-trademark',
			WIFORM_URL . 'assets/css/trademark-calculator.css',
			[],
			'0.1.0'
		);

		wp_register_script(
			'wiform-trademark',
			WIFORM_URL . 'assets/js/trademark-calculator.js',
			[ 'jquery' ],
			'0.1.0',
			true
		);
	}

	/**
	 * Shortcode callback: [wi_form_trademark]
	 */
	public function render_trademark_shortcode( $atts = [], $content = '' ): string {

		// Load assets only when shortcode is used.
		wp_enqueue_style( 'wiform-trademark' );
		wp_enqueue_script( 'wiform-trademark' );

		// Default settings (later we will override from admin settings).
		$settings = [
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

		// Make settings available to JS as window.wiformTrademarkSettings
		wp_localize_script( 'wiform-trademark', 'wiformTrademarkSettings', $settings );

		ob_start();
		?>

		<div class="wiform-trademark-calculator" data-wiform-calculator="trademark">

			<!-- Customer type -->
			<div class="wiform-field wiform-field--customer-type">
				<label class="wiform-label"><?php esc_html_e( 'Customer type', 'wiform' ); ?></label>
				<div class="wiform-options">
					<label>
						<input type="radio" name="wiform_customer_type" value="company" checked>
						<span><?php esc_html_e( 'Company', 'wiform' ); ?></span>
					</label>
					<label>
						<input type="radio" name="wiform_customer_type" value="private">
						<span><?php esc_html_e( 'Private', 'wiform' ); ?></span>
					</label>
				</div>
			</div>

			<!-- Trademark rows (Repeater) -->
			<div class="wiform-trademarks">
				<div class="wiform-trademark-row" data-wiform-row>
					<div class="wiform-field">
						<label>
							<?php esc_html_e( 'Number of classes for this trademark', 'wiform' ); ?>
							<input
								type="number"
								name="wiform_classes[]"
								min="1"
								max="100"
								value="1"
								class="wiform-input-classes"
							>
						</label>
					</div>
					<button type="button" class="wiform-remove-row" style="display:none;">
						<?php esc_html_e( 'Remove', 'wiform' ); ?>
					</button>
				</div>
			</div>

			<button type="button" class="wiform-add-row">
				<?php esc_html_e( 'Add another trademark', 'wiform' ); ?>
			</button>

			<!-- Email -->
			<div class="wiform-field wiform-field--email">
				<label>
					<?php esc_html_e( 'Your email to receive a proposal', 'wiform' ); ?>
					<input
						type="email"
						name="wiform_email"
						class="wiform-input-email"
						placeholder="<?php esc_attr_e( 'you@example.com', 'wiform' ); ?>"
					>
				</label>
			</div>

			<!-- Calculate -->
			<button type="button" class="wiform-calculate">
				<?php esc_html_e( 'Calculate', 'wiform' ); ?>
			</button>

			<!-- Results -->
			<div class="wiform-results" style="display:none;">
				<h3><?php esc_html_e( 'Calculation results', 'wiform' ); ?></h3>
				<p class="wiform-result-trademarks"></p>
				<p class="wiform-result-classes"></p>
				<p class="wiform-result-state-duty"></p>
				<p class="wiform-result-service"></p>
				<p class="wiform-result-total"></p>
			</div>

		</div>

		<?php
		return ob_get_clean();
	}
}