<?php
/**
 * User registration form template.
 *
 * @package    WPFormsUserRegistration
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
*/
class WPForms_Template_User_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name        = __( 'User Registration Form', 'wpforms_user_registration' );
		$this->slug        = 'user_registration';
		$this->description = __( 'Create customized WordPress user registration form and add them anywhere on your website.', 'wpforms_user_registration' );
		$this->includes    = '';
		$this->icon        = '';
		$this->core        = true;
		$this->modal       = array(
			'title'   => __( 'Don&#39;t Forget', 'wpforms_user_registration' ),
			'message' => __( 'Additional user registration options are available in the settings panel.', 'wpforms_user_registration' ),
		);
		$this->data        = array(
			'field_id' => '6',
			'fields'   => array(
				'1' => array(
					'id'           => '1',
					'type'         => 'name',
					'label'        => __( 'Name', 'wpforms_user_registration' ),
					'format'       => 'first-last',
					'required'     => '1',
				),
				'2' => array(
					'id'           => '2',
					'type'         => 'text',
					'label'        => __( 'Username', 'wpforms_user_registration' ),
					'required'     => '1',
					'size'         => 'medium',
				),
				'3' => array(
					'id'           => '3',
					'type'         => 'email',
					'label'        => __( 'Email', 'wpforms_user_registration' ),
					'required'     => '1',
					'size'         => 'medium',
					'meta'  	   => array(
						'nickname' => 'email',
						'delete'   => false,
					)
				),
				'4' => array(
					'id'           => '4',
					'type'         => 'password',
					'label'        => __( 'Password', 'wpforms_user_registration' ),
					'required'     => '1',
					'size'         => 'medium'
				),
				'5' => array(
					'id'           => '5',
					'type'         => 'textarea',
					'label'        => __( 'Short Bio', 'wpforms_user_registration' ),
					'description'  => __( 'Share a little information about yourself.', 'wpforms_user_registration' ),
					'size'         => 'small',
				),
			),
			'settings' => array(
				'honeypot'                    => '1',
				'confirmation_message_scroll' => '1',
				'registration_username'       => '2',
				'registration_name'           => '1',
				'registration_password'       => '4',
				'registration_bio'            => '5',
				'registration_email_user'     => '1',
				'registration_email_admin'    => '1',
			),
			'meta'     => array(
				'template' => $this->slug,
			),
		);
	}

	/**
	 * Conditional to determine if the template informational modal screens
	 * should display.
	 *
	 * @since 1.0.0
	 * @param array $form_data
	 * @return boolean
	 */
	public function template_modal_conditional( $form_data ) {

		return true;
	}
}
new WPForms_Template_User_Registration;
