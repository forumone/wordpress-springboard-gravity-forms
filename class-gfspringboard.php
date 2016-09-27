<?php

GFForms::include_addon_framework();

class GFSpringboard extends GFAddOn {

	protected $_version = GF_SIMPLE_ADDON_VERSION;
	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'gftospringboard';
	protected $_path = 'gftospringboard/springboard.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Gravity Forms To Springboard Add-On (Forum One)';
	protected $_short_title = 'Springboard API';

	private static $_instance = null;

	private static $_submit_form_api_endpoint = 'springboard-api/springboard-forms/submit';

	/**
	 * Get an instance of this class.
	 *
	 * @return GFSpringboard
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFSpringboard();
		}

		return self::$_instance;
	}

	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {
		parent::init();
		add_filter( 'gform_confirmation', array( $this, 'post_to_springboard' ), 10, 3 );
	}


	// # FRONTEND FUNCTIONS --------------------------------------------------------------------------------------------

	function post_to_springboard( $confirmation, $form, $entry ) {

		$settings = $this->get_form_settings( $form );
		$base_url = $this->fix_base_url( $settings['baseurl'] );
		$base_url .= self::$_submit_form_api_endpoint;

		$params = array(
			'form_id' => absint( $settings['formid'] ),
			'api_key' => sanitize_text_field( $settings['apikey'] )
			);
		
		$post_url = add_query_arg( $params, esc_url_raw( $base_url ) );

		GFCommon::log_debug( '$post_url => ' . print_r( $post_url, true ) );

		$body = array(
			'mail' => rgar( $entry, '1' )
			);

		$request  = new WP_Http();
		$response = $request->post( $post_url, array( 'body' => $body ) );

		// Check the response code
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 != $response_code ) {
			return 'Some error occurred';
		} else {
			return $confirmation;
		}

	}


	// # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------


	/**
	 * Configures the settings which should be rendered on the Form Settings > Springboard API tab.
	 *
	 * @return array
	 */
	public function form_settings_fields( $form ) {
		return array(
			array(
				'title'  => esc_html__( 'Springboard API Settings', 'gftospringboard' ),
				'fields' => array(
					array(
						'label'             => esc_html__( 'API Key', 'gftospringboard' ),
						'type'              => 'text',
						'name'              => 'apikey',
						'tooltip'           => esc_html__( 'The API Key for the Springboard API', 'gftospringboard' ),
						'class'             => 'medium',
						),
					array(
						'label'             => esc_html__( 'Base URL', 'gftospringboard' ),
						'type'              => 'text',
						'name'              => 'baseurl',
						'tooltip'           => esc_html__( 'The base URL for the Springboard API to communicate with. (Include either http:// or https://)', 'gftospringboard' ),
						'class'             => 'medium',
						),
					array(
						'label'             => esc_html__( 'Form ID', 'gftospringboard' ),
						'type'              => 'text',
						'name'              => 'formid',
						'tooltip'           => esc_html__( 'The Form ID to submit the GF form data', 'gftospringboard' ),
						'class'             => 'medium',
						),					
					),
				),
			);
	}



	// # HELPERS -------------------------------------------------------------------------------------------------------

	private function fix_base_url( $str ) {
		return ( substr( $str, -1 ) != '/' ) ? $str .= '/' : $str;
	}

}