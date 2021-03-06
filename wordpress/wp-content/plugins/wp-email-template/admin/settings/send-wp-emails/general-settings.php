<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WP Email Teplate Send WP Emails General Settings

TABLE OF CONTENTS

- var parent_tab
- var subtab_data
- var option_name
- var form_key
- var position
- var form_fields
- var form_messages

- __construct()
- subtab_init()
- set_default_settings()
- get_settings()
- subtab_data()
- add_subtab()
- settings_form()
- init_form_fields()

-----------------------------------------------------------------------------------*/

class WP_ET_Send_WP_Emails_General_Settings extends WP_Email_Tempate_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'generate';
	
	/**
	 * @var array
	 */
	private $subtab_data;
	
	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = 'wp_et_send_wp_emails_general';
	
	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'wp_et_send_wp_emails_general';
	
	/**
	 * @var string
	 * You can change the order show of this sub tab in list sub tabs
	 */
	private $position = 1;
	
	/**
	 * @var array
	 */
	public $form_fields = array();
	
	/**
	 * @var array
	 */
	public $form_messages = array();
	
	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		$this->init_form_fields();
		//$this->subtab_init();
		
		$this->form_messages = array(
				'success_message'	=> __( 'Sending Settings successfully saved.', 'wp_email_template' ),
				'error_message'		=> __( 'Error: Sending Settings can not save.', 'wp_email_template' ),
				'reset_message'		=> __( 'Sending Settings successfully reseted.', 'wp_email_template' ),
			);
		
		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'include_script' ) );
			
		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );
		
		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_init' , array( $this, 'after_save_settings' ) );
		
		add_action( $this->plugin_name . '_get_all_settings' , array( $this, 'get_settings' ) );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* subtab_init() */
	/* Sub Tab Init */
	/*-----------------------------------------------------------------------------------*/
	public function subtab_init() {
		
		add_filter( $this->plugin_name . '-' . $this->parent_tab . '_settings_subtabs_array', array( $this, 'add_subtab' ), $this->position );
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* set_default_settings()
	/* Set default settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function set_default_settings() {
		global $wp_email_template_admin_interface;
		
		$wp_email_template_admin_interface->reset_settings( $this->form_fields, $this->option_name, false );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* after_save_settings()
	/* Process when clean on deletion option is un selected */
	/*-----------------------------------------------------------------------------------*/
	public function after_save_settings() {
		global $wp_email_template_admin_interface;
		
		if ( isset( $_POST['bt_save_settings'] ) && ! isset( $_POST[$this->option_name]['email_delivery_provider'] ) ) {
			$settings_array = get_option( $this->option_name, array() );
			$settings_array['email_delivery_provider'] = 'smtp';
			update_option( $this->option_name, $settings_array );
		}
		if ( isset( $_POST['bt_save_settings'] ) && trim( get_option( 'wp_email_template_test_send_email', '' ) ) != '' ) {
			$wp_email_template_test_send_email = get_option( 'wp_email_template_test_send_email', '' );
			delete_option( 'wp_email_template_test_send_email' );
			
			// Send a test email here
			global $wp_et_send_wp_emails;
			$sent_result = $wp_et_send_wp_emails->send_a_test_email( $wp_email_template_test_send_email  );
			if ( $sent_result ) {
				echo $wp_email_template_admin_interface->get_success_message( __( 'Test Email successfully sent', 'wp_email_template' ) );		
			} else {
				echo $wp_email_template_admin_interface->get_error_message( __( 'Error: Test Email can not send', 'wp_email_template' ) . '<br /><a href="#TB_inline?width=600&height=550&inlineId=test_error_container" class="thickbox" >' . __( 'View Detailed Debug', 'wp_email_template' ) . '</a>' );
			}
		}
		
		
		// Check the ports are openned by server for some smtp delivery
		$settings_array = get_option( 'wp_et_send_wp_emails_general', array() );
		if ( $settings_array['email_sending_option'] == 'provider' ) {
			$errno = '';
			$errstr = '';
			$timeout = 3;
			switch( $settings_array['email_delivery_provider'] ) :
				case 'mandrill':
					global $wp_et_send_wp_emails;
					$wp_et_mandrill_provider_configuration = get_option( 'wp_et_mandrill_provider_configuration', array() );
					if ( $wp_et_mandrill_provider_configuration['mandrill_connect_type'] == 'smtp' ) {
						$check_port =  @fsockopen( $wp_et_mandrill_provider_configuration['smtp_host'] , $wp_et_mandrill_provider_configuration['smtp_port'], $errno, $errstr, $timeout);
						if ( ! $check_port ) echo $wp_email_template_admin_interface->get_error_message( __( 'Error: Port', 'wp_email_template' ) . ' '.$wp_et_mandrill_provider_configuration['smtp_port'] . ' ' . __( 'is blocked on your server. First check the Port Number is Correct. If it is contact your Hosting support and ask them to <br />1. Open the Port <br />2. Ensure that it can be listened to from the outside.', 'wp_email_template' ) );

						// check api key
						$api_key_valid = $wp_et_send_wp_emails->check_mandrill_api_key( trim( $wp_et_mandrill_provider_configuration['smtp_password'] ) );
						if ( $api_key_valid ) {
							update_option( 'wp_et_mandrill_api_key_valid', 1 );
						} else {
							delete_option( 'wp_et_mandrill_api_key_valid');
							echo $wp_email_template_admin_interface->get_error_message(  __( "Your API key is invalid", 'wp_email_template' ) );
						}
					} else {
						// check api key
						$api_key_valid = $wp_et_send_wp_emails->check_mandrill_api_key( trim( $wp_et_mandrill_provider_configuration['api_key'] ) );
						if ( $api_key_valid ) {
							update_option( 'wp_et_mandrill_api_key_valid', 1 );
						} else {
							delete_option( 'wp_et_mandrill_api_key_valid');
							echo $wp_email_template_admin_interface->get_error_message(  __( "Your API key is invalid", 'wp_email_template' ) );
						}
					}
				break;
				case 'gmail-smtp':
					$check_port =  @fsockopen( 'smtp.gmail.com' , '465', $errno, $errstr, $timeout);
					if ( ! $check_port ) echo $wp_email_template_admin_interface->get_error_message( __( 'Error: Port', 'wp_email_template' ) . ' 465 ' . __( 'is blocked on your server. First check the Port Number is Correct. If it is contact your Hosting support and ask them to <br />1. Open the Port <br />2. Ensure that it can be listened to from the outside.', 'wp_email_template' ) );
				break;
				default:
					$wp_et_smtp_provider_configuration = get_option( 'wp_et_smtp_provider_configuration', array() );
					$check_port =  @fsockopen( $wp_et_smtp_provider_configuration['smtp_host'] , $wp_et_smtp_provider_configuration['smtp_port'], $errno, $errstr, $timeout);
					if ( ! $check_port ) echo $wp_email_template_admin_interface->get_error_message( __( 'Error: Port', 'wp_email_template' ) . ' '.$wp_et_smtp_provider_configuration['smtp_port'] . ' ' . __( 'is blocked on your server. First check the Port Number is Correct. If it is contact your Hosting support and ask them to <br />1. Open the Port <br />2. Ensure that it can be listened to from the outside.', 'wp_email_template' ) );
				break;
			endswitch;
		}
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* get_settings()
	/* Get settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function get_settings() {
		global $wp_email_template_admin_interface;
		
		$wp_email_template_admin_interface->get_settings( $this->form_fields, $this->option_name );
	}
	
	/**
	 * subtab_data()
	 * Get SubTab Data
	 * =============================================
	 * array ( 
	 *		'name'				=> 'my_subtab_name'				: (required) Enter your subtab name that you want to set for this subtab
	 *		'label'				=> 'My SubTab Name'				: (required) Enter the subtab label
	 * 		'callback_function'	=> 'my_callback_function'		: (required) The callback function is called to show content of this subtab
	 * )
	 *
	 */
	public function subtab_data() {
		
		$subtab_data = array( 
			'name'				=> 'general',
			'label'				=> __( 'Sending Settings', 'wp_email_template' ),
			'callback_function'	=> 'wp_et_send_wp_emails_general_settings_form',
		);
		
		if ( $this->subtab_data ) return $this->subtab_data;
		return $this->subtab_data = $subtab_data;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* add_subtab() */
	/* Add Subtab to Admin Init
	/*-----------------------------------------------------------------------------------*/
	public function add_subtab( $subtabs_array ) {
	
		if ( ! is_array( $subtabs_array ) ) $subtabs_array = array();
		$subtabs_array[] = $this->subtab_data();
		
		return $subtabs_array;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* settings_form() */
	/* Call the form from Admin Interface
	/*-----------------------------------------------------------------------------------*/
	public function settings_form() {
		global $wp_email_template_admin_interface;
		
		$output = '';
		$output .= $wp_email_template_admin_interface->admin_forms( $this->form_fields, $this->form_key, $this->option_name, $this->form_messages );

		return $output;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* init_form_fields() */
	/* Init all fields of this form */
	/*-----------------------------------------------------------------------------------*/
	public function init_form_fields() {
		
  		// Define settings			
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(
		
			array(
            	'name' 		=> __( 'Configure WordPress Email Sending', 'wp_email_template' ),
				'desc'		=> __( 'Email Spammers have made successful email delivery a very complicated and specialized function. WordPress by default will use your web hosts local mail server to send all WordPress and plugin generated emails. Generally emails sent from a web host local mail server have poor delivery rates because they have no reputation. Use the settings below to improve your delivery rate by configuring a custom sending provider.', 'wp_email_template' ),
                'type' 		=> 'heading',
                'id'		=> 'configure_sending_provider_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( 'Via Web Host', 'wp_email_template' ),
				'id' 		=> 'email_sending_option',
				'type' 		=> 'onoff_radio',
				'default' 	=> 'local',
				'onoff_options' => array(
					array(
						'val' 				=> 'local',
						'text' 				=> __( "WordPress Default email send option uses your web host's local mail server to send emails.", 'wp_email_template' ) ,
						'checked_label'		=> __( 'ON', 'wp_email_template') ,
						'unchecked_label' 	=> __( 'OFF', 'wp_email_template') ,
					),
					
				),			
			),
			
			array(
				'class'		=> 'select_email_delivery_local_container',
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'GoDaddy Hosting', 'wp_email_template' ),
				'desc'		=> __( 'Turn ON if Hosting with GoDaddy and it auto sets the smtp host to <strong>relay-hosting.secureserver.net</strong>', 'wp_email_template' ),
				'id' 		=> 'is_godaddy_hosting',
				'type' 		=> 'onoff_checkbox',
				'default' 	=> 'no',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'wp_email_template' ),
				'unchecked_label' 	=> __( 'OFF', 'wp_email_template' ),
			),
			
			array(
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Via Provider', 'wp_email_template' ),
				'class'		=> 'email_sending_option_provider',
				'id' 		=> 'email_sending_option',
				'type' 		=> 'onoff_radio',
				'default' 	=> 'local',
				'onoff_options' => array(
					array(
						'val' 				=> 'provider',
						'text' 				=> '',
						'checked_label'		=> __( 'ON', 'wp_email_template') ,
						'unchecked_label' 	=> __( 'OFF', 'wp_email_template') ,
					),

				),
			),

			// SMTP Configuration
			array(
            	'name' 		=> __( 'SMTP Activation', 'wp_email_template' ),
				'class'		=> 'select_email_delivery_provider_container',
                'type' 		=> 'heading',
                'id'		=> 'smtp_activation_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( 'SMTP Provider', 'wp_email_template' ),
				'id' 		=> 'email_delivery_provider',
				'class'		=> 'email_delivery_provider',
				'type' 		=> 'onoff_radio',
				'default' 	=> 'smtp',
				'onoff_options' => array(
					array(
						'val' 				=> 'smtp',
						'text' 				=> '',
						'checked_label'		=> __( 'ON', 'wp_email_template') ,
						'unchecked_label' 	=> __( 'OFF', 'wp_email_template') ,
					),
				),
			),
			array(
            	'name' 		=> __( 'SMTP Configuration', 'wp_email_template' ),
            	'class'		=> 'smtp_configuration_container',
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Host', 'wp_email_template' ),
				'id' 		=> 'wp_et_smtp_provider_configuration[smtp_host]',
				'type' 		=> 'text',
				'default'	=> '',
				'separate_option'	=> true,
			),
			array(  
				'name' 		=> __( 'Port', 'wp_email_template' ),
				'id' 		=> 'wp_et_smtp_provider_configuration[smtp_port]',
				'style'		=> 'width:100px;',	
				'type' 		=> 'text',
				'default'	=> '25',
				'separate_option'	=> true,
			),
			array(  
				'name' 		=> __( 'Encryption', 'wp_email_template' ),
				'id' 		=> 'wp_et_smtp_provider_configuration[smtp_encrypt_type]',
				'type' 		=> 'onoff_radio',
				'default' 	=> 'none',
				'separate_option'	=> true,
				'onoff_options' => array(
					array(
						'val' 				=> 'none',
						'text' 				=> __( 'No encryption', 'wp_email_template' ),
						'checked_label'		=> __( 'ON', 'wp_email_template') ,
						'unchecked_label' 	=> __( 'OFF', 'wp_email_template') ,
					),
					array(
						'val' 				=> 'ssl',
						'text' 				=> __( 'Use SSL encryption', 'wp_email_template' ),
						'checked_label'		=> __( 'ON', 'wp_email_template') ,
						'unchecked_label' 	=> __( 'OFF', 'wp_email_template') ,
					),
					array(
						'val' 				=> 'tls',
						'text' 				=> __( 'Use TLS encryption.', 'wp_email_template' ),
						'checked_label'		=> __( 'ON', 'wp_email_template') ,
						'unchecked_label' 	=> __( 'OFF', 'wp_email_template') ,
					),
				),			
			),
			array(  
				'name' 		=> __( 'Enable Authentication', 'wp_email_template' ),
				'class'		=> 'enable_smtp_authentication',
				'id' 		=> 'wp_et_smtp_provider_configuration[enable_smtp_authentication]',
				'type' 		=> 'onoff_checkbox',
				'default' 	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'wp_email_template' ),
				'unchecked_label' 	=> __( 'OFF', 'wp_email_template' ),
				'separate_option'	=> true,
			),
			
			array(
				'class'		=> 'smtp_authentication_container',
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'SMTP Username', 'wp_email_template' ),
				'id' 		=> 'wp_et_smtp_provider_configuration[smtp_username]',
				'type' 		=> 'text',
				'default'	=> '',
				'separate_option'	=> true,
			),
			array(  
				'name' 		=> __( 'SMTP Password', 'wp_email_template' ),
				'id' 		=> 'wp_et_smtp_provider_configuration[smtp_password]',
				'type' 		=> 'password',
				'default'	=> '',
				'separate_option'	=> true,
			),


			// Gmail SMTP Configuration
			array(
            	'name' 		=> __( 'Gmail SMTP Activation', 'wp_email_template' ),
				'class'		=> 'select_email_delivery_provider_container',
                'type' 		=> 'heading',
                'id'		=> 'gmail_smtp_activation_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( 'Gmail SMTP Provider', 'wp_email_template' ),
				'id' 		=> 'email_delivery_provider',
				'class'		=> 'email_delivery_provider',
				'type' 		=> 'onoff_radio',
				'default' 	=> 'smtp',
				'onoff_options' => array(
					array(
						'val' 				=> 'gmail-smtp',
						'text' 				=> __( 'Gmail limit is 500 emails per day.', 'wp_email_template' ),
						'checked_label'		=> __( 'ON', 'wp_email_template') ,
						'unchecked_label' 	=> __( 'OFF', 'wp_email_template') ,
					),

				),
			),
			array(
            	'name' 		=> __( 'Gmail SMTP Credentials', 'wp_email_template' ),
				'desc'		=> sprintf( __( 'Due to the 500 email a day sending limit recommend that you open a dedicated Gmail account for this purpose. As an extra security measure we also recommend that you set up a <a href="%s" target="_blank">Google Application Specific Password</a> and use it instead of your Gmail account password.', 'wp_email_template' ), 'https://accounts.google.com/b/0/IssuedAuthSubTokens?hide_authsub=1' ),
                'class'		=> 'gmail_smtp_configuration_container',
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Gmail Username', 'wp_email_template' ),
				'id' 		=> 'wp_et_gmail_smtp_provider_configuration[smtp_username]',
				'type' 		=> 'text',
				'default'	=> '',
				'separate_option'	=> true,
			),
			array(  
				'name' 		=> __( 'Gmail Password', 'wp_email_template' ),
				'id' 		=> 'wp_et_gmail_smtp_provider_configuration[smtp_password]',
				'type' 		=> 'password',
				'default'	=> '',
				'separate_option'	=> true,
			),


			// Mandrill Configuration
			array(
            	'name' 		=> __( 'Mandrill Activation', 'wp_email_template' ),
				'class'		=> 'select_email_delivery_provider_container',
                'type' 		=> 'heading',
                'id'		=> 'mandrill_activation_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Mandrill Provider', 'wp_email_template' ),
				'id' 		=> 'email_delivery_provider',
				'class'		=> 'email_delivery_provider',
				'type' 		=> 'onoff_radio',
				'default' 	=> 'smtp',
				'onoff_options' => array(
					array(
						'val' 				=> 'mandrill',
						'text' 				=> __( 'Send up to 12,000 emails per month for free', 'wp_email_template' ),
						'checked_label'		=> __( 'ON', 'wp_email_template') ,
						'unchecked_label' 	=> __( 'OFF', 'wp_email_template') ,
					),

				),
			),
			array(
            	'name' 		=> __( 'Mandrill Credentials', 'wp_email_template' ),
				'desc'		=> sprintf( __( 'Send up to 12,000 emails a month for free <a href="%s" target="_blank">with Mandrill</a>. Register an account and generate the API Key or SMTP creds and enter those here.', 'wp_email_template' ), 'http://mandrill.com/' ),
                'type' 		=> 'heading',
                'class'		=> 'mandrill_configuration_container',
           	),
			array(  
				'name' 		=> __( 'Connect Type', 'wp_email_template' ),
				'class'		=> 'mandrill_connect_type',
				'id' 		=> 'wp_et_mandrill_provider_configuration[mandrill_connect_type]',
				'type' 		=> 'switcher_checkbox',
				'default'	=> 'api',
				'checked_value'		=> 'api',
				'unchecked_value'	=> 'smtp',
				'checked_label'		=> __( 'API', 'wp_email_template' ),
				'unchecked_label' 	=> __( 'SMTP', 'wp_email_template' ),
				'separate_option'	=> true,
			),
			
			array(
				'class'		=> 'mandrill_api_connect_container',
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Mandrill API', 'wp_email_template' ),
				'id' 		=> 'wp_et_mandrill_provider_configuration[api_key]',
				'type' 		=> 'text',
				'default'	=> '',
				'placeholder'	=> __( 'enter API key', 'wp_email_template' ),
				'separate_option'	=> true,
			),
			array(  
				'name' 		=> __( 'Track Opens', 'wp_email_template' ),
				'id' 		=> 'wp_et_mandrill_provider_configuration[enable_track_opens]',
				'type' 		=> 'onoff_checkbox',
				'default' 	=> '1',
				'checked_value'		=> '1',
				'unchecked_value'	=> '0',
				'checked_label'		=> __( 'ON', 'wp_email_template' ),
				'unchecked_label' 	=> __( 'OFF', 'wp_email_template' ),
				'separate_option'	=> true,
			),
			array(  
				'name' 		=> __( 'Track Clicks', 'wp_email_template' ),
				'id' 		=> 'wp_et_mandrill_provider_configuration[enable_track_clicks]',
				'type' 		=> 'onoff_checkbox',
				'default' 	=> '1',
				'checked_value'		=> '1',
				'unchecked_value'	=> '0',
				'checked_label'		=> __( 'ON', 'wp_email_template' ),
				'unchecked_label' 	=> __( 'OFF', 'wp_email_template' ),
				'separate_option'	=> true,
			),
			
			array(
				'class'		=> 'mandrill_smtp_connect_container',
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Host', 'wp_email_template' ),
				'id' 		=> 'wp_et_mandrill_provider_configuration[smtp_host]',
				'type' 		=> 'text',
				'default'	=> 'smtp.mandrillapp.com',
				'placeholder'	=> 'smtp.mandrillapp.com',
				'separate_option'	=> true,
			),
			array(  
				'name' 		=> __( 'Port', 'wp_email_template' ),
				'id' 		=> 'wp_et_mandrill_provider_configuration[smtp_port]',
				'style'		=> 'width:100px;',	
				'type' 		=> 'text',
				'default'	=> '587',
				'placeholder'	=> '587',
				'separate_option'	=> true,
			),
			array(  
				'name' 		=> __( 'SMTP Username', 'wp_email_template' ),
				'id' 		=> 'wp_et_mandrill_provider_configuration[smtp_username]',
				'type' 		=> 'text',
				'default'	=> '',
				'separate_option'	=> true,
			),
			array(  
				'name' 		=> __( 'SMTP Password', 'wp_email_template' ),
				'id' 		=> 'wp_et_mandrill_provider_configuration[smtp_password]',
				'type' 		=> 'password',
				'default'	=> '',
				'placeholder'	=> __( 'any valid API key', 'wp_email_template' ),
				'separate_option'	=> true,
			),

			array(
            	'name' 		=> __( 'Send a Test Email', 'wp_email_template' ),
				'class'		=> 'send_a_test_email_container',
				'desc'		=> __( "Test delivery. Type a valid email address that you have access to and click Save Changes to send. If the message successfully sends but you do not receive it - check your Spam folder.", 'wp_email_template' ),
                'type' 		=> 'heading',
                'id'		=> 'send_a_test_email_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( 'Send To', 'wp_email_template' ),
				'id' 		=> 'wp_email_template_test_send_email',
				'type' 		=> 'text',
				'separate_option'	=> true,
				'default'	=> '',
				'placeholder'	=> __( 'test@example.com', 'wp_email_template' ),
			),
        ));
	}

	public function include_script() {
	?>
<script>
(function($) {
$(document).ready(function() {
	if ( $("input.email_sending_option_provider:checked").val() == 'provider') {
		$(".select_email_delivery_local_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
	} else {
		$(".select_email_delivery_provider_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
	}

	// SMTP Configuration
	if ( $("input.enable_smtp_authentication:checked").val() != 'yes') {
		$(".smtp_authentication_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
	}
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.enable_smtp_authentication', function( event, value, status ) {
		$(".smtp_authentication_container").attr('style','display:none;');
		if ( status == 'true') {
			$(".smtp_authentication_container").slideDown();
		} else {
			$(".smtp_authentication_container").slideUp();
		}
	});

	// Mandrill Configuration
	if ( $("input.mandrill_connect_type:checked").val() == 'api') {
		$(".mandrill_smtp_connect_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
	} else {
		$(".mandrill_api_connect_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
	}
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.mandrill_connect_type', function( event, value, status ) {
		$(".mandrill_api_connect_container").attr('style','display:none;');
		$(".mandrill_smtp_connect_container").attr('style','display:none;');
		if ( status == 'true' ) {
			$(".mandrill_api_connect_container").slideDown();
			$(".mandrill_smtp_connect_container").slideUp();
		} else {
			$(".mandrill_api_connect_container").slideUp();
			$(".mandrill_smtp_connect_container").slideDown();
		}
	});


	$(document).on( "a3rev-ui-onoff_radio-switch", '.email_sending_option_provider', function( event, value, status ) {
		$(".select_email_delivery_provider_container").attr('style','display:none;');
		$(".select_email_delivery_local_container").attr('style','display:none;');
		if ( value == 'provider') {
			$(".select_email_delivery_provider_container").slideDown();
			$(".select_email_delivery_local_container").slideUp();
		} else {
			$(".select_email_delivery_provider_container").slideUp();
			$(".select_email_delivery_local_container").slideDown();
		}
	});

	// Provider Selector
	if ( $("input.email_delivery_provider:checked").val() == 'mandrill' ) {
		$(".smtp_configuration_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		$(".smtp_authentication_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		$(".gmail_smtp_configuration_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
	} else if ( $("input.email_delivery_provider:checked").val() == 'gmail-smtp' ) {
		$(".smtp_configuration_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		$(".smtp_authentication_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		$(".mandrill_configuration_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		$(".mandrill_smtp_connect_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		$(".mandrill_api_connect_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
	} else {
		$(".gmail_smtp_configuration_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		$(".mandrill_configuration_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		$(".mandrill_smtp_connect_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		$(".mandrill_api_connect_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
	}

	$(document).on( "a3rev-ui-onoff_radio-switch", '.email_delivery_provider', function( event, value, status ) {
		$(".smtp_configuration_container").attr('style','display:none;');
		$(".smtp_authentication_container").attr('style','display:none;');
		$(".gmail_smtp_configuration_container").attr('style','display:none;');
		$(".mandrill_configuration_container").attr('style','display:none;');
		$(".mandrill_api_connect_container").attr('style','display:none;');
		$(".mandrill_smtp_connect_container").attr('style','display:none;');
		if ( value == 'mandrill' && status == 'true' ) {
			$(".mandrill_configuration_container").slideDown();
			if ( $("input.mandrill_connect_type:checked").val() == 'api') {
				$(".mandrill_api_connect_container").slideDown();
				$(".mandrill_smtp_connect_container").slideUp();
			} else {
				$(".mandrill_api_connect_container").slideUp();
				$(".mandrill_smtp_connect_container").slideDown();
			}
		} else if ( value == 'gmail-smtp' && status == 'true' ) {
			$(".gmail_smtp_configuration_container").slideDown();
		} else if ( value == 'smtp' && status == 'true' ) {
			$(".smtp_configuration_container").slideDown();
			if ( $("input.enable_smtp_authentication:checked").val() == 'yes') {
				$(".smtp_authentication_container").slideDown();
			} else {
				$(".smtp_authentication_container").slideUp();
			}
		}
	});
});
})(jQuery);
</script>
    <?php
	}
}

global $wp_et_send_wp_emails_general_settings;
$wp_et_send_wp_emails_general_settings = new WP_ET_Send_WP_Emails_General_Settings();

/**
 * wp_et_send_wp_emails_general_settings_form()
 * Define the callback function to show subtab content
 */
function wp_et_send_wp_emails_general_settings_form() {
	global $wp_et_send_wp_emails_general_settings;
	$wp_et_send_wp_emails_general_settings->settings_form();
}

?>
