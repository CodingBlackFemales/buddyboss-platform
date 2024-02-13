<?php
/**
 * Recaptcha integration admin tab
 *
 * @since   BuddyBoss [BBVERSION]
 * @package BuddyBoss\Recaptcha
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup Recaptcha integration admin tab class.
 *
 * @since BuddyBoss [BBVERSION]
 */
class BB_Recaptcha_Admin_Integration_Tab extends BP_Admin_Integration_tab {

	/**
	 * Current section.
	 *
	 * @var $current_section
	 */
	protected $current_section;

	/**
	 * Initialize.
	 *
	 * @since BuddyBoss [BBVERSION]
	 */
	public function initialize() {
		$this->tab_order       = 53;
		$this->current_section = 'bb_recaptcha-integration';
		$this->intro_template  = $this->root_path . '/templates/admin/integration-tab-intro.php';

		add_filter( 'bb_admin_icons', array( $this, 'admin_setting_icons' ), 10, 2 );
	}

	/**
	 * Recaptcha Integration is active?
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @return bool
	 */
	public function is_active() {
		return (bool) apply_filters( 'bb_recaptcha_integration_is_active', true );
	}

	/**
	 * Recaptcha integration tab scripts.
	 *
	 * @since BuddyBoss [BBVERSION]
	 */
	public function register_admin_script() {

		$active_tab = bp_core_get_admin_active_tab();

		if ( 'bb-recaptcha' === $active_tab ) {
			$min     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			$rtl_css = is_rtl() ? '-rtl' : '';
			wp_enqueue_style( 'bb-recaptcha-admin', bb_recaptcha_integration_url( '/assets/css/bb-recaptcha-admin' . $rtl_css . $min . '.css' ), false, buddypress()->version );
		}

		parent::register_admin_script();
	}

