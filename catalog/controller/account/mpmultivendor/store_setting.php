<?php
class ControllerAccountMpmultivendorStoreSetting extends Controller {
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

		$this->load->language('account/mpmultivendor/store_setting');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');			
		}
		
		$this->document->addScript('catalog/view/javascript/mpseller/seller.js');
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/mpmultivendor/seller');

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());
		
		if(!$seller_info) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		if(!empty($seller_info)) {
			$mpseller_id = $seller_info['mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_account_mpmultivendor_seller->addSellerStoreSetting($this->request->post, $mpseller_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('account/mpmultivendor/store_setting'));
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['payment_type'])) {
			$data['error_payment_type'] = $this->error['payment_type'];
		} else {
			$data['error_payment_type'] = '';
		}

		if (isset($this->error['paypal_email'])) {
			$data['error_paypal_email'] = $this->error['paypal_email'];
		} else {
			$data['error_paypal_email'] = '';
		}

		if (isset($this->error['bank_details'])) {
			$data['error_bank_details'] = $this->error['bank_details'];
		} else {
			$data['error_bank_details'] = '';
		}

		if (isset($this->error['cheque_payee_name'])) {
			$data['error_cheque_payee_name'] = $this->error['cheque_payee_name'];
		} else {
			$data['error_cheque_payee_name'] = '';
		}

		if (isset($this->error['shipping_amount'])) {
			$data['error_shipping_amount'] = $this->error['shipping_amount'];
		} else {
			$data['error_shipping_amount'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

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
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/store_setting', '', true)
		);

		$data['action'] = $this->url->link('account/mpmultivendor/store_setting', '', true);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['entry_payment_type'] = $this->language->get('entry_payment_type');
		$data['entry_paypal_email'] = $this->language->get('entry_paypal_email');
		$data['entry_bank_details'] = $this->language->get('entry_bank_details');
		$data['entry_cheque_payee'] = $this->language->get('entry_cheque_payee');
		$data['entry_shipping_type'] = $this->language->get('entry_shipping_type');
		$data['entry_shipping_amount'] = $this->language->get('entry_shipping_amount');

		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_paypal'] = $this->language->get('text_paypal');
		$data['text_bank_transfer'] = $this->language->get('text_bank_transfer');
		$data['text_cheque'] = $this->language->get('text_cheque');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_order_wise'] = $this->language->get('text_order_wise');
		$data['text_product_wise'] = $this->language->get('text_product_wise');

		$data['tab_shipping'] = $this->language->get('tab_shipping');
		$data['tab_payment'] = $this->language->get('tab_payment');
	
		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->getId());
		
		if(isset($this->request->post['shipping_type'])) {
			$data['shipping_type'] = $this->request->post['shipping_type'];
		} else if($seller_info['shipping_type']) {
			$data['shipping_type'] = $seller_info['shipping_type'];
		} else {
			$data['shipping_type'] = 'order_wise';
		}

		if(isset($this->request->post['shipping_amount'])) {
			$data['shipping_amount'] = $this->request->post['shipping_amount'];
		} else if($seller_info['shipping_amount']) {
			$data['shipping_amount'] = $seller_info['shipping_amount'];
		} else {
			$data['shipping_amount'] = 0.00;
		}

		if(isset($this->request->post['payment_type'])) {
			$data['payment_type'] = $this->request->post['payment_type'];
		} else if($seller_info['payment_type']) {
			$data['payment_type'] = $seller_info['payment_type'];
		} else {
			$data['payment_type'] = 'paypal';
		}

		if(isset($this->request->post['paypal_email'])) {
			$data['paypal_email'] = $this->request->post['paypal_email'];
		} else if($seller_info) {
			$data['paypal_email'] = $seller_info['paypal_email'];
		} else {
			$data['paypal_email'] = '';
		}

		if(isset($this->request->post['bank_details'])) {
			$data['bank_details'] = $this->request->post['bank_details'];
		} else if($seller_info) {
			$data['bank_details'] = $seller_info['bank_details'];
		} else {
			$data['bank_details'] = '';
		}

		if(isset($this->request->post['cheque_payee_name'])) {
			$data['cheque_payee_name'] = $this->request->post['cheque_payee_name'];
		} else if($seller_info) {
			$data['cheque_payee_name'] = $seller_info['cheque_payee_name'];
		} else {
			$data['cheque_payee_name'] = '';
		}

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
		if(VERSION < '2.2.0.0') {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/mpmultivendor/store_setting.tpl')) {
		    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/mpmultivendor/store_setting.tpl', $data));
		   } else {
		   		$this->response->setOutput($this->load->view('default/template/account/mpmultivendor/store_setting.tpl', $data));
		   }
	  	} else {
		   $this->response->setOutput($this->load->view('account/mpmultivendor/store_setting', $data));
		}
	}

	private function validate() {
		if (!empty($this->request->post['payment_type'])) {
			if ($this->request->post['payment_type'] == 'paypal') {
				if ((utf8_strlen($this->request->post['paypal_email']) > 96) || !filter_var($this->request->post['paypal_email'], FILTER_VALIDATE_EMAIL)) {
					$this->error['paypal_email'] = $this->language->get('error_paypal_email');
				}
			}

			if ($this->request->post['payment_type'] == 'bank') {
				if (empty($this->request->post['bank_details'])) {
					$this->error['bank_details'] = $this->language->get('error_bank_details');
				}
			}

			if ($this->request->post['payment_type'] == 'cheque') {
				if (empty($this->request->post['cheque_payee_name'])) {
					$this->error['cheque_payee_name'] = $this->language->get('error_cheque_payee_name');
				}
			}
		} else {
			$this->error['payment_type'] = $this->language->get('error_payment_type');
		}

		if ((int)$this->request->post['shipping_amount'] < 0) {
			$this->error['shipping_amount'] = $this->language->get('error_shipping_amount');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
}