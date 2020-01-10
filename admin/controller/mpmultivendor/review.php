<?php
class ControllerMpmultivendorReview extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('mpmultivendor/review');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('mpmultivendor/review');

		$this->getList();
	}

	public function add() {
		$this->load->language('mpmultivendor/review');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('mpmultivendor/review');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_mpmultivendor_review->addReview($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_mpseller_id'])) {
				$url .= '&filter_mpseller_id=' . urlencode(html_entity_decode($this->request->get['filter_mpseller_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_store_owner'])) {
				$url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('mpmultivendor/review');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('mpmultivendor/review');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_mpmultivendor_review->editReview($this->request->get['mpseller_review_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_mpseller_id'])) {
				$url .= '&filter_mpseller_id=' . urlencode(html_entity_decode($this->request->get['filter_mpseller_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_store_owner'])) {
				$url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('mpmultivendor/review');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('mpmultivendor/review');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mpseller_review_id) {
				$this->model_mpmultivendor_review->deleteReview($mpseller_review_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_mpseller_id'])) {
				$url .= '&filter_mpseller_id=' . urlencode(html_entity_decode($this->request->get['filter_mpseller_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_store_owner'])) {
				$url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {

		$this->document->addStyle('view/stylesheet/mpmultivendor/mpmultivendor.css');
		
		if (isset($this->request->get['filter_mpseller_id'])) {
			$filter_mpseller_id = $this->request->get['filter_mpseller_id'];
		} else {
			$filter_mpseller_id = null;
		}

		if (isset($this->request->get['filter_store_owner'])) {
			$filter_store_owner = $this->request->get['filter_store_owner'];
		} else {
			$filter_store_owner = null;
		}

		if (isset($this->request->get['filter_author'])) {
			$filter_author = $this->request->get['filter_author'];
		} else {
			$filter_author = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.date_added';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_mpseller_id'])) {
			$url .= '&filter_mpseller_id=' . urlencode(html_entity_decode($this->request->get['filter_mpseller_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_store_owner'])) {
			$url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
			'href' => $this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('mpmultivendor/review/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('mpmultivendor/review/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['reviews'] = array();

		$filter_data = array(
			'filter_mpseller_id'    => $filter_mpseller_id,
			'filter_store_owner'    => $filter_store_owner,
			'filter_author'     => $filter_author,
			'filter_status'     => $filter_status,
			'filter_date_added' => $filter_date_added,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$review_total = $this->model_mpmultivendor_review->getTotalReviews($filter_data);

		$results = $this->model_mpmultivendor_review->getReviews($filter_data);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'mpseller_review_id'  	=> $result['mpseller_review_id'],
				'store_owner'       	=> $result['store_owner'],
				'title'      => $result['title'],
				'author'     => $result['author'],
				'rating'     => $result['rating'],
				'description'=> nl2br($result['description']),
				'status_int' => $result['status'],
				'status'     => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'       => $this->url->link('mpmultivendor/review/edit', 'user_token=' . $this->session->data['user_token'] . '&mpseller_review_id=' . $result['mpseller_review_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['column_store_owner'] = $this->language->get('column_store_owner');
		$data['column_author'] = $this->language->get('column_author');
		$data['column_title'] = $this->language->get('column_title');
		$data['column_rating'] = $this->language->get('column_rating');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_store_owner'] = $this->language->get('entry_store_owner');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_added'] = $this->language->get('entry_date_added');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['user_token'] = $this->session->data['user_token'];

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

		if (isset($this->request->get['filter_mpseller_id'])) {
			$url .= '&filter_mpseller_id=' . urlencode(html_entity_decode($this->request->get['filter_mpseller_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_store_owner'])) {
			$url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_store_owner'] = $this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . '&sort=store_owner' . $url, true);
		$data['sort_title'] = $this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . '&sort=r.title' . $url, true);
		$data['sort_author'] = $this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . '&sort=r.author' . $url, true);
		$data['sort_rating'] = $this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . '&sort=r.rating' . $url, true);
		$data['sort_status'] = $this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . '&sort=r.status' . $url, true);
		$data['sort_date_added'] = $this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . '&sort=r.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_mpseller_id'])) {
			$url .= '&filter_mpseller_id=' . urlencode(html_entity_decode($this->request->get['filter_mpseller_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_store_owner'])) {
			$url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($review_total - $this->config->get('config_limit_admin'))) ? $review_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $review_total, ceil($review_total / $this->config->get('config_limit_admin')));

		$data['filter_mpseller_id'] = $filter_mpseller_id;
		$data['filter_store_owner'] = $filter_store_owner;
		$data['filter_author'] = $filter_author;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->config->set('template_engine', 'template');
		$this->response->setOutput($this->load->view('mpmultivendor/review_list', $data));
	}

	protected function getForm() {
		$this->document->addScript('view/javascript/mpseller/ratepicker/rate-picker.js');
		$this->document->addStyle('view/stylesheet/mpmultivendor/mpmultivendor.css');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['mpseller_review_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_store_owner'] = $this->language->get('entry_store_owner');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_text'] = $this->language->get('entry_text');

		$data['help_store_owner'] = $this->language->get('help_store_owner');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['store_owner'])) {
			$data['error_store_owner'] = $this->error['store_owner'];
		} else {
			$data['error_store_owner'] = '';
		}

		if (isset($this->error['author'])) {
			$data['error_author'] = $this->error['author'];
		} else {
			$data['error_author'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = '';
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = '';
		}

		if (isset($this->error['rating'])) {
			$data['error_rating'] = $this->error['rating'];
		} else {
			$data['error_rating'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_mpseller_id'])) {
			$url .= '&filter_mpseller_id=' . urlencode(html_entity_decode($this->request->get['filter_mpseller_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_store_owner'])) {
			$url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
			'href' => $this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['mpseller_review_id'])) {
			$data['action'] = $this->url->link('mpmultivendor/review/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('mpmultivendor/review/edit', 'user_token=' . $this->session->data['user_token'] . '&mpseller_review_id=' . $this->request->get['mpseller_review_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['mpseller_review_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$review_info = $this->model_mpmultivendor_review->getReview($this->request->get['mpseller_review_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('mpmultivendor/mpseller');

		if (isset($this->request->post['mpseller_id'])) {
			$data['mpseller_id'] = $this->request->post['mpseller_id'];
		} elseif (!empty($review_info)) {
			$data['mpseller_id'] = $review_info['mpseller_id'];
		} else {
			$data['mpseller_id'] = '';
		}

		if (isset($this->request->post['store_owner'])) {
			$data['store_owner'] = $this->request->post['store_owner'];
		} elseif (!empty($review_info)) {
			$data['store_owner'] = $review_info['store_owner'];
		} else {
			$data['store_owner'] = '';
		}

		if (isset($this->request->post['author'])) {
			$data['author'] = $this->request->post['author'];
		} elseif (!empty($review_info)) {
			$data['author'] = $review_info['author'];
		} else {
			$data['author'] = '';
		}

		if (isset($this->request->post['title'])) {
			$data['title'] = $this->request->post['title'];
		} elseif (!empty($review_info)) {
			$data['title'] = $review_info['title'];
		} else {
			$data['title'] = '';
		}

		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($review_info)) {
			$data['description'] = $review_info['description'];
		} else {
			$data['description'] = '';
		}

		if (isset($this->request->post['rating'])) {
			$data['rating'] = $this->request->post['rating'];
		} elseif (!empty($review_info)) {
			$data['rating'] = $review_info['rating'];
		} else {
			$data['rating'] = '';
		}

		if (isset($this->request->post['date_added'])) {
			$data['date_added'] = $this->request->post['date_added'];
		} elseif (!empty($review_info)) {
			$data['date_added'] = ($review_info['date_added'] != '0000-00-00 00:00' ? $review_info['date_added'] : '');
		} else {
			$data['date_added'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($review_info)) {
			$data['status'] = $review_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->config->set('template_engine', 'template');
		$this->response->setOutput($this->load->view('mpmultivendor/review_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'mpmultivendor/review')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['mpseller_id']) {
			$this->error['store_owner'] = $this->language->get('error_store_owner');
		}

		if ((utf8_strlen($this->request->post['author']) < 3) || (utf8_strlen($this->request->post['author']) > 64)) {
			$this->error['author'] = $this->language->get('error_author');
		}

		if (utf8_strlen($this->request->post['description']) < 1) {
			$this->error['description'] = $this->language->get('error_description');
		}

		if (utf8_strlen($this->request->post['title']) < 1) {
			$this->error['title'] = $this->language->get('error_title');
		}

		if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
			$this->error['rating'] = $this->language->get('error_rating');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'mpmultivendor/review')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}