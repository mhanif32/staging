<?php
class ControllerMpmultivendorMvSeller extends Controller {
	public function index() {
		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('common/home', '', true));
		}
		
		$this->load->language('mpmultivendor/mv_seller');
		
		$this->load->model('mpmultivendor/mv_seller');
		
		$this->load->model('tool/image');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('mpmultivendor/mv_seller', '', true)
		);

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_empty'] = $this->language->get('text_empty');

		$data['display_seller_name'] = $this->config->get('mpmultivendor_seller_name');
		$data['display_seller_email'] = $this->config->get('mpmultivendor_seller_email');
		$data['display_seller_telephone'] = $this->config->get('mpmultivendor_seller_telephone');
		$data['display_seller_address'] = $this->config->get('mpmultivendor_seller_address');
		$data['display_seller_image'] = $this->config->get('mpmultivendor_seller_image');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';


		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$filter_data = array(
			'start' => ($page - 1) * $this->config->get('mpmultivendor_store_list'),
			'limit' => $this->config->get('mpmultivendor_store_list')
		);

		$seller_store_total = $this->model_mpmultivendor_mv_seller->getTotalSellerStores($filter_data);
		$sellers = $this->model_mpmultivendor_mv_seller->getSellerStores($filter_data);

		$data['sellers'] = array();

		foreach($sellers as $seller) {
			if($seller['image']) {
				$image = $this->model_tool_image->resize($seller['image'], $this->config->get('mpmultivendor_profile_image_width_listing'), $this->config->get('mpmultivendor_profile_image_height_listing'));
			} else {
				$image = $this->model_tool_image->resize('no_image.png', $this->config->get('mpmultivendor_profile_image_width_listing'), $this->config->get('mpmultivendor_profile_image_height_listing'));
			}
			
			if($seller['banner']) {
				$banner = $this->model_tool_image->resize($seller['banner'], 500, 300);
			} else {
				$banner = $this->model_tool_image->resize('no_image.png', 500, 400);
			}

			$social_icons = array();
			if($seller['facebook_url']) {
				$social_icons[] = array(
					'class' 			=> 'fb',
					'icon_class' 		=> 'fa-facebook-square',
					'url' 		=> $seller['facebook_url'],
				);
			}

			if($seller['google_plus_url']) {
				$social_icons[] = array(
					'class' 			=> 'gp',
					'icon_class' 		=> 'fa-google-plus-square',
					'url' 				=> $seller['google_plus_url'],
				);
			}

			if($seller['twitter_url']) {
				$social_icons[] = array(
					'class' 			=> 'tw',
					'icon_class' 		=> 'fa-twitter-square',
					'url' 				=> $seller['twitter_url'],
				);
			}

			if($seller['pinterest_url']) {
				$social_icons[] = array(
					'class' 			=> 'pr',
					'icon_class' 		=> 'fa-pinterest',
					'url' 				=> $seller['pinterest_url'],
				);
			}

			if($seller['linkedin_url']) {
				$social_icons[] = array(
					'class' 			=> 'ld',
					'icon_class' 		=> 'fa-linkedin',
					'url' 				=> $seller['linkedin_url'],
				);
			}

			if($seller['youtube_url']) {
				$social_icons[] = array(
					'class' 			=> 'yt',
					'icon_class' 		=> 'fa-youtube',
					'url' 				=> $seller['youtube_url'],
				);
			}

			if($seller['instagram_url']) {
				$social_icons[] = array(
					'class' 			=> 'ig',
					'icon_class' 		=> 'fa-instagram',
					'url' 				=> $seller['instagram_url'],
				);
			}

			if($seller['flickr_url']) {
				$social_icons[] = array(
					'class' 			=> 'fl',
					'icon_class' 		=> 'fa-flickr',
					'url' 				=> $seller['flickr_url'],
				);
			}

			$data['sellers'][] = array(
				'mpseller_id'	=> $seller['mpseller_id'],
				'store_name'	=> $seller['store_name'],
				'image'			=> $image,
				'banner'		=> $banner,
				'store_owner'	=> $seller['store_owner'],
				'email'			=> $seller['email'],
				'telephone'		=> $seller['telephone'],
				'address'		=> $seller['address'],
				'rating'		=> $seller['rating'],
				'social_icons'	=> $social_icons,
				'href'			=> $this->url->link('mpmultivendor/store', 'mpseller_id='. $seller['mpseller_id'], true),
			);
		}

		$url = '';

		$pagination = new Pagination();
		$pagination->total = $seller_store_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('mpmultivendor_store_list');
		$pagination->url = $this->url->link('mpmultivendor/mv_seller', '' . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($seller_store_total) ? (($page - 1) * $this->config->get('mpmultivendor_store_list')) + 1 : 0, ((($page - 1) * $this->config->get('mpmultivendor_store_list')) > ($seller_store_total - $this->config->get('mpmultivendor_store_list'))) ? $seller_store_total : ((($page - 1) * $this->config->get('mpmultivendor_store_list')) + $this->config->get('mpmultivendor_store_list')), $seller_store_total, ceil($seller_store_total / $this->config->get('mpmultivendor_store_list')));
		
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
		
		$this->response->setOutput($this->load->view('mpmultivendor/mv_seller', $data));
	} 
}