<?php
/**
 * User activation.
 *
 * This has been adapted from the WP Approve Users plugin, by Konstantin Obenland
 *
 * @link       https://github.com/obenland/wp-approve-user
 * @package    WPFormsUserRegistration
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
*/
class WPForms_User_Activation {

	protected $unapproved_users = array();

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

		add_action( 'init', array( $this, 'listen' ) );

		if ( is_admin() ) {
			add_action( 'pre_user_query', array( $this, 'pre_user_query'  ) );
			add_action( 'views_users',    array( $this, 'users_list_view' ) );
		}
		add_action( 'admin_print_scripts-users.php',       array( $this, 'users_list_enqueue' ) );
		add_action( 'admin_print_scripts-site-users.php',  array( $this, 'users_list_enqueue' ) );
		add_action( 'user_row_actions',                    array( $this, 'users_list_actions' ), 10, 2 );
		add_action( 'ms_user_row_actions',                 array( $this, 'users_list_actions' ), 10, 2 );
		add_action( 'admin_action_wpforms_approve',        array( $this, 'approve'            ) );
		add_action( 'admin_action_wpforms_bulk_approve',   array( $this, 'bulk_approve'       ) );
		add_action( 'admin_action_wpforms_unapprove',      array( $this, 'unapprove'          ) );
		add_action( 'admin_action_wpforms_bulk_unapprove', array( $this, 'bulk_unapprove'     ) );
		add_action( 'admin_action_wpforms_update',         array( $this, 'update_message'     ) );
		add_action( 'wp_authenticate_user',                array( $this, 'user_authenticate'  ) );
	}

	/**
	 * Listen for activation key.
	 * 
	 * @since 1.0.0
	 */
	public function listen() {

		if ( empty( $_GET['wpforms_activate'] ) )
			return;

		$query_args = base64_decode( $_GET['wpforms_activate'] );
		parse_str( $query_args );

		if ( empty( $hash ) || empty( $user_id ) || empty( $user_email ) )
			return;

		// Verify hash matches
		if ( $hash != wp_hash( $user_id . ',' . $user_email ) )
			return;

		delete_user_meta( $user_id, 'wpforms-pending' );

		// Check if we need to assign new role
		$role = get_user_meta( $user_id, 'wpforms-role', true );
		if ( $role ) {
			wp_update_user( array( 'ID' => $user_id, 'role' => $role ) );
			delete_user_meta( $user_id, 'wpforms-role' );
		}

		// Redirect user to confirmation page
		$confirmation = get_permalink( get_user_meta( $user_id, 'wpforms-confirmation', true ) );
		$redirect     = $confirmation ? $confirmation : home_url();

		delete_user_meta( $user_id, 'wpforms-confirmation' );

		do_action( 'wpforms_user_approve', $user_id );

		wp_redirect( $redirect );
		exit;
	}

	/**
	 * Resets the user query to handle request for unapproved users only.
	 *
	 * @since 1.0.0
	 * @param  WP_User_Query $query
	 * @return void
	 */
	public function pre_user_query( $query ) {

		if ( 'wpforms_unapproved' == $query->query_vars['role'] ) {

			unset( $query->query_vars['meta_query'][0] );

			$query->query_vars['role']       = '';
			$query->query_vars['meta_key']   = 'wpforms-pending';
			$query->query_vars['meta_value'] = true;
			$query->prepare_query();
		}
	}

	/**
	 * Enqueues the script for users table.
	 *
	 * @since 1.0.0
	 */
	public function users_list_enqueue() {

		wp_enqueue_script(
			'wpforms-admin-users',
			plugin_dir_url( __FILE__ ) . 'assets/js/admin-users.js',
			array( 'jquery' ),
			WPFORMS_USER_REGISTRATION_VERSION,
			true
		);

		wp_localize_script(
			'wpforms-admin-users',
			'wpforms_admin_users',
			array(
				'approve'   => __( 'Approve',   'wpforms_user_registration' ),
				'unapprove' => __( 'Unapprove', 'wpforms_user_registration' ),
			)
		);
	}

	/**
	 * User table view.
	 * 
	 * @since  1.0.0
	 * @param  array $views
	 * @return array
	 */
	public function users_list_view( $views ) {

		$unapproved_users = get_users( array(
			'meta_key'   => 'wpforms-pending',
			'meta_value' => true,
		) );

		if ( $unapproved_users ) {

			$site_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
			$url     = 'site-users-network' == get_current_screen()->id ? add_query_arg( array( 'id' => $site_id ), 'site-users.php' ) : 'users.php';

			$views['unapproved'] = sprintf( '<a href="%1$s" class="%2$s">%3$s <span class="count">(%4$s)</span></a>',
					esc_url( add_query_arg( array( 'role' => 'wpforms_unapproved' ), $url ) ),
					'wpforms_unapproved' == $this->get_role() ? 'current' : '',
					__( 'Unapproved', 'wpforms_user_registration' ),
					count( $unapproved_users )
			);
		}

		return $views;
	}

	/**
	 * User table row action.
	 *
	 * @since 1.0.0
	 * @param array $actions
	 * @param WP_User $user_object
	 * @return array
	 */
	public function users_list_actions( $actions, $user_object ) {

		if ( ( get_current_user_id() != $user_object->ID ) && current_user_can( apply_filters( 'wpforms_manage_cap', 'manage_options' ) ) ) {

			$site_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
			$url     = 'site-users-network' == get_current_screen()->id ? add_query_arg( array( 'id' => $site_id ), 'site-users.php' ) : 'users.php';

			if ( get_user_meta( $user_object->ID, 'wpforms-pending', true ) ) {
			
				$url = wp_nonce_url( add_query_arg( array(
					'action' => 'wpforms_approve',
					'user'   => $user_object->ID,
					'role'   => $this->get_role(),
				), $url ), 'wpforms-approve-users' );

				$actions['wpforms-approve'] = sprintf( '<a class="submitapprove" href="%1$s">%2$s</a>', esc_url( $url ), __( 'Approve', 'wpforms_user_registration' ) );
			
			} else {

				$url = wp_nonce_url( add_query_arg( array(
					'action' => 'wpforms_unapprove',
					'user'   => $user_object->ID,
					'role'   => $this->get_role(),
				), $url ), 'wpforms-unapprove-users' );

				$actions['wpforms-unapprove'] = sprintf( '<a class="submitunapprove" href="%1$s">%2$s</a>', esc_url( $url ), __( 'Unapprove', 'wpforms_user_registration' ) );
			}
		}

		return $actions;
	}

	/**
	 * Updates user_meta to approve user.
	 *
	 * @since 1.0.0
	 */
	public function approve() {

		check_admin_referer( 'wpforms-approve-users' );
		$this->do_approve();
	}

	/**
	 * Bulkupdates user_meta to approve user.
	 *
	 * @since 1.0.0
	 */
	public function bulk_approve() {

		check_admin_referer( 'bulk-users' );
		$this->set_up_role_context();
		$this->do_approve();
	}

	/**
	 * Updates user_meta to unapprove user.
	 *
	 * @since 1.0.0
	 */
	public function unapprove() {

		check_admin_referer( 'wpforms-unapprove-users' );
		$this->do_unapprove();
	}

	/**
	 * Bulkupdates user_meta to unapprove user.
	 *
	 * @since 1.0.0
	 */
	public function bulk_unapprove() {

		check_admin_referer( 'bulk-users' );
		$this->set_up_role_context();
		$this->do_unapprove();
	}

	/**
	 * Adds the update message to the admin notices queue.
	 *
	 * @since 1.0.0
	 */
	public function update_message() {

		$count = absint( $_REQUEST['count'] );

		if ( !isset( $_REQUEST['update'] ) )
			return;

		switch( $_REQUEST['update'] ) {
			case 'wpforms-approved':
				$message = _n( 'User approved.', '%d users approved.', $count, 'wpforms_user_registration' );
				break;

			case 'wpforms-unapproved':
				$message = _n( 'User unapproved.', '%d users unapproved.', $count, 'wpforms_user_registration' );
				break;
			default:
				$message = apply_filters( 'wpforms_update_message_handler', '', $_REQUEST['update'] );
		}

		if ( isset( $message ) ) {
			add_settings_error(
				'wpforms_user_registration',
				esc_attr( $_REQUEST['update'] ),
				sprintf( $message, $count ),
				'updated'
			);

			add_action( 'all_admin_notices', function(){
				settings_errors( 'wpforms_user_registration' );
			} );
		}

		// Prevent other admin action handlers from trying to handle our action.
		$_REQUEST['action'] = -1;
	}

	/**
	 * Checks whether the user is approved. Throws error if not.
	 *
	 * @since 1.0.0
	 * @param WP_User|WP_Error $userdata
	 * @return WP_User|WP_Error
	 */
	public function user_authenticate( $userdata ) {

		if ( ! is_wp_error( $userdata ) AND get_user_meta( $userdata->ID, 'wpforms-pending', true ) AND $userdata->user_email != get_bloginfo( 'admin_email' ) ) {
			$userdata = new WP_Error(
				'wpforms_confirmation_error',
				__( '<strong>ERROR:</strong> Your account must be activated before you can login.', 'wpforms_user_registration' )
			);
		}

		return $userdata;
	}

	//************************************************************************//
	//
	//	Protected Methods
	//
	//************************************************************************//

	/**
	 * Updates user_meta to approve user.
	 *
	 * @since 1.0.0
	 */
	protected function do_approve() {

		list( $userids, $url ) = $this->check_user();

		foreach ( (array) $userids as $id ) {
			$id = (int) $id;

			if ( ! current_user_can( 'edit_user', $id ) ) {
				wp_die( __( 'You can&#8217;t edit that user.' ), '', array(
					'back_link' => true,
				) );
			}

			delete_user_meta( $id, 'wpforms-pending' );
			delete_user_meta( $id, 'wpforms-confirmation' );
		
			// Check if we need to assign new role
			$role = get_user_meta( $id, 'wpforms-role', true );
			if ( $role ) {
				wp_update_user( array( 'ID' => $id, 'role' => $role ) );
				delete_user_meta( $id, 'wpforms-role' );
			}

			do_action( 'wpforms_user_approve', $id );
		}

		wp_redirect( add_query_arg( array(
			'action' => 'wpforms_update',
			'update' => 'wpforms-approved',
			'count'  => count( $userids ),
			'role'   => $this->get_role(),
		), $url ) );
		exit();
	}

	/**
	 * Updates user_meta to unapprove user.
	 *
	 * @since 1.0.0
	 */
	protected function do_unapprove() {

		list( $userids, $url ) = $this->check_user();

		foreach ( (array) $userids as $id ) {
			$id = (int) $id;

			if ( ! current_user_can( 'edit_user', $id ) ) {
				wp_die( __( 'You can&#8217;t edit that user.' ), '', array(
					'back_link' => true,
				) );
			}

			update_user_meta( $id, 'wpforms-pending', true );
			do_action( 'wpforms_user_unapprove', $id );
		}

		wp_redirect( add_query_arg( array(
			'action' => 'wpforms_update',
			'update' => 'wpforms-unapproved',
			'count'  => count( $userids ),
			'role'   => $this->get_role(),
		), $url ) );
		exit();
	}

	/**
	 * Checks permissions and assembles User IDs.
	 *
	 * @since 1.0.0
	 * @return array User IDs and URL
	 */
	protected function check_user() {

		$site_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
		$url     = 'site-users-network' == get_current_screen()->id ? add_query_arg( array( 'id' => $site_id ), 'site-users.php' ) : 'users.php';

		if ( empty( $_REQUEST['users'] ) AND empty( $_REQUEST['user'] ) ) {
			wp_redirect( $url );
			exit();
		}

		if ( ! current_user_can( 'promote_users' ) ) {
			wp_die( __( 'You can&#8217;t unapprove users.', 'wpforms_user_registration' ), '', array(
				'back_link' => true,
			) );
		}

		$userids = empty( $_REQUEST['users'] ) ? array( intval( $_REQUEST['user'] ) ) : array_map( 'intval', (array) $_REQUEST['users'] );
		$userids = array_diff( $userids, array( get_user_by( 'email', get_bloginfo( 'admin_email' ) )->ID ) );

		return array( $userids, $url );
	}

	/**
	 * Sets the role context on bulk actions.
	 *
	 * On bulk actions the role parameter is not passed, since we're using a form
	 * to submit information. The information is only available through the
	 * `_wp_http_referer` parameter, so we get it from there and make it available
	 * for the request.
	 *
	 * @since 1.0.0
	 */
	protected function set_up_role_context() {

		if ( empty( $_REQUEST['role'] ) && ! empty( $_REQUEST['_wp_http_referer'] ) ) {
			$referrer = parse_url( $_REQUEST['_wp_http_referer'] );

			if ( ! empty( $referrer['query'] ) ) {
				$args = wp_parse_args( $referrer['query'] );

				if ( ! empty( $args['role'] ) ) {
					$_REQUEST['role'] = $args['role'];
				}
			}
		}
	}

	/**
	 * Returns the current role.
	 *
	 * If the user list is in the context of a specific role, this function makes
	 * sure that the requested role is valid. By returning `false` otherwise, we
	 * make sure that parameter gets removed from the activation link.
	 *
	 * @since 1.0.0
	 * @return string|bool The role key if set, false otherwise.
	 */
	protected function get_role() {

		$roles   = array_keys( get_editable_roles() );
		$roles[] = 'wpforms_unapproved';
		$role    = false;

		if ( isset( $_REQUEST['role'] ) && in_array( $_REQUEST['role'], $roles ) ) {
			$role = $_REQUEST['role'];
		}

		return $role;
	}

}
new WPForms_User_Activation;