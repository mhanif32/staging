<?php
class ControllerDeliverypartnerMessage extends Controller {
	private $error = array();

	public function index() {

        error_reporting(E_ALL);
        ini_set("display_errors", 1);

		$this->load->language('deliverypartner/message');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('deliverypartner/message');

		$this->getList();
	}

	public function delete() {
		$this->load->language('mpmultivendor/mpseller_message');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('mpmultivendor/mpseller_message');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mpseller_message_id) {
				$this->model_mpmultivendor_mpseller_message->deleteSellerMessage($mpseller_message_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('mpmultivendor/mpseller_message', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_firstname'])) {
			$filter_fname = $this->request->get['filter_firstname'];
		} else {
			$filter_fname = null;
		}

		if (isset($this->request->get['filter_lastname'])) {
			$filter_lname = $this->request->get['filter_lastname'];
		} else {
			$filter_lname = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'mps.date_added';
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

		if (isset($this->request->get['filter_fname'])) {
			$url .= '&filter_fname=' . urlencode(html_entity_decode($this->request->get['filter_fname'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_lname'])) {
			$url .= '&filter_lname=' . urlencode(html_entity_decode($this->request->get['filter_lname'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('deliverypartner/message', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['delete'] = $this->url->link('deliverypartner/message/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['messages'] = array();

		$filter_data = array(
			'filter_store_owner'       => $filter_fname,
			'filter_store_name' 	   => $filter_lname,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$message_total = $this->model_deliverypartner_message->getTotalSellerMessages($filter_data);

		$results = $this->model_deliverypartner_message->getSellerMessages($filter_data);

		foreach ($results as $result) {
			// Total unread Messages
			$total_unreads = $this->model_deliverypartner_message->getTotalUnreadMessages($result['mpseller_id']);

			$data['seller_messages'][] = array(
				'mpseller_id' 	 => $result['mpseller_id'],
				'seller_name'    => $result['store_owner'],
				'store_name'     => $result['store_name'],
				'message'     	 => $result['message'],
				'date_added'     => $result['date_added'],
				'total_unreads'  => $total_unreads,
				'edit'           => $this->url->link('deliverypartner/message/view', 'user_token=' . $this->session->data['user_token'] . '&mpseller_id=' . $result['mpseller_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['entry_store_owner'] = $this->language->get('entry_store_owner');
		$data['entry_store_name'] = $this->language->get('entry_store_name');

		$data['column_seller_name'] = $this->language->get('column_seller_name');
		$data['column_store_name'] = $this->language->get('column_store_name');
		$data['column_message'] = $this->language->get('column_message');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_view'] = $this->language->get('button_view');
		$data['button_filter'] = $this->language->get('button_filter');

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

		if (isset($this->request->get['filter_store_owner'])) {
			$url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_store_name'])) {
			$url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_seller_name'] = $this->url->link('mpmultivendor/mpseller_message', 'user_token=' . $this->session->data['user_token'] . '&sort=mp.store_owner' . $url, true);
		$data['sort_store_name'] = $this->url->link('mpmultivendor/mpseller_message', 'user_token=' . $this->session->data['user_token'] . '&sort=mp.store_name' . $url, true);
		$data['sort_message'] = $this->url->link('mpmultivendor/mpseller_message', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.message' . $url, true);
		$data['sort_date_added'] = $this->url->link('mpmultivendor/mpseller_message', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_store_owner'])) {
			$url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_store_name'])) {
			$url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $message_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('mpmultivendor/mpseller_message', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($message_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($message_total - $this->config->get('config_limit_admin'))) ? $message_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $message_total, ceil($message_total / $this->config->get('config_limit_admin')));

		$data['filter_fname'] = $filter_fname;
		$data['filter_lname'] = $filter_lname;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->config->set('template_engine', 'template');
		$this->response->setOutput($this->load->view('deliverypartner/message_list', $data));
	}

	public function view() {
		$this->document->addStyle('view/stylesheet/mpmultivendor/mpmultivendor.css');

		$this->load->model('mpmultivendor/mpseller');

		if (isset($this->request->get['mpseller_id'])) {
			$data['mpseller_id'] = $mpseller_id = $this->request->get['mpseller_id'];
		} else {
			$data['mpseller_id'] = $mpseller_id = 0;
		}

		$page_list = 50;

		$seller_info = $this->model_mpmultivendor_mpseller->getMpseller($mpseller_id);

		if ($seller_info) {
			$this->load->language('mpmultivendor/mpseller_message');

			$this->load->model('mpmultivendor/mpseller_message');

			$this->load->model('tool/image');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_form'] = sprintf($this->language->get('text_form'), $seller_info['store_owner'], $seller_info['store_name']);

			$data['entry_message'] = $this->language->get('entry_message');

			$data['button_cancel'] = $this->language->get('button_cancel');
			$data['button_send'] = $this->language->get('button_send');

			$data['cancel'] = $this->url->link('mpmultivendor/mpseller_message', 'user_token=' . $this->session->data['user_token'], true);

			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}

			$url = '';

			if (isset($this->request->get['mpseller_id'])) {
				$url .= '&mpseller_id=' . $this->request->get['mpseller_id'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('mpmultivendor/mpseller_message', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			$data['chats'] = array();

			$filter_data = array(
				'mpseller_id' 	=> $mpseller_id,
				'start' 		=> ($page - 1) * $page_list,
				'limit' 		=> $page_list
			);

			$chats_total = $this->model_mpmultivendor_mpseller_message->getTotalSellerMessageChats($filter_data);

			$chats = $this->model_mpmultivendor_mpseller_message->getSellerMessageChats($filter_data);
			foreach($chats as $chat) {
				if($chat['from'] == 'seller') {
					$from_name = $seller_info['store_owner'];
					$from_store = $seller_info['store_name'];
					if($seller_info['image']) {
						$from_image = $this->model_tool_image->resize($seller_info['image'], 40, 40);
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

			$data['user_token'] = $this->session->data['user_token'];

			$url = '';

			if (isset($this->request->get['mpseller_id'])) {
				$url .= '&mpseller_id=' . $this->request->get['mpseller_id'];
			}

			$pagination = new Pagination();
			$pagination->total = $chats_total;
			$pagination->page = $page;
			$pagination->limit = $page_list;
			$pagination->url = $this->url->link('mpmultivendor/mpseller_message/view', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($chats_total) ? (($page - 1) * $page_list) + 1 : 0, ((($page - 1) * $page_list) > ($chats_total - $page_list)) ? $chats_total : ((($page - 1) * $page_list) + $page_list), $chats_total, ceil($chats_total / $page_list));

			// Mark as Read Messages
			$this->model_mpmultivendor_mpseller_message->MarkAsReadMessage($mpseller_id);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->config->set('template_engine', 'template');
			$this->response->setOutput($this->load->view('mpmultivendor/mpseller_message_view', $data));
		} else {
			return new Action('error/not_found');
		}
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'mpmultivendor/mpseller_message')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'mpmultivendor/mpseller_message')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function SendMessage() {
		$json = array();

		$this->load->language('mpmultivendor/mpseller_message');

		$this->load->model('mpmultivendor/mpseller');

		$this->load->model('mpmultivendor/mpseller_message');

		if (isset($this->request->get['mpseller_id'])) {
			$data['mpseller_id'] = $mpseller_id = $this->request->get['mpseller_id'];
		} else {
			$data['mpseller_id'] = $mpseller_id = 0;
		}

		$seller_info = $this->model_mpmultivendor_mpseller->getMpseller($mpseller_id);

		if (!$seller_info) {
			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('mpmultivendor/mpseller_message', 'user_token='. $this->session->data['user_token'], true));
		}

		if (utf8_strlen($this->request->post['message']) < 2) {
			$json['warning'] = $this->language->get('error_message');
		}

		if(!$json) {
			$add_data = array(
				'mpseller_id'			=> $mpseller_id,
				'from'					=> 'admin',
				'message'				=> $this->request->post['message'],
			);

			$this->model_mpmultivendor_mpseller_message->SendMessage($add_data);

			$json['success'] = true;

			$this->session->data['success'] = $this->language->get('text_success_sent');

			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('mpmultivendor/mpseller_message/view', 'user_token='. $this->session->data['user_token'] . '&mpseller_id='. $mpseller_id, true));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}