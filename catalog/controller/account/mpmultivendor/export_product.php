<?php
class ControllerAccountMpmultivendorExportProduct extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}
        $this->load->language('account/edit');

		$this->load->language('account/mpmultivendor/export_product');

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
			'href' => $this->url->link('account/mpmultivendor/export_product', '', true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['back'] = $this->url->link('account/mpmultivendor/product', '', true);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['button_back'] = $this->language->get('button_back');

		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_language'] = $this->language->get('entry_language');
		$data['entry_qty'] = $this->language->get('entry_qty');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_product_limit'] = $this->language->get('entry_product_limit');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
		$data['entry_format'] = $this->language->get('entry_format');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['text_xls'] = $this->language->get('text_xls');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_status'] = $this->language->get('text_all_status');
		$data['text_all_store'] = $this->language->get('text_all_store');
		$data['text_all_default'] = $this->language->get('text_all_default');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_help_category'] = $this->language->get('text_help_category');		
		$data['text_xlsx'] = $this->language->get('text_xlsx');
		$data['text_csv'] = $this->language->get('text_csv');

		$data['button_export'] = $this->language->get('button_export');

		$data['placeholder_product_start'] = $this->language->get('placeholder_product_start');		
		$data['placeholder_price_start'] = $this->language->get('placeholder_price_start');
		$data['placeholder_price_limit'] = $this->language->get('placeholder_price_limit');
		$data['placeholder_product_limit'] = $this->language->get('placeholder_product_limit');
		$data['placeholder_quantity_limit'] = $this->language->get('placeholder_quantity_limit');
		$data['placeholder_quantity_start'] = $this->language->get('placeholder_quantity_start');

		$data['find_product_limit'] = $this->language->get('find_product_limit');

		$data['help_product'] = $this->language->get('help_product');
		$data['help_category'] = $this->language->get('help_category');
		$data['help_product'] = $this->language->get('help_product');
		$data['help_product_limit'] = $this->language->get('help_product_limit');
		$data['help_price'] = $this->language->get('help_price');
		$data['help_qty'] = $this->language->get('help_qty');

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
		$this->response->setOutput($this->load->view('account/mpmultivendor/export_product', $data));
	}

	public function export() {
		require_once(DIR_SYSTEM.'library/PHPExcel.php');
	
		$this->load->language('account/mpmultivendor/export_product');
		
		$this->load->model('account/mpmultivendor/export_product');
		
		$this->load->model('account/mpmultivendor/seller');

		$this->load->model('setting/store');
		
		$objPHPExcel = new PHPExcel();
		
		$objPHPExcel->setActiveSheetIndex(0);  
		
		if(isset($this->request->post['find_store_id']) && $this->request->post['find_store_id'] != '') {
			$find_store_id = $this->request->post['find_store_id'];
		}else{
			$find_store_id = null;
		}
		
		if(isset($this->request->post['find_language_id']) && $this->request->post['find_language_id'] != '') {
			$find_language_id = $this->request->post['find_language_id'];
		}else{
			$find_language_id = null;
		}
		
		if(isset($this->request->post['find_quantity_start']) && $this->request->post['find_quantity_start'] != '') {
			$find_quantity_start = $this->request->post['find_quantity_start'];
		}else{
			$find_quantity_start = '';
		}
		
		if(isset($this->request->post['find_quantity_limit']) && $this->request->post['find_quantity_limit'] != '') {
			$find_quantity_limit = $this->request->post['find_quantity_limit'];
		}else{
			$find_quantity_limit = '';
		}
		
		if(isset($this->request->post['find_price_start']) && $this->request->post['find_price_start'] != '') {
			$find_price_start = $this->request->post['find_price_start'];
		}else{
			$find_price_start = '';
		}
		
		if(isset($this->request->post['find_price_limit']) && $this->request->post['find_price_limit'] != '') {
			$find_price_limit = $this->request->post['find_price_limit'];
		}else{
			$find_price_limit = '';
		}
		
		if(isset($this->request->post['find_product_start']) && $this->request->post['find_product_start'] != '') {
			$find_product_start = $this->request->post['find_product_start'];
		}else{
			$find_product_start = '';
		}
		
		if(isset($this->request->post['find_product_limit']) && $this->request->post['find_product_limit'] != '') {
			$find_product_limit = $this->request->post['find_product_limit'];
		}else{
			$find_product_limit = '';
		}
		
		if(isset($this->request->post['find_image']) && $this->request->post['find_image'] != '') {
			$find_image = $this->request->post['find_image'];
		}else{
			$find_image = null;
		}
		
		if(isset($this->request->post['find_format']) && $this->request->post['find_format'] != '') {
			$find_format = $this->request->post['find_format'];
		}else{
			$find_format = null;
		}
		
		if(isset($this->request->post['find_product']) && $this->request->post['find_product'] != '') {
			$find_product = $this->request->post['find_product'];
		}else{
			$find_product = null;
		}

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

		if(!empty($seller_info)) {
			$mpseller_id = $seller_info['mpseller_id'];
		} else {
			$mpseller_id = 0;
		}
		
		$filter_data = array(
			'find_store_id'					=> $find_store_id,
			'find_language_id'				=> $find_language_id,
			'find_quantity_start'			=> $find_quantity_start,
			'find_quantity_limit'			=> $find_quantity_limit,
			'find_price_start'				=> $find_price_start,
			'find_price_limit'				=> $find_price_limit,
			'find_product_start'			=> $find_product_start,
			'find_product_limit'			=> $find_product_limit,
			'find_product'					=> $find_product,
			'filter_mpseller_id'			=> $mpseller_id,
		);
				
		$i = 1;
		$char = 'A';
		
		$objPHPExcel->getActiveSheet()->getStyle('1')->getFill()->applyFromArray(array(
			'type'				=> PHPExcel_Style_Fill::FILL_SOLID,
			'startcolor' 	=> array(
			'rgb' 				=> '017FBE',
			),
		));
				
		$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray(array(
			'font'  => array(
			'color' => array('rgb' => 'FFFFFF'),
			'bold'  => true,
			)
		));

		$objPHPExcel->getActiveSheet()->freezePane('D2');

		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_product_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_product_name'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_model'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_language'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_store'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_description'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_meta_title'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_meta_description'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_meta_keyword'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_tag'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_sku'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_upc'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_ean'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_jan'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_isbn'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char++ .$i, $this->language->get('export_mpn'));
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_location'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_price'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_min_quantity'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_quantity'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_subtract'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_shipping'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);		
		$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_date_avaiable'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
		
		// Fetch Products
		$results = $this->model_account_mpmultivendor_export_product->getProducts($filter_data);

		if($results) {
			// Fetch Total Products
			$objPHPExcel->getActiveSheet()->setTitle(sprintf($this->language->get('export_title'), count($results)));
			
			foreach($results as $result) {
				$char_value = 'A'; $i++; 
				
				// Language
				$language_info = $this->model_account_mpmultivendor_export_product->getLanguage($result['language_id']);
				$result['language'] = ($language_info) ? $language_info['code']: '';
							
				// Store
				if (isset($find_store_id) && $find_store_id != '') {
					$stores = $this->model_account_mpmultivendor_export_product->getProductStores($result['product_id'], $find_store_id);
				}else{
					$stores = $this->model_account_mpmultivendor_export_product->getProductStores($result['product_id']);
				}
				
				$export_stores = array();
				foreach($stores as $store_id) {
					if($store_id == '0') {
						$export_stores[] = $this->language->get('text_default');
					}else{
						$store_info = $this->model_setting_store->getStore($store_id);
						$export_stores[] = ($store_info) ? $store_info['name'] : '';
					}
				}
				$result['store'] = implode('::', $export_stores);
				
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['product_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'));
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['model']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['language']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['store']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'));
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['meta_title']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['meta_description']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['meta_keyword']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['tag']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['sku']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['upc']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['ean']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['jan']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['isbn']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['mpn']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['location']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value .$i, number_format((float)$result['price'], 2))->getStyle($char_value++ .$i)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['minimum']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['quantity']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['subtract']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['shipping']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['date_available']);
			}
			
			// Find Format
			if($find_format == 'xls') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
				$file_name = 'SellerProductList.xls';
			}else if($find_format == 'xlsx') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
				$file_name = 'SellerProductList.xlsx';
			}else if($find_format == 'csv') {
				$file_name = 'SellerProductList.csv';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
			}else{
				$file_name = '';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			}
			
			$file_to_save = DIR_UPLOAD . $file_name;
			$objWriter->save(DIR_UPLOAD . $file_name); 
			
			$json['href'] = str_replace('&amp;', '&', $this->url->link('account/mpmultivendor/export_product/fileDownload', 'file_name='. $file_name .'&find_format='. $find_format, true));
			
			$json['success'] = $this->language->get('text_success');
		} else{
			$json['error'] = $this->language->get('text_no_results');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function fileDownload() {
		$file_to_save = DIR_UPLOAD . $this->request->get['file_name'];
		
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="'. $this->request->get['file_name'] .'"'); 
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '. filesize($file_to_save));
		header('Cache-Control: max-age=0'); 
		header('Accept-Ranges: bytes');
		readfile($file_to_save);
		
		unlink($file_to_save);
	}
}