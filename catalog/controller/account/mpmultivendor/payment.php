<?php
class ControllerAccountMpmultivendorPayment extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}
		
		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}
		
		$this->load->language('account/mpmultivendor/payment');

		$this->load->model('account/mpmultivendor/payment');
		
		$this->load->model('account/mpmultivendor/seller');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');			
		}
		
		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = null;
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = null;
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

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . urlencode(html_entity_decode($this->request->get['filter_date_start'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('account/mpmultivendor/payment', ''. $url, true)
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');		
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_final_payment'] = $this->language->get('text_final_payment');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');

		$data['column_order_id'] = $this->language->get('column_order_id');
		$data['column_product_name'] = $this->language->get('column_product_name');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_amount'] = $this->language->get('column_amount');
		$data['column_date_added'] = $this->language->get('column_date_added');

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

		$data['payments'] = array();

		$filter_data = array(
			'filter_mpseller_id'  	=> $mpseller_id,
			'filter_date_start'  	=> $filter_date_start,
			'filter_date_end'  		=> $filter_date_end,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('mpmultivendor_seller_list'),
			'limit' => $this->config->get('mpmultivendor_seller_list')
		);

		$final_payment = $this->model_account_mpmultivendor_payment->getPaymentTotal($filter_data);
		$data['final_payment'] = $this->currency->format($final_payment, $this->config->get('config_currency'));

		$payment_total = $this->model_account_mpmultivendor_payment->getTotalPayments($filter_data);

		$results = $this->model_account_mpmultivendor_payment->getPayments($filter_data);

		foreach ($results as $result) {
			$data['payments'][] = array(
				'order_id' 		  			=> $result['order_id'],
				'amount' 		  			=> $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'date_added'    			=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
			);
		}


		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_amount'] = $this->url->link('mpmultivendor/payment', 'sort=mc.amount' . $url, true);
		$data['sort_date_added'] = $this->url->link('mpmultivendor/payment', 'sort=mc.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $payment_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('mpmultivendor_seller_list');
		$pagination->url = $this->url->link('account/mpmultivendor/payment', ''. $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($payment_total) ? (($page - 1) * $this->config->get('mpmultivendor_seller_list')) + 1 : 0, ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) > ($payment_total - $this->config->get('mpmultivendor_seller_list'))) ? $payment_total : ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) + $this->config->get('mpmultivendor_seller_list')), $payment_total, ceil($payment_total / $this->config->get('mpmultivendor_seller_list')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
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
		/* Theme Work Ends */

		if(VERSION < '2.2.0.0') {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/mpmultivendor/payment.tpl')) {
		    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/mpmultivendor/payment.tpl', $data));
		   } else {
		   		$this->response->setOutput($this->load->view('default/template/account/mpmultivendor/payment.tpl', $data));
		   }
	  	} else {
		   $this->response->setOutput($this->load->view('account/mpmultivendor/payment', $data));
		}
	}
}