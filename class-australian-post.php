<?php 

class WC_Australian_Post_Shipping_Method extends WC_Shipping_Method{



	public $postageParcelURL = 'http://auspost.com.au/api/postage/parcel/domestic/calculate.json';
	public $postage_domestic_url = 'https://auspost.com.au/api/postage/parcel/domestic/service';
	public $postage_intl_url = 'https://auspost.com.au/api/postage/parcel/international/service.json';
	
	public $api_key = '20b5d076-5948-448f-9be4-f2fd20d4c258';
	public $supported_services = array( 'AUS_PARCEL_REGULAR' => 'Parcel Post',
										'AUS_PARCEL_EXPRESS' => 'Express Post');
	public $supported_international_services = array( 'INTL_SERVICE_SEA_MAIL' => 'Sea Mail',
													   'INTL_SERVICE_AIR_MAIL' => 'Air Mail');

	public function __construct(){
		$this->id = 'auspost';
		$this->method_title = __('Australian Post','australian-post');
		$this->title = __('Australian Post','australian-post');
		

		$this->init_form_fields();
		$this->init_settings();


		$this->enabled = $this->get_option('enabled');
		$this->title = $this->get_option('title');
		//$this->api_key = $this->get_option('api_key');
		$this->shop_post_code = $this->get_option('shop_post_code');
		$this->handling_fee = trim($this->get_option('handling_fee'));
		$this->tax_status = $this->get_option('tax_status');
		$this->default_weight = $this->get_option('default_weight');
		$this->default_width = $this->get_option('default_width');
		$this->default_length = $this->get_option('default_length');
		$this->default_height = $this->get_option('default_height');
		$this->domestic_options = $this->get_option('domestic_options');
		$this->intl_options = $this->get_option('intl_options');

		$this->availability = $this->get_option('availability');
		$this->countries = $this->get_option('countries');

		$this->auspost_key = $this->get_option('auspost_key');
		$this->customer_email = $this->get_option('customer_email');




		add_action('woocommerce_update_options_shipping_'.$this->id, array($this, 'process_admin_options'));


	}


