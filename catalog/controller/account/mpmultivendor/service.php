<?php
class ControllerAccountMpmultivendorService extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$this->load->language('account/mpmultivendor/service');

		$this->load->model('account/mpmultivendor/seller');

		$this->load->model('account/mpmultivendor/service');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
		}

		$this->document->setTitle($this->language->get('heading_title'));
		$this->getList();
	}

	public function add() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$this->load->language('account/mpmultivendor/service');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/mpmultivendor/seller');

		$this->load->model('account/mpmultivendor/service');

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
			$this->model_account_mpmultivendor_service->addService($this->request->post, $mpseller_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('account/mpmultivendor/service', ''. $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$this->load->language('account/mpmultivendor/service');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/mpmultivendor/seller');

		$this->load->model('account/mpmultivendor/product');

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

		if(!$seller_info) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		if(!empty($seller_info)) {
			$mpseller_id = $seller_info['mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			if($this->model_account_mpmultivendor_product->getProductBySellerID($this->request->get['product_id'], $mpseller_id)) {
				$this->model_account_mpmultivendor_product->editProduct($this->request->get['product_id'], $this->request->post, $mpseller_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('account/mpmultivendor/product', ''. $url, true));
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

		$this->load->language('account/mpmultivendor/service');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/mpmultivendor/seller');

		$this->load->model('account/mpmultivendor/product');

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
			foreach ($this->request->post['selected'] as $product_id) {
				if($this->model_account_mpmultivendor_product->getProductBySellerID($product_id, $mpseller_id)) {
					$this->model_account_mpmultivendor_product->deleteProduct($product_id);
				}
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('account/mpmultivendor/product', ''. $url, true));
		}

		$this->getList();
	}

	protected function getList() {

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_image'])) {
			$filter_image = $this->request->get['filter_image'];
		} else {
			$filter_image = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
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

		$data['add'] = $this->url->link('account/mpmultivendor/product/add', ''. $url, true);

		$data['delete'] = $this->url->link('account/mpmultivendor/product/delete', ''. $url, true);

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
			'text' => $this->language->get('text_product'),
			'href' => $this->url->link('account/mpmultivendor/product', '', true)
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_image'] = $this->language->get('entry_image');

		$data['button_import_products'] = $this->language->get('button_import_products');
		$data['button_export_products'] = $this->language->get('button_export_products');
		$data['import_products'] = $this->url->link('account/mpmultivendor/import_product');
		$data['export_products'] = $this->url->link('account/mpmultivendor/export_product');

		$data['button_copy'] = $this->language->get('button_copy');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['products'] = array();

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
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
			'filter_image'    => $filter_image,
			'filter_mpseller_id'    => $mpseller_id,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('mpmultivendor_seller_list'),
			'limit'           => $this->config->get('mpmultivendor_seller_list')
		);

		$this->load->model('tool/image');

		$product_total = $this->model_account_mpmultivendor_product->getTotalProducts($filter_data);

		$results = $this->model_account_mpmultivendor_product->getProducts($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_account_mpmultivendor_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $product_special['price'];

					break;
				}
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'price'      => $result['price'],
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('account/mpmultivendor/product/edit', 'product_id=' . $result['product_id'] . $url, true)
			);
		}

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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('account/mpmultivendor/product', 'sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('account/mpmultivendor/product', 'sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('account/mpmultivendor/product', 'sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('account/mpmultivendor/product', 'sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('account/mpmultivendor/product', 'sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('account/mpmultivendor/product', 'sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('mpmultivendor_seller_list');
		$pagination->url = $this->url->link('account/mpmultivendor/product', ''. $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('mpmultivendor_seller_list')) + 1 : 0, ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) > ($product_total - $this->config->get('mpmultivendor_seller_list'))) ? $product_total : ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) + $this->config->get('mpmultivendor_seller_list')), $product_total, ceil($product_total / $this->config->get('mpmultivendor_seller_list')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_status'] = $filter_status;
		$data['filter_image'] = $filter_image;

		$data['sort'] = $sort;
		$data['order'] = $order;

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

		if(VERSION < '2.2.0.0') {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/mpmultivendor/product_list.tpl')) {
		    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/mpmultivendor/product_list.tpl', $data));
		   } else {
		   		$this->response->setOutput($this->load->view('default/template/account/mpmultivendor/product_list.tpl', $data));
		   }
	  	} else {
		   $this->response->setOutput($this->load->view('account/mpmultivendor/product_list', $data));
		}
	}

	protected function getForm() {
		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');
		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
		}

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

		if(!empty($seller_info)) {
			$mpseller_id = $seller_info['mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		$this->document->addScript('catalog/view/javascript/mpseller/seller.js');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_location'] = $this->language->get('entry_location');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_image'] = $this->language->get('entry_image');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = array();
		}

		$url = '';

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/mpmultivendor/service', '' . $url, true)
		);

		if (!isset($this->request->get['service_id'])) {
			$data['action'] = $this->url->link('account/mpmultivendor/service/add', '' . $url, true);
		} else {
			$data['action'] = $this->url->link('account/mpmultivendor/service/edit', '' . '&service_id=' . $this->request->get['service_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('account/mpmultivendor/service', '' . $url, true);

		if (isset($this->request->get['service_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$service_info = $this->model_account_mpmultivendor_service->getService($this->request->get['service_id'], $mpseller_id);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['service_description'])) {
			$data['service_description'] = $this->request->post['service_description'];
		} elseif (isset($this->request->get['service_id']) && !empty($service_info)) {
			$data['service_description'] = $this->model_account_mpmultivendor_service->getServiceDescriptions($this->request->get['service_id']);
		} else {
			$data['service_description'] = array();
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($service_info)) {
			$data['location'] = $service_info['location'];
		} else {
			$data['location'] = '';
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

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($service_info)) {
			$data['price'] = $service_info['price'];
		} else {
			$data['price'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($service_info)) {
			$data['status'] = $service_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($service_info)) {
			$data['manufacturer_id'] = $service_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}

		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} elseif (!empty($product_info)) {
			$manufacturer_info = $this->model_account_mpmultivendor_product->getManufacturer($product_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}

		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($product_info)) {
			$data['image'] = $product_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($product_info) && is_file(DIR_IMAGE . $product_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Images
		if (isset($this->request->post['product_image'])) {
			$product_images = $this->request->post['product_image'];
		} elseif (isset($this->request->get['product_id']) && !empty($product_info)) {
			$product_images = $this->model_account_mpmultivendor_product->getProductImages($this->request->get['product_id']);
		} else {
			$product_images = array();
		}

		$data['product_images'] = array();

		foreach ($product_images as $product_image) {
			if (is_file(DIR_IMAGE . $product_image['image'])) {
				$image = $product_image['image'];
				$thumb = $product_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $product_image['sort_order']
			);
		}

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

		if(VERSION < '2.2.0.0') {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/mpmultivendor/service_form.tpl')) {
		    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/mpmultivendor/service_form.tpl', $data));
		   } else {
		   		$this->response->setOutput($this->load->view('default/template/account/mpmultivendor/service_form.tpl', $data));
		   }
	  	} else {
		   $this->response->setOutput($this->load->view('account/mpmultivendor/service_form', $data));
		}
	}

	protected function validateForm() {
		foreach ($this->request->post['service_description'] as $language_id => $value) {

		    //print_r($value);exit('aaaa');

			if ((utf8_strlen($value['description']) < 3) || (utf8_strlen($value['description']) > 255)) {
				$this->error['description'][$language_id] = $this->language->get('error_name');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('account/mpmultivendor/product');
			$this->load->model('account/mpmultivendor/seller');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

			if(!empty($seller_info)) {
				$mpseller_id = $seller_info['mpseller_id'];
			} else {
				$mpseller_id = 0;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'filter_mpseller_id'    => $mpseller_id,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_account_mpmultivendor_product->getProducts($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function manufacturer_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('account/mpmultivendor/product');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_account_mpmultivendor_product->getManufacturers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'manufacturer_id' => $result['manufacturer_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function category_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('account/mpmultivendor/product');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_account_mpmultivendor_product->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function filter_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('account/mpmultivendor/product');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$filters = $this->model_account_mpmultivendor_product->getFilters($filter_data);

			foreach ($filters as $filter) {
				$json[] = array(
					'filter_id' => $filter['filter_id'],
					'name'      => strip_tags(html_entity_decode($filter['group'] . ' &gt; ' . $filter['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function download_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('account/mpmultivendor/seller');

			$this->load->model('account/mpmultivendor/download');

			$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

			if(!empty($seller_info)) {
				$mpseller_id = $seller_info['mpseller_id'];
			} else {
				$mpseller_id = 0;
			}

			$filter_data = array(
				'mpseller_id' => $mpseller_id,
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_account_mpmultivendor_download->getDownloads($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'download_id' => $result['download_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function attribute_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('account/mpmultivendor/product');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_account_mpmultivendor_product->getAttributes($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'attribute_id'    => $result['attribute_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'attribute_group' => $result['attribute_group']
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function option_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->language('account/mpmultivendor/service');

			$this->load->model('account/mpmultivendor/product');

			$this->load->model('tool/image');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$options = $this->model_account_mpmultivendor_product->getOptions($filter_data);

			foreach ($options as $option) {
				$option_value_data = array();

				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
					$option_values = $this->model_account_mpmultivendor_product->getOptionValues($option['option_id']);

					foreach ($option_values as $option_value) {
						if (is_file(DIR_IMAGE . $option_value['image'])) {
							$image = $this->model_tool_image->resize($option_value['image'], 50, 50);
						} else {
							$image = $this->model_tool_image->resize('no_image.png', 50, 50);
						}

						$option_value_data[] = array(
							'option_value_id' => $option_value['option_value_id'],
							'name'            => strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8')),
							'image'           => $image
						);
					}

					$sort_order = array();

					foreach ($option_value_data as $key => $value) {
						$sort_order[$key] = $value['name'];
					}

					array_multisort($sort_order, SORT_ASC, $option_value_data);
				}

				$type = '';

				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
					$type = $this->language->get('text_choose');
				}

				if ($option['type'] == 'text' || $option['type'] == 'textarea') {
					$type = $this->language->get('text_input');
				}

				if ($option['type'] == 'file') {
					$type = $this->language->get('text_file');
				}

				if ($option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
					$type = $this->language->get('text_date');
				}

				$json[] = array(
					'option_id'    => $option['option_id'],
					'name'         => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
					'category'     => $type,
					'type'         => $option['type'],
					'option_value' => $option_value_data
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}