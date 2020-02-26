<?php
class ControllerAccountMpmultivendorInformationSection extends Controller {
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

		$this->load->language('account/mpmultivendor/information_section');

		$this->load->model('account/mpmultivendor/information_section');

		$this->load->model('account/mpmultivendor/seller');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');			
		}

		$this->document->addScript('catalog/view/javascript/mpseller/seller.js');
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->getList();
	}

	public function add() {

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');			
		}
		
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$this->load->language('account/mpmultivendor/information_section');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/mpmultivendor/information_section');

		$this->load->model('account/mpmultivendor/seller');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');
		
		$this->document->addScript('catalog/view/javascript/mpseller/seller.js');

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

		if(!empty($seller_info)) {
			$mpseller_id = $seller_info['mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		if(!$seller_info) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_account_mpmultivendor_information_section->addInformationSection($this->request->post, $mpseller_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('account/mpmultivendor/information_section', '' . $url, true));
		}

		$this->getForm();
	}

	public function edit() {

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');			
		}
		
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$this->load->language('account/mpmultivendor/information_section');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/mpmultivendor/information_section');

		$this->load->model('account/mpmultivendor/seller');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');
		
		$this->document->addScript('catalog/view/javascript/mpseller/seller.js');

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

		if(!empty($seller_info)) {
			$mpseller_id = $seller_info['mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		if(!$seller_info) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_account_mpmultivendor_information_section->editInformationSection($this->request->get['mpseller_information_section_id'], $this->request->post, $mpseller_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('account/mpmultivendor/information_section', '' . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}
		
		$this->load->language('account/mpmultivendor/information_section');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/mpmultivendor/information_section');

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

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $mpseller_information_section_id) {
				$this->model_account_mpmultivendor_information_section->deleteInformationSection($mpseller_information_section_id, $mpseller_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('account/mpmultivendor/information_section', '' . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
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
			'href' => $this->url->link('account/mpmultivendor/information_section', '', true)
		);

		$data['action'] = $this->url->link('account/mpmultivendor/information_section', '', true);

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

		if(!$seller_info) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		if(!empty($seller_info)) {
			$mpseller_id = $seller_info['mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		$filter_data = array(
			'mpseller_id' 	=> $mpseller_id,
			'start' 		=> ($page - 1) * $this->config->get('mpmultivendor_seller_list'),
			'limit' 		=> $this->config->get('mpmultivendor_seller_list'),
		);

		$information_section_total = $this->model_account_mpmultivendor_information_section->getTotalInformationSections($filter_data);

		$results = $this->model_account_mpmultivendor_information_section->getInformationSections($filter_data);

		$data['information_sections'] = array();

		foreach ($results as $result) {
			$data['information_sections'][] = array(
				'mpseller_information_section_id' 	=> $result['mpseller_information_section_id'],
				'title'        				=> $result['title'],
				'status'        			=> $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'sort_order'  				=> $result['sort_order'],
				'edit'        				=> $this->url->link('account/mpmultivendor/information_section/edit', 'mpseller_information_section_id=' . $result['mpseller_information_section_id'] . $url, true),
				'delete'      				=> $this->url->link('account/mpmultivendor/information_section/delete', 'mpseller_information_section_id=' . $result['mpseller_information_section_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

		$data['add'] = $this->url->link('account/mpmultivendor/information_section/add', '' . $url, true);

		$data['delete'] = $this->url->link('account/mpmultivendor/information_section/delete', '' . $url, true);

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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $information_section_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('mpmultivendor_seller_list');
		$pagination->url = $this->url->link('account/mpmultivendor/information_section', '' . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($information_section_total) ? (($page - 1) * $this->config->get('mpmultivendor_seller_list')) + 1 : 0, ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) > ($information_section_total - $this->config->get('mpmultivendor_seller_list'))) ? $information_section_total : ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) + $this->config->get('mpmultivendor_seller_list')), $information_section_total, ceil($information_section_total / $this->config->get('mpmultivendor_seller_list')));

		
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
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/mpmultivendor/information_section_list.tpl')) {
		    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/mpmultivendor/information_section_list.tpl', $data));
		   } else {
		   		$this->response->setOutput($this->load->view('default/template/account/mpmultivendor/information_section_list.tpl', $data));
		   }
	  	} else {
		   $this->response->setOutput($this->load->view('account/mpmultivendor/information_section_list', $data));
		}
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['mpseller_information_section_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_seo_keyword'] = $this->language->get('entry_seo_keyword');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_seo'] = $this->language->get('tab_seo');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = '';
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = '';
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = array();
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '' . $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/mpmultivendor/information_section', '' . $url, true)
		);

		if (!isset($this->request->get['mpseller_information_section_id'])) {
			$data['action'] = $this->url->link('account/mpmultivendor/information_section/add', '' . $url, true);
		} else {
			$data['action'] = $this->url->link('account/mpmultivendor/information_section/edit', 'mpseller_information_section_id=' . $this->request->get['mpseller_information_section_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('account/mpmultivendor/information_section', '' . $url, true);

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

		if(!empty($seller_info)) {
			$mpseller_id = $seller_info['mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		if (isset($this->request->get['mpseller_information_section_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$information_section_info = $this->model_account_mpmultivendor_information_section->getInformationSection($this->request->get['mpseller_information_section_id'], $mpseller_id);
		}

		if (isset($this->request->post['title'])) {
			$data['title'] = $this->request->post['title'];
		} elseif (!empty($information_section_info)) {
			$data['title'] = $information_section_info['title'];
		} else {
			$data['title'] = '';
		}

		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($information_section_info)) {
			$data['description'] = $information_section_info['description'];
		} else {
			$data['description'] = '';
		}

		if (isset($this->request->post['meta_title'])) {
			$data['meta_title'] = $this->request->post['meta_title'];
		} elseif (!empty($information_section_info)) {
			$data['meta_title'] = $information_section_info['meta_title'];
		} else {
			$data['meta_title'] = '';
		}

		if (isset($this->request->post['meta_description'])) {
			$data['meta_description'] = $this->request->post['meta_description'];
		} elseif (!empty($information_section_info)) {
			$data['meta_description'] = $information_section_info['meta_description'];
		} else {
			$data['meta_description'] = '';
		}

		if (isset($this->request->post['meta_keyword'])) {
			$data['meta_keyword'] = $this->request->post['meta_keyword'];
		} elseif (!empty($information_section_info)) {
			$data['meta_keyword'] = $information_section_info['meta_keyword'];
		} else {
			$data['meta_keyword'] = '';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($information_section_info)) {
			$data['sort_order'] = $information_section_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($information_section_info)) {
			$data['status'] = $information_section_info['status'];
		} else {
			$data['status'] = true;
		}

		$this->load->model('setting/store');
		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();
		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->post['information_seo_url'])) {
			$data['information_seo_url'] = $this->request->post['information_seo_url'];
		} elseif (isset($this->request->get['mpseller_information_section_id'])) {
			$data['information_seo_url'] = $this->model_account_mpmultivendor_information_section->getInformationSeoUrls($this->request->get['mpseller_information_section_id']);
		} else {
			$data['information_seo_url'] = array();
		}
		
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

		if(empty($custom_themename)) {
			$custom_themename = 'default';
		}

		$data['custom_themename'] = $custom_themename;
		/* Theme Work Ends */

		if(VERSION < '2.2.0.0') {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/mpmultivendor/information_section_form.tpl')) {
		    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/mpmultivendor/information_section_form.tpl', $data));
		   } else {
		   		$this->response->setOutput($this->load->view('default/template/account/mpmultivendor/information_section_form.tpl', $data));
		   }
	  	} else {
		   $this->response->setOutput($this->load->view('account/mpmultivendor/information_section_form', $data));
		}
	}

	protected function validateForm() {
		if ((utf8_strlen($this->request->post['title']) < 2) || (utf8_strlen($this->request->post['title']) > 255)) {
			$this->error['title'] = $this->language->get('error_title');
		}

		if ((utf8_strlen($this->request->post['meta_title']) < 3) || (utf8_strlen($this->request->post['meta_title']) > 255)) {
			$this->error['meta_title'] = $this->language->get('error_meta_title');
		}

		if ($this->request->post['information_seo_url']) {
			foreach ($this->request->post['information_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}						
						
						$seo_urls = $this->model_account_mpmultivendor_information_section->getSeoUrlsByKeyword($keyword);
						
						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['mpseller_information_section_id']) || ($seo_url['query'] != 'page_id=' . $this->request->get['mpseller_information_section_id']))) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
							}
						}
					}
				}
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
}