	public function init_form_fields(){
		/**/
		if($this->auspost_key ==''){
			$this->form_fields = array(
					'auspost_key' => array(
						'title' 		=> __( 'licence key', 'woocommerce' ),
						'type' 			=> 'text',
						'description' 	=> __( 'If you purchased the plugin, you\'ll find the key in the confirmation email, If you lost it, you can <a href="http://waseem-senjer.com/lost-licence/">restore your license keys</a>.', 'woocommerce' ),
						'default'		=> '',
						
					),
					'customer_email' => array(
						'title' 		=> __( 'Customer email', 'woocommerce' ),
						'type' 			=> 'text',
						'description' 	=> __( 'This is the email which you provide when purchased the plugin.', 'woocommerce' ),
						'default'		=> '',
						
					),

					);	
		}else{
				$this->form_fields = array(

					'enabled' => array(
					'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
					'type' 			=> 'checkbox',
					'label' 		=> __( 'Enable Australian Post', 'woocommerce' ),
					'default' 		=> 'yes'
					),
					'title' => array(
						'title' 		=> __( 'Method Title', 'woocommerce' ),
						'type' 			=> 'text',
						'description' 	=> __( 'This controls the title', 'woocommerce' ),
						'default'		=> __( 'Australian Post Shipping', 'woocommerce' ),
						'desc_tip'		=> true,
					),
					'api_key' => array(
							'title'             => __( 'API Key', 'australian-post' ),
							'type'              => 'text',
							'description'       => __( 'Get your key from https://developers.auspost.com.au/apis/pacpcs-registration', 'australian-post' ),
							'default'           => ''
					),
					'shop_post_code' => array(
							'title'             => __( 'Shop Origin Post Code', 'australian-post' ),
							'type'              => 'text',
							'description'       => __( 'Enter your Shop postcode.', 'australian-post' ),
							'default'           => '2000'
					),
					'handling_fee' => array(
							'title'             => __( 'Handling Fees', 'australian-post' ),
							'type'              => 'text',
							'description'       => __( '(Optional) Enter an amount e.g. 3.5 or a percentage e.g. 3%', 'australian-post' ),
							'default'           => ''
					),
					'tax_status' => array(
								'title'			=> __( 'Tax Status', 'woocommerce' ),
								'type'			=> 'select',
								'class'         => 'wc-enhanced-select',
								'default'		=> 'none',
								'options'		=> array(
									'taxable'	=> __( 'Taxable', 'woocommerce' ),
									'none'		=> _x( 'None', 'Tax status', 'woocommerce' )
								)
							),
					'default_weight' => array(
							'title'             => __( 'Default Package Weight', 'australian-post' ),
							'type'              => 'text',
							'default'           => '0.5',
							'description'       => __( 'KG', 'australian-post' ),
					),
					'default_width' => array(
							'title'             => __( 'Default Package Width', 'australian-post' ),
							'type'              => 'text',
							'default'           => '5',
							'description'       => __( 'cm', 'australian-post' ),
					),
					'default_height' => array(
							'title'             => __( 'Default Package Height', 'australian-post' ),
							'type'              => 'text',
							'default'           => '5',
							'description'       => __( 'cm', 'australian-post' ),
					),
					'default_length' => array(
							'title'             => __( 'Default Package Length', 'australian-post' ),
							'type'              => 'text',
							'default'           => '10',
							'description'       => __( 'cm', 'australian-post' ),
					),
					'domestic_options' => array(
						'title' 		=> __( 'Domestic Options', 'australian-post' ),
						'type' 			=> 'multiselect',
						'default' 		=> 'AUS_PARCEL_REGULAR',
						'class'			=> 'availability wc-enhanced-select',
						'options'		=> $this->supported_services,
					),
					'intl_options' => array(
						'title' 		=> __( 'International Options', 'australian-post' ),
						'type' 			=> 'multiselect',
						'default' 		=> 'INTL_SERVICE_AIR_MAIL',
						'class'			=> 'availability wc-enhanced-select',
						'options'		=> $this->supported_international_services,
					),


					'availability' => array(
						'title' 		=> __( 'Method availability', 'woocommerce' ),
						'type' 			=> 'select',
						'default' 		=> 'all',
						'class'			=> 'availability wc-enhanced-select',
						'options'		=> array(
							'all' 		=> __( 'All allowed countries', 'woocommerce' ),
							'specific' 	=> __( 'Specific Countries', 'woocommerce' )
						)
					),
					'countries' => array(
						'title' 		=> __( 'Specific Countries', 'woocommerce' ),
						'type' 			=> 'multiselect',
						'class'			=> 'wc-enhanced-select',
						'css'			=> 'width: 450px;',
						'default' 		=> '',
						'options'		=> WC()->countries->get_shipping_countries(),
						'custom_attributes' => array(
							'data-placeholder' => __( 'Select some countries', 'woocommerce' )
						)
					),
					





			 );
		}
		

	}

	public function validate_auspost_key_field($k){
		static $just_one  = 0;
		$just_one++;
		if($just_one === 1){


			if($k == 'auspost_key'){
					

				if($_POST['woocommerce_auspost_auspost_key'] != ''){
					$licence_url = 'http://www.waseem-senjer.com/?';
					$activation_data['wc-api'] = 'software-api';
					$activation_data['request'] = 'activation';
					$activation_data['email'] = $_POST['woocommerce_auspost_customer_email'];
					$activation_data['licence_key'] = $_POST['auspost_key'];
					$activation_data['product_id'] = 'AUSPOST';
					$activation_data['secret_key'] = 'k8j4s3980dlg0jka74bc84bc832ghsqw';
					$request = wp_remote_get($licence_url.http_build_query($activation_data));
					$response = json_decode($request['body']);
					var_dump($response);
					if($response->activated === true){
						add_option('auspost_key',$_POST['woocommerce_auspost_auspost_key']) OR update_option('auspost_key', $_POST['woocommerce_auspost_auspost_key']);
						return $_POST['woocommerce_auspost_auspost_key'];
					}else{
						WC_Admin_Settings::add_error( __(' Your licence Key or Email is not valid', 'australian-post' ) );
						
					}
				}
			}
		}
	}
	

	public function is_available( $package ){

		if ( 'specific' == $this->availability ) {
			$ship_to_countries = $this->countries;
		} else {
			$ship_to_countries = array_keys( WC()->countries->get_shipping_countries() );
		}

		if ( is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries ) ) {
			return false;
		}