	/**
	 * Register setting fields for recaptcha integration.
	 *
	 * @since BuddyBoss [BBVERSION]
	 */
	public function register_fields() {

		$sections = $this->get_settings_sections();

		foreach ( (array) $sections as $section_id => $section ) {

			// Only add section and fields if section has fields.
			$fields = $this->get_settings_fields_for_section( $section_id );

			if ( empty( $fields ) ) {
				continue;
			}

			$section_title     = ! empty( $section['title'] ) ? $section['title'] : '';
			$section_callback  = ! empty( $section['callback'] ) ? $section['callback'] : false;
			$tutorial_callback = ! empty( $section['tutorial_callback'] ) ? $section['tutorial_callback'] : false;
			$notice            = ! empty( $section['notice'] ) ? $section['notice'] : false;

			// Add the section.
			$this->add_section( $section_id, $section_title, $section_callback, $tutorial_callback, $notice );

			// Loop through fields for this section.
			foreach ( (array) $fields as $field_id => $field ) {

				$field['args'] = isset( $field['args'] ) ? $field['args'] : array();

				if ( ! empty( $field['callback'] ) && ! empty( $field['title'] ) ) {
					$sanitize_callback = isset( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : array();
					$this->add_field( $field_id, $field['title'], $field['callback'], $sanitize_callback, $field['args'] );
				}
			}
		}
	}

	/**
	 * Get setting sections for recaptcha integration.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @return array $settings Settings sections for recaptcha integration.
	 */
	public function get_settings_sections() {

		$status      = 'not-connected';
		$status_text = __( 'Not Connected', 'buddyboss' );
		$html        = '<div class="bb-recaptcha-status">' .
			'<span class="status-line ' . esc_attr( $status ) . '">' . esc_html( $status_text ) . '</span>' .
		'</div>';

		$settings = array(
			'bb_recaptcha_settings_section' => array(
				'page'              => 'Recaptcha',
				'title'             => __( 'reCAPTCHA', 'buddyboss' ) . $html,
				'tutorial_callback' => array( $this, 'setting_callback_recaptcha_tutorial' ),
				'notice'            => sprintf(
				/* translators: recaptcha link */
					__( 'Check reCAPTCHA %s for usage statistics and monitor its performance. Adjust settings if necessary to maintain security.', 'buddyboss' ),
					'<a href="#" target="_blank">' . esc_html__( 'Admin Console', 'buddyboss' ) . '</a>'
				),
			),
			'bb_recaptcha_settings'         => array(
				'page'              => 'recaptcha',
				'title'             => __( 'reCAPTCHA Settings', 'buddyboss' ),
				'tutorial_callback' => array( $this, 'setting_callback_recaptcha_tutorial' ),
			),
		);

		return $settings;
	}

	/**
	 * Get setting fields for section in recaptcha integration.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @param string $section_id Section ID.
	 *
	 * @return array|false $fields setting fields for section in recaptcha integration false otherwise.
	 */
	public function get_settings_fields_for_section( $section_id = '' ) {

		// Bail if section is empty.
		if ( empty( $section_id ) ) {
			return false;
		}

		$fields = $this->get_settings_fields();
		$fields = isset( $fields[ $section_id ] ) ? $fields[ $section_id ] : false;

		return $fields;
	}

	/**
	 * Register setting fields for recaptcha integration.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @return array $fields setting fields for pusher integration.
	 */
	public function get_settings_fields() {

		$fields = array();

		$fields['bb_recaptcha_settings_section'] = array(
			'information' => array(
				'title'             => esc_html__( 'Information', 'buddyboss' ),
				'callback'          => array( $this, 'setting_callback_recaptcha_information' ),
				'sanitize_callback' => 'string',
				'args'              => array( 'class' => 'notes-hidden-header' ),
			),
		);

		$fields['bb_recaptcha_settings'] = array(
			'bb_recaptcha_score_threshold' => array(
				'title'             => esc_html__( 'Score Threshold', 'buddyboss' ),
				'callback'          => array( $this, 'setting_callback_score_threshold' ),
				'sanitize_callback' => 'absint',
				'args'              => array(),
			),
			'bb_recaptcha_enabled_for'     => array(
				'title'    => esc_html__( 'Enabled For', 'buddyboss' ),
				'callback' => array( $this, 'setting_callback_enabled_for' ),
				'args'     => array(),
			),
			'bb_recaptcha_allow_bypass'    => array(
				'title'    => ' ',
				'callback' => array( $this, 'setting_callback_allow_bypass' ),
				'args'     => array(),
			),
		);

		return $fields;
	}

	/**
	 * Link to Recaptcha Settings tutorial.
	 *
	 * @since BuddyBoss [BBVERSION]
	 */
	public function setting_callback_recaptcha_tutorial() {
		?>
		<p>
			<a class="button" href="
			<?php
				echo esc_url(
					bp_get_admin_url(
						add_query_arg(
							array(
								'page'    => 'bp-help',
								'article' => '125826',
							),
							'admin.php'
						)
					)
				);
			?>
			"><?php esc_html_e( 'View Tutorial', 'buddyboss' ); ?></a>
		</p>
		<?php
	}

	/**
	 * Callback fields for recaptcha information.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @return void
	 */
	public function setting_callback_recaptcha_information() {
		echo '<div class="show-full-width">' .
		     sprintf(
		     /* translators: recaptcha link */
			     esc_html__( 'Enter your %s to integrate fraud, spam and abuse protection into your website.', 'buddyboss' ),
			     '<a href="#" target="_blank">' . esc_html__( 'Google reCAPTCHA API keys', 'buddyboss' ) . '</a>'
		     ) .
		     '</div>';
	}

	public function setting_callback_score_threshold() {
		?>
		<input name="bb_recaptcha_score_threshold" id="bb-recaptcha-score-threshold" type="number" min="0" max="10" value="<?php echo esc_attr( bb_recaptcha_score_threshold() ); ?>" required />
		<p class="description">
			<?php
			esc_html_e( 'reCAPTCHA v3 provides a score for every request seamlessly, without causing user friction. Input a risk score between 1 and 10 in the field above to evaluate the probability of being identified as a bot.', 'buddyboss' );
			?>
		</p>
		<?php
	}

	public function setting_callback_enabled_for() {
		$actions = bb_recaptcha_actions();

		foreach ( $actions as $action => $setting ) {

			$disabled = ! empty( $setting['disabled'] ) ? ' disabled="disabled"' : '';
			$checked  = ! empty( $setting['enabled'] ) ? ' checked="checked"' : '';

			echo '<input id="recaptcha_' . esc_attr( $action ) . '" name="bb_recaptcha_enabled_for[' . esc_attr( $action ) . ']" type="checkbox" value="1" ' . $disabled . $checked . '/>' .
				'<label for="recaptcha_' . esc_attr( $action ) . '">' . esc_html( $setting['label'] ) . '</label><br /><br />';
		}

		echo '<p class="description">' . esc_html__( 'Select the pages to include in the reCAPTCHA submission. Make sure to Enable Registration if both registration and account activation are disabled.', 'buddyboss' ) . '</p>';
	}

	public function setting_callback_allow_bypass() {
		?>
		<input id="bb_recaptcha_allow_bypass" name="bb_recaptcha_allow_bypass" type="checkbox" value="1" <?php checked( bp_get_option( 'bb_recaptcha_allow_bypass' ) ); ?> />
		<label for="bb_recaptcha_allow_bypass"><?php esc_html_e( 'Allow bypass, enter a 6 to 10-character string to customize your URL', 'buddyboss' ); ?></label>
		<input type="text" name="bb_recaptcha_bypass_text" value="<?php esc_attr( bp_get_option( 'bb_recaptcha_bypass_text', '' ) ); ?>" placeholder="<?php esc_attr_e( 'stringxs', 'buddyboss' ); ?>">
		<p class="description"><?php esc_html_e( 'The bypass URL enables you to bypass reCAPTCHA in case of issues. We recommend keeping the link below securely stored for accessing your site.', 'buddyboss' ); ?></p>
        <div class="copy-toggle">
            <input type="text" readonly class="zoom-group-instructions-main-input is-disabled" value="domain.com/wp-login.php/?bypass_captcha=xxUNIQUE_STRINGXS">
            <span role="button" class="bb-copy-button hide-if-no-js" data-balloon-pos="up" data-balloon="<?php esc_attr_e( 'Copy', 'buddyboss' ); ?>" data-copied-text="<?php esc_attr_e( 'Copied', 'buddyboss' ); ?>">
                <i class="bb-icon-f bb-icon-copy"></i>
            </span>
        </div>
		<?php
	}

	/**
	 * Added icon for the recaptcha admin settings.
	 *
	 * @since BuddyBoss [BBVERSION]
	 *
	 * @param string $meta_icon Icon class.
	 * @param string $id        Section ID.
	 *
	 * @return mixed|string
	 */
	public function admin_setting_icons( $meta_icon, $id = '' ) {
		if ( 'bb_recaptcha_settings_section' === $id || 'bb_recaptcha_settings' === $id ) {
			$meta_icon = 'bb-icon-i bb-icon-brand-google';
		}

		return $meta_icon;
	}
}
