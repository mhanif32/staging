<?php
class ControllerAccountMpmultivendorReviews extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$this->load->language('account/mpmultivendor/reviews');

		$this->load->model('account/mpmultivendor/reviews');

		$this->load->model('account/mpmultivendor/seller');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');			
		}
		
		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_author'])) {
			$filter_author = $this->request->get['filter_author'];
		} else {
			$filter_author = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_rating'])) {
			$filter_rating = $this->request->get['filter_rating'];
		} else {
			$filter_rating = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'date_added';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . $this->request->get['filter_rating'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

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
			'href' => $this->url->link('account/reviews', ''. $url, true)
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');		
		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_author'] = $this->language->get('column_author');
		$data['column_title'] = $this->language->get('column_title');
		$data['column_description'] = $this->language->get('column_description');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_rating'] = $this->language->get('column_rating');

		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_rating'] = $this->language->get('entry_rating');

		$data['text_all'] = $this->language->get('text_all');
		$data['text_star'] = $this->language->get('text_star');

		$data['button_filter'] = $this->language->get('button_filter');

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
			'filter_mpseller_id'	=> $mpseller_id,
			'filter_author'	  		=> $filter_author,
			'filter_date_added'		=> $filter_date_added,
			'filter_status'	  		=> $filter_status,
			'filter_rating'	  		=> $filter_rating,
			'sort'            		=> $sort,
			'order'           		=> $order,
			'start'           		=> ($page - 1) * $this->config->get('mpmultivendor_seller_list'),
			'limit'           		=> $this->config->get('mpmultivendor_seller_list'),
		);

		$review_total = $this->model_account_mpmultivendor_reviews->getTotalReviews($filter_data);

		$results = $this->model_account_mpmultivendor_reviews->getReviews($filter_data);

		$data['reviews'] = array();

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'mpseller_review_id'     => $result['mpseller_review_id'],
				'author'     => $result['author'],
				'title'      => $result['title'],
				'description'=> nl2br($result['description']),
				'status'     => $result['status'],
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$url = '';

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . $this->request->get['filter_rating'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('mpmultivendor_seller_list');
		$pagination->url = $this->url->link('account/mpmultivendor/reviews', ''. $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * $this->config->get('mpmultivendor_seller_list')) + 1 : 0, ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) > ($review_total - $this->config->get('mpmultivendor_seller_list'))) ? $review_total : ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) + $this->config->get('mpmultivendor_seller_list')), $review_total, ceil($review_total / $this->config->get('mpmultivendor_seller_list')));

		$data['filter_author'] = $filter_author;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_rating'] = $filter_rating;

		$data['changereview_status'] = $this->config->get('mpmultivendor_seller_changereview');

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
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/mpmultivendor/reviews.tpl')) {
		    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/mpmultivendor/reviews.tpl', $data));
		   } else {
		   		$this->response->setOutput($this->load->view('default/template/account/mpmultivendor/reviews.tpl', $data));
		   }
	  	} else {
		   $this->response->setOutput($this->load->view('account/mpmultivendor/reviews', $data));
		}
	}

	public function addStatus() {
		if($this->config->get('mpmultivendor_seller_changereview')) {
			$json = array();

			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('account/account', '', true);
				
				$json['redirect'] = $this->url->link('account/login', '', true);
			}

			if(!$this->config->get('mpmultivendor_status')) {
				$json['redirect'] = $this->url->link('account/login', '', true);
			}
			

			$this->load->model('account/mpmultivendor/reviews');

			$this->load->model('account/mpmultivendor/seller');

			if (isset($this->request->post['action_status'])) {
				$status = $this->request->post['action_status'];
			} else {
				$status = 0;
			}

			if (isset($this->request->post['mpseller_review_id'])) {
				$mpseller_review_id = $this->request->post['mpseller_review_id'];
			} else {
				$mpseller_review_id = 0;
			}

			$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

			if(!$seller_info) {
				$json['redirect'] = $this->url->link('account/account', '', true);
			}

			if(!empty($seller_info)) {
				$mpseller_id = $seller_info['mpseller_id'];
			} else {
				$mpseller_id = 0;
			}

			if(!$json) {
				$this->model_account_mpmultivendor_reviews->updatedReviewStatus($mpseller_review_id, $mpseller_id, $status);

				$json['success'] = true;
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}
}