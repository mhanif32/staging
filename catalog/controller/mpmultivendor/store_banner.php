<?php
class ControllerMpmultivendorStoreBanner extends Controller {
	public function index() {
		$this->load->language('mpmultivendor/store');

		$this->load->model('mpmultivendor/mv_seller');

		$this->load->model('tool/image');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');			
		}

		if (isset($this->request->get['mpseller_id'])) {
			$mpseller_id = (int)$this->request->get['mpseller_id'];
		} else if (isset($this->request->get['review_mpseller_id'])) {
			$mpseller_id = (int)$this->request->get['review_mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		$seller_info = $this->model_mpmultivendor_mv_seller->getSellerStoreInfo($mpseller_id);

		if (!empty($seller_info['status']) && !empty($seller_info['approved'])) {
			$data['display_seller_name'] = $this->config->get('mpmultivendor_seller_name');
			$data['display_seller_email'] = $this->config->get('mpmultivendor_seller_email');
			$data['display_seller_telephone'] = $this->config->get('mpmultivendor_seller_telephone');
			$data['display_seller_address'] = $this->config->get('mpmultivendor_seller_address');
			$data['display_seller_image'] = $this->config->get('mpmultivendor_seller_image');

			$data['store_name'] = $seller_info['store_name'];
			$data['store_owner'] = $seller_info['store_owner'];
			$data['email'] = $seller_info['email'];
			$data['telephone'] = $seller_info['telephone'];
			$data['address'] = $seller_info['address'];

			if($seller_info['banner']) {
				$data['banner'] = $this->model_tool_image->resize($seller_info['banner'], 500, 300);
			} else {
				$data['banner'] = $this->model_tool_image->resize('no_image.png', 500, 300);
			}
			
			if($seller_info['image']) {
				$data['image'] = $this->model_tool_image->resize($seller_info['image'], $this->config->get('mpmultivendor_profile_image_width'), $this->config->get('mpmultivendor_profile_image_height'));
			} else {
				$data['image'] = $this->model_tool_image->resize('no_image.png', $this->config->get('mpmultivendor_profile_image_width'), $this->config->get('mpmultivendor_profile_image_height'));
			}

			$filter_data = array(
				'mpseller_id'		=> $mpseller_id,
				'filter_status'		=> 1,
			);

			$information_sections = $this->model_mpmultivendor_mv_seller->getInformationSections($filter_data);

			$data['information_sections'] = array();
			foreach($information_sections as $information_section) {
				$data['information_sections'][] = array(
					'title'			=> $information_section['title'],
					'href'		=> $this->url->link('mpmultivendor/information_page', 'page_id='. $information_section['mpseller_information_section_id'], true),
				);
			}

			if(VERSION < '2.2.0.0') {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mpmultivendor/store_banner.tpl')) {
			    	return $this->load->view($this->config->get('config_template') . '/template/mpmultivendor/store_banner.tpl', $data);
			   } else {
			   		return $this->load->view('default/template/mpmultivendor/store_banner.tpl', $data);
			   }
		  	} else {
			   return $this->load->view('mpmultivendor/store_banner', $data);
			}
		}
	}
}