<?php
class ControllerMpmultivendorMpmultivendor extends Controller {
	private $error = array();

	public function index() {
		// Add Databse Table For Multivendor Extension
		$this->addDatabseTableForMultivendorExtension();

		$this->document->addStyle('view/stylesheet/mpmultivendor/mpmultivendor.css');

		if(isset($this->request->get['store_id'])) {
			$data['store_id'] = $this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}

		$this->load->language('mpmultivendor/mpmultivendor');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('mpmultivendor', $this->request->post, $data['store_id']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('mpmultivendor/mpmultivendor', 'user_token=' . $this->session->data['user_token'].'&store_id='. $data['store_id'], true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_image_size'] = $this->language->get('tab_image_size');
		$data['tab_itemsperpage'] = $this->language->get('tab_itemsperpage');
		$data['tab_info'] = $this->language->get('tab_info');
		$data['tab_modulepoints'] = $this->language->get('tab_modulepoints');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_applyseller_page'] = $this->language->get('entry_applyseller_page');
		$data['entry_received_commission_status'] = $this->language->get('entry_received_commission_status');
		$data['entry_commission_rate'] = $this->language->get('entry_commission_rate');
		$data['entry_autoapproved_seller'] = $this->language->get('entry_autoapproved_seller');
		$data['entry_restrict_orderstatus'] = $this->language->get('entry_restrict_orderstatus');
		$data['entry_limit_seller'] = $this->language->get('entry_limit_seller');
		$data['entry_limit_store'] = $this->language->get('entry_limit_store');
		$data['entry_limit_store_product'] = $this->language->get('entry_limit_store_product');
		$data['entry_limit_store_review'] = $this->language->get('entry_limit_store_review');
		$data['entry_main_banner_size'] = $this->language->get('entry_main_banner_size');
		$data['entry_store_logo_size'] = $this->language->get('entry_store_logo_size');
		$data['entry_profile_image_size'] = $this->language->get('entry_profile_image_size');
		$data['entry_main_banner_size_listing'] = $this->language->get('entry_main_banner_size_listing');
		$data['entry_profile_image_size_listing'] = $this->language->get('entry_profile_image_size_listing');
		$data['entry_seller_name'] = $this->language->get('entry_seller_name');
		$data['entry_seller_email'] = $this->language->get('entry_seller_email');
		$data['entry_seller_telephone'] = $this->language->get('entry_seller_telephone');
		$data['entry_seller_image'] = $this->language->get('entry_seller_image');
		$data['entry_seller_address'] = $this->language->get('entry_seller_address');
		$data['entry_seller_changereview'] = $this->language->get('entry_seller_changereview');

		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');

		$data['legend_seller_info'] = $this->language->get('legend_seller_info');
		$data['legend_seller_listing'] = $this->language->get('legend_seller_listing');

		$data['help_restrict_orderstatus'] = $this->language->get('help_restrict_orderstatus');
		$data['help_received_commission_status'] = $this->language->get('help_received_commission_status');
		$data['help_commission_rate'] = $this->language->get('help_commission_rate');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_show'] = $this->language->get('text_show');
		$data['text_hide'] = $this->language->get('text_hide');

		$data['button_savechanges'] = $this->language->get('button_savechanges');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_store'] = $this->language->get('text_store');
		$data['text_store'] = $this->language->get('text_store');

		$data['text_default'] = $this->language->get('text_default');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['commission_rate'])) {
			$data['error_commission_rate'] = $this->error['commission_rate'];
		} else {
			$data['error_commission_rate'] = '';
		}

		if (isset($this->error['restrict_orderstatus'])) {
			$data['error_restrict_orderstatus'] = $this->error['restrict_orderstatus'];
		} else {
			$data['error_restrict_orderstatus'] = '';
		}

		if (isset($this->error['limit_seller'])) {
			$data['error_limit_seller'] = $this->error['limit_seller'];
		} else {
			$data['error_limit_seller'] = '';
		}

		if (isset($this->error['limit_store'])) {
			$data['error_limit_store'] = $this->error['limit_store'];
		} else {
			$data['error_limit_store'] = '';
		}

		if (isset($this->error['limit_store_product'])) {
			$data['error_limit_store_product'] = $this->error['limit_store_product'];
		} else {
			$data['error_limit_store_product'] = '';
		}

		if (isset($this->error['limit_store_review'])) {
			$data['error_limit_store_review'] = $this->error['limit_store_review'];
		} else {
			$data['error_limit_store_review'] = '';
		}


		if (isset($this->error['main_banner_size'])) {
			$data['error_main_banner_size'] = $this->error['main_banner_size'];
		} else {
			$data['error_main_banner_size'] = '';
		}


		if (isset($this->error['store_logo_size'])) {
			$data['error_store_logo_size'] = $this->error['store_logo_size'];
		} else {
			$data['error_store_logo_size'] = '';
		}

		if (isset($this->error['profile_image_size'])) {
			$data['error_profile_image_size'] = $this->error['profile_image_size'];
		} else {
			$data['error_profile_image_size'] = '';
		}

		if (isset($this->error['main_banner_size_listing'])) {
			$data['error_main_banner_size_listing'] = $this->error['main_banner_size_listing'];
		} else {
			$data['error_main_banner_size_listing'] = '';
		}

		if (isset($this->error['profile_image_size_listing'])) {
			$data['error_profile_image_size_listing'] = $this->error['profile_image_size_listing'];
		} else {
			$data['error_profile_image_size_listing'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('mpmultivendor/mpmultivendor', 'user_token=' . $this->session->data['user_token'], true)
		);

		if(isset($data['store_id'])) {
			$data['action'] = $this->url->link('mpmultivendor/mpmultivendor', 'user_token=' . $this->session->data['user_token'].'&store_id='. $data['store_id'], true);
		} else {
			$data['action'] = $this->url->link('mpmultivendor/mpmultivendor', 'user_token=' . $this->session->data['user_token'], true);
		}

		$data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);

		$module_info = $this->model_setting_setting->getSetting('mpmultivendor', $data['store_id']);

		if (isset($this->request->post['mpmultivendor_status'])) {
			$data['mpmultivendor_status'] = $this->request->post['mpmultivendor_status'];
		} else if(isset($module_info['mpmultivendor_status'])) {
			$data['mpmultivendor_status'] = $module_info['mpmultivendor_status'];
		} else {
			$data['mpmultivendor_status'] = '';
		}

		if (isset($this->request->post['mpmultivendor_applyseller_page'])) {
			$data['mpmultivendor_applyseller_page'] = $this->request->post['mpmultivendor_applyseller_page'];
		} else if(isset($module_info['mpmultivendor_applyseller_page'])) {
			$data['mpmultivendor_applyseller_page'] = $module_info['mpmultivendor_applyseller_page'];
		} else {
			$data['mpmultivendor_applyseller_page'] = '';
		}

		if (isset($this->request->post['mpmultivendor_received_commission_status_id'])) {
			$data['mpmultivendor_received_commission_status_id'] = $this->request->post['mpmultivendor_received_commission_status_id'];
		} else if(isset($module_info['mpmultivendor_received_commission_status_id'])) {
			$data['mpmultivendor_received_commission_status_id'] = $module_info['mpmultivendor_received_commission_status_id'];
		} else {
			$data['mpmultivendor_received_commission_status_id'] = '';
		}

		if (isset($this->request->post['mpmultivendor_restrict_orderstatus'])) {
			$data['mpmultivendor_restrict_orderstatus'] = $this->request->post['mpmultivendor_restrict_orderstatus'];
		} else if(isset($module_info['mpmultivendor_restrict_orderstatus'])) {
			$data['mpmultivendor_restrict_orderstatus'] = $this->config->get('mpmultivendor_restrict_orderstatus');
		} else {
			$data['mpmultivendor_restrict_orderstatus'] = array();
		}

		if (isset($this->request->post['mpmultivendor_commission_rate'])) {
			$data['mpmultivendor_commission_rate'] = $this->request->post['mpmultivendor_commission_rate'];
		} else if(isset($module_info['mpmultivendor_commission_rate'])) {
			$data['mpmultivendor_commission_rate'] = $module_info['mpmultivendor_commission_rate'];
		} else {
			$data['mpmultivendor_commission_rate'] = 10;
		}

		if (isset($this->request->post['mpmultivendor_autoapproved_seller'])) {
			$data['mpmultivendor_autoapproved_seller'] = $this->request->post['mpmultivendor_autoapproved_seller'];
		} else if(isset($module_info['mpmultivendor_autoapproved_seller'])) {
			$data['mpmultivendor_autoapproved_seller'] = $module_info['mpmultivendor_autoapproved_seller'];
		} else {
			$data['mpmultivendor_autoapproved_seller'] = '';
		}

		if (isset($this->request->post['mpmultivendor_seller_name'])) {
			$data['mpmultivendor_seller_name'] = $this->request->post['mpmultivendor_seller_name'];
		} else if(isset($module_info['mpmultivendor_seller_name'])) {
			$data['mpmultivendor_seller_name'] = $module_info['mpmultivendor_seller_name'];
		} else {
			$data['mpmultivendor_seller_name'] = 1;
		}

		if (isset($this->request->post['mpmultivendor_seller_email'])) {
			$data['mpmultivendor_seller_email'] = $this->request->post['mpmultivendor_seller_email'];
		} else if(isset($module_info['mpmultivendor_seller_email'])) {
			$data['mpmultivendor_seller_email'] = $module_info['mpmultivendor_seller_email'];
		} else {
			$data['mpmultivendor_seller_email'] = 1;
		}

		if (isset($this->request->post['mpmultivendor_seller_telephone'])) {
			$data['mpmultivendor_seller_telephone'] = $this->request->post['mpmultivendor_seller_telephone'];
		} else if(isset($module_info['mpmultivendor_seller_telephone'])) {
			$data['mpmultivendor_seller_telephone'] = $module_info['mpmultivendor_seller_telephone'];
		} else {
			$data['mpmultivendor_seller_telephone'] = 1;
		}

		if (isset($this->request->post['mpmultivendor_seller_address'])) {
			$data['mpmultivendor_seller_address'] = $this->request->post['mpmultivendor_seller_address'];
		} else if(isset($module_info['mpmultivendor_seller_address'])) {
			$data['mpmultivendor_seller_address'] = $module_info['mpmultivendor_seller_address'];
		} else {
			$data['mpmultivendor_seller_address'] = 1;
		}

		if (isset($this->request->post['mpmultivendor_seller_image'])) {
			$data['mpmultivendor_seller_image'] = $this->request->post['mpmultivendor_seller_image'];
		} else if(isset($module_info['mpmultivendor_seller_image'])) {
			$data['mpmultivendor_seller_image'] = $module_info['mpmultivendor_seller_image'];
		} else {
			$data['mpmultivendor_seller_image'] = 1;
		}

		if (isset($this->request->post['mpmultivendor_seller_list'])) {
			$data['mpmultivendor_seller_list'] = $this->request->post['mpmultivendor_seller_list'];
		} else if(!empty($module_info['mpmultivendor_seller_list'])) {
			$data['mpmultivendor_seller_list'] = $module_info['mpmultivendor_seller_list'];
		} else {
			$data['mpmultivendor_seller_list'] = 20;
		}

		if (isset($this->request->post['mpmultivendor_store_list'])) {
			$data['mpmultivendor_store_list'] = $this->request->post['mpmultivendor_store_list'];
		} else if(!empty($module_info['mpmultivendor_store_list'])) {
			$data['mpmultivendor_store_list'] = $module_info['mpmultivendor_store_list'];
		} else {
			$data['mpmultivendor_store_list'] = 20;
		}

		if (isset($this->request->post['mpmultivendor_store_list_product'])) {
			$data['mpmultivendor_store_list_product'] = $this->request->post['mpmultivendor_store_list_product'];
		} else if(!empty($module_info['mpmultivendor_store_list_product'])) {
			$data['mpmultivendor_store_list_product'] = $module_info['mpmultivendor_store_list_product'];
		} else {
			$data['mpmultivendor_store_list_product'] = 20;
		}

		if (isset($this->request->post['mpmultivendor_store_list_review'])) {
			$data['mpmultivendor_store_list_review'] = $this->request->post['mpmultivendor_store_list_review'];
		} else if(!empty($module_info['mpmultivendor_store_list_review'])) {
			$data['mpmultivendor_store_list_review'] = $module_info['mpmultivendor_store_list_review'];
		} else {
			$data['mpmultivendor_store_list_review'] = 20;
		}



		if (isset($this->request->post['mpmultivendor_main_banner_width'])) {
			$data['mpmultivendor_main_banner_width'] = $this->request->post['mpmultivendor_main_banner_width'];
		} else if(!empty($module_info['mpmultivendor_main_banner_width'])) {
			$data['mpmultivendor_main_banner_width'] = $module_info['mpmultivendor_main_banner_width'];
		} else {
			$data['mpmultivendor_main_banner_width'] = '845';
		}

		if (isset($this->request->post['mpmultivendor_main_banner_height'])) {
			$data['mpmultivendor_main_banner_height'] = $this->request->post['mpmultivendor_main_banner_height'];
		} else if(!empty($module_info['mpmultivendor_main_banner_height'])) {
			$data['mpmultivendor_main_banner_height'] = $module_info['mpmultivendor_main_banner_height'];
		} else {
			$data['mpmultivendor_main_banner_height'] = '220';
		}

		if (isset($this->request->post['mpmultivendor_store_logo_width'])) {
			$data['mpmultivendor_store_logo_width'] = $this->request->post['mpmultivendor_store_logo_width'];
		} else if(!empty($module_info['mpmultivendor_store_logo_width'])) {
			$data['mpmultivendor_store_logo_width'] = $module_info['mpmultivendor_store_logo_width'];
		} else {
			$data['mpmultivendor_store_logo_width'] = '245';
		}

		if (isset($this->request->post['mpmultivendor_store_logo_height'])) {
			$data['mpmultivendor_store_logo_height'] = $this->request->post['mpmultivendor_store_logo_height'];
		} else if(!empty($module_info['mpmultivendor_store_logo_height'])) {
			$data['mpmultivendor_store_logo_height'] = $module_info['mpmultivendor_store_logo_height'];
		} else {
			$data['mpmultivendor_store_logo_height'] = '166';
		}

		if (isset($this->request->post['mpmultivendor_profile_image_width'])) {
			$data['mpmultivendor_profile_image_width'] = $this->request->post['mpmultivendor_profile_image_width'];
		} else if(!empty($module_info['mpmultivendor_profile_image_width'])) {
			$data['mpmultivendor_profile_image_width'] = $module_info['mpmultivendor_profile_image_width'];
		} else {
			$data['mpmultivendor_profile_image_width'] = '80';
		}

		if (isset($this->request->post['mpmultivendor_profile_image_height'])) {
			$data['mpmultivendor_profile_image_height'] = $this->request->post['mpmultivendor_profile_image_height'];
		} else if(!empty($module_info['mpmultivendor_profile_image_height'])) {
			$data['mpmultivendor_profile_image_height'] = $module_info['mpmultivendor_profile_image_height'];
		} else {
			$data['mpmultivendor_profile_image_height'] = '80';
		}

		if (isset($this->request->post['mpmultivendor_main_banner_width_listing'])) {
			$data['mpmultivendor_main_banner_width_listing'] = $this->request->post['mpmultivendor_main_banner_width_listing'];
		} else if(!empty($module_info['mpmultivendor_main_banner_width_listing'])) {
			$data['mpmultivendor_main_banner_width_listing'] = $module_info['mpmultivendor_main_banner_width_listing'];
		} else {
			$data['mpmultivendor_main_banner_width_listing'] = '385';
		}

		if (isset($this->request->post['mpmultivendor_main_banner_height_listing'])) {
			$data['mpmultivendor_main_banner_height_listing'] = $this->request->post['mpmultivendor_main_banner_height_listing'];
		} else if(!empty($module_info['mpmultivendor_main_banner_height_listing'])) {
			$data['mpmultivendor_main_banner_height_listing'] = $module_info['mpmultivendor_main_banner_height_listing'];
		} else {
			$data['mpmultivendor_main_banner_height_listing'] = '100';
		}


		if (isset($this->request->post['mpmultivendor_profile_image_width_listing'])) {
			$data['mpmultivendor_profile_image_width_listing'] = $this->request->post['mpmultivendor_profile_image_width_listing'];
		} else if(!empty($module_info['mpmultivendor_profile_image_width_listing'])) {
			$data['mpmultivendor_profile_image_width_listing'] = $module_info['mpmultivendor_profile_image_width_listing'];
		} else {
			$data['mpmultivendor_profile_image_width_listing'] = '80';
		}

		if (isset($this->request->post['mpmultivendor_profile_image_height_listing'])) {
			$data['mpmultivendor_profile_image_height_listing'] = $this->request->post['mpmultivendor_profile_image_height_listing'];
		} else if(!empty($module_info['mpmultivendor_profile_image_height_listing'])) {
			$data['mpmultivendor_profile_image_height_listing'] = $module_info['mpmultivendor_profile_image_height_listing'];
		} else {
			$data['mpmultivendor_profile_image_height_listing'] = '80';
		}

		if (isset($this->request->post['mpmultivendor_seller_changereview'])) {
			$data['mpmultivendor_seller_changereview'] = $this->request->post['mpmultivendor_seller_changereview'];
		} else if(isset($module_info['mpmultivendor_seller_changereview'])) {
			$data['mpmultivendor_seller_changereview'] = $module_info['mpmultivendor_seller_changereview'];
		} else {
			$data['mpmultivendor_seller_changereview'] = 1;
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

		$store_info = $this->model_setting_store->getStore($data['store_id']);

		if($store_info) {
			$data['store_name'] = $store_info['name'];
		} else {
			$data['store_name'] = $this->language->get('text_default');
		}

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->config->set('template_engine', 'template');
		$this->response->setOutput($this->load->view('mpmultivendor/mpmultivendor', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'mpmultivendor/mpmultivendor')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((int)$this->request->post['mpmultivendor_commission_rate'] < 0) {
			$this->error['commission_rate'] = $this->language->get('error_commission_rate');
		}

		if (!isset($this->request->post['mpmultivendor_restrict_orderstatus'])) {
			$this->error['restrict_orderstatus'] = $this->language->get('error_restrict_orderstatus');
		}

		// Per Page Limits
		if (empty($this->request->post['mpmultivendor_seller_list']) || (int)$this->request->post['mpmultivendor_seller_list'] < 1) {
			$this->error['limit_seller'] = $this->language->get('error_limit_seller');
		}

		if (empty($this->request->post['mpmultivendor_store_list']) || (int)$this->request->post['mpmultivendor_store_list'] < 1) {
			$this->error['limit_store'] = $this->language->get('error_limit_store');
		}

		if (empty($this->request->post['mpmultivendor_store_list_product']) || (int)$this->request->post['mpmultivendor_store_list_product'] < 1) {
			$this->error['limit_store_product'] = $this->language->get('error_limit_store_product');
		}

		if (empty($this->request->post['mpmultivendor_store_list_review']) || (int)$this->request->post['mpmultivendor_store_list_review'] < 1) {
			$this->error['limit_store_review'] = $this->language->get('error_limit_store_review');
		}

		if (!$this->request->post['mpmultivendor_main_banner_width'] || !$this->request->post['mpmultivendor_main_banner_height']) {
			$this->error['main_banner_size'] = $this->language->get('error_main_banner_size');
		}

		if (!$this->request->post['mpmultivendor_store_logo_width'] || !$this->request->post['mpmultivendor_store_logo_height']) {
			$this->error['store_logo_size'] = $this->language->get('error_store_logo_size');
		}

		if (!$this->request->post['mpmultivendor_profile_image_width'] || !$this->request->post['mpmultivendor_profile_image_height']) {
			$this->error['profile_image_size'] = $this->language->get('error_profile_image_size');
		}

		if (!$this->request->post['mpmultivendor_main_banner_width_listing'] || !$this->request->post['mpmultivendor_main_banner_height_listing']) {
			$this->error['main_banner_size_listing'] = $this->language->get('error_main_banner_size');
		}

		if (!$this->request->post['mpmultivendor_profile_image_width_listing'] || !$this->request->post['mpmultivendor_profile_image_height_listing']) {
			$this->error['profile_image_size_listing'] = $this->language->get('error_profile_image_size');
		}


		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function addDatabseTableForMultivendorExtension() {
		if(!$this->config->has('mpmultivendor_status')) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpseller` (`mpseller_id` int(11) NOT NULL AUTO_INCREMENT, `customer_id` int(11) NOT NULL,`store_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`description` text NOT NULL, `meta_description` varchar(255) NOT NULL, `meta_keyword` varchar(255) NOT NULL, `store_owner` varchar(255) NOT NULL, `store_name` varchar(255) NOT NULL, `address` varchar(255) NOT NULL,`email` varchar(96) NOT NULL, `telephone` varchar(32) NOT NULL, `fax` varchar(32) NOT NULL,`seo_keyword` varchar(255) NOT NULL,`city` varchar(255) NOT NULL,`zone_id` int(11) NOT NULL,`country_id` int(11) NOT NULL,`image` varchar(255) NOT NULL,`logo` varchar(255) NOT NULL,`banner` varchar(255) NOT NULL,`shipping_type` varchar(255) NOT NULL COMMENT '1 = product wise, 1 = product wise, 0 = order wise',`shipping_amount` decimal(10,2) NOT NULL,`payment_type` varchar(100) NOT NULL,`paypal_email` varchar(96) NOT NULL,`bank_details` text NOT NULL,`cheque_payee_name` varchar(255) NOT NULL,`facebook_url` text NOT NULL,`google_plus_url` text NOT NULL,`twitter_url` text NOT NULL,`pinterest_url` text NOT NULL,`linkedin_url` text NOT NULL,`youtube_url` text NOT NULL,`instagram_url` text NOT NULL,`flickr_url` text NOT NULL,`approved` tinyint(4) NOT NULL,`status` tinyint(4) NOT NULL,`commission_rate` int(10) NOT NULL COMMENT 'Percent (%)',`date_added` datetime NOT NULL,PRIMARY KEY (`mpseller_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpseller_commission` (`mpseller_commission_id` int(11) NOT NULL AUTO_INCREMENT,`mpseller_id` int(11) NOT NULL,`order_product_id` int(11) NOT NULL,`product_id` int(11) NOT NULL,`order_id` int(11) NOT NULL,`amount` decimal(15,4) NOT NULL,`type` varchar(30) NOT NULL COMMENT 'sale, withdraw',`status` tinyint(4) NOT NULL COMMENT '1 = Complete, 0 = Pending',`date_added` datetime NOT NULL,`date_modified` datetime NOT NULL,PRIMARY KEY (`mpseller_commission_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");



			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpseller_enquiry` (`mpseller_enquiry_id` int(11) NOT NULL AUTO_INCREMENT,`mpseller_id` int(11) NOT NULL,`name` varchar(32) NOT NULL,`email` varchar(96) NOT NULL,`message` text NOT NULL,`date_added` datetime NOT NULL,PRIMARY KEY (`mpseller_enquiry_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpseller_information_section` (`mpseller_information_section_id` int(11) NOT NULL AUTO_INCREMENT,`mpseller_id` int(11) NOT NULL,`title` varchar(255) NOT NULL,`description` text NOT NULL,`meta_title` varchar(255) NOT NULL,`meta_description` varchar(255) NOT NULL,`meta_keyword` varchar(255) NOT NULL,`sort_order` int(11) NOT NULL,`status` tinyint(4) NOT NULL,PRIMARY KEY (`mpseller_information_section_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpseller_order_history` (`mpseller_order_history_id` int(11) NOT NULL AUTO_INCREMENT,`order_id` int(11) NOT NULL,`mpseller_id` int(11) NOT NULL,`order_status_id` int(11) NOT NULL,`notify` tinyint(1) NOT NULL DEFAULT '0',`comment` text NOT NULL,`date_added` datetime NOT NULL,PRIMARY KEY (`mpseller_order_history_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");


			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpseller_order_product` (`mpseller_order_product_id` int(11) NOT NULL AUTO_INCREMENT,`order_id` int(11) NOT NULL,`order_product_id` int(11) NOT NULL,`mpseller_id` int(11) NOT NULL,`product_id` int(11) NOT NULL,`quantity` int(11) NOT NULL,`shipping_amount` decimal(10,4) NOT NULL,`tax` decimal(15,4) NOT NULL,`price` decimal(10,4) NOT NULL,`total` decimal(10,4) NOT NULL,`order_status_id` int(11) NOT NULL,`date_added` datetime NOT NULL,`date_modified` datetime NOT NULL,PRIMARY KEY (`mpseller_order_product_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpseller_order_total` (`mpseller_order_total_id` int(10) NOT NULL AUTO_INCREMENT,`order_id` int(11) NOT NULL,`mpseller_id` int(11) NOT NULL,`code` varchar(32) NOT NULL,`title` varchar(255) NOT NULL,`value` decimal(15,4) NOT NULL DEFAULT '0.0000',`sort_order` int(3) NOT NULL,PRIMARY KEY (`mpseller_order_total_id`),KEY `order_id` (`order_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpseller_review` (`mpseller_review_id` int(11) NOT NULL AUTO_INCREMENT,`mpseller_id` int(11) NOT NULL,`title` varchar(255) NOT NULL,`description` text NOT NULL,`author` varchar(255) NOT NULL,`rating` int(11) NOT NULL,`status` tinyint(4) NOT NULL,`date_added` datetime NOT NULL,`date_modified` datetime NOT NULL,PRIMARY KEY (`mpseller_review_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");
		}

		$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpseller_enquiry_chat` (`mpseller_enquiry_chat_id` int(11) NOT NULL AUTO_INCREMENT, `mpseller_enquiry_id` int(11) NOT NULL, `from` varchar(255) NOT NULL, `mpseller_id` int(11) NOT NULL, `customer_id` int(11) NOT NULL, `message` text NOT NULL, `date_added` datetime NOT NULL, PRIMARY KEY (`mpseller_enquiry_chat_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

		$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpseller_message` (`mpseller_message_id` int(11) NOT NULL AUTO_INCREMENT, `mpseller_id` int(11) NOT NULL, `from` varchar(100) NOT NULL, `message` text NOT NULL, `read_status` int(11) NOT NULL, `date_added` datetime NOT NULL, PRIMARY KEY (`mpseller_message_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

		// Add customer_id In Enquiry Table
		$query = $this->db->query("SHOW COLUMNS FROM `". DB_PREFIX ."mpseller_enquiry` WHERE `Field` = 'customer_id'");
		if(!$query->num_rows) {
			$this->db->query("ALTER TABLE `". DB_PREFIX ."mpseller_enquiry` ADD `customer_id` INT NOT NULL AFTER `mpseller_enquiry_id`");
		}

		// Add date_modified In Enquiry Table
		$query = $this->db->query("SHOW COLUMNS FROM `". DB_PREFIX ."mpseller_enquiry` WHERE `Field` = 'date_modified'");
		if(!$query->num_rows) {
			$this->db->query("ALTER TABLE `". DB_PREFIX ."mpseller_enquiry` ADD `date_modified` datetime NOT NULL AFTER `date_added`");
		}

		// Add Seller Id In Product Table
		$query = $this->db->query("SHOW COLUMNS FROM `". DB_PREFIX ."product` WHERE `Field` = 'mpseller_id'");
		if(!$query->num_rows) {
			$this->db->query("ALTER TABLE `". DB_PREFIX ."product` ADD `mpseller_id` INT NOT NULL AFTER `date_modified`");
		}

		// Add Seller Id In Download Table
		$query = $this->db->query("SHOW COLUMNS FROM `". DB_PREFIX ."download` WHERE `Field` = 'mpseller_id'");
		if(!$query->num_rows) {
			$this->db->query("ALTER TABLE `". DB_PREFIX ."download` ADD `mpseller_id` INT NOT NULL AFTER `download_id`");
		}
	}
}