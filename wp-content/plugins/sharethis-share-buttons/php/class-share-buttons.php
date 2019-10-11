<?php
/**
 * Share Buttons.
 *
 * @package ShareThisShareButtons
 */

namespace ShareThisShareButtons;

/**
 * Share Buttons Class
 *
 * @package ShareThisShareButtons
 */
class Share_Buttons {

	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Button Widget instance.
	 *
	 * @var object
	 */
	public $button_widget;

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	public $menu_slug;

	/**
	 * Menu hook suffix.
	 *
	 * @var string
	 */
	private $hook_suffix;

	/**
	 * Sub Menu hook suffix.
	 *
	 * @var string
	 */
	private $general_hook_suffix;

	/**
	 * Holds the settings sections.
	 *
	 * @var string
	 */
	public $setting_sections;

	/**
	 * Holds the settings fields.
	 *
	 * @var string
	 */
	public $setting_fields;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 * @param object $button_widget Button Widget class.
	 */
	public function __construct( $plugin, $button_widget ) {
		$this->button_widget = $button_widget;
		$this->plugin = $plugin;
		$this->menu_slug = 'sharethis';
		$this->set_settings();

		// Configure your buttons notice on activation.
		register_activation_hook( $this->plugin->dir_path . '/sharethis-share-buttons.php', array( $this, 'st_activation_hook' ) );

		// Clean up plugin information on deactivation.
		register_deactivation_hook( $this->plugin->dir_path . '/sharethis-share-buttons.php', array( $this, 'st_deactivation_hook' ) );
	}

