<?php
class ControllerMpmultivendorStoreProfile extends Controller {
	public function index() {
		$this->load->language('mpmultivendor/store_profile');

		$this->load->model('mpmultivendor/mv_seller');

		$this->load->model('tool/image');

		$this->document->addScript('catalog/view/javascript/mpseller/ratepicker/rate-picker.js');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
		}

		$data['review_title'] = $this->language->get('review_title');
		$data['contact_seller_title'] = $this->language->get('contact_seller_title');

		$data['text_rating'] = $this->language->get('text_rating');

		$data['entry_your_name'] = $this->language->get('entry_your_name');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email_address'] = $this->language->get('entry_email_address');
		$data['entry_message'] = $this->language->get('entry_message');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_write_review'] = $this->language->get('button_write_review');
		$data['button_send_message'] = $this->language->get('button_send_message');

		$data['text_reviews'] = $this->language->get('text_reviews');

		$data['name'] = $data['author'] = $this->customer->getFirstName() .' '. $this->customer->getLastName();
		$data['customer_email'] = $this->customer->getEmail();
		$data['customer_logged'] = $this->customer->isLogged();

		if (isset($this->request->get['mpseller_id'])) {
			$data['mpseller_id'] = $mpseller_id = (int)$this->request->get['mpseller_id'];
		} else if (isset($this->request->get['review_mpseller_id'])) {
			$data['mpseller_id'] = $mpseller_id = (int)$this->request->get['review_mpseller_id'];
		} else {
			$data['mpseller_id'] = $mpseller_id = 0;
		}

		$seller_info = $this->model_mpmultivendor_mv_seller->getSellerStoreInfo($mpseller_id);

		if (!empty($seller_info['status']) && !empty($seller_info['approved'])) {
			$data['store_name'] = $seller_info['store_name'];
			$data['email'] = $seller_info['email'];
			$data['telephone'] = $seller_info['telephone'];
			$data['address'] = $seller_info['address'];
			$data['rating'] = $seller_info['rating'];

			if($seller_info['logo']) {
				$data['logo'] = $this->model_tool_image->resize($seller_info['logo'], $this->config->get('mpmultivendor_store_logo_width'), $this->config->get('mpmultivendor_store_logo_height'));
			} else {
				$data['logo'] = $this->model_tool_image->resize('no_image.png', $this->config->get('mpmultivendor_store_logo_width'), $this->config->get('mpmultivendor_store_logo_height'));
			}

			$data['total_reviews'] = $this->model_mpmultivendor_mv_seller->getTotalReviews($seller_info['mpseller_id']);

			$data['gotoreviews'] = $this->url->link('mpmultivendor/mv_reviews', 'review_mpseller_id='. $seller_info['mpseller_id'], true);

			$data['facebook_url'] = $seller_info['facebook_url'];
			$data['google_plus_url'] = $seller_info['google_plus_url'];
			$data['twitter_url'] = $seller_info['twitter_url'];
			$data['pinterest_url'] = $seller_info['pinterest_url'];
			$data['linkedin_url'] = $seller_info['linkedin_url'];
			$data['youtube_url'] = $seller_info['youtube_url'];
			$data['instagram_url'] = $seller_info['instagram_url'];
			$data['flickr_url'] = $seller_info['flickr_url'];

            $data['is_product_purchased'] = !$this->customer->isLogged() ? false : ($this->model_mpmultivendor_mv_seller->isProductPurchased($seller_info['mpseller_id']) ? true : false);

			if(VERSION < '2.2.0.0') {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mpmultivendor/store_profile.tpl')) {
			    	return $this->load->view($this->config->get('config_template') . '/template/mpmultivendor/store_profile.tpl', $data);
			   } else {
			   		return $this->load->view('default/template/mpmultivendor/store_profile.tpl', $data);
			   }
		  	} else {
			   return $this->load->view('mpmultivendor/store_profile', $data);
			}
		}
	}

	public function addreview() {
		$json = array();

		$this->load->model('mpmultivendor/mv_seller');

		$this->load->language('mpmultivendor/store_profile');

		if ((utf8_strlen(trim($this->request->post['author'])) < 1) || (utf8_strlen(trim($this->request->post['author'])) > 32)) {
			$json['error']['author'] = $this->language->get('error_author');
		}

		if ((utf8_strlen(trim($this->request->post['title'])) < 3) || (utf8_strlen(trim($this->request->post['title'])) > 255)) {
			$json['error']['title'] = $this->language->get('error_title');
		}

		if (utf8_strlen(trim($this->request->post['description'])) < 10) {
			$json['error']['description'] = $this->language->get('error_description');
		}

		if (empty($this->request->post['rating'])) {
			$json['error']['rating'] = $this->language->get('error_rating');
		}

		if (isset($this->request->get['mpseller_id'])) {
			$mpseller_id = (int)$this->request->get['mpseller_id'];
		} else if (isset($this->request->get['review_mpseller_id'])) {
			$mpseller_id = (int)$this->request->get['review_mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		$seller_info = $this->model_mpmultivendor_mv_seller->getSellerStoreInfo($mpseller_id);

		if(!$seller_info) {
			$json['error']['warning'] = $this->language->get('error_seller_notfound');
		}

		if(!$json) {
			$review_data = array(
				'author'				=> $this->request->post['author'],
				'title'					=> $this->request->post['title'],
				'description'			=> $this->request->post['description'],
				'rating'				=> $this->request->post['rating'],
				'mpseller_id'			=> $mpseller_id,
			);

			$mpseller_review_id = $this->model_mpmultivendor_mv_seller->addReview($review_data);

			$review_info = $this->model_mpmultivendor_mv_seller->getReviewInfo($mpseller_review_id);
			if($review_info['status']) {
				$this->session->data['success'] = $this->language->get('text_success_review');
			} else {
				$this->session->data['success'] = $this->language->get('text_success_review_approval');
			}

			$json['success'] = str_replace('&amp;', '&', $this->url->link('mpmultivendor/mv_reviews', 'review_mpseller_id='. $mpseller_id, true));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function sendEnquiry() {
		$json = array();

		$this->load->model('mpmultivendor/mv_seller');

		$this->load->language('mpmultivendor/store_profile');

		if(!$this->customer->isLogged()) {
			if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen(trim($this->request->post['name'])) > 32)) {
				$json['error']['name'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['email'] = $this->language->get('error_email');
			}
		}

		if (utf8_strlen(trim($this->request->post['message'])) < 10) {
			$json['error']['message'] = $this->language->get('error_message');
		}

		if (isset($this->request->get['mpseller_id'])) {
			$mpseller_id = (int)$this->request->get['mpseller_id'];
		} else if (isset($this->request->get['review_mpseller_id'])) {
			$mpseller_id = (int)$this->request->get['review_mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		$seller_info = $this->model_mpmultivendor_mv_seller->getSellerStoreInfo($mpseller_id);

		if(!$seller_info) {
			$json['error']['warning'] = $this->language->get('error_seller_notfound');
		}

		if(!$json) {
			$enquiry_data = array(
				'mpseller_id'	=> $mpseller_id,
				'customer_id'	=> $this->customer->getId(),
				'name'			=> !empty($this->request->post['name']) ? $this->request->post['name'] : $this->customer->getFirstName() .' '. $this->customer->getLastName(),
				'email'			=> !empty($this->request->post['email']) ? $this->request->post['email'] : $this->customer->getEmail(),
				'message'		=> $this->request->post['message'],
			);
		 	$this->model_mpmultivendor_mv_seller->sendEnquiry($enquiry_data);

		 	$json['success'] = $this->language->get('success_send_enquiry');
		}


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}