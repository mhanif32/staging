<?php
class ControllerMpmultivendorInformationPage extends Controller {
	public function index() {
		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('common/home', '', true));
		}

		$this->load->language('mpmultivendor/information_page');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');			
		}

		$this->load->model('mpmultivendor/mv_seller');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home'),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('mv_seller'),
			'href' => $this->url->link('mpmultivendor/mv_seller', '', true),
		);

		if (isset($this->request->get['page_id'])) {
			$page_id = (int)$this->request->get['page_id'];
		} else {
			$page_id = 0;
		}

		$information_section_info = $this->model_mpmultivendor_mv_seller->getInformationSection($page_id);

		if ($information_section_info) {
			$this->document->setTitle($information_section_info['meta_title']);
			$this->document->setDescription($information_section_info['meta_description']);
			$this->document->setKeywords($information_section_info['meta_keyword']);

			$seller_info = $this->model_mpmultivendor_mv_seller->getSellerStoreInfo($information_section_info['mpseller_id']);
			
			if($seller_info) {
				$data['breadcrumbs'][] = array(
					'text' => $seller_info['store_name'],
					'href' => $this->url->link('mpmultivendor/store', 'mpseller_id=' .  $seller_info['mpseller_id'])
				);
			}

			$data['breadcrumbs'][] = array(
				'text' => $information_section_info['title'],
				'href' => $this->url->link('mpmultivendor/information_page', 'page_id=' .  $page_id)
			);

			$data['heading_title'] = $information_section_info['title'];

			$data['button_continue'] = $this->language->get('button_continue');

			$data['description'] = html_entity_decode($information_section_info['description'], ENT_QUOTES, 'UTF-8');

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if(VERSION < '2.2.0.0') {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mpmultivendor/information_page.tpl')) {
			    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/mpmultivendor/information_page.tpl', $data));
			   } else {
			   		$this->response->setOutput($this->load->view('default/template/mpmultivendor/information_page.tpl', $data));
			   }
		  	} else {
			   $this->response->setOutput($this->load->view('mpmultivendor/information_page', $data));
			}
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('mpmultivendor/information_page', 'page_id=' . $page_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

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

			if(VERSION < '2.2.0.0') {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
			    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/error/not_found.tpl', $data));
			   } else {
			   		$this->response->setOutput($this->load->view('default/template/error/not_found.tpl', $data));
			   }
		  	} else {
			   $this->response->setOutput($this->load->view('error/not_found', $data));
			}
		}
	}
}