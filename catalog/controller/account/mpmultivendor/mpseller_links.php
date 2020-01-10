<?php
class ControllerAccountMpmultivendorMpsellerLinks extends Controller {
	public function index() {
		if($this->config->get('mpmultivendor_status') && $this->customer->isLogged()) {			
			$this->load->language('account/mpmultivendor/mpseller_links');
			
			$this->load->model('account/customer');

			$this->load->model('account/mpmultivendor/seller');

			$data['applyseller_page'] = $this->config->get('mpmultivendor_applyseller_page');

			$data['text_seller_menus'] = $this->language->get('text_seller_menus');
			$data['text_enabled_seller'] = $this->language->get('text_enabled_seller');
			$data['text_profile'] = $this->language->get('text_profile');
			$data['text_dashboard'] = $this->language->get('text_dashboard');
			$data['text_profile'] = $this->language->get('text_profile');
			$data['text_product'] = $this->language->get('text_product');
			$data['text_orders'] = $this->language->get('text_orders');
			$data['text_reviews'] = $this->language->get('text_reviews');
			$data['text_commission'] = $this->language->get('text_commission');
			$data['text_payment'] = $this->language->get('text_payment');
			$data['text_information_section'] = $this->language->get('text_information_section');
			$data['text_store_information'] = $this->language->get('text_store_information');
			$data['text_store_setting'] = $this->language->get('text_store_setting');
			$data['text_visit_store'] = $this->language->get('text_visit_store');
			$data['text_enquiries'] = $this->language->get('text_enquiries');

			$data['seller_dashboard'] = $this->url->link('account/mpmultivendor/dashboard', '', true);
			$data['seller_profile'] = $this->url->link('account/edit', '', true);
			$data['seller_product'] = $this->url->link('account/mpmultivendor/product', '', true);
			$data['seller_orders'] = $this->url->link('account/mpmultivendor/orders', '', true);
			$data['seller_reviews'] = $this->url->link('account/mpmultivendor/reviews', '', true);
			$data['seller_commission'] = $this->url->link('account/mpmultivendor/commission', '', true);
			$data['seller_payment'] = $this->url->link('account/mpmultivendor/payment', '', true);
			$data['seller_information_section'] = $this->url->link('account/mpmultivendor/information_section', '', true);
			$data['seller_store_info'] = $this->url->link('account/mpmultivendor/store_info', '', true);
			$data['seller_store_setting'] = $this->url->link('account/mpmultivendor/store_setting', '', true);
			$data['seller_enquiries'] = $this->url->link('account/mpmultivendor/enquiries', '', true);

			$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

			if(!empty($seller_info)) {
				$data['has_seller_profile'] = true;
				$data['seller_visit_store'] = $this->url->link('mpmultivendor/store', 'mpseller_id='. $seller_info['mpseller_id'], true);
			} else {
				$data['has_seller_profile'] = false;
				$data['seller_visit_store'] = '';
			}
		
			if($data['has_seller_profile']) {
				if(VERSION < '2.2.0.0') {
					if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/mpmultivendor/mpseller_links.tpl')) {
				    	return $this->load->view($this->config->get('config_template') . '/template/account/mpmultivendor/mpseller_links.tpl', $data);
				   } else {
				   		return $this->load->view('default/template/account/mpmultivendor/mpseller_links.tpl', $data);
				   }
			  	} else {
				   return $this->load->view('account/mpmultivendor/mpseller_links', $data);
				}
			}
		}
	}
}