<?php
class ControllerAccountDeliverypartnerMessage extends Controller {
	public function index() {

        error_reporting(E_ALL);
        ini_set("display_errors", 1);
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/deliverypartner/message', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

//		if(!$this->config->get('mpmultivendor_status')) {
//			$this->response->redirect($this->url->link('account/account', '', true));
//		}
        $this->load->language('account/edit');
		$this->load->language('account/deliverypartner/message');

		$this->load->model('account/deliverypartner/message');
		//$this->load->model('account/mpmultivendor/seller');
        $this->load->model('account/customer');
		$this->load->model('tool/image');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$page_list = 30;

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
			'href' => $this->url->link('account/deliverypartner/message', ''. $url, true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['entry_message'] = $this->language->get('entry_message');

		$data['button_submit'] = $this->language->get('button_submit');

		$deliverypartner_info = $this->model_account_customer->getCustomer($this->customer->isLogged());
//echo '<pre>';print_r($deliverypartner_info['firstname']);exit('okoko');
		if(!$deliverypartner_info) {
			$this->response->redirect($this->url->link('account/account', '', true));
        }

		if(!empty($deliverypartner_info)) {
			$deliverypartner_id = $deliverypartner_info['customer_id'];
		} else {
			$deliverypartner_id = 0;
		}

		$data['chats'] = array();

		$filter_data = array(
			'delivery_partner_id' 	=> $deliverypartner_id,
			'start' 		=> ($page - 1) * $page_list,
			'limit' 		=> $page_list
		);

		$total_chats = $this->model_account_deliverypartner_message->getTotalSellerMessageChats($filter_data);

		$chats = $this->model_account_deliverypartner_message->getDeliveryPartnerMessageChats($filter_data);

		foreach($chats as $chat) {

			if($chat['from'] == 'delivery-partner') {

				$from_name = $deliverypartner_info['firstname'];
				$from_store = $deliverypartner_info['lastname'];
				if($deliverypartner_info['image']) {
					$from_image = $this->model_tool_image->resize($deliverypartner_info['image'], 40, 40);
				} else {
					$from_image = $this->model_tool_image->resize('nouserpic.png', 40, 40);
				}

				$from_type_name = $this->language->get('text_seller_type');
			} else if($chat['from'] == 'admin') {
				$from_name = $this->language->get('text_administrator');
				$from_store = '';
				$from_image = $this->model_tool_image->resize('nouserpic.png', 40, 40);
				$from_type_name = $this->language->get('text_customer_type');
			}
			$data['chats'][] = array(
				'message'			 	 => $chat['message'],
				'from'			 	 	 => $chat['from'],
				'from_name'			 	 => $from_name,
				'from_store'			 => $from_store,
				'from_type_name'		 => $from_type_name,
				'from_image'		 	 => $from_image,
				'date_added'			 => date('d M Y h:i A', strtotime($chat['date_added'])),
			);
		}

		$pagination = new Pagination();
		$pagination->total = $total_chats;
		$pagination->page = $page;
		$pagination->limit = $page_list;
		$pagination->url = $this->url->link('account/deliverypartner/message', '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total_chats) ? (($page - 1) * $page_list) + 1 : 0, ((($page - 1) * $page_list) > ($total_chats - $page_list)) ? $total_chats : ((($page - 1) * $page_list) + $page_list), $total_chats, ceil($total_chats / $page_list));

		// Mark as Read Messages
		$this->model_account_deliverypartner_message->MarkAsReadMessage($deliverypartner_id);

		$data['mpseller_links'] = $this->load->controller('account/mpmultivendor/mpseller_links');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
        $data['back_url'] = $this->url->link('account/account', '', true);

		$data['custom_themename'] = $this->model_account_mpmultivendor_seller->getactiveTheme();

		$data['text_username'] = $deliverypartner_info['firstname'];
		$data['text_email'] = $deliverypartner_info['email'];

		if(VERSION < '2.2.0.0') {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/deliverypartner/message.tpl')) {
		    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/deliverypartner/message.tpl', $data));
		   } else {
		   		$this->response->setOutput($this->load->view('default/template/account/deliverypartner/message.tpl', $data));
		   }
	  	} else {
		   $this->response->setOutput($this->load->view('account/deliverypartner/message', $data));
		}
	}

	public function Submitreply() {

//        error_reporting(E_ALL);
//        ini_set("display_errors", 1);
		$json = array();

		$this->load->language('account/deliverypartner/message');

		$this->load->model('account/customer');

		$this->load->model('account/deliverypartner/message');

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page='. $this->request->get['page'];
		}

		$deliverypartner_info = $this->model_account_customer->getCustomer($this->customer->isLogged());

		if(!$deliverypartner_info) {
			$json['redirect'] = $this->url->link('account/account', '', true);
		}

		if(!empty($deliverypartner_info)) {
			$deliverypartner_id = $deliverypartner_info['customer_id'];
		} else {
			$deliverypartner_id = 0;
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/deliverypartner/message', '', true);

			$json['redirect'] = $this->url->link('account/login', '', true);
		}

		if (utf8_strlen($this->request->post['message']) < 2) {
			$json['warning'] = $this->language->get('error_message');
		}

		if(!$json) {
			$add_data = array(
				'delivery_partner_id'	=> $deliverypartner_id,
				'from'					=> 'delivery-partner',
				'message'				=> isset($this->request->post['message']) ? $this->request->post['message'] : '',
				'subject'				=> isset($this->request->post['subject']) ? $this->request->post['subject'] : '',
			);

			$this->model_account_deliverypartner_message->addMessage($add_data);

			$json['success'] = true;

			$this->session->data['success'] = $this->language->get('text_success_sent');

			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('account/deliverypartner/message', '' . $url, true));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}