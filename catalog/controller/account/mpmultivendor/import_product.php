<?php
class ControllerAccountMpmultivendorImportProduct extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		
		$this->load->language('account/mpmultivendor/import_product');

		$this->load->model('account/mpmultivendor/seller');

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

		if(!$seller_info) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');			
		}
		
		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_product'),
			'href' => $this->url->link('account/mpmultivendor/product', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/mpmultivendor/import_product', '', true)
		);

		$data['heading_title'] = $this->language->get('heading_title');

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['button_back'] = $this->language->get('button_back');

		$data['back'] = $this->url->link('account/mpmultivendor/product', '', true);
		
		$data['sample_download'] = $server . 'demo-file/importer/ExampleSellerProduct.xls';
		
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_language'] = $this->language->get('entry_language');
		$data['entry_importon'] = $this->language->get('entry_importon');
		$data['entry_file'] = $this->language->get('entry_file');
		$data['entry_format'] = $this->language->get('entry_format');
		$data['entry_review'] = $this->language->get('entry_review');
		$data['entry_images'] = $this->language->get('entry_images');
		$data['entry_custom_fields'] = $this->language->get('entry_custom_fields');
		$data['entry_existsupdate'] = $this->language->get('entry_existsupdate');
		$data['entry_great'] = $this->language->get('entry_great');
		$data['entry_dragfile'] = $this->language->get('entry_dragfile');
		$data['entry_cell_operation'] = $this->language->get('entry_cell_operation');
		$data['entry_cell_operation'] = $this->language->get('entry_cell_operation');		
		
		$data['help_file'] = $this->language->get('help_file');
		$data['help_store'] = $this->language->get('help_store');
		$data['help_language'] = $this->language->get('help_language');
		$data['help_review'] = $this->language->get('help_review');
		$data['help_images'] = $this->language->get('help_images');
		$data['help_custom_fields'] = $this->language->get('help_custom_fields');
		$data['help_importon'] = $this->language->get('help_importon');
		$data['help_existsupdate'] = $this->language->get('help_existsupdate');
		$data['help_format'] = $this->language->get('help_format');
		$data['help_cell_operation'] = $this->language->get('help_cell_operation');
		
		$data['button_import'] = $this->language->get('button_import');
		$data['button_download'] = $this->language->get('button_download');
		
		$data['text_default'] = $this->language->get('text_default');
		$data['text_product_id'] = $this->language->get('text_product_id');
		$data['text_model'] = $this->language->get('text_model');
		$data['text_product_name'] = $this->language->get('text_product_name');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_xls'] = $this->language->get('text_xls');
		$data['text_xlsx'] = $this->language->get('text_xlsx');
		$data['text_csv'] = $this->language->get('text_csv');
		$data['text_all'] = $this->language->get('text_all');
		$data['text_unall'] = $this->language->get('text_unall');

		$data['cell_operations'] = array();
		$data['cell_operations']['product_name'] = $this->language->get('cell_product_name');
		$data['cell_operations']['model'] = $this->language->get('cell_model');
		$data['cell_operations']['store'] = $this->language->get('cell_store');
		$data['cell_operations']['description'] = $this->language->get('cell_description');
		$data['cell_operations']['meta_title'] = $this->language->get('cell_meta_title');
		$data['cell_operations']['meta_description'] = $this->language->get('cell_meta_description');
		$data['cell_operations']['meta_keyword'] = $this->language->get('cell_meta_keyword');
		$data['cell_operations']['meta_tag'] = $this->language->get('cell_meta_tag');
		$data['cell_operations']['sku'] = $this->language->get('cell_sku');
		$data['cell_operations']['upc'] = $this->language->get('cell_upc');
		$data['cell_operations']['ean'] = $this->language->get('cell_ean');
		$data['cell_operations']['jan'] = $this->language->get('cell_jan');
		$data['cell_operations']['isbn'] = $this->language->get('cell_isbn');
		$data['cell_operations']['mpn'] = $this->language->get('cell_mpn');
		$data['cell_operations']['location'] = $this->language->get('cell_location');
		$data['cell_operations']['price'] = $this->language->get('cell_price');
		$data['cell_operations']['minimum_quantity'] = $this->language->get('cell_minimum_quantity');
		$data['cell_operations']['quantity'] = $this->language->get('cell_quantity');
		$data['cell_operations']['shipping_required'] = $this->language->get('cell_shipping_required');
		$data['cell_operations']['date_available'] = $this->language->get('cell_date_available');

		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['mpseller_links'] = $this->load->controller('account/mpmultivendor/mpseller_links');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		/* Theme Work Starts */
		if($this->config->get('config_theme')) {			
     		$custom_themename = $this->config->get('config_theme');
    	} if($this->config->get('theme_default_directory')) {    		
			$custom_themename = $this->config->get('theme_default_directory');
		} if($this->config->get('config_template')) {			
			$custom_themename = $this->config->get('config_template');
		} 
		// else{
		// 	$custom_themename = 'default';
		// }

		if (defined('JOURNAL3_ACTIVE')) {
			$custom_themename = 'journal3';
		}

		if(strpos($this->config->get('config_template'), 'journal2') === 0){
			$custom_themename = 'journal2';
		}

		if(empty($custom_themename)) {
			$custom_themename = 'default';
		}

		$data['custom_themename'] = $custom_themename;
		/* Theme Work Ends */

        //for profile right column
        $data['profile_column_left'] = $this->load->controller('common/profile_column_left');
		$this->response->setOutput($this->load->view('account/mpmultivendor/import_product', $data));
	}

	public function import() {
		require_once(DIR_SYSTEM.'library/PHPExcel.php');
		
		$this->load->language('account/mpmultivendor/import_product');
		
		$this->load->model('account/mpmultivendor/import_product');
		
		$this->load->model('account/mpmultivendor/seller');
		
		$json = array();

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

		if(!empty($seller_info)) {
			$mpseller_id = $seller_info['mpseller_id'];
		} else {
			$mpseller_id = 0;
		}
		
		if(empty($this->request->files['find_file'])) {
			$json['error']['file'] = $this->language->get('error_file');
			$json['error']['warning'] = $this->language->get('error_file');
		}
		
		if(!isset($this->request->get['find_store'])) {
			$json['error']['store'] = $this->language->get('error_store');
		}
		
		if(empty($this->request->get['find_language_id'])) {
			$json['error']['language'] = $this->language->get('error_language');
		}
		
		if(empty($this->request->get['find_importon'])) {
			$json['error']['importon'] = $this->language->get('error_importon');
		}

		// Check to see if any PHP files are trying to be uploaded
		if(!empty($this->request->files['find_file'])) {
			$content = file_get_contents($this->request->files['find_file']['tmp_name']);
			
			if (preg_match('/\<\?php/i', $content)) {
				$json['error']['file'] = $this->language->get('error_filetype');
				$json['error']['warning'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['find_file']['error'] != UPLOAD_ERR_OK) {
				$json['error']['file'] = $this->language->get('error_upload_' . $this->request->files['find_file']['error']);
				$json['error']['warning'] = $this->language->get('error_upload_' . $this->request->files['find_file']['error']);
			}
		}
				
		if(!$json && $this->request->files) {
			$file = basename($this->request->files['find_file']['name']);
			move_uploaded_file($this->request->files['find_file']['tmp_name'], $file);
			$inputFileName = $file;
				
			$extension = pathinfo($inputFileName);

			$extension['extension'] = strtolower(strtoupper($extension['extension']));
			
			if(!in_array($extension['extension'], array('xls','xlsx','csv'))) {
				$json['error']['file'] = $this->language->get('error_format_diff');
				$json['error']['warning'] = $this->language->get('error_format_diff');
			}
			
			if($extension['extension']=='xlsx' || $extension['extension']=='xls' || $extension['extension']=='csv') {
				try{
					$inputFileType = $extension['extension'];
					
					if($extension['extension']=='csv') {
						$objReader  = PHPExcel_IOFactory::createReader('CSV');
						$objPHPExcel = $objReader->load($inputFileName);
					}else{
						$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
					}
				}catch(Exception $e){
					$json['error']['warning'] = $this->language->get('error_loading_file') .'"'. pathinfo($inputFileName,PATHINFO_BASENAME) .'": '.$e->getMessage();
				}
			}
		}
		
		if(!$json) {
			if(isset($this->request->get['find_importon'])) {
				$find_importon = $this->request->get['find_importon'];
			} else{
				$find_importon = 'product_id';
			}
			
			if(isset($this->request->get['find_store'])) {
				$find_store = $this->request->get['find_store'];
			} else{
				$find_store = array();
			}			

			if(isset($this->request->get['find_language_id'])) {
				$find_language_id = $this->request->get['find_language_id'];
			} else{
				$find_language_id = 0;
			}
			
			if(isset($this->request->get['find_existsupdate'])) {
				$find_existsupdate = $this->request->get['find_existsupdate'];
			} else{
				$find_existsupdate = 0;
			}
			if(isset($this->request->get['find_cell_operations'])) {
				$find_cell_operations = $this->request->get['find_cell_operations'];
			} else{
				$find_cell_operations = array();
			}
			
			$find_data = array(
				'store'					=> $find_store,
				'language_id'			=> $find_language_id,
				'importon'				=> $find_importon,
				'existsupdate'			=> $find_existsupdate,
				'cell_operations'		=> $find_cell_operations,
				'mpseller_id'			=> $mpseller_id,
			);
			
			$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			
			$i=0;
			$insert_product = 0;
			$edit_product = 0;
			$static_columns = 57;
			
			if(count($allDataInSheet) > 1) {
				foreach($allDataInSheet as $default_value) {
					$value = $this->clean($default_value);
					
					// Column Values
					if($i != '0') {
						$product_id = (isset($value['A']) ? (int)$value['A'] : '');
						$product_name = (isset($value['B']) ? $value['B'] : '');
						
						if(trim($product_name) == '') {
							continue;
						}
						
						$model_number = (isset($value['C']) ? $value['C'] : '');
						$language_code = (isset($value['D']) ? $value['D'] : '');
						$store_names = (isset($value['E']) ? explode('::', $value['E']) : '');
						$description = (isset($value['F']) ? $value['F'] : '');
						$meta_title = (isset($value['G']) ? $value['G'] : '');
						$meta_description = (isset($value['H']) ? $value['H'] : '');
						$meta_keyword = (isset($value['I']) ? $value['I'] : '');
						$meta_tag = (isset($value['J']) ? $value['J'] : '');
						$sku = (isset($value['K']) ? $value['K'] : '');
						$upc = (isset($value['L']) ? $value['L'] : '');
						$ean = (isset($value['M']) ? $value['M'] : '');
						$jan = (isset($value['N']) ? $value['N'] : '');
						$isbn = (isset($value['O']) ? $value['O'] : '');
						$mpn = (isset($value['P']) ? $value['P'] : '');
						$location = (isset($value['Q']) ? $value['Q'] : '');
						$price = (isset($value['R']) ? $value['R'] : '');
						$minimum_quantity = (isset($value['S']) ? $value['S'] : '');
						$quantity = (isset($value['T']) ? $value['T'] : '');
						$subtract = (isset($value['U']) ? $value['U'] : '');
						$shipping_required = (isset($value['V']) ? $value['V'] : '');
						$date_available = (!empty($value['W']) ?  date('Y-m-d', strtotime($value['W'])) : '');
						$date_available_count = explode('-', $date_available); 
						$date_available = ((!empty($date_available_count) && count($date_available_count) == '3') ?  date('Y-m-d', strtotime($date_available)) : date('Y-m-d H:i:s'));
						
						// Insert Data
						$insert_data = array();
						
						$insert_data['product_id'] 							= $product_id;
						$insert_data['product_name']   						= $product_name;
						$insert_data['model'] 	    						= $model_number;
						$insert_data['language_code']  						= $language_code;
						$insert_data['store_names']  						= $store_names;
						$insert_data['description']							= $description;
						$insert_data['meta_title'] 							= $meta_title;
						$insert_data['meta_description'] 					= $meta_description;
						$insert_data['meta_keyword'] 						= $meta_keyword;
						$insert_data['meta_tag'] 							= $meta_tag;
						$insert_data['sku'] 								= $sku;
						$insert_data['upc'] 								= $upc;
						$insert_data['ean'] 								= $ean;
						$insert_data['jan'] 								= $jan;
						$insert_data['isbn'] 								= $isbn;
						$insert_data['mpn'] 								= $mpn;
						$insert_data['location'] 						= $location;
						$insert_data['price'] 							= $price;
						$insert_data['minimum_quantity'] 				= $minimum_quantity;
						$insert_data['quantity'] 						= $quantity;
						$insert_data['subtract'] 						= $subtract;
						$insert_data['shipping_required'] 				= $shipping_required;
						$insert_data['date_available'] 					= $date_available;
						
						if($find_importon == 'product_id') {
							$product_info = $this->model_account_mpmultivendor_import_product->getProductById($product_id, $mpseller_id);
						}else if($find_importon == 'model_number') {
							$product_info = $this->model_account_mpmultivendor_import_product->getProductByModel($model_number, $mpseller_id);
						}else{
							$product_info = $this->model_account_mpmultivendor_import_product->getProductByName($product_name, $find_language_id, $mpseller_id);
						}
						
						if($product_info) {
							// Update Exists Product
							if($find_existsupdate) {
								$this->model_account_mpmultivendor_import_product->editProduct($product_info['product_id'], $insert_data, $find_data);
								
								$edit_product++;
							}
						}else{
							// ($find_importon == 'model_number' || $find_importon == 'product_name') then check for product id exist or not, if yes then empty coming product id from $insert_data array() \\
							if($find_importon == 'model_number' || $find_importon == 'product_name') {
								// get Product Info By Excel Product Id
								$product_info_by_excelid = $this->model_account_mpmultivendor_import_product->getProductById($insert_data['product_id']);
								if($product_info_by_excelid) {
									$insert_data['product_id'] = '';
								}
							}
							
							// Insert Exists Product
							$this->model_account_mpmultivendor_import_product->addProduct($insert_data, $find_data);
							
							$insert_product++;
						}
					}
					
					$i++;
				}
				
				$text_success  = $this->language->get('text_success');
				
				if($edit_product) {
					$text_success .= sprintf($this->language->get('text_success_update'), $edit_product);
				}
				
				if($insert_product) {
					$text_success .= sprintf($this->language->get('text_success_insert'), $insert_product);
				}
				
				if(empty($insert_product) && empty($edit_product)) {
					$text_success = $this->language->get('text_success_zero');
				}
				
				$json['success'] = $text_success;
			} else{
				$json['error']['warning'] = $this->language->get('text_no_result');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function clean($data) {
		if (is_array($data)) {
		   foreach ($data as $key => $value) {
		    unset($data[$key]);

		    $data[$this->clean($key)] = $this->clean($value);
		   }
	  	} else {
			if (ini_get('magic_quotes_gpc')) {
				$data = htmlspecialchars(stripslashes($data), ENT_COMPAT, 'UTF-8');
			} else {
		   		$data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
		   	}
		}

		return $data;
	}
}