<?php
/**
 * User login form template.
 *
 * @package    WPFormsUserRegistration
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
*/
class WPForms_Template_User_Login extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->name        = __( 'User Login Form', 'wpforms_user_registration' );
		$this->slug        = 'user_login';
		$this->description = __( 'Allow your users to easily login to your site with their username and password.', 'wpforms_user_registration' );
		$this->includes    = '';
		$this->icon        = '';
		$this->modal       = '';
		$this->core        = true;
		$this->data        = array(
			'field_id' => '2',
			'fields'   => array(
				'0' => array(
					'id'           => '0',
					'type'         => 'text',
					'label'        => __( 'Username or Email', 'wpforms_user_registration' ),
					'required'     => '1',
					'size'         => 'medium',
					'meta'  	   => array(
						'nickname' => 'login',
						'delete'   => false,
					)
				),
				'1' => array(
					'id'           => '1',
					'type'         => 'password',
					'label'        => __( 'Password', 'wpforms_user_registration' ),
					'required'     => '1',
					'size'         => 'medium',
					'meta'  	   => array(
						'nickname' => 'password',
						'delete'   => false,
					)
				),
			),
			'settings' => array(
				'confirmation_type' 	=> 'redirect',
				'confirmation_redirect' => home_url(),
				'notification_enable'   => '0',
				'disable_entries'       => '1',
				'user_login_hide'       => '1',
			),
			'meta'     => array(
				'template' => $this->slug,
			),
		);
	}
}
new WPForms_Template_User_Login;
