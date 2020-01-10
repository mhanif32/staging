<?php
class ControllerMpmultivendorStoreContact extends Controller {
	public function index() {
		$this->load->language('mpmultivendor/store_contact');

		$this->load->model('mpmultivendor/mv_seller');


		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');			
		}


		$data['contact_seller_title'] = $this->language->get('contact_seller_title');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email_address'] = $this->language->get('entry_email_address');
		$data['entry_message'] = $this->language->get('entry_message');

		$data['button_send_message'] = $this->language->get('button_send_message');


		$data['name'] = $this->customer->getFirstName() .' '. $this->customer->getLastName();
		$data['customer_email'] = $this->customer->getEmail();

		if (isset($this->request->get['mpseller_id'])) {
			$data['mpseller_id'] = $mpseller_id = (int)$this->request->get['mpseller_id'];
		} else if (isset($this->request->get['review_mpseller_id'])) {
			$data['mpseller_id'] = $mpseller_id = (int)$this->request->get['review_mpseller_id'];
		} else {
			$data['mpseller_id'] = $mpseller_id = 0;
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
			$this->load->model('catalog/product');
			$product_info = $this->model_catalog_product->getProduct($product_id);
			if($product_info) {
				$data['mpseller_id'] = $mpseller_id = $product_info['mpseller_id'];
			}
		}

		$seller_info = $this->model_mpmultivendor_mv_seller->getSellerStoreInfo($mpseller_id);

		if (!empty($seller_info['status']) && !empty($seller_info['approved'])) {


			if(VERSION < '2.2.0.0') {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mpmultivendor/store_contact.tpl')) {
			    	return $this->load->view($this->config->get('config_template') . '/template/mpmultivendor/store_contact.tpl', $data);
			   } else {
			   		return $this->load->view('default/template/mpmultivendor/store_contact.tpl', $data);
			   }
		  	} else {
			   return $this->load->view('mpmultivendor/store_contact', $data);
			}
		}
	}

	public function sendEnquiry() {
		$json = array();

		$this->load->model('mpmultivendor/mv_seller');

		$this->load->language('mpmultivendor/store_contact');

		if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen(trim($this->request->post['name'])) > 32)) {
			$json['error']['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$json['error']['email'] = $this->language->get('error_email');
		}

		if (utf8_strlen(trim($this->request->post['message'])) < 10) {
			$json['error']['message'] = $this->language->get('error_message');
		}

		if (isset($this->request->get['mpseller_id'])) {
			$mpseller_id = (int)$this->request->get['mpseller_id'];
		} else if (isset($this->request->get['review_mpseller_id'])) {
			$mpseller_id = (int)$this->request->get['review_mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		$seller_info = $this->model_mpmultivendor_mv_seller->getSellerStoreInfo($mpseller_id);

		if(!$seller_info) {
			$json['error']['warning'] = $this->language->get('error_seller_notfound');
		}

		if(!$json) {
			$enquiry_data = array(
				'mpseller_id'	=> $mpseller_id,
				'name'			=> $this->request->post['name'],
				'email'			=> $this->request->post['email'],
				'message'		=> $this->request->post['message'],
			);
			
		 	$this->model_mpmultivendor_mv_seller->sendEnquiry($enquiry_data);

		 	$json['success'] = $this->language->get('success_send_enquiry');
		}


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
	}
}