	/**
	 * Set the settings sections and fields.
	 *
	 * @access private
	 */
	private function set_settings() {
		// Sections config.
		$this->setting_sections = array(
			'<span id="Inline" class="st-arrow">&#9658;</span>' . esc_html__( 'Inline Share Buttons', 'sharethis-share-buttons' ),
			'<span id="Sticky" class="st-arrow">&#9658;</span>' . esc_html__( 'Sticky Share Buttons', 'sharethis-share-buttons' ),
		);

		// Setting configs.
		$this->setting_fields = array(
			array(
				'id_suffix'   => 'inline',
				'description' => '',
				'callback'    => 'enable_cb',
				'section'     => 'share_button_section_1',
				'arg'         => 'inline',
			),
			array(
				'id_suffix'   => 'inline_settings',
				'description' => $this->get_descriptions( 'Inline' ),
				'callback'    => 'config_settings',
				'section'     => 'share_button_section_1',
				'arg'         => 'inline',
			),
			array(
				'id_suffix'   => 'sticky',
				'description' => '',
				'callback'    => 'enable_cb',
				'section'     => 'share_button_section_2',
				'arg'         => 'sticky',
			),
			array(
				'id_suffix'   => 'sticky_settings',
				'description' => $this->get_descriptions( 'Sticky' ),
				'callback'    => 'config_settings',
				'section'     => 'share_button_section_2',
				'arg'         => 'sticky',
			),
			array(
				'id_suffix'   => 'shortcode',
				'description' => $this->get_descriptions( '', 'shortcode' ),
				'callback'    => 'shortcode_template',
				'section'     => 'share_button_section_1',
				'arg'         => array(
					'type' => 'shortcode',
					'value' => '[sharethis-inline-buttons]',
				),
			),
			array(
				'id_suffix'   => 'template',
				'description' => $this->get_descriptions( '', 'template' ),
				'callback'    => 'shortcode_template',
				'section'     => 'share_button_section_1',
				'arg'         => array(
					'type' => 'template',
					'value' => '<?php echo sharethis_inline_buttons(); ?>',
				),
			),
		);

		// Inline setting array.
		$this->inline_setting_fields = array(
			array(
				'id_suffix'  => 'inline_post_top',
				'title'      => esc_html__( 'Top of post body', 'sharethis-share-buttons' ),
				'callback'   => 'onoff_cb',
				'type'       => '',
				'default'    => array(
					'true'   => 'checked="checked"',
					'false'  => '',
					'margin' => true,
				),
			),
			array(
				'id_suffix'  => 'inline_post_bottom',
				'title'      => esc_html__( 'Bottom of post body', 'sharethis-share-buttons' ),
				'callback'   => 'onoff_cb',
				'type'       => '',
				'default'    => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
			array(
				'id_suffix' => 'inline_page_top',
				'title'     => esc_html__( 'Top of page body', 'sharethis-share-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'    => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
			array(
				'id_suffix' => 'inline_page_bottom',
				'title'     => esc_html__( 'Bottom of page body', 'sharethis-share-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'    => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
			array(
				'id_suffix' => 'excerpt',
				'title'     => esc_html__( 'Include in excerpts', 'sharethis-share-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'    => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
		);

		// Sticky setting array.
		$this->sticky_setting_fields = array(
			array(
				'id_suffix' => 'sticky_home',
				'title'     => esc_html__( 'Home Page', 'sharethis-share-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'   => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
			array(
				'id_suffix' => 'sticky_post',
				'title'     => esc_html__( 'Posts', 'sharethis-share-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'   => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
			array(
				'id_suffix' => 'sticky_custom_posts',
				'title'     => esc_html__( 'Custom Post Types', 'sharethis-share-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'   => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
			array(
				'id_suffix' => 'sticky_page',
				'title'     => esc_html__( 'Pages', 'sharethis-share-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'   => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
			array(
				'id_suffix' => 'sticky_page_off',
				'title'     => esc_html__( 'Exclude specific pages:', 'sharethis-share-buttons' ),
				'callback'  => 'list_cb',
				'type'      => array(
					'single' => 'page',
					'multi'  => 'pages',
				),
			),
			array(
				'id_suffix' => 'sticky_category',
				'title'     => esc_html__( 'Category archive pages', 'sharethis-share-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'   => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
			array(
				'id_suffix' => 'sticky_category_off',
				'title'     => esc_html__( 'Exclude specific category archives:', 'sharethis-share-buttons' ),
				'callback'  => 'list_cb',
				'type'      => array(
					'single' => 'category',
					'multi'  => 'categories',
				),
			),
			array(
				'id_suffix' => 'sticky_tags',
				'title'     => esc_html__( 'Tags Archives', 'sharethis-share-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'   => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
			array(
				'id_suffix' => 'sticky_author',
				'title'     => esc_html__( 'Author pages', 'sharethis-share-buttons' ),
				'callback'  => 'onoff_cb',
				'type'      => '',
				'default'   => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
		);
	}

	/**
	 * Add in ShareThis menu option.
	 *
	 * @action admin_menu
	 */
	public function define_sharethis_menus() {
		$propertyid = get_option( 'sharethis_property_id' );

		// Menu base64 Encoded icon.
		$icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDE2IDE2IiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPCEtLSBHZW5lcmF0b3I6IFNrZXRjaCA0NC4xICg0MTQ1NSkgLSBodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2ggLS0+CiAgICA8dGl0bGU+RmlsbCAzPC90aXRsZT4KICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPgogICAgPGRlZnM+PC9kZWZzPgogICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9IkRlc2t0b3AtSEQiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0xMC4wMDAwMDAsIC00MzguMDAwMDAwKSIgZmlsbD0iI0ZFRkVGRSI+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik0yMy4xNTE2NDMyLDQ0OS4xMDMwMTEgQzIyLjcyNjg4NzcsNDQ5LjEwMzAxMSAyMi4zMzM1MDYyLDQ0OS4yMjg5OSAyMS45OTcwODA2LDQ0OS40Mzc5ODkgQzIxLjk5NTE0OTksNDQ5LjQzNTA5MyAyMS45OTcwODA2LDQ0OS40Mzc5ODkgMjEuOTk3MDgwNiw0NDkuNDM3OTg5IEMyMS44ODA3NTU1LDQ0OS41MDg5NDMgMjEuNzM1NDY5OCw0NDkuNTQ1NjI2IDIxLjU4OTIxODgsNDQ5LjU0NTYyNiBDMjEuNDUzMTA0LDQ0OS41NDU2MjYgMjEuMzE5ODg1Miw0NDkuNTA3NDk0IDIxLjIwODg2OTYsNDQ5LjQ0NTIyOSBMMTQuODczNzM4Myw0NDYuMDM4OTggQzE0Ljc2NDE3MDcsNDQ1Ljk5MDIzIDE0LjY4NzkwNzgsNDQ1Ljg3ODczMSAxNC42ODc5MDc4LDQ0NS43NTEzMDUgQzE0LjY4NzkwNzgsNDQ1LjYyMzM5NSAxNC43NjUxMzYsNDQ1LjUxMTg5NyAxNC44NzQ3MDM2LDQ0NS40NjI2NjQgTDIxLjIwODg2OTYsNDQyLjA1Njg5NyBDMjEuMzE5ODg1Miw0NDEuOTk1MTE1IDIxLjQ1MzEwNCw0NDEuOTU2NTAxIDIxLjU4OTIxODgsNDQxLjk1NjUwMSBDMjEuNzM1NDY5OCw0NDEuOTU2NTAxIDIxLjg4MDc1NTUsNDQxLjk5MzY2NyAyMS45OTcwODA2LDQ0Mi4wNjQ2MiBDMjEuOTk3MDgwNiw0NDIuMDY0NjIgMjEuOTk1MTQ5OSw0NDIuMDY3MDM0IDIxLjk5NzA4MDYsNDQyLjA2NDYyIEMyMi4zMzM1MDYyLDQ0Mi4yNzMxMzcgMjIuNzI2ODg3Nyw0NDIuMzk5MTE1IDIzLjE1MTY0MzIsNDQyLjM5OTExNSBDMjQuMzY2NTQwMyw0NDIuMzk5MTE1IDI1LjM1MTY4MzQsNDQxLjQxNDQ1NSAyNS4zNTE2ODM0LDQ0MC4xOTk1NTggQzI1LjM1MTY4MzQsNDM4Ljk4NDY2IDI0LjM2NjU0MDMsNDM4IDIzLjE1MTY0MzIsNDM4IEMyMi4wMTYzODc2LDQzOCAyMS4wOTMwMjcyLDQzOC44NjMwMjYgMjAuOTc1MjU0MSw0MzkuOTY3MzkgQzIwLjk3MTM5MjYsNDM5Ljk2MzA0NiAyMC45NzUyNTQxLDQzOS45NjczOSAyMC45NzUyNTQxLDQzOS45NjczOSBDMjAuOTUwNjM3NSw0NDAuMjM5MTM3IDIwLjc2OTE1MTEsNDQwLjQ2NzkyNiAyMC41MzYwMTgzLDQ0MC41ODQyNTEgTDE0LjI3OTU2MzMsNDQzLjk0NzU0MiBDMTQuMTY0MjAzNiw0NDQuMDE3MDQ3IDE0LjAyNDIyNzMsNDQ0LjA1NjE0NCAxMy44Nzk0MjQzLDQ0NC4wNTYxNDQgQzEzLjcwODU1NjgsNDQ0LjA1NjE0NCAxMy41NDgzMDgxLDQ0NC4wMDQ0OTggMTMuNDIwODgxNSw0NDMuOTEwMzc2IEMxMy4wNzUyODUsNDQzLjY4NDk2NiAxMi42NjUwMDk4LDQ0My41NTEyNjQgMTIuMjIxOTEyNiw0NDMuNTUxMjY0IEMxMS4wMDcwMTU1LDQ0My41NTEyNjQgMTAuMDIyMzU1MSw0NDQuNTM2NDA3IDEwLjAyMjM1NTEsNDQ1Ljc1MTMwNSBDMTAuMDIyMzU1MSw0NDYuOTY2MjAyIDExLjAwNzAxNTUsNDQ3Ljk1MDg2MiAxMi4yMjE5MTI2LDQ0Ny45NTA4NjIgQzEyLjY2NTAwOTgsNDQ3Ljk1MDg2MiAxMy4wNzUyODUsNDQ3LjgxNzY0MyAxMy40MjA4ODE1LDQ0Ny41OTIyMzMgQzEzLjU0ODMwODEsNDQ3LjQ5NzYyOSAxMy43MDg1NTY4LDQ0Ny40NDY0NjUgMTMuODc5NDI0Myw0NDcuNDQ2NDY1IEMxNC4wMjQyMjczLDQ0Ny40NDY0NjUgMTQuMTY0MjAzNiw0NDcuNDg1MDc5IDE0LjI3OTU2MzMsNDQ3LjU1NDU4NSBMMjAuNTM2MDE4Myw0NTAuOTE4MzU4IEMyMC43Njg2Njg0LDQ1MS4wMzQyMDEgMjAuOTUwNjM3NSw0NTEuMjYzNDcyIDIwLjk3NTI1NDEsNDUxLjUzNTIxOSBDMjAuOTc1MjU0MSw0NTEuNTM1MjE5IDIwLjk3MTM5MjYsNDUxLjUzOTU2MyAyMC45NzUyNTQxLDQ1MS41MzUyMTkgQzIxLjA5MzAyNzIsNDUyLjYzOTEwMSAyMi4wMTYzODc2LDQ1My41MDI2MDkgMjMuMTUxNjQzMiw0NTMuNTAyNjA5IEMyNC4zNjY1NDAzLDQ1My41MDI2MDkgMjUuMzUxNjgzNCw0NTIuNTE3NDY2IDI1LjM1MTY4MzQsNDUxLjMwMjU2OSBDMjUuMzUxNjgzNCw0NTAuMDg3NjcyIDI0LjM2NjU0MDMsNDQ5LjEwMzAxMSAyMy4xNTE2NDMyLDQ0OS4xMDMwMTEiIGlkPSJGaWxsLTMiPjwvcGF0aD4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPg==';

		if ( empty( $GLOBALS['admin_page_hooks']['sharethis-general'] ) ) {
			// Main sharethis menu.
			add_menu_page(
				__( 'Share Buttons by ShareThis', 'sharethis-share-buttons' ),
				__( 'ShareThis', 'sharethis-share-buttons' ),
				'manage_options',
				$this->menu_slug . '-general',
				null,
				$icon,
				26
			);

			// Create submenu to replace default submenu item. Set hook for enqueueing styles.
			$this->general_hook_suffix = add_submenu_page(
				$this->menu_slug . '-general',
				__( 'ShareThis General Settings', 'sharethis-share-buttons' ),
				__( 'General Settings', 'sharethis-share-buttons' ),
				'manage_options',
				$this->menu_slug . '-general',
				array( $this, 'general_settings_display' )
			);

		}

		// If the property ID is set then register the share buttons menu.
		if ( $this->is_property_id_set( 'empty' ) ) {
			$this->share_buttons_settings();
		}
	}

	/**
	 * Add Share Buttons settings page.
	 */
	public function share_buttons_settings() {
		$this->hook_suffix = add_submenu_page(
			$this->menu_slug . '-general',
			$this->get_descriptions( '', 'share_buttons' ),
			__( 'Share Buttons', 'sharethis-share-buttons' ),
			'manage_options',
			$this->menu_slug . '-share-buttons',
			array( $this, 'share_button_display' )
		);
	}

	/**
	 * Enqueue main MU script.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueue_mu() {
		wp_enqueue_script( "{$this->plugin->assets_prefix}-mu" );
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @action admin_enqueue_scripts
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		// Are sticky and inline buttons enabled.
		$inline = 'true' === get_option( 'sharethis_inline' ) || true === get_option( 'sharethis_inline' ) ? true : false;
		$sticky = 'true' === get_option( 'sharethis_sticky' ) || true === get_option( 'sharethis_sticky' ) ? true : false;
		$first_exists = get_option( 'sharethis_first_product' );
		$first_button = false !== $first_exists && null !== $first_exists ? $first_exists : '';
		$first_exists = false === $first_exists || null === $first_exists || '' === $first_exists ? true : false;
		$propertyid = explode( '-', get_option( 'sharethis_property_id' ), 2 );
		$property_id = isset( $propertyid[0] ) ? $propertyid[0] : '';
		$secret = isset( $propertyid[1] ) ? $propertyid[1] : '';
		$admin_url = str_replace( 'http://', '', str_replace( 'https://', '', site_url() ) );
		$button_config = get_option( 'sharethis_button_config', true );
		$button_config = false !== $button_config && null !== $button_config ? $button_config : '';

		// Only euqueue assets on this plugin admin menu.
		if ( $hook_suffix !== $this->hook_suffix && $hook_suffix !== $this->general_hook_suffix ) {
			return;
		}

		// Enqueue the styles globally throughout the ShareThis menus.
		wp_enqueue_style( "{$this->plugin->assets_prefix}-admin" );
		wp_enqueue_script( "{$this->plugin->assets_prefix}-mua" );

		// Only enqueue these scripts on share buttons plugin admin menu.
		if ( $hook_suffix === $this->hook_suffix ) {
			if ( $first_exists && ( $inline || $sticky ) ) {
				$first = $inline ? 'inline' : 'sticky';

				update_option( 'sharethis_first_product', $first );
			}

			wp_enqueue_script( "{$this->plugin->assets_prefix}-admin" );
			wp_add_inline_script( "{$this->plugin->assets_prefix}-admin", sprintf( 'ShareButtons.boot( %s );',
				wp_json_encode( array(
					'inlineEnabled' => $inline,
					'stickyEnabled' => $sticky,
					'propertyid'    => $property_id,
					'secret'        => $secret,
					'buttonConfig'  => $button_config,
					'nonce'         => wp_create_nonce( $this->plugin->meta_prefix ),
				) )
			) );
		}

		// Only enqueue this script on the general settings page for credentials.
		if ( $hook_suffix === $this->general_hook_suffix ) {
			wp_enqueue_script( "{$this->plugin->assets_prefix}-credentials" );
			wp_add_inline_script( "{$this->plugin->assets_prefix}-credentials", sprintf( 'Credentials.boot( %s );',
				wp_json_encode( array(
					'nonce'        => wp_create_nonce( $this->plugin->meta_prefix ),
					'url'          => $admin_url,
					'propertyid'   => $property_id,
					'secret'       => $secret,
					'firstButton'  => $first_button,
					'buttonConfig' => $button_config,
				) )
			) );
		}
	}

	/**
	 * Call back for displaying the General Settings page.
	 */
	public function general_settings_display() {
		global $current_user;

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// If the property id is set then show the general settings template.
		if ( $this->is_property_id_set() ) {
			include_once( "{$this->plugin->dir_path}/templates/general/general-settings.php" );
		} else {
			// Get the current sites true url including sub directories.
			$admin_url = str_replace( '/wp-admin/', '', admin_url() );
			$setup_steps = $this->get_setup_steps();
			$networks = $this->get_networks();

			$button = isset( $_GET['b'] ) && 'i' === $_GET['b'] ? 'Inline' : 'Sticky'; // WPCS: CSRF ok. // Input var okay.
			$page = ! isset( $_GET['s'] ) && ! isset( $_GET['l'] ) && ! isset( $_GET['p'] ) ? 'first' : ''; // WPCS: CSRF ok. // Input var okay.
			$page = isset( $_GET['s'] ) && '' === $page && '2' === $_GET['s'] ? 'second' : $page; // WPCS: CSRF ok. // Input var okay.
			$page = isset( $_GET['s'] ) && '' === $page && '3' === $_GET['s'] ? 'third' : $page; // WPCS: CSRF ok. // Input var okay.
			$page = isset( $_GET['l'] ) && '' === $page && 't' === $_GET['l'] ? 'login' : $page; // WPCS: CSRF ok. // Input var okay.
			$page = isset( $_GET['p'] ) && '' === $page && 't' === $_GET['p'] ? 'property' : $page; // WPCS: CSRF ok. // Input var okay.
			$step_class = '';

			include_once( "{$this->plugin->dir_path}/templates/general/connection-template.php" );
		}
	}

	/**
	 * Call back for property id setting view.
	 */
	public function property_setting() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$credential = get_option( 'sharethis_property_id' );
		$credential = null !== $credential && false !== $credential ? $credential : '';
		$error_message = '' === $credential ? '<div class="st-error"><strong>' . esc_html__( 'ERROR', 'sharethis-share-buttons' ) . '</strong>: ' . esc_html__( 'Property ID is required.', 'sharethis-share-buttons' ) . '</div>' : '';

		include_once( "{$this->plugin->dir_path}/templates/general/property-setting.php" );
	}

	/**
	 * Call back for displaying Share Buttons settings page.
	 */
	public function share_button_display() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$description = $this->get_descriptions( '', 'share_buttons' );

		include_once( "{$this->plugin->dir_path}/templates/share-buttons/share-button-settings.php" );
	}

	/**
	 * Define general setting section and fields.
	 *
	 * @action admin_init
	 */
	public function general_settings() {
		// Add setting section.
		add_settings_section(
			'property_id_section',
			null,
			null,
			$this->menu_slug . '-general'
		);

		// Register Setting.
		register_setting( $this->menu_slug . '-general', 'sharethis_property_id' );

		// Property id field.
		add_settings_field(
			'property_id',
			$this->get_descriptions( '', 'property' ),
			array( $this, 'property_setting' ),
			$this->menu_slug . '-general',
			'property_id_section'
		);
	}

	/**
	 * Define share button setting sections and fields.
	 *
	 * @action admin_init
	 */
	public function settings_api_init() {
		// Register sections.
		foreach ( $this->setting_sections as $index => $title ) {
			// Since the index starts at 0, let's increment it by 1.
			$i = $index + 1;
			$section = "share_button_section_{$i}";

			if ( 1 === $i ) {
				$arg = 'inline';
			} else {
				$arg = 'sticky';
			}

			// Add setting section.
			add_settings_section(
				$section,
				$title,
				array( $this, 'social_button_link' ),
				$this->menu_slug . '-share-buttons',
				array( $arg )
			);
		}

		// Register setting fields.
		foreach ( $this->setting_fields as $setting_field ) {
			register_setting( $this->menu_slug . '-share-buttons', $this->menu_slug . '_' . $setting_field['id_suffix'] );
			add_settings_field(
				$this->menu_slug . '_' . $setting_field['id_suffix'],
				$setting_field['description'],
				array( $this, $setting_field['callback'] ),
				$this->menu_slug . '-share-buttons',
				$setting_field['section'],
				$setting_field['arg']
			);
		}

		// Register omit settings.
		register_setting( $this->menu_slug . '-share-buttons', $this->menu_slug . '_sticky_page_off' );
		register_setting( $this->menu_slug . '-share-buttons', $this->menu_slug . '_sticky_category_off' );
	}

	/**
	 * Call back function for on / off buttons.
	 *
	 * @param string $type The setting type.
	 */
	public function config_settings( $type ) {
		$config_array = 'inline' === $type ? $this->inline_setting_fields : $this->sticky_setting_fields;

		// Display on off template for inline settings.
		foreach ( $config_array as $setting ) {
			$option = 'sharethis_' . $setting['id_suffix'];
			$title = isset( $setting['title'] ) ? $setting['title'] : '';
			$option_value = get_option( 'sharethis_' . $type . '_settings' );
			$default = isset( $setting['default'] ) ? $setting['default'] : '';
			$allowed = array(
				'li' => array(
					'class' => array(),
				),
				'span' => array(
					'id'    => array(),
					'class' => array(),
				),
				'input' => array(
					'id'    => array(),
					'name'  => array(),
					'type'  => array(),
					'value' => array(),
				),
			);

			// Margin control variables.
			$margin = isset( $setting['default']['margin'] ) ? $setting['default']['margin'] : false;
			$mclass = isset( $option_value[ $option . '_margin_top' ] ) && 0 !== (int) $option_value[ $option . '_margin_top' ] || isset( $option_value[ $option . '_margin_bottom' ] ) && 0 !== (int) $option_value[ $option . '_margin_bottom' ] ? 'active-margin' : '';
			$onoff = '' !== $mclass ? __( 'On', 'sharethis-share-buttons' ) : __( 'Off', 'sharethis-share-buttons' );
			$active = array(
				'class' => $mclass,
				'onoff' => esc_html( $onoff ),
			);

			if ( isset( $option_value[ $option ] ) && false !== $option_value[ $option ] && null !== $option_value[ $option ] ) {
				$default = array(
					'true'  => '',
					'false' => '',
				);
			}

			// Display the list call back if specified.
			if ( 'onoff_cb' === $setting['callback'] ) {
				include( "{$this->plugin->dir_path}/templates/share-buttons/onoff-buttons.php" );
			} else {
				$current_omit = $this->get_omit( $setting['type'] );

				$this->list_cb( $setting['type'], $current_omit, $allowed );
			}
		} // End foreach().
	}

	/**
	 * Helper function to build the omit list html
	 *
	 * @access private
	 *
	 * @param array $setting the omit type.
	 * @return string The html for omit list.
	 */
	private function get_omit( $setting ) {
		$current_omit = get_option( 'sharethis_sticky_' . $setting['single'] . '_off' );
		$current_omit = isset( $current_omit ) ? $current_omit : '';
		$html = '';

		if ( is_array( $current_omit ) ) {
			foreach ( $current_omit as $title => $id ) {
				$html .= '<li class="omit-item">';
				$html .= $title;
				$html .= '<span id="' . $id . '" class="remove-omit">X</span>';
				$html .= '<input type="hidden" name="sharethis_sticky_' . $setting['single'] . '_off[' . $title . ']" value="' . $id . '" id="sharethis_sticky_' . $setting['single'] . '_off[' . $title . ']">';
				$html .= '</li>';
			}
		}

		// Add ommit ids to meta box option.
		$this->update_metabox_list( $current_omit );

		return $html;
	}

	/**
	 * Helper function to update metabox list to sync with omit.
	 *
	 * @param array $current_omit The omit list.
	 */
	private function update_metabox_list( $current_omit ) {
		$current_on = get_option( 'sharethis_sticky_page_on' );

		if ( isset( $current_on, $current_omit ) && is_array( $current_on ) && is_array( $current_omit ) ) {
			$new_on = array_diff( $current_on, $current_omit );

			if ( is_array( $new_on ) ) {
				delete_option( 'sharethis_sticky_page_on' );
				delete_option( 'sharethis_sticky_page_off' );

				update_option( 'sharethis_sticky_page_off', $current_omit );
				update_option( 'sharethis_sticky_page_on', $new_on );
			}
		}
	}

	/**
	 * Callback function for onoff buttons
	 *
	 * @param array $id The setting type.
	 */
	public function enable_cb( $id ) {
		include( "{$this->plugin->dir_path}/templates/share-buttons/enable-buttons.php" );
	}

	/**
	 * Callback function for omitting fields.
	 *
	 * @param array $type The type of list to return for exlusion.
	 * @param array $current_omit The currently omited items.
	 * @param array $allowed The allowed html that an omit item can echo.
	 */
	public function list_cb( $type, $current_omit, $allowed ) {
		include( "{$this->plugin->dir_path}/templates/share-buttons/list.php" );
	}

	/**
	 * Callback function for the shortcode and template code fields.
	 *
	 * @param string $type The type of template to pull.
	 */
	public function shortcode_template( $type ) {
		include( "{$this->plugin->dir_path}/templates/share-buttons/shortcode-templatecode.php" );
	}

	/**
	 * Callback function for the login buttons.
	 *
	 * @param string $button The specific product to link to.
	 */
	public function social_button_link( $button ) {
		$networks = $this->get_networks();

		include( "{$this->plugin->dir_path}/templates/share-buttons/button-config.php" );
	}

	/**
	 * Callback function for random gif field.
	 *
	 * @access private
	 * @return string
	 */
	private function random_gif() {
		if ( ! is_wp_error( wp_safe_remote_get( 'http://api.giphy.com/v1/gifs/random?api_key=dc6zaTOxFJmzC&rating=g' ) ) ) {
			$content = wp_safe_remote_get( 'http://api.giphy.com/v1/gifs/random?api_key=dc6zaTOxFJmzC&rating=g' )['body'];

			return '<div id="random-gif-container"><img src="' . esc_url( json_decode( $content, ARRAY_N )['data']['image_url'] ) . '"/></div>';
		} else {
			return esc_html__( 'Sorry we couldn\'t show you a funny gif.  Refresh if you can\'t live without it.', 'sharethis-share-buttons' );
		}
	}

	/**
	 * Define setting descriptions.
	 *
	 * @param string $type Type of button.
	 * @param string $subtype Setting type.
	 *
	 * @access private
	 * @return string|void
	 */
	private function get_descriptions( $type = '', $subtype = '' ) {
		global $current_user;

		switch ( $subtype ) {
			case '':
				$description = esc_html__( 'WordPress Display Settings', 'sharethis-share-buttons' );
				$description .= '<span>';
				$description .= esc_html__( 'Use these settings to automatically include or restrict the display of ', 'sharethis-share-buttons' ) . esc_html( $type ) . esc_html__( ' Share Buttons on specific pages of your site.', 'sharethis-share-buttons' );
				$description .= '</span>';
				break;
			case 'shortcode':
				$description = esc_html__( 'Shortcode', 'sharethis-share-buttons' );
				$description .= '<span>';
				$description .= esc_html__( 'Use this shortcode to deploy your inline share buttons in a widget, or WYSIWYG editor.', 'sharethis-share-buttons' );
				$description .= '</span>';
				break;
			case 'template':
				$description = esc_html__( 'PHP', 'sharethis-share-buttons' );
				$description .= '<span>';
				$description .= esc_html__( 'Use this PHP snippet to include your inline share buttons anywhere else in your template.', 'sharethis-share-buttons' );
				$description .= '</span>';
				break;
			case 'social':
				$description = esc_html__( 'Social networks and button styles', 'sharethis-share-buttons' );
				$description .= '<span>';
				$description .= esc_html__( 'Login to ShareThis Platform to add, remove or re-order social networks in your ', 'sharethis-share-buttons' ) . esc_html( $type ) . esc_html__( ' Share buttons.  You may also update the alignment, size, labels and count settings.', 'sharethis-share-buttons' );
				$description .= '</span>';
				break;
			case 'property':
				$description = esc_html__( 'Property ID', 'sharethis-share-buttons' );
				$description .= '<span>';
				$description .= esc_html__( 'We use this unique ID to identify your property. Copy it from your ', 'sharethis-share-buttons' );
				$description .= '<a class="st-support" href="https://platform.sharethis.com/settings?utm_source=sharethis-plugin&utm_medium=sharethis-plugin-page&utm_campaign=property-settings" target="_blank">';
				$description .= esc_html__( 'ShareThis platform settings', 'sharethis-share-buttons' );
				$description .= '</a></span>';
				break;
			case 'share_buttons':
				$description = '<h1>';
				$description .= esc_html__( 'Share Buttons by ShareThis', 'sharethis-share-buttons' );
				$description .= '</h1>';
				$description .= '<h3>';
				$description .= esc_html__( 'Welcome aboard, ', 'sharethis-share-buttons' ) . esc_html( $current_user->display_name ) . '! ';
				$description .= esc_html__( 'Use the settings panels below for complete control over where and how share buttons appear on your site.', 'sharethis-share-buttons' );
				break;
		} // End switch().

		return wp_kses_post( $description );
	}

	/**
	 * Set the property id and secret key for the user's platform account if query params are present.
	 *
	 * @action wp_ajax_set_credentials
	 */
	public function set_credentials() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['data'], $_POST['token'] ) || '' === $_POST['data'] ) { // WPCS: input var ok.
			wp_send_json_error( 'Set credentials failed.' );
		}

		$data = sanitize_text_field( wp_unslash( $_POST['data'] ) ); // WPCS: input var ok.
		$token = sanitize_text_field( wp_unslash( $_POST['token'] ) ); // WPCS: input var ok.

		// If both variables exist add them to a database option.
		if ( false === get_option( 'sharethis_property_id' ) ) {
			update_option( 'sharethis_property_id', $data );
			update_option( 'sharethis_token', $token );
		}
	}

	/**
	 * Helper function to determine if property ID is set.
	 *
	 * @param string $type Should empty count as false.
	 *
	 * @access private
	 * @return bool
	 */
	private function is_property_id_set( $type = '' ) {
		$property_id = get_option( 'sharethis_property_id' );

		// If the property id is set then show the general settings template.
		if ( false !== $property_id && null !== $property_id ) {
			if ( 'empty' === $type && '' === $property_id ) {
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * AJAX Call back to update status of buttons
	 *
	 * @action wp_ajax_update_buttons
	 */
	public function update_buttons() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['type'], $_POST['onoff'] ) ) { // WPCS: CSRF ok. input var ok.
			wp_send_json_error( 'Update buttons failed.' );
		}

		// Set option type and button value.
		$type = 'sharethis_' . strtolower( sanitize_text_field( wp_unslash( $_POST['type'] ) ) ); // WPCS: input var ok.
		$onoff = sanitize_text_field( wp_unslash( $_POST['onoff'] ) ); // WPCS: input var ok.

		if ( 'On' === $onoff ) {
			update_option( $type, 'true' );
		} elseif ( 'Off' === $onoff ) {
			update_option( $type, 'false' );
		}
	}

	/**
	 * AJAX Call back to set defaults when rest button is clicked.
	 *
	 * @action wp_ajax_set_default_settings
	 */
	public function set_default_settings() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['type'] ) ) { // WPCS: CRSF ok. input var ok.
			wp_send_json_error( 'Update buttons failed.' );
		}

		// Set option type and button value.
		$type = strtolower( sanitize_text_field( wp_unslash( $_POST['type'] ) ) ); // WPCS: input var ok.

		$this->set_the_defaults( $type );
	}

	/**
	 * Helper function to set the default button options.
	 *
	 * @param string $type The type of default to set.
	 */
	private function set_the_defaults( $type ) {
		$default = array(
			'inline_settings'     => array(
				'sharethis_inline_post_top'                  => 'true',
				'sharethis_inline_post_bottom'               => 'false',
				'sharethis_inline_page_top'                  => 'false',
				'sharethis_inline_page_bottom'               => 'false',
				'sharethis_excerpt'                          => 'false',
				'sharethis_inline_post_top_margin_top'       => 0,
				'sharethis_inline_post_top_margin_bottom'    => 0,
				'sharethis_inline_post_bottom_margin_top'    => 0,
				'sharethis_inline_post_bottom_margin_bottom' => 0,
				'sharethis_inline_page_top_margin_top'       => 0,
				'sharethis_inline_page_top_margin_bottom'    => 0,
				'sharethis_inline_page_bottom_margin_top'    => 0,
				'sharethis_inline_page_bottom_margin_bottom' => 0,
				'sharethis_excerpt_margin_top'               => 0,
				'sharethis_excerpt_margin_bottom'            => 0,
			),
			'sticky_settings'     => array(
				'sharethis_sticky_home'         => 'true',
				'sharethis_sticky_post'         => 'true',
				'sharethis_sticky_custom_posts' => 'true',
				'sharethis_sticky_page'         => 'true',
				'sharethis_sticky_category'     => 'true',
				'sharethis_sticky_tags'         => 'true',
				'sharethis_sticky_author'       => 'true',
			),
			'sticky_page_off'     => '',
			'sticky_category_off' => '',
		);

		if ( 'both' !== $type ) {
			update_option( 'sharethis_' . $type . '_settings', $default[ $type . '_settings' ] );

			if ( 'sticky' === $type ) {
				update_option( 'sharethis_sticky_page_off', '' );
				update_option( 'sharethis_sticky_category_off', '' );
			}
		} else {
			foreach ( $default as $types => $settings ) {
				update_option( 'sharethis_' . $types, $settings );
			}
		}
	}

	/**
	 * AJAC Call back to return categories or pages based on input.
	 *
	 * @action wp_ajax_return_omit
	 */
	public function return_omit() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['key'], $_POST['type'] ) || '' === $_POST['key'] ) { // WPCS: input var ok.
			wp_send_json_error( '' );
		}

		$key_input = sanitize_text_field( wp_unslash( $_POST['key'] ) ); // WPCS: input var ok.
		$type = sanitize_text_field( wp_unslash( $_POST['type'] ) ); // WPCS: input var ok.
		$current_cat = array_values( get_option( 'sharethis_sticky_category_off' ) );

		if ( 'category' === $type ) {
			// Search category names LIKE $key_input.
			$categories = get_categories( array(
				'name__like' => $key_input,
				'exclude'    => $current_cat,
				'hide_empty' => false,
			) );

			foreach ( $categories as $cats ) {
				$related[] = array(
					'id'    => $cats->term_id,
					'title' => $cats->name,
				);
			}
		} else {
			// Search page names like $key_input.
			$pages = get_pages();

			foreach ( $pages as $page ) {
				if ( false !== stripos( $page->post_title, $key_input ) && $this->not_in_list( $page->ID ) ) {
					$related[] = array(
						'id'     => $page->ID,
						'title'  => $page->post_title,
					);
				}
			}
		}

		// Create output list if any results exist.
		if ( count( $related ) > 0 ) {
			foreach ( $related as $items ) {
				$item_option[] = sprintf(
					'<li class="ta-' . $type . '-item" data-id="%1$d">%2$s</li>',
					(int) $items['id'],
					esc_html( $items['title'] )
				);
			}

			wp_send_json_success( $item_option );
		} else {
			wp_send_json_error( 'no results' );
		}
	}

	/**
	 * Helper function to determine if page is in the list already.
	 *
	 * @param integer $id The page id.
	 *
	 * @return bool
	 */
	private function not_in_list( $id ) {
		$current_pages = array_values( get_option( 'sharethis_sticky_page_off' ) );

		if ( ! is_array( $current_pages ) || array() === $current_pages || ! in_array( (string) $id, $current_pages, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Display custom admin notice.
	 *
	 * @action admin_notices
	 */
	public function connection_made_admin_notice() {
		settings_errors();

		$screen = get_current_screen();
		if ( 'sharethis_page_sharethis-share-buttons' === $screen->base ) {
			if ( isset( $_GET['reset'] ) && '' !== $_GET['reset'] ) { // WPCS: CSRF ok. Input var ok.
				?>
					<div class="notice notice-success is-dismissible">
						<p>
							<?php
							// translators: The type of button.
							printf( esc_html__( 'Successfully reset your %1$s share button options!', 'sharethis-share-buttons' ), esc_html( sanitize_text_field( wp_unslash( $_GET['reset'] ) ) ) ); // WPCS: CSRF ok. Input var ok.
							?>
						</p>
					</div>
			<?php
			};
		}
	}

	/**
	 * Runs only when the plugin is activated.
	 */
	public function st_activation_hook() {
		// Create transient data.
		set_transient( 'st-activation', true, 5 );
		set_transient( 'st-connection', true, 360 );

		// Set the default optons.
		$this->set_the_defaults( 'both' );
	}

	/**
	 * Admin Notice on Activation.
	 *
	 * @action admin_notices
	 */
	public function activation_inform_notice() {
		$screen = get_current_screen();
		$product = get_option( 'sharethis_first_product' );
		$product = null !== $product && false !== $product ? ucfirst( $product ) : 'your';
		$gen_url = '<a href="' . esc_url( admin_url( 'admin.php?page=sharethis-share-buttons&nft' ) ) . '">configuration</a>';

		if ( ! $this->is_property_id_set() ) {
			$gen_url = '<a href="' . esc_url( admin_url( 'admin.php?page=sharethis-general' ) ) . '">configuration</a>';
		}

		// Check transient, if available display notice.
		if ( get_transient( 'st-activation' ) ) {
			?>
			<div class="updated notice is-dismissible">
				<p>
					<?php
					// translators: The general settings url.
					printf( esc_html__( 'Your ShareThis Share Button plugin requires %1$s', 'sharethis-share-button' ), wp_kses_post( $gen_url ) );
					?>
					.
				</p>
			</div>
			<?php
			// Delete transient, only display this notice once.
			delete_transient( 'st-activation' );
		}

		if ( 'sharethis_page_sharethis-share-buttons' === $screen->base && get_transient( 'st-connection' ) && ! isset( $_GET['nft'] ) ) { // WPCS: CSRF ok. input var ok.
			?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					// translators: The product type.
					printf( esc_html__( 'Congrats! You’ve activated %1$s Share Buttons. Sit tight, they’ll appear on your site in just a few minutes!', 'sharethis-share-buttons' ), esc_html( $product ) );
					?>
				</p>
			</div>
			<?php
			delete_transient( 'st-connection' );
		}
	}

	/**
	 * Remove all database information when plugin is deactivated.
	 */
	public function st_deactivation_hook() {
		global $wp_registered_settings;

		foreach ( $wp_registered_settings as $name => $value ) {
			if ( in_array( 'sharethis', explode( '_', $name ), true ) && 'sharethis_property_id' !== $name ) {
				delete_option( $name );
			}
		}
	}

	/**
	 * Register the button widget.
	 *
	 * @action widgets_init
	 */
	public function register_widgets() {
		register_widget( $this->button_widget );
	}

	/**
	 * Return the set up steps.
	 */
	private function get_setup_steps() {
		$steps = array(
			1 => esc_html__( 'Choose button type', 'sharethis-share-buttons' ),
			2 => esc_html__( 'Design your buttons', 'sharethis-share-buttons' ),
			3 => esc_html__( 'Register with ShareThis', 'sharethis-share-buttons' ),
			4 => esc_html__( 'Configure WordPress Settings', 'sharethis-share-buttons' ),
		);

		return $steps;
	}

	/**
	 * Return network array with info.
	 */
	private function get_networks() {
		$networks = array(
			'facebook' => array(
				'color' => '#3B5998',
				'color-rgba' => '59, 89, 152',
				'path' => 'm21.7 16.7h5v5h-5v11.6h-5v-11.6h-5v-5h5v-2.1c0-2 0.6-4.5 1.8-5.9 1.3-1.3 2.8-2 4.7-2h3.5v5h-3.5c-0.9 0-1.5 0.6-1.5 1.5v3.5z',
				'selected' => 'true',
			),
			'twitter' => array(
				'color' => '#55acee',
				'color-rgba' => '85, 172, 238',
				'path' => 'm31.5 11.7c1.3-0.8 2.2-2 2.7-3.4-1.4 0.7-2.7 1.2-4 1.4-1.1-1.2-2.6-1.9-4.4-1.9-1.7 0-3.2 0.6-4.4 1.8-1.2 1.2-1.8 2.7-1.8 4.4 0 0.5 0.1 0.9 0.2 1.3-5.1-0.1-9.4-2.3-12.7-6.4-0.6 1-0.9 2.1-0.9 3.1 0 2.2 1 3.9 2.8 5.2-1.1-0.1-2-0.4-2.8-0.8 0 1.5 0.5 2.8 1.4 4 0.9 1.1 2.1 1.8 3.5 2.1-0.5 0.1-1 0.2-1.6 0.2-0.5 0-0.9 0-1.1-0.1 0.4 1.2 1.1 2.3 2.1 3 1.1 0.8 2.3 1.2 3.6 1.3-2.2 1.7-4.7 2.6-7.6 2.6-0.7 0-1.2 0-1.5-0.1 2.8 1.9 6 2.8 9.5 2.8 3.5 0 6.7-0.9 9.4-2.7 2.8-1.8 4.8-4.1 6.1-6.7 1.3-2.6 1.9-5.3 1.9-8.1v-0.8c1.3-0.9 2.3-2 3.1-3.2-1.1 0.5-2.3 0.8-3.5 1z',
				'selected' => 'true',
			),
			'pinterest' => array(
				'color' => '#CB2027',
				'color-rgba' => '203, 32, 39',
				'path' => 'm37.3 20q0 4.7-2.3 8.6t-6.3 6.2-8.6 2.3q-2.4 0-4.8-0.7 1.3-2 1.7-3.6 0.2-0.8 1.2-4.7 0.5 0.8 1.7 1.5t2.5 0.6q2.7 0 4.8-1.5t3.3-4.2 1.2-6.1q0-2.5-1.4-4.7t-3.8-3.7-5.7-1.4q-2.4 0-4.4 0.7t-3.4 1.7-2.5 2.4-1.5 2.9-0.4 3q0 2.4 0.8 4.1t2.7 2.5q0.6 0.3 0.8-0.5 0.1-0.1 0.2-0.6t0.2-0.7q0.1-0.5-0.3-1-1.1-1.3-1.1-3.3 0-3.4 2.3-5.8t6.1-2.5q3.4 0 5.3 1.9t1.9 4.7q0 3.8-1.6 6.5t-3.9 2.6q-1.3 0-2.2-0.9t-0.5-2.4q0.2-0.8 0.6-2.1t0.7-2.3 0.2-1.6q0-1.2-0.6-1.9t-1.7-0.7q-1.4 0-2.3 1.2t-1 3.2q0 1.6 0.6 2.7l-2.2 9.4q-0.4 1.5-0.3 3.9-4.6-2-7.5-6.3t-2.8-9.4q0-4.7 2.3-8.6t6.2-6.2 8.6-2.3 8.6 2.3 6.3 6.2 2.3 8.6z',
				'selected' => 'true',
			),
			'email' => array(
				'color' => '#7d7d7d',
				'color-rgba' => '125, 125, 125',
				'path' => 'm33.4 13.4v-3.4l-13.4 8.4-13.4-8.4v3.4l13.4 8.2z m0-6.8q1.3 0 2.3 1.1t0.9 2.3v20q0 1.3-0.9 2.3t-2.3 1.1h-26.8q-1.3 0-2.3-1.1t-0.9-2.3v-20q0-1.3 0.9-2.3t2.3-1.1h26.8z',
				'selected' => 'true',
			),
			'sms' => array(
				'color' => '#ffbd00',
				'color-rgba' => '255, 189, 0',
				'path' => 'M29.577,23.563 C27.233,23.563 25.935,22.138 25.935,22.138 L27.22,20.283 C27.22,20.283 28.349,21.315 29.605,21.315 C30.108,21.315 30.652,21.12 30.652,20.52 C30.652,19.334 26.158,19.376 26.158,16.306 C26.158,14.464 27.707,13.25 29.688,13.25 C31.839,13.25 32.898,14.38 32.898,14.38 L31.866,16.376 C31.866,16.376 30.861,15.497 29.661,15.497 C29.159,15.497 28.6,15.72 28.6,16.278 C28.6,17.534 33.094,17.311 33.094,20.464 C33.094,22.125 31.824,23.563 29.577,23.563 L29.577,23.563 Z M23.027,23.394 L22.721,18.901 C22.665,18.147 22.721,17.227 22.721,17.227 L22.692,17.227 C22.692,17.227 22.356,18.273 22.134,18.901 L21.088,21.79 L18.994,21.79 L17.947,18.901 C17.724,18.273 17.389,17.227 17.389,17.227 L17.361,17.227 C17.361,17.227 17.417,18.147 17.361,18.901 L17.055,23.394 L14.598,23.394 L15.422,13.417 L18.073,13.417 L19.524,17.631 C19.748,18.273 20.026,19.278 20.026,19.278 L20.055,19.278 C20.055,19.278 20.334,18.273 20.557,17.631 L22.008,13.417 L24.66,13.417 L25.469,23.394 L23.027,23.394 Z M10.548,23.563 C8.204,23.563 6.906,22.138 6.906,22.138 L8.19,20.283 C8.19,20.283 9.32,21.315 10.576,21.315 C11.078,21.315 11.623,21.12 11.623,20.52 C11.623,19.334 7.129,19.376 7.129,16.306 C7.129,14.464 8.678,13.25 10.66,13.25 C12.808,13.25 13.869,14.38 13.869,14.38 L12.836,16.376 C12.836,16.376 11.832,15.497 10.632,15.497 C10.129,15.497 9.571,15.72 9.571,16.278 C9.571,17.534 14.064,17.311 14.064,20.464 C14.064,22.125 12.795,23.563 10.548,23.563 L10.548,23.563 Z M32.814,6 L7.185,6 C5.437,6 4,7.438 4,9.213 L4,28.99 C4,30.756 5.426,32.203 7.185,32.203 L10.61,32.203 L12.445,34.295 C13.086,34.952 14.117,34.949 14.755,34.295 L16.59,32.203 L32.814,32.203 C34.562,32.203 36,30.764 36,28.99 L36,9.213 C36,7.446 34.574,6 32.814,6 L32.814,6 Z',
				'selected' => 'true',
			),
			'messenger' => array(
				'color' => '#448AFF',
				'color-rgba' => '68, 138, 255',
				'path' => 'M25,2C12.3,2,2,11.6,2,23.5c0,6.3,2.9,12.2,8,16.3v8.8l8.6-4.5c2.1,0.6,4.2,0.8,6.4,0.8c12.7,0,23-9.6,23-21.5 C48,11.6,37.7,2,25,2z M27.3,30.6l-5.8-6.2l-10.8,6.1l12-12.7l5.9,5.9l10.5-5.9L27.3,30.6z',
				'selected' => 'false',
				'url' => 'messenger.com/',
				'viewbox' => '50',
			),
			'sharethis' => array(
				'color' => '#95D03A',
				'color-rgba' => '149, 208, 58',
				'path' => 'm30 26.8c2.7 0 4.8 2.2 4.8 4.8s-2.1 5-4.8 5-4.8-2.3-4.8-5c0-0.3 0-0.7 0-1.1l-11.8-6.8c-0.9 0.8-2.1 1.3-3.4 1.3-2.7 0-5-2.3-5-5s2.3-5 5-5c1.3 0 2.5 0.5 3.4 1.3l11.8-6.8c-0.1-0.4-0.2-0.8-0.2-1.1 0-2.8 2.3-5 5-5s5 2.2 5 5-2.3 5-5 5c-1.3 0-2.5-0.6-3.4-1.4l-11.8 6.8c0.1 0.4 0.2 0.8 0.2 1.2s-0.1 0.8-0.2 1.2l11.9 6.8c0.9-0.7 2.1-1.2 3.3-1.2z',
				'selected' => 'true',
			),
			'linkedin' => array(
				'color' => '#0077b5',
				'color-rgba' => '0, 119, 181',
				'path' => 'm13.3 31.7h-5v-16.7h5v16.7z m18.4 0h-5v-8.9c0-2.4-0.9-3.5-2.5-3.5-1.3 0-2.1 0.6-2.5 1.9v10.5h-5s0-15 0-16.7h3.9l0.3 3.3h0.1c1-1.6 2.7-2.8 4.9-2.8 1.7 0 3.1 0.5 4.2 1.7 1 1.2 1.6 2.8 1.6 5.1v9.4z m-18.3-20.9c0 1.4-1.1 2.5-2.6 2.5s-2.5-1.1-2.5-2.5 1.1-2.5 2.5-2.5 2.6 1.2 2.6 2.5z',
				'selected' => 'false',
			),
			'reddit' => array(
				'color' => '#ff4500',
				'color-rgba' => '255, 69, 0',
				'path' => 'm40 18.9q0 1.3-0.7 2.3t-1.7 1.7q0.2 1 0.2 2.1 0 3.5-2.3 6.4t-6.5 4.7-9 1.7-8.9-1.7-6.4-4.7-2.4-6.4q0-1.1 0.2-2.1-1.1-0.6-1.8-1.6t-0.7-2.4q0-1.8 1.3-3.2t3.1-1.3q1.9 0 3.3 1.4 4.8-3.3 11.5-3.6l2.6-11.6q0-0.3 0.3-0.5t0.6-0.1l8.2 1.8q0.4-0.8 1.2-1.3t1.8-0.5q1.4 0 2.4 1t0.9 2.3-0.9 2.4-2.4 1-2.4-1-0.9-2.4l-7.5-1.6-2.3 10.5q6.7 0.2 11.6 3.6 1.3-1.4 3.2-1.4 1.8 0 3.1 1.3t1.3 3.2z m-30.7 4.4q0 1.4 1 2.4t2.4 1 2.3-1 1-2.4-1-2.3-2.3-1q-1.4 0-2.4 1t-1 2.3z m18.1 8q0.3-0.3 0.3-0.6t-0.3-0.6q-0.2-0.2-0.5-0.2t-0.6 0.2q-0.9 0.9-2.7 1.4t-3.6 0.4-3.6-0.4-2.7-1.4q-0.2-0.2-0.5-0.2t-0.6 0.2q-0.3 0.2-0.3 0.6t0.3 0.6q1 0.9 2.6 1.5t2.8 0.6 2 0.1 2-0.1 2.8-0.6 2.6-1.6z m-0.1-4.6q1.4 0 2.4-1t1-2.4q0-1.3-1-2.3t-2.4-1q-1.3 0-2.3 1t-1 2.3 1 2.4 2.3 1z',
				'selected' => 'false',
			),
			'tumblr' => array(
				'color' => '#32506d',
				'color-rgba' => '50, 80, 109',
				'path' => 'm25.9 29.9v-3.5c-1.1 0.8-2.2 1.1-3.3 1.1-0.5 0-1-0.1-1.6-0.4-0.4-0.3-0.6-0.5-0.7-0.9-0.2-0.3-0.3-1.1-0.3-2.4v-5.5h5v-3.3h-5v-5.6h-3c-0.2 1.3-0.5 2.2-0.7 2.8-0.3 0.7-0.8 1.3-1.5 1.9-0.7 0.5-1.4 0.9-2.1 1.2v3h2.3v7.6c0 0.8 0.1 1.6 0.4 2.2 0.2 0.5 0.5 1 1.1 1.5 0.4 0.4 1 0.8 1.8 1.1 1 0.3 1.9 0.4 2.7 0.4 0.8 0 1.6-0.1 2.4-0.3 0.8-0.2 1.7-0.5 2.5-0.9z',
				'selected' => 'false',
			),
			'digg' => array(
				'color' => '#262626',
				'color-rgba' => '38, 38, 38',
				'path' => 'm6.4 8.1h3.9v19.1h-10.3v-13.6h6.4v-5.5z m0 15.9v-7.2h-2.4v7.2h2.4z m5.5-10.4v13.6h4v-13.6h-4z m0-5.5v3.9h4v-3.9h-4z m5.6 5.5h10.3v18.3h-10.3v-3.1h6.4v-1.6h-6.4v-13.6z m6.4 10.4v-7.2h-2.4v7.2h2.4z m5.5-10.4h10.4v18.3h-10.4v-3.1h6.4v-1.6h-6.4v-13.6z m6.4 10.4v-7.2h-2.4v7.2h2.4z',
				'selected' => 'false',
			),
			'googleplus' => array(
				'color' => '#dc4e41',
				'color-rgba' => '220, 78, 65',
				'path' => 'm25.2 20.3q0 3.6-1.6 6.5t-4.3 4.4-6.5 1.6q-2.6 0-5-1t-4.1-2.7-2.7-4.1-1-5 1-5 2.7-4.1 4.1-2.7 5-1q5 0 8.6 3.3l-3.5 3.4q-2-2-5.1-2-2.1 0-4 1.1t-2.8 2.9-1.1 4.1 1.1 4.1 2.8 2.9 4 1.1q1.5 0 2.7-0.4t2-1 1.4-1.4 0.8-1.4 0.4-1.3h-7.3v-4.4h12.1q0.3 1.1 0.3 2.1z m15.1-2.1v3.6h-3.6v3.7h-3.7v-3.7h-3.7v-3.6h3.7v-3.7h3.7v3.7h3.6z',
				'selected' => 'false',
			),
			'stumbleupon' => array(
				'color' => '#eb4924',
				'color-rgba' => '235, 73, 36',
				'path' => 'm22.1 16.2v-2.5q0-0.8-0.7-1.5t-1.5-0.6-1.5 0.6-0.6 1.5v12.7q0 3.7-2.6 6.2t-6.3 2.6q-3.7 0-6.3-2.6t-2.6-6.3v-5.5h6.8v5.4q0 0.9 0.6 1.5t1.5 0.6 1.5-0.6 0.6-1.5v-12.8q0-3.6 2.7-6.1t6.2-2.5q3.7 0 6.3 2.5t2.6 6.1v2.8l-4 1.2z m11 4.6h6.8v5.5q0 3.7-2.6 6.3t-6.3 2.6q-3.7 0-6.3-2.6t-2.6-6.2v-5.6l2.7 1.3 4-1.2v5.6q0 0.9 0.6 1.5t1.5 0.6 1.5-0.6 0.7-1.5v-5.7z',
				'selected' => 'false',
			),
			'whatsapp' => array(
				'color' => '#25d366',
				'color-rgba' => '37, 211, 102',
				'path' => 'm25 21.7q0.3 0 2.2 1t2 1.2q0 0.1 0 0.3 0 0.8-0.4 1.7-0.3 0.9-1.6 1.5t-2.2 0.6q-1.3 0-4.3-1.4-2.2-1-3.8-2.6t-3.3-4.2q-1.6-2.3-1.6-4.3v-0.2q0.1-2 1.7-3.5 0.5-0.5 1.2-0.5 0.1 0 0.4 0t0.4 0.1q0.4 0 0.6 0.1t0.3 0.6q0.2 0.5 0.8 2t0.5 1.7q0 0.5-0.8 1.3t-0.7 1q0 0.2 0.1 0.3 0.7 1.7 2.3 3.1 1.2 1.2 3.3 2.2 0.3 0.2 0.5 0.2 0.4 0 1.2-1.1t1.2-1.1z m-4.5 11.9q2.8 0 5.4-1.1t4.5-3 3-4.5 1.1-5.4-1.1-5.5-3-4.5-4.5-2.9-5.4-1.2-5.5 1.2-4.5 2.9-2.9 4.5-1.2 5.5q0 4.5 2.7 8.2l-1.7 5.2 5.4-1.8q3.5 2.4 7.7 2.4z m0-30.9q3.4 0 6.5 1.4t5.4 3.6 3.5 5.3 1.4 6.6-1.4 6.5-3.5 5.3-5.4 3.6-6.5 1.4q-4.4 0-8.2-2.1l-9.3 3 3-9.1q-2.4-3.9-2.4-8.6 0-3.5 1.4-6.6t3.6-5.3 5.3-3.6 6.6-1.4z',
				'selected' => 'false',
			),
			'vk' => array(
				'color' => '#4c6c91',
				'color-rgba' => '76, 108, 145',
				'path' => 'm39.8 12.2q0.5 1.3-3.1 6.1-0.5 0.7-1.4 1.8-1.6 2-1.8 2.7-0.4 0.8 0.3 1.7 0.3 0.4 1.6 1.7h0.1l0 0q3 2.8 4 4.6 0.1 0.1 0.1 0.3t0.2 0.5 0 0.8-0.5 0.5-1.3 0.3l-5.3 0.1q-0.5 0.1-1.1-0.1t-1.1-0.5l-0.4-0.2q-0.7-0.5-1.5-1.4t-1.4-1.6-1.3-1.2-1.1-0.3q-0.1 0-0.2 0.1t-0.4 0.3-0.4 0.6-0.4 1.1-0.1 1.6q0 0.3-0.1 0.5t-0.1 0.4l-0.1 0.1q-0.4 0.4-1.1 0.5h-2.4q-1.5 0.1-3-0.4t-2.8-1.1-2.1-1.3-1.5-1.2l-0.5-0.5q-0.2-0.2-0.6-0.6t-1.4-1.9-2.2-3.2-2.6-4.4-2.7-5.6q-0.1-0.3-0.1-0.6t0-0.3l0.1-0.1q0.3-0.4 1.2-0.4l5.7-0.1q0.2 0.1 0.5 0.2t0.3 0.2l0.1 0q0.3 0.2 0.5 0.7 0.4 1 1 2.1t0.8 1.7l0.3 0.6q0.6 1.3 1.2 2.2t1 1.4 0.9 0.8 0.7 0.3 0.5-0.1q0.1 0 0.1-0.1t0.3-0.5 0.3-0.9 0.2-1.7 0-2.6q-0.1-0.9-0.2-1.5t-0.3-1l-0.1-0.2q-0.5-0.7-1.8-0.9-0.3-0.1 0.1-0.5 0.4-0.4 0.8-0.7 1.1-0.5 5-0.5 1.7 0.1 2.8 0.3 0.4 0.1 0.7 0.3t0.4 0.5 0.2 0.7 0.1 0.9 0 1.1-0.1 1.5 0 1.7q0 0.3 0 0.9t-0.1 1 0.1 0.8 0.3 0.8 0.4 0.6q0.2 0 0.4 0t0.5-0.2 0.8-0.7 1.1-1.4 1.4-2.2q1.2-2.2 2.2-4.7 0.1-0.2 0.2-0.4t0.3-0.2l0 0 0.1-0.1 0.3-0.1 0.4 0 6 0q0.8-0.1 1.3 0t0.7 0.4z',
				'selected' => 'false',
			),
			'weibo' => array(
				'color' => '#ff9933',
				'color-rgba' => '255, 153, 51',
				'path' => 'm15.1 28.7q0.4-0.8 0.2-1.6t-1-1.1q-0.8-0.3-1.6 0t-1.4 1q-0.5 0.8-0.3 1.5t1 1.2 1.7 0 1.4-1z m2.1-2.7q0.1-0.3 0-0.6t-0.3-0.4q-0.4-0.2-0.7 0t-0.5 0.4q-0.3 0.7 0.3 1 0.3 0.1 0.7 0t0.5-0.4z m3.8 2.3q-1 2.3-3.5 3.4t-5 0.3q-2.4-0.8-3.3-2.9t0.2-4.1q1-2.1 3.4-3.1t4.7-0.5q2.4 0.7 3.5 2.7t0 4.2z m7-3.5q-0.2-2.2-2-3.8t-4.6-2.5-6.2-0.4q-4.9 0.5-8.2 3.1t-3 5.9q0.2 2.2 2 3.8t4.7 2.5 6.1 0.4q5-0.5 8.3-3.1t2.9-5.9z m6.9 0.1q0 1.5-0.8 3.1t-2.5 3-3.7 2.7-5.1 1.8-6 0.7-6.2-0.7-5.3-2.1-3.9-3.4-1.4-4.4q0-2.6 1.6-5.5t4.4-5.8q3.7-3.7 7.6-5.2t5.5 0.1q1.4 1.4 0.4 4.7-0.1 0.3 0 0.4t0.2 0.2 0.4 0 0.3-0.1l0.1-0.1q3.1-1.3 5.5-1.3t3.4 1.4q1 1.4 0 4 0 0.3-0.1 0.4t0.1 0.3 0.3 0.2 0.3 0.1q1.3 0.4 2.3 1t1.8 1.9 0.8 2.6z m-1.7-14q1 1.1 1.3 2.5t-0.2 2.6q-0.2 0.5-0.7 0.7t-0.9 0.1q-0.6-0.1-0.8-0.6t-0.1-1q0.5-1.4-0.5-2.5t-2.4-0.8q-0.6 0.1-1-0.2t-0.6-0.8q-0.1-0.5 0.2-1t0.8-0.5q1.4-0.3 2.7 0.1t2.2 1.4z m4.1-3.6q1.9 2.1 2.5 5t-0.3 5.4q-0.2 0.6-0.8 0.8t-1.1 0.1-0.9-0.7-0.1-1.2q0.6-1.8 0.2-3.8t-1.8-3.5q-1.4-1.6-3.3-2.2t-3.9-0.2q-0.6 0.2-1.1-0.2t-0.7-1 0.2-1.1 1-0.7q2.7-0.5 5.4 0.3t4.7 3z',
				'selected' => 'false',
			),
			'odnoklassniki' => array(
				'color' => '#d7772d',
				'color-rgba' => '215, 119, 45',
				'path' => 'm19.8 20.2q-4.2 0-7.2-2.9t-2.9-7.2q0-4.2 2.9-7.1t7.2-3 7.1 3 3 7.1q0 4.2-3 7.2t-7.1 2.9z m0-15.1q-2.1 0-3.5 1.5t-1.5 3.5q0 2.1 1.5 3.5t3.5 1.5 3.5-1.5 1.5-3.5q0-2-1.5-3.5t-3.5-1.5z m11.7 16.4q0.3 0.6 0.3 1.1t-0.1 0.9-0.6 0.8-0.9 0.9-1.4 0.9q-2.6 1.6-7 2.1l1.6 1.6 5.9 6q0.7 0.7 0.7 1.6t-0.7 1.6l-0.2 0.3q-0.7 0.7-1.7 0.7t-1.6-0.7q-1.5-1.5-6-6l-6 6q-0.6 0.7-1.6 0.7t-1.6-0.7l-0.3-0.3q-0.7-0.6-0.7-1.6t0.7-1.6l7.6-7.6q-4.6-0.5-7.1-2.1-0.9-0.6-1.4-0.9t-0.9-0.9-0.6-0.8-0.1-0.9 0.3-1.1q0.2-0.5 0.6-0.8t1-0.5 1.2 0 1.5 0.8q0.1 0.1 0.3 0.3t1 0.5 1.5 0.7 2.1 0.5 2.5 0.3q2 0 3.9-0.6t2.6-1.1l0.9-0.6q0.7-0.5 1.4-0.8t1.3 0 0.9 0.5 0.7 0.8z',
				'selected' => 'false',
			),
			'xing' => array(
				'color' => '#1a7576',
				'color-rgba' => '26, 117, 118',
				'path' => 'm17.8 14.9q-0.2 0.4-5.7 10.2-0.6 1-1.5 1h-5.3q-0.5 0-0.7-0.4t0-0.8l5.7-10q0 0 0 0l-3.6-6.2q-0.3-0.5-0.1-0.9 0.2-0.3 0.8-0.3h5.3q0.9 0 1.5 1z m18-14.3q0.3 0.3 0 0.8l-11.8 20.8v0.1l7.5 13.7q0.3 0.4 0.1 0.8-0.3 0.3-0.8 0.3h-5.3q-0.9 0-1.5-1l-7.5-13.8 11.8-21.1q0.6-1 1.4-1h5.4q0.5 0 0.7 0.4z',
				'selected' => 'false',
			),
			'print' => array(
				'color' => '#222222',
				'color-rgba' => '34, 34, 34',
				'path' => 'm30 5v6.6h-20v-6.6h20z m1.6 15c1 0 1.8-0.7 1.8-1.6s-0.8-1.8-1.8-1.8-1.6 0.8-1.6 1.8 0.7 1.6 1.6 1.6z m-5 11.6v-8.2h-13.2v8.2h13.2z m5-18.2c2.8 0 5 2.2 5 5v10h-6.6v6.6h-20v-6.6h-6.6v-10c0-2.8 2.2-5 5-5h23.2z',
				'selected' => 'false',
			),
			'blogger' => array(
				'color' => '#ff8000',
				'color-rgba' => '235, 73, 36',
				'path' => 'M27.5,30 L12.5,30 C11.125,30 10,28.875 10,27.5 C10,26.125 11.125,25 12.5,25 L27.5,25 C28.875,25 30,26.125 30,27.5 C30,28.875 28.875,30 27.5,30 M12.5,10 L20,10 C21.375,10 22.5,11.125 22.5,12.5 C22.5,13.875 21.375,15 20,15 L12.5,15 C11.125,15 10,13.875 10,12.5 C10,11.125 11.125,10 12.5,10 M37.41375,15 L35.21875,15 L35.17125,15 C33.7975,15 32.59375,13.8375 32.5,12.5 C32.5,5.365 26.7475,0 19.5625,0 L13.0075,0 C5.8275,0 0.005,5.78125 0,12.91625 L0,27.08875 C0,34.22375 5.8275,40 13.0075,40 L27.0075,40 C34.1925,40 40,34.22375 40,27.08875 L40,17.93375 C40,16.5075 38.85,15 37.41375,15',
				'selected' => 'false',
			),
			'flipboard' => array(
				'color' => '#e12828',
				'color-rgba' => '37, 211, 102',
				'path' => 'M0,0 L13.3333333,0 L13.3333333,13.3333333 L0,13.3333333 L0,0 Z M0,13.3333333 L13.3333333,13.3333333 L13.3333333,26.6666667 L0,26.6666667 L0,13.3333333 Z M13.3333333,13.3333333 L26.6666667,13.3333333 L26.6666667,26.6666667 L13.3333333,26.6666667 L13.3333333,13.3333333 Z M0,26.6666667 L13.3333333,26.6666667 L13.3333333,40 L0,40 L0,26.6666667 Z M13.3333333,0 L26.6666667,0 L26.6666667,13.3333333 L13.3333333,13.3333333 L13.3333333,0 Z M26.6666667,0 L40,0 L40,13.3333333 L26.6666667,13.3333333 L26.6666667,0 Z',
				'selected' => 'false',
			),
			'meneame' => array(
				'color' => '#ff6400',
				'color-rgba' => '255, 100, 0',
				'path' => 'M37.6371624,10.0104081 C36.6268087,11.0735024 35.3851257,11.7323663 34.1607384,12.4190806 C33.0545379,13.0405452 31.9144669,13.5911899 30.8702425,14.343154 C29.735216,15.1635509 28.7926035,16.1645784 28.4798406,17.69397 C28.2268918,18.9376949 28.4322776,20.1686881 28.6600035,21.3837667 C29.1586946,24.0598043 30.1380603,26.5496412 31.1094988,29.042661 C31.6074692,30.3293553 32.1421928,31.6009307 32.5421545,32.93139 C33.1842552,35.0758805 32.648811,36.7294059 30.9206881,37.9357315 C29.6761225,38.7998935 28.2831027,39.1786606 26.852609,39.4309068 C26.0166529,39.5765253 25.1691665,39.6234733 24.305105,39.6855402 C24.1220595,39.6171075 23.7847945,39.8407074 23.727863,39.5614064 C23.6774173,39.2876755 24.0290954,39.1977581 24.2373638,39.1006792 C25.4934598,38.5261626 26.7574829,37.975518 28.0128583,37.4010014 C28.7954861,37.043719 29.5579356,36.6363056 30.2591298,36.1079413 C30.5012688,35.9273108 30.7232295,35.7188297 30.9178055,35.4801109 C31.2276857,35.0973651 31.3033542,34.6931347 31.1231912,34.1934167 C30.1661657,31.5539827 29.1017631,28.9583137 28.2629244,26.2671573 C27.5905563,24.0940206 27.0529501,21.8930335 26.9117024,19.5838271 C26.7430699,16.8608415 27.5992042,14.7410187 29.7272888,13.2832426 C30.7318773,12.5965283 31.8099723,12.0832829 32.8880674,11.5676503 C34.2227144,10.9302712 35.5429484,10.2777731 36.7140075,9.29266029 C38.4651913,7.81578675 39.0395507,5.83919521 38.8896551,3.5045255 C38.8226345,2.45416285 38.6619292,1.42210198 38.445013,0.399589849 C38.4197902,0.284209103 38.2763805,0.113127308 38.4651913,0.0232098993 C38.6367064,-0.0571587581 38.7267879,0.0852767832 38.8002943,0.234873888 C39.3710505,1.38151978 39.8164133,2.56556495 39.9569404,3.88408837 C40.2243022,6.41052884 39.2218756,8.33778516 37.6371624,10.0104081 M23.0864829,39.5518577 C23.0021667,39.6791743 22.9092026,39.8407074 22.7398494,39.7754576 C22.5877919,39.7165736 22.6079701,39.539126 22.6022049,39.3966905 C22.5596865,38.3829313 22.7088614,37.3882697 22.7938983,36.3872423 C22.9149678,34.9517467 23.1340459,33.5218212 23.0129764,32.067228 C22.9762232,31.6072965 22.7852505,31.3271998 22.4133942,31.1314504 C21.3216068,30.5529552 20.1505477,30.3635717 18.9650755,30.236255 C17.5151242,30.0810878 16.0601283,30.0962066 14.6072944,29.9999234 C14.5172129,29.9935575 14.4271315,29.9720728 14.33705,29.9593412 C17.1382234,29.4500744 19.9365141,29.1731607 22.7571451,29.5463577 C24.0968367,29.7230096 24.3533887,30.0277739 24.3843767,31.5229492 C24.4348223,33.8886524 24.1335899,36.2105904 23.5282424,38.4792146 C23.4266305,38.8587775 23.3005165,39.2287916 23.0864829,39.5518577 M21.7576011,39.7070249 C20.0035347,39.8629878 18.250189,39.9529052 16.4903574,39.9807557 C14.0192426,40.0213379 11.5358766,40.0396397 9.09574982,39.586074 C5.13216519,38.8492287 2.06074743,36.6705219 0.568998308,32.3711966 C-0.230925135,30.0651732 -0.129313238,27.6620707 0.512787472,25.318648 C1.47774017,21.803116 2.93922192,18.5772295 5.31809334,15.9377955 C5.93425059,15.2542641 6.62679692,14.6662201 7.30781283,14.0638531 C7.37267149,14.0041734 7.43753014,13.914256 7.57229202,13.961204 C7.53914204,14.1848039 7.42599972,14.3527028 7.31646065,14.5269675 C5.46726826,17.4393367 3.88471701,20.5068731 2.69131771,23.8202898 C1.8611269,26.127109 1.66078571,28.4832634 2.20992234,30.8919359 C2.88805564,33.8759207 4.68752306,35.7164425 7.22061397,36.7978386 C9.05899658,37.5840192 10.9759302,37.9603991 12.9180867,38.2150325 C15.6205308,38.5667448 18.3345053,38.8022807 21.0398319,39.0919261 C21.3014285,39.1197767 21.5579805,39.1977581 21.8109293,39.2749439 C21.9348814,39.3091602 22.0927041,39.365657 22.0754085,39.5486747 C22.0559509,39.7603387 21.8786706,39.6950889 21.7576011,39.7070249 M11.1424008,3.1782765 C12.8453009,1.61785138 14.8717736,0.862704291 17.0229191,0.440172043 C20.3977312,-0.219487531 23.5902185,0.505421844 26.6782112,1.97831674 C28.265807,2.73664675 29.797192,3.63184219 31.4359541,4.25967259 C31.79628,4.39653803 32.1623711,4.5206718 32.5421545,4.58353441 C33.2289356,4.68857067 33.7917647,4.43075439 34.242172,3.86817379 C34.8338271,3.13132847 35.0420955,2.24568178 35.0420955,1.2788707 C35.2078454,1.27011768 35.241716,1.41255322 35.2950442,1.52156814 C35.7987798,2.54408026 35.6553701,4.20715446 34.9714716,5.07131646 C34.2537024,5.97844784 33.3024421,6.14077661 32.2913677,6.01027701 C31.1426488,5.86386282 30.0508614,5.49384871 29.0037544,4.94399977 C27.0356545,3.90557306 25.0372872,2.94831074 22.8976721,2.41358066 C20.2204509,1.74516806 17.5634079,1.86373173 14.9647377,2.81781113 C11.1784334,4.20397154 9.1887139,8.35688266 10.2185252,12.618013 C10.8152249,15.0768164 12.0684383,17.0136215 14.0250078,18.3838673 C14.868891,18.9774814 15.8035763,19.1612948 16.7894279,19.1485632 C17.7853686,19.1334443 18.7568072,18.9185974 19.7282457,18.7132993 C19.8968783,18.6790829 20.0604662,18.6329306 20.3220628,18.7196651 C19.4716937,19.3013432 18.6242073,19.6307751 17.7291578,19.8384605 C16.4175716,20.1432248 15.1031028,20.1432248 13.790796,19.8639238 C12.6593727,19.6275922 11.7304526,18.9496308 10.9816955,18.011466 C9.4978735,16.1462766 8.4284263,14.0415726 8.11061888,11.5461656 C7.67678652,8.12373398 8.76352936,5.35380035 11.1424008,3.1782765',
				'selected' => 'false',
			),
			'mailru' => array(
				'color' => '#168de2',
				'color-rgba' => '22, 141, 226',
				'path' => 'M26.9076184,19.8096616 C26.680007,15.199769 23.3977769,12.428026 19.4332382,12.428026 L19.2839504,12.428026 C14.7091187,12.428026 12.1717509,16.1777412 12.1717509,20.4369883 C12.1717509,25.2068628 15.2416138,28.2191267 19.2660779,28.2191267 C23.7536497,28.2191267 26.7047131,24.7932107 26.9181316,20.7410637 L26.9076184,19.8096616 Z M19.3065538,8.30410621 C22.3632752,8.30410621 25.2370663,9.71216703 27.347597,11.9168506 L27.347597,11.9256167 C27.347597,10.8665577 28.0309569,10.0688392 28.9803015,10.0688392 L29.2200031,10.0671956 C30.7049967,10.0671956 31.0093547,11.5316884 31.0093547,11.9951979 L31.016714,28.4558125 C30.9121073,29.5324037 32.0838067,30.0885055 32.7335243,29.3981722 C35.2693151,26.6817654 38.3029074,15.4348111 31.1570656,8.91773583 C24.496935,2.84225472 15.561216,3.84378592 10.8087108,7.25764856 C5.75657856,10.8895689 2.52376063,18.9264732 5.66406215,26.4752133 C9.08716952,34.7109994 18.8828707,37.1655178 24.7045713,34.7170261 C27.6524807,33.4766176 29.0144695,37.630671 25.9524915,38.9872308 C21.3261451,41.0423421 8.45006784,40.8352421 2.43492385,29.9750936 C-1.62896484,22.6416718 -1.41239232,9.73846544 9.36577009,3.05373779 C17.6112956,-2.05965974 28.4819745,-0.643380663 35.0369728,6.49225518 C41.8889698,13.9511423 41.4894671,27.9183387 34.8056817,33.3517002 C31.7778718,35.8177242 27.2803124,33.4163505 27.3092238,29.8211384 L27.2776841,28.6453802 C25.169256,30.8265047 22.3632752,32.0986904 19.3065538,32.0986904 C13.2661781,32.0986904 7.95174078,26.5590395 7.95174078,20.2655007 C7.95174078,13.9073117 13.2661781,8.30410621 19.3065538,8.30410621 L19.3065538,8.30410621 Z',
				'selected' => 'false',
			),
			'delicious' => array(
				'color' => '#205cc0',
				'color-rgba' => '32, 92, 192',
				'path' => 'm35.9 30.7v-10.7h-15.8v-15.7h-10.7q-2 0-3.5 1.4t-1.5 3.6v10.7h15.7v15.7h10.8q2 0 3.5-1.4t1.5-3.6z m1.4-21.4v21.4q0 2.7-1.9 4.6t-4.5 1.8h-21.5q-2.6 0-4.5-1.8t-1.9-4.6v-21.4q0-2.7 1.9-4.6t4.5-1.8h21.5q2.6 0 4.5 1.8t1.9 4.6z',
				'selected' => 'false',
			),
		);

		return $networks;
	}

	/**
	 * AJAX Call back to save the set up button config for setup.
	 *
	 * @action wp_ajax_set_button_config
	 */
	public function set_button_config() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['config'], $_POST['button'] ) || '' === $_POST['config'] ) { // WPCS: input var ok.
			wp_send_json_error( 'Button Config Set Failed' );
		}

		$networks = array_map( 'sanitize_text_field', wp_unslash( $_POST['config']['networks'] ) ); // WPCS: input var ok.
		$first = isset( $_POST['first'] ) && 'upgrade' !== $_POST['first'] ? false : true; // Input var okay.
		$type = isset( $_POST['type'] ) ? true : false; // Input var okay.
		$button = sanitize_text_field( wp_unslash( $_POST['button'] ) ); // WPCS: input var ok.
		$config = $_POST['config']; // WPCS: input var ok. WPCS: sanitization ok. Can't sanitize initially.  Sanitizing once checking value.

		// If user doesn't have a sharethis account already.
		if ( ! $type ) {
			$newconfig[ strtolower( $button ) ] = $config;
			$config = $newconfig;
		} else {
			$config = 'platform' !== $button ? json_decode( str_replace( '\\', '', $config ), true ) : $config;
		}

		if ( ! $first ) {
			$current_config                        = get_option( 'sharethis_button_config', true );
			$current_config                        = false !== $current_config && null !== $current_config ? $current_config : array();
			$current_config[ $button ]             = array_map( 'sanitize_text_field', wp_unslash( $_POST['config'] ) ); // WPCS: input var ok.
			$current_config[ $button ]['networks'] = $networks;
			$config                                = $current_config;
		}

		// Make sure bool is "true" or "false".
		if ( isset( $config['inline'] ) ) {
			$config['inline']['enabled'] = true === $config['inline']['enabled'] || '1' === $config['inline']['enabled'] || 'true' === $config['inline']['enabled'] ? 'true' : 'false';
		}

		if ( isset( $config['sticky'] ) ) {
			$config['sticky']['enabled'] = true === $config['sticky']['enabled'] || '1' === $config['sticky']['enabled'] || 'true' === $config['sticky']['enabled'] ? 'true' : 'false';
		}

		update_option( 'sharethis_button_config', $config );

		if ( $first && 'platform' !== $button ) {
			update_option( 'sharethis_first_product', strtolower( $button ) );
			update_option( 'sharethis_' . strtolower( $button ), 'true' );
		}
	}
}
