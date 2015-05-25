<?php 

class WC_Australian_Post_Shipping_Method extends WC_Shipping_Method{



	public $postageParcelURL = 'http://auspost.com.au/api/postage/parcel/domestic/calculate.json';
	public $postage_domestic_url = 'https://auspost.com.au/api/postage/parcel/domestic/service';
	public $postage_intl_url = 'https://auspost.com.au/api/postage/parcel/international/service.json';
	
	public $api_key = '20b5d076-5948-448f-9be4-f2fd20d4c258';
	public $supported_services = array( 'AUS_PARCEL_REGULAR' => 'Parcel Post',
										'AUS_PARCEL_EXPRESS' => 'Express Post');
	
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
		
		
		$this->default_weight = $this->get_option('default_weight');
		$this->default_width = $this->get_option('default_width');
		$this->default_length = $this->get_option('default_length');
		$this->default_height = $this->get_option('default_height');
		
		
		



		add_action('woocommerce_update_options_shipping_'.$this->id, array($this, 'process_admin_options'));


	}


	public function init_form_fields(){
		
		
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
							'description'       => __( 'Get your key from <a target="_blank" href="https://developers.auspost.com.au/apis/pacpcs-registration">https://developers.auspost.com.au/apis/pacpcs-registration</a>', 'australian-post' ),
							'default'           => $this->api_key
					),
					'shop_post_code' => array(
							'title'             => __( 'Shop Origin Post Code', 'australian-post' ),
							'type'              => 'text',
							'description'       => __( 'Enter your Shop postcode.', 'australian-post' ),
							'default'           => '2000'
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
					'default_length' => array(
							'title'             => __( 'Default Package Length', 'australian-post' ),
							'type'              => 'text',
							'default'           => '10',
							'description'       => __( 'cm', 'australian-post' ),
					),




			 );
		
		

	}

	
	
	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_options() {

		?>
		<h3><?php _e( 'Austrlia Post Settings', 'woocommerce' ); ?></h3>
		<table class="form-table">
		<?php
			// Generate the HTML For the settings form.
			$this->generate_settings_html();
		?>
		</table><!--/.form-table-->
		<p>
			
			<h3>Notes: </h3>
			<ol>
				<li><a target="_blank" href="http://auspost.com.au/parcels-mail/size-and-weight-guidelines.html">Weight and Size Guidlines </a>from Australia Post.</li>
				<li>Do you ship internationally? Do you charge handling fees? <a href="http://waseem-senjer.com/product/australian-post-woocommerce-extension-pro/" target="_blank">Get the PRO</a> version from this plugin with other cool features for <span style="color:green;">only 9$</span> </li>
				<li>If you encountered any problem with the plugin, please do not hesitate <a target="_blank" href="http://waseem-senjer.com/submit-ticket/">submitting a support ticket</a>.</li>
				<li>If you like the plugin please leave us a <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/australian-post-woocommerce-extension?filter=5#postform">★★★★★</a> rating. A huge thank you from me in advance!</li>
				
			</ol>

			
		</p>
		<?php
	}

	public function is_available( $package ){
		if($package['destination']['country'] != 'AU') return false;

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
		
			//domestic
			if($weight > 22) return false;
			if($length > 105) return false;
			if( (($length * $height * $width)/1000000) > 0.25  ) return false;
		

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

						if( $this->supported_services[$service->code]!=''){
							var_dump($service);
							$this->add_rate(array(
								'id' => $service->code,
								'label' => 'Australia ' . $this->supported_services[$service->code].' '.$service->delivery_time, //( '.$service->delivery_time.' )
								'cost' =>  $service->price , 
								
							)); 
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


	





}