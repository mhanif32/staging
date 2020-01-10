<?php
class ControllerMpmultivendorMvReviews extends Controller {
	public function index() {
		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('common/home', '', true));
		}
		
		$this->load->language('mpmultivendor/mv_reviews');

		$this->load->model('mpmultivendor/mv_seller');

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
			'text' => $this->language->get('mv_seller'),
			'href' => $this->url->link('mpmultivendor/mv_seller', '', true),
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		} 

		if (isset($this->request->get['review_mpseller_id'])) {
			$mpseller_id = (int)$this->request->get['review_mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		$seller_info = $this->model_mpmultivendor_mv_seller->getSellerStoreInfo($mpseller_id);

		if (!empty($seller_info['status']) && !empty($seller_info['approved'])) {
			$data['breadcrumbs'][] = array(
				'text' => $seller_info['store_name'],
				'href' => $this->url->link('mpmultivendor/store', 'mpseller_id='. $mpseller_id, true),
			);

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('mpmultivendor/mv_reviews', 'mpseller_id='. $mpseller_id . $url, true),
			);

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			
			$data['text_no_reviews'] = $this->language->get('text_no_reviews');

			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}

			$data['reviews'] = array();

			$review_total = $this->model_mpmultivendor_mv_seller->getTotalReviews($this->request->get['review_mpseller_id']);

			$data['title_reviews'] = sprintf($this->language->get('title_reviews'), $review_total);

			$results = $this->model_mpmultivendor_mv_seller->getReviews($this->request->get['review_mpseller_id'], ($page - 1) * $this->config->get('mpmultivendor_store_list_review'), $this->config->get('mpmultivendor_store_list_review'));

			foreach ($results as $result) {
				$data['reviews'][] = array(
					'author'     => $result['author'],
					'title'      => $result['title'],
					'description'=> nl2br($result['description']),
					'rating'     => (int)$result['rating'],
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
				);
			}

			$pagination = new Pagination();
			$pagination->total = $review_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('mpmultivendor_store_list_review');
			$pagination->url = $this->url->link('mpmultivendor/mv_reviews', 'mpseller_id=' . $this->request->get['review_mpseller_id'] . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * $this->config->get('mpmultivendor_store_list_review')) + 1 : 0, ((($page - 1) * $this->config->get('mpmultivendor_store_list_review')) > ($review_total - $this->config->get('mpmultivendor_store_list_review'))) ? $review_total : ((($page - 1) * $this->config->get('mpmultivendor_store_list_review')) + $this->config->get('mpmultivendor_store_list_review')), $review_total, ceil($review_total / $this->config->get('mpmultivendor_store_list_review')));


			$data['store_profile'] = $this->load->controller('mpmultivendor/store_profile');
			$data['store_banner'] = $this->load->controller('mpmultivendor/store_banner');

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
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mpmultivendor/mv_reviews.tpl')) {
			    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/mpmultivendor/mv_reviews.tpl', $data));
			   } else {
			   		$this->response->setOutput($this->load->view('default/template/mpmultivendor/mv_reviews.tpl', $data));
			   }
		  	} else {
			   $this->response->setOutput($this->load->view('mpmultivendor/mv_reviews', $data));
			}
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('mpmultivendor/store', 'mpseller_id=' . $mpseller_id)
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