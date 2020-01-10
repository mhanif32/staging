<?php
class ControllerExtensionModuleMpsellerAccount extends Controller {
	public function index() {
		if($this->config->get('mpmultivendor_status') && $this->customer->isLogged()) {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

			$this->load->language('extension/module/mpseller_account');

			$this->load->model('account/customer');

			$this->load->model('account/mpmultivendor/seller');

			$data['applyseller_page'] = $this->config->get('mpmultivendor_applyseller_page');

			$data['text_enabled_seller'] = $this->language->get('text_enabled_seller');
			$data['text_profile'] = $this->language->get('text_profile');
			$data['text_dashboard'] = $this->language->get('text_dashboard');
			$data['text_profile'] = $this->language->get('text_profile');
			$data['text_product'] = $this->language->get('text_product');
			$data['text_download'] = $this->language->get('text_download');
			$data['text_orders'] = $this->language->get('text_orders');
			$data['text_reviews'] = $this->language->get('text_reviews');
			$data['text_commission'] = $this->language->get('text_commission');
			$data['text_payment'] = $this->language->get('text_payment');
			$data['text_information_section'] = $this->language->get('text_information_section');
			$data['text_store_information'] = $this->language->get('text_store_information');
			$data['text_store_setting'] = $this->language->get('text_store_setting');
			$data['text_visit_store'] = $this->language->get('text_visit_store');
			$data['text_enquiries'] = $this->language->get('text_enquiries');
			$data['text_seller_message'] = $this->language->get('text_seller_message');

			$data['seller_dashboard'] = $this->url->link('account/mpmultivendor/dashboard', '', true);
			$data['seller_profile'] = $this->url->link('account/edit', '', true);
			$data['seller_product'] = $this->url->link('account/mpmultivendor/product', '', true);
			$data['seller_download'] = $this->url->link('account/mpmultivendor/download', '', true);
			$data['seller_orders'] = $this->url->link('account/mpmultivendor/orders', '', true);
			$data['seller_reviews'] = $this->url->link('account/mpmultivendor/reviews', '', true);
			$data['seller_commission'] = $this->url->link('account/mpmultivendor/commission', '', true);
			$data['seller_payment'] = $this->url->link('account/mpmultivendor/payment', '', true);
			$data['seller_information_section'] = $this->url->link('account/mpmultivendor/information_section', '', true);
			$data['seller_store_info'] = $this->url->link('account/mpmultivendor/store_info', '', true);
			$data['seller_store_setting'] = $this->url->link('account/mpmultivendor/store_setting', '', true);
			$data['seller_enquiries'] = $this->url->link('account/mpmultivendor/enquiries', '', true);
			$data['seller_message'] = $this->url->link('account/mpmultivendor/message', '', true);

			$customer_info = $this->model_account_customer->getCustomer($this->customer->isLogged());
			$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

			if(!empty($seller_info)) {
				$data['has_seller_profile'] = true;
				$data['seller_visit_store'] = $this->url->link('mpmultivendor/store', 'mpseller_id='. $seller_info['mpseller_id'], true);
			} else {
				$data['has_seller_profile'] = false;
				$data['seller_visit_store'] = '';
			}

			$data['seller_dashboard_class'] = '';
			$data['seller_edit_class'] = '';
			$data['seller_product_class'] = '';
			$data['seller_download_class'] = '';
			$data['seller_order_class'] = '';
			$data['seller_reviews_class'] = '';
			$data['seller_commission_class'] = '';
			$data['seller_payment_class'] = '';
			$data['seller_information_section_class'] = '';
			$data['seller_store_info_class'] = '';
			$data['seller_store_setting_class'] = '';
			$data['seller_enquiries_class'] = '';
			$data['seller_message_class'] = '';

			if(isset($this->request->get['route']) && $this->request->get['route'] == 'account/mpmultivendor/dashboard') {
				$data['seller_dashboard_class'] = 'active';
			}

			if(isset($this->request->get['route']) && $this->request->get['route'] == 'account/edit') {
				$data['seller_edit_class'] = 'active';
			}

			if(isset($this->request->get['route']) && ($this->request->get['route'] == 'account/mpmultivendor/product' || $this->request->get['route'] == 'account/mpmultivendor/product/add' || $this->request->get['route'] == 'account/mpmultivendor/product/edit')) {
				$data['seller_product_class'] = 'active';
			}

			if(isset($this->request->get['route']) && ($this->request->get['route'] == 'account/mpmultivendor/download' || $this->request->get['route'] == 'account/mpmultivendor/download/add' || $this->request->get['route'] == 'account/mpmultivendor/download/edit')) {
				$data['seller_download_class'] = 'active';
			}

			if(isset($this->request->get['route']) && $this->request->get['route'] == 'account/mpmultivendor/orders' || $this->request->get['route'] == 'account/mpmultivendor/orders/info') {
				$data['seller_order_class'] = 'active';
			}

			if(isset($this->request->get['route']) && $this->request->get['route'] == 'account/mpmultivendor/reviews') {
				$data['seller_reviews_class'] = 'active';
			}

			if(isset($this->request->get['route']) && $this->request->get['route'] == 'account/mpmultivendor/commission') {
				$data['seller_commission_class'] = 'active';
			}

			if(isset($this->request->get['route']) && $this->request->get['route'] == 'account/mpmultivendor/payment') {
				$data['seller_payment_class'] = 'active';
			}

			if(isset($this->request->get['route']) && ($this->request->get['route'] == 'account/mpmultivendor/information_section' || $this->request->get['route'] == 'account/mpmultivendor/information_section/add' || $this->request->get['route'] == 'account/mpmultivendor/information_section/edit')) {
				$data['seller_information_section_class'] = 'active';
			}

			if(isset($this->request->get['route']) && $this->request->get['route'] == 'account/mpmultivendor/store_info') {
				$data['seller_store_info_class'] = 'active';
			}

			if(isset($this->request->get['route']) && $this->request->get['route'] == 'account/mpmultivendor/store_setting') {
				$data['seller_store_setting_class'] = 'active';
			}

			if(isset($this->request->get['route']) && $this->request->get['route'] == 'account/mpmultivendor/enquiries') {
				$data['seller_enquiries_class'] = 'active';
			}

			if(isset($this->request->get['route']) && $this->request->get['route'] == 'account/mpmultivendor/message') {
				$data['seller_message_class'] = 'active';
			}

			// Total unread Messages
			if($seller_info) {
				$this->load->model('account/mpmultivendor/message');
				$data['unreads_messages'] = $this->model_account_mpmultivendor_message->getTotalUnreadMessages($seller_info['mpseller_id']);
			} else {
				$data['unreads_messages'] = '';
			}

			return $this->load->view('extension/module/mpseller_account', $data);
		}
	}
}