		$weight = 0;
		$length = 0;
		$width = 0;
		$height = 0;
		foreach($package['contents'] as  $item_id => $values){
			$_product =  $values['data'];
			$weight = $weight + $_product->get_weight();
			$height = $height + $_product->height;
			$width = $width + $_product->width;
			$length = $length + $_product->length;

		}
		$weight = ($weight === 0)?$this->default_weight:$weight;
		$length = ($length === 0)?$this->default_length:$length;
		$width = ($width === 0)?$this->default_width:$width;
		$height = ($height === 0)?$this->default_height:$height;


		//http://auspost.com.au/parcels-mail/size-and-weight-guidelines.html
		if($package['destination']['country'] == 'AU'){
			//domestic
			if($weight > 22) return false;
			if($length > 105) return false;
			if( (($length * $height * $width)/1000000) > 0.25  ) return false;
		}else{
			//international
			if($weight > 20) return false;
			if($length > 105) return false;
			//girth
			if( (( $height + $width )*2) > 104  ) return false;
		}

		return true;
		

	}

	public function calculate_shipping( $package ){
		$this->rates = array();	


		$weight = 0;
		$length = 0;
		$width = 0;
		$height = 0;
		foreach($package['contents'] as  $item_id => $values){
			$_product =  $values['data'];
			$weight = $weight + $_product->get_weight();
			$height = $height + $_product->height;
			$width = $width + $_product->width;
			$length = $length + $_product->length;

		}

		$weight = ($weight === 0)?$this->default_weight:$weight;
		$length = ($length === 0)?$this->default_length:$length;
		$width = ($width === 0)?$this->default_width:$width;
		$height = ($height === 0)?$this->default_height:$height;






		if($package['destination']['country'] == 'AU'){
			//domestic
			$query_params['from_postcode'] = $this->shop_post_code;
			$query_params['to_postcode'] = $package['destination']['postcode'];
			$query_params['length'] = $length;
			$query_params['width'] = $width;
			$query_params['height'] = $height;
			$query_params['weight'] = $weight;
			
			//$query_params['service_code'] = 'AUS_PARCEL_REGULAR';

			$response = wp_remote_get( $this->postage_domestic_url.'?'.http_build_query($query_params),
				array('headers' => array(
					'AUTH-KEY'=> $this->api_key
					))

			 );
			
			if(is_wp_error( $response )){
				wc_add_notice('Unknown Problem. Please Contact the admin','error');
				return;
			}

			$aus_response = json_decode(wp_remote_retrieve_body($response));
			
			
			if($aus_response->services){
				foreach($aus_response->services->service as $service){
					if(in_array($service->code, $this->domestic_options)){
						$this->add_rate(array(
							'id' => $service->code,
							'label' => 'Australia ' . $this->supported_services[$service->code], //( '.$service->delivery_time.' )
							'cost' =>  $this->calculate_handling_fee($package['contents_cost']) + $service->price , 
							
						)); 
					}

				}
			}
		}else{
			//international
			$query_params['weight'] = $weight;
			$query_params['country_code'] = $package['destination']['country'];
			$response = wp_remote_get( $this->postage_intl_url.'?'.http_build_query($query_params),
				array('headers' => array(
					'AUTH-KEY'=> $this->api_key
					))

			 );
			$aus_response = json_decode(wp_remote_retrieve_body($response));
			
			if($aus_response->services){
				foreach($aus_response->services->service as $service){
					if(in_array($service->code, $this->intl_options)){
						$this->add_rate(array(
							'id' => $service->code,
							'label' =>  $this->supported_international_services[$service->code], //( '.$service->delivery_time.' )
							'cost' =>  $this->calculate_handling_fee($package['contents_cost']) + $service->price , 
							
						)); 
					}

				}
			}
		}
		/*if($aus_response->error){
			wc_add_notice($aus_response->error->errorMessage,'error');
			return;
		}

		if($aus_response->error){
			wc_add_notice($aus_response->error->details->errorMessage,'error');
			return;
		}*/


	}


	/**
	 * [calculate the handling fees]
	 * @param  [number] $cost [description]
	 * @return [number]       [description]
	 */
	public function calculate_handling_fee( $cost ){
		
		if($this->handling_fee == '') return 0;
		
		if(substr($this->handling_fee,-1) == '%'){
			
			$handling_fee =trim( str_replace('%', '', $this->handling_fee) );
			$result =  ( $cost * ($handling_fee/100) );
			return $result;
		}

		if(is_numeric($this->handling_fee) && $this->handling_fee > 0){
			
			return $this->handling_fee;
		}
		return 0;

		
	}





}