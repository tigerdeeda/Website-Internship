<?php
/**
 * Registration form.
 *
 * @package    WPFormsUserRegistration
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
*/
class WPForms_User_Registration {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'wpforms_loaded', array( $this, 'init' ) );
	}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Actions/Filters
		add_action( 'init',                                array( $this, 'load_template'             ), 15, 1 );
		add_action( 'wpforms_builder_enqueues',            array( $this, 'admin_enqueues'            )        );
		add_action( 'wpforms_process',                     array( $this, 'process_user_registration' ), 30, 3 );
		add_filter( 'wpforms_builder_settings_sections',   array( $this, 'settings_register'         ), 20, 2 );
		add_action( 'wpforms_form_settings_panel_content', array( $this, 'settings_content'          ), 20, 2 );
		add_filter( 'wpforms_process_after_filter',        array( $this, 'remove_password_value'     ), 10, 3 );
	}

	/**
	 * Load the user registration form template.
	 *
	 * @since 1.0.2
	 */
	public function load_template() {

		// Template
		require_once plugin_dir_path( __FILE__ ) . 'class-user-registration-template.php';
	}

	/**
	 * Enqueue assets for the builder.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueues() {

		wp_enqueue_script(
			'wpforms-builder-user-registration',
			plugin_dir_url( __FILE__ ) . 'assets/js/admin-builder-user-registration.js',
			array( 'jquery' ),
			WPFORMS_USER_REGISTRATION_VERSION,
			false
		);
	}

	/**
	 * Validate and process the user registration form.
	 *
	 * @since 1.0.0
	 * @param array $fields    The fields that have been submitted.
	 * @param array $entry 	   The post data submitted by the form.
	 * @param array $form_data The information for the form.
	 */
	public function process_user_registration( $fields, $entry, $form_data ) {

		// If no form template can be found, return early.
		if ( empty( $form_data['meta']['template'] ) ) {
			return;
		}

		// If the form template doesn't match the user registration template, return early.
		if ( 'user_registration' !== $form_data['meta']['template'] ) {
			return;
		}

		$form_settings = $form_data['settings'];
		$form_fields   = $form_data['fields'];
		$optional      = array( 'name', 'bio', 'website', 'password' );
		$required      = array( 'username', 'email' );
		$reg_fields    = array();

		// Decide if this user requires activation and if so what type
		if ( !empty( $form_settings['registration_activation'] ) && !empty( $form_settings['registration_activation_method'] ) ) {
			$activation = $form_settings['registration_activation_method'];
		} else {
			$activation = false;
		}

		// Required fields, defined by meta key
		foreach( $fields as $field ) {
			$nickname = wpforms_get_form_field_meta( $field['id'], 'nickname', $form_data );
			if ( !empty( $nickname ) && in_array( $nickname, $required ) ) {
				$reg_fields[$nickname] = $field['value'];
			}
		}

		// If a username was not set by field meta method (<=1.3.0) then check
		// for the mapped field.
		if ( !empty( $form_settings['registration_username'] ) && !empty( $fields[ $form_settings['registration_username'] ]['value'] ) ) {
			$reg_fields['username'] = $fields[ $form_settings['registration_username'] ]['value'];
		}

		// Check that we have all the required fields, if not abort
		if ( empty( $reg_fields['email'] ) ) {
			wpforms()->process->errors[ $form_data['id'] ]['header'] = __( 'Email address is required.', 'wpforms_user_registration' );
			return;
		}

		// If we _still_ don't have a username, then fallback to using email
		if ( empty( $reg_fields['username'] ) ) {
			$reg_fields['username'] = $reg_fields['email'];
		}

		// Check that username does not already exist
		if ( username_exists( $reg_fields['username'] ) ) {
			wpforms()->process->errors[ $form_data['id'] ]['header'] = apply_filters( 'wpforms_user_registration_username_exists', __( 'A user with that username already exists.', 'wpforms_user_registration' ) );
			return;
		}

		// Check that email does not already exist
		if ( email_exists( $reg_fields['email'] ) ) {
			wpforms()->process->errors[ $form_data['id'] ]['header'] = apply_filters( 'wpforms_user_registration_email_exists', __( 'A user with that email already exists.', 'wpforms_user_registration' ) );
			return;
		}

		// Optional fields
		foreach( $optional as $opt ) {
			$key = 'registration_' . $opt;
			$id  = !empty( $form_settings[$key] ) ? absint( $form_settings[$key] ) : '';
			if ( !empty( $fields[$id]['value'] ) ) {
				$reg_fields[$opt] = $fields[$id]['value'];
				if ( 'name' == $opt && !empty( $fields[$id]['first'] ) ) {
					$reg_fields['first_name'] = $fields[$id]['first'];
				}
				if ( 'name' == $opt && !empty( $fields[$id]['last'] ) ) {
					$reg_fields['last_name'] = $fields[$id]['last'];
				}
			}
		}

		// User role
		$reg_fields['role'] = !empty( $form_settings['registration_role'] ) ? $form_settings['registration_role'] : get_option( 'default_role' );
		if ( $activation ) {
			$approved_role = $reg_fields['role'];
			$reg_fields['role'] = false;
		}

		// User Password
		if ( empty( $reg_fields['password'] ) ) {
			$reg_fields['password'] = wp_generate_password( 18 );
		}

		// Required user information
		$userdata = array(
			'user_login'  => $reg_fields['username'],
			'user_email'  => $reg_fields['email'],
			'user_pass'   => $reg_fields['password'],
			'role'        => $reg_fields['role']
		);

		// Optional user information
		if ( !empty( $reg_fields['website'] ) ) {
			$userdata['user_url'] = $reg_fields['website'];
		}
		if ( !empty( $reg_fields['first_name'] ) ) {
			$userdata['first_name'] = $reg_fields['first_name'];
		}
		if ( !empty( $reg_fields['last_name'] ) ) {
			$userdata['last_name'] = $reg_fields['last_name'];
		}
		if ( !empty( $reg_fields['bio'] ) ) {
			$userdata['description'] = $reg_fields['bio'];
		}

		// Create user
		$user_id = wp_insert_user( $userdata ) ;

		// Something's wrong with user creatuin
		if ( is_wp_error( $user_id ) ) {
			wpforms()->process->errors[ $form_data['id'] ]['header'] = $user_id->get_error_message();
			return;
		}

		// Add custom user meta
		if ( !empty( $form_settings['registration_meta'] ) ) {
			foreach( $form_settings['registration_meta'] as $meta_key => $meta_field ) {
				if ( !empty( $fields[$meta_field]['value'] ) ) {
					add_user_meta( $user_id, $meta_key, $fields[$meta_field]['value'] );
				}
			}
		}

		// Set user to pending if user activation is enabled
		if ( $activation ) {
			add_user_meta( $user_id, 'wpforms-pending', true );
			if ( !$reg_fields['role'] ) {
				add_user_meta( $user_id, 'wpforms-role', $approved_role );
			}
			if ( !empty( $form_settings['registration_activation_confirmation'] ) ) {
				add_user_meta( $user_id, 'wpforms-confirmation', $form_settings['registration_activation_confirmation'] );
			}
		}

		// Send admin email notification is enabled OR if user activation is
		// enabled and requires the manual admin activation.
		if ( !empty( $form_settings['registration_email_admin'] ) || 'admin' == $activation ) {
			$this->new_user_notification( $user_id, $reg_fields['password'], 'admin', $activation );
		}

		// Send user email notification is enabled OR if user activation is
		// enabled and requires user activation.
		if ( !empty( $form_settings['registration_email_user'] ) || 'user' == $activation ) {
			$this->new_user_notification( $user_id, $reg_fields['password'], 'user', $activation );
		}

		do_action( 'wpforms_user_registered', $user_id, $fields, $form_data, $userdata );
	}

	/**
	 * Custom email we send to new users, with credentials.
	 *
	 * @since 1.0.0
	 * @param int $user_id
	 * @param string $plaintext_pass
	 */
	function new_user_notification( $user_id, $plaintext_pass, $type = 'admin', $activation ) {

		$user     = get_userdata( $user_id );
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$message  = '';
		$link     = '';

		if ( 'admin' == $type ) {

			// Admin notification
			$subject  = sprintf( __('[%s] New User Registration' ), $blogname );
			$message .= sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
			$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n";
			$message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n\r\n";

			// Additional details if user activation is enabled
			if ( $activation ) {

				$subject .= __( ' (Activation Required)', 'wpforms_user_registration' );
				$message .= __( 'Account activation is required before the user can login.', 'wpforms_user_registration' ) . "\r\n";

				if ( 'user' == $activation ) {
					$message .= __( 'The user has been emailed a link to activate their account.', 'wpforms_user_registration' ) . "\r\n\r\n";
				} else {
					$message .= __( 'You must manually activate their account.', 'wpforms_user_registration' ) . "\r\n\r\n";
				}
				$message .= sprintf( __( 'Manage user activations: %s' ), admin_url( 'users.php?role=wpforms_unapproved' ) ) . "\r\n";
			}

			$email = array(
				'address'    => apply_filters( 'wpforms_user_registration_admin_email', get_option('admin_email') ),
				'subject'    => $subject,
				'message'    => $message,
				'user'       => $user,
				'password'   => $plaintext_pass,
				'activation' => $activation,
			);
			$email = apply_filters( 'wpforms_user_registration_email_admin', $email );

			$emails = new WPForms_WP_Emails;
			$emails->send( $email['address'], $email['subject'], $email['message'] );

		} else {

			// User notification
			$subject  = sprintf( __( '[%s] Your username and password info' ), $blogname );
			$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n";
			$message .= sprintf( __( 'Password: %s' ), $plaintext_pass ) . "\r\n\r\n";
			$message .= wp_login_url() . "\r\n\r\n";

			// Additional details if user activation is enabled
			if ( $activation ) {

				$subject .= __( ' (Activation Required)', 'wpforms_user_registration' );

				if ( 'user' == $activation ) {

					// Create activation link
					$args     = 'user_id=' . $user_id . '&user_email=' . $user->user_email . '&hash=' . wp_hash( $user_id . ',' . $user->user_email );
					$link     = esc_url( add_query_arg( array( 'wpforms_activate' => base64_encode( $args ) ), home_url() ) );
					$message .= __( 'IMPORTANT: You must activate you account before you can login. Please visit the link below.', 'wpforms_user_registration' ) . "\r\n";
					$message .= $link;
				} else {
					$message .= __( 'Site administrator must activate your account before you can login.', 'wpforms_user_registration' ) . "\r\n";
				}
			}

			$email = array(
				'address'    => $user->user_email,
				'subject'    => $subject,
				'message'    => $message,
				'user'       => $user,
				'password'   => $plaintext_pass,
				'activation' => $activation,
				'link'       => $link,
			);
			$email = apply_filters( 'wpforms_user_registration_email_user', $email );

			$emails = new WPForms_WP_Emails;
			$emails->send( $email['address'], $email['subject'], $email['message'] );
		}
	}

	/**
	 * User Registration settings register section
	 *
	 * @since 1.0.0
	 * @param array $sections
	 * @param array $form_data
	 */
	public function settings_register( $sections, $form_data ) {

		// Only enable for our specific template
		if ( !empty( $form_data['meta']['template'] ) && 'user_registration' == $form_data['meta']['template'] ) {
			$sections['user_registration'] = __( 'User Registration', 'wpforms_user_registration' );
		}

		return $sections;
	}

	/**
	 * User Registration settings content
	 *
	 * @since 1.0.0
	 * @param object $instance
	 */
	public function settings_content( $instance ) {

		// Only enable for our specific template
		if ( empty( $instance->form_data['meta']['template'] ) || 'user_registration' != $instance->form_data['meta']['template'] ) {
			return;
		}

		echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-user_registration">';

			echo '<div class="wpforms-panel-content-section-title">';
				_e( 'User Registration', 'wpforms_user_registration' );
			echo '</div>';

			// Username - only display for v1.3.1+ forms
			$username = wpforms_get_form_fields_by_meta( 'nickname', 'username', $instance->form_data);
			if ( ! $username ) :
			wpforms_panel_field(
				'select',
				'settings',
				'registration_username',
				$instance->form_data,
				__( 'User Name', 'wpforms_user_registration' ),
				array(
					'field_map'   => array( 'name', 'text' ),
					'placeholder' => __( '-- Select Field --', 'wpforms_user_registration' ),
					'tooltip'     => __( 'If a username is not set or provided, it will become the user\'s email address', 'wpforms_user_registration' ),
				)
			);
			endif;

			// Name
			wpforms_panel_field(
				'select',
				'settings',
				'registration_name',
				$instance->form_data,
				__( 'Name', 'wpforms_user_registration' ),
				array(
					'field_map'   => array( 'name' ),
					'placeholder' => __( '-- Select Field --', 'wpforms_user_registration' ),
				)
			);

			// Password
			wpforms_panel_field(
				'select',
				'settings',
				'registration_password',
				$instance->form_data,
				__( 'Password', 'wpforms_user_registration' ),
				array(
					'field_map'   => array( 'password' ),
					'placeholder' => __( 'Auto generate', 'wpforms_user_registration' ),
				)
			);

			// Website
			wpforms_panel_field(
				'select',
				'settings',
				'registration_website',
				$instance->form_data,
				__( 'Website', 'wpforms_user_registration' ),
				array(
					'field_map'   => array( 'text', 'url' ),
					'placeholder' => __( '-- Select Field --', 'wpforms_user_registration' ),
				)
			);

			// Bio
			wpforms_panel_field(
				'select',
				'settings',
				'registration_bio',
				$instance->form_data,
				__( 'Biographical Info', 'wpforms_user_registration' ),
				array(
					'field_map'   => array( 'textarea' ),
					'placeholder' => __( '-- Select Field --', 'wpforms_user_registration' ),
				)
			);

			// Role
			$editable_roles = array_reverse( get_editable_roles() );
			$roles_options = array();
			foreach ( $editable_roles as $role => $details ) {
				$roles_options[$role] = translate_user_role( $details['name'] );
			}
			wpforms_panel_field(
				'select',
				'settings',
				'registration_role',
				$instance->form_data,
				__( 'User Role', 'wpforms_user_registration' ),
				array(
					'default' => get_option( 'default_role' ),
					'options' => $roles_options,
				)
			);

			// User Email
			wpforms_panel_field(
				'checkbox',
				'settings',
				'registration_email_user',
				$instance->form_data,
				__( 'Send email to the user containing account information', 'wpforms_user_registration' )
			);

			// Admin Email
			wpforms_panel_field(
				'checkbox',
				'settings',
				'registration_email_admin',
				$instance->form_data,
				__( 'Send email to the admin', 'wpforms_user_registration' )
			);

			// User Activation
			wpforms_panel_field(
				'checkbox',
				'settings',
				'registration_activation',
				$instance->form_data,
				__( 'Enable user activation', 'wpforms_user_registration' )
			);

			// User Activation Method
			wpforms_panel_field(
				'select',
				'settings',
				'registration_activation_method',
				$instance->form_data,
				__( 'User Activation Method', 'wpforms_user_registration' ),
				array(
					'default' => 'user',
					'options' => array(
						'user'  => __( 'User Email', 'wpforms_user_registration' ),
						'admin' => __( 'Manual Approval', 'wpforms_user_registration' ),
					),
					'tooltip' => __( 'User Email method sends an email to the user with a link to activate their account. Manual Approval requires site admin to approve account.', 'wpforms_user_registration' )
				)
			);

			$p = array();
			$pages = get_pages();
			foreach( $pages as $page ) {
				$depth = sizeof( $page->ancestors );
				$p[$page->ID] = str_repeat( '-', $depth ) . ' ' . $page->post_title;
			}
			wpforms_panel_field(
				'select',
				'settings',
				'registration_activation_confirmation',
				$instance->form_data,
				__( 'User Activation Confirmation Page', 'wpforms_user_registration' ),
				array(
					'placeholder' => __( 'Home page', 'wpforms_user_registration', 'wpforms_user_registration' ),
					'options'     => $p,
					'tooltip'     => __( 'Select the page to show the user after they activate their account.', 'wpforms_user_registration' )
				)
			);
			?>

			<div class="wpforms-field-map-table">
				<h3><?php _e( 'Custom User Meta', 'wpforms_user_registration' ); ?></h3>
				<table>
					<tbody>
						<?php
						$fields = wpforms_get_form_fields( $instance->form_data );
						$meta   = !empty( $instance->form_data['settings']['registration_meta'] ) ? $instance->form_data['settings']['registration_meta'] : array( false );
						foreach( $meta as $meta_key => $meta_field ) :
							$key  = $meta_field !== false ? preg_replace( '/[^a-zA-Z0-9_\-]/', '', $meta_key ) : '';
							$name = !empty($key) ? 'settings[registration_meta][' . $key . ']' : '';
							?>
							<tr>
								<td class="key">
									<input type="text" value="<?php echo $key; ?>" placeholder="<?php _e( 'Enter meta key...', 'wpforms_user_registration' ); ?>" class="key-source">
								</td>
								<td class="field">
									<select data-name="settings[registration_meta][{source}]" name="<?php echo $name; ?>" class="key-destination wpforms-field-map-select" data-field-map-allowed="all-fields">
										<option value=""><?php _e( '-- Select Field --', 'wpforms_user_registration' ); ?></option>
										<?php
										if ( !empty( $fields ) ) {
											foreach( $fields as $id => $field ) {
												printf( '<option value="%s" %s>%s</option>', esc_attr( $field['id'] ), selected( $meta_field, $id, false ), $field['label'] );
											}
										}
										?>
									<select>
								</td>
								<td class="actions">
									<a class="add" href="#"><i class="fa fa-plus-circle"></i></a>
									<a class="remove" href="#"><i class="fa fa-minus-circle"></i></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

		<?php
		echo '</div>';
	}

	/**
	 * Don't store the real password value.
	 *
	 * @since 1.0.0
	 */
	public function remove_password_value( $fields, $entry, $form_data ) {

		if ( empty( $form_data['meta']['template'] ) || 'user_registration' != $form_data['meta']['template'] )
			return $fields;

		foreach( $fields as $id => $field ) {
			if ( 'password' == $field['type'] ) {
				$fields[$id]['value'] = '**********';
			}
		}
		return $fields;
	}
}
new WPForms_User_Registration;