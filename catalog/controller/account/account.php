<?php
class ControllerAccountAccount extends Controller {

    private $error = array();

    public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/account');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		} 
		
		$data['edit'] = $this->url->link('account/edit', '', true);
		$data['password'] = $this->url->link('account/password', '', true);
		$data['address'] = $this->url->link('account/address', '', true);
        $data['seller_messages'] = $this->url->link('account/mpmultivendor/message', '', true);
		
		$data['credit_cards'] = array();
		
		$files = glob(DIR_APPLICATION . 'controller/extension/credit_card/*.php');
		
		foreach ($files as $file) {
			$code = basename($file, '.php');
			
			if ($this->config->get('payment_' . $code . '_status') && $this->config->get('payment_' . $code . '_card')) {
				$this->load->language('extension/credit_card/' . $code, 'extension');

				$data['credit_cards'][] = array(
					'name' => $this->language->get('extension')->get('heading_title'),
					'href' => $this->url->link('extension/credit_card/' . $code, '', true)
				);
			}
		}

		//if seller
        $this->load->model('account/customer');
        $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
        if(!empty($customer_info['role']) && $customer_info['role'] == 'seller') {
            $data['is_seller'] = true;
            $data['seller_link'] = $this->url->link('account/mpmultivendor/store_info', '', true);
            $checkIsSellerApproved = $this->model_account_customer->checkIsSellerApproved($this->customer->getId());
            $data['checkIsSellerApproved'] = empty($checkIsSellerApproved) ? false : true;
            $data['checkIsSellerApprovedtext'] = empty($checkIsSellerApproved) ? 'Complete Your Sellers Registration' : 'Update Seller Info';
            $data['dashboard_link'] = $this->url->link('account/mpmultivendor/dashboard', '', true);
            $data['product_link'] = $this->url->link('account/mpmultivendor/product/add', '', true);

            //print_r($data['checkIsSellerApprovedtext'] );exit('addd');
        }

		$data['wishlist'] = $this->url->link('account/wishlist');
		$data['order'] = $this->url->link('account/order', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		
		if ($this->config->get('total_reward_status')) {
			$data['reward'] = $this->url->link('account/reward', '', true);
		} else {
			$data['reward'] = '';
		}		
		
		$data['return'] = $this->url->link('account/return', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
		$data['recurring'] = $this->url->link('account/recurring', '', true);
		$data['my_seller_enquiries_href'] = $this->url->link('account/enquiries', '', true);

		$this->load->model('account/customer');
		
		$affiliate_info = $this->model_account_customer->getAffiliate($this->customer->getId());
		
		if (!$affiliate_info) {	
			$data['affiliate'] = $this->url->link('account/affiliate/add', '', true);
		} else {
			$data['affiliate'] = $this->url->link('account/affiliate/edit', '', true);
		}
		
		if ($affiliate_info) {		
			$data['tracking'] = $this->url->link('account/tracking', '', true);
		} else {
			$data['tracking'] = '';
		}

		//order tracking system
        $data['orders'] = array();
        $this->load->model('account/order');
        $data['order_status'] = $this->model_account_order->getTrackTotalOrders();
		//echo '<pre>';print_r($data['order_status']);exit('okok');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['role'] = $customer_info['role'];

		$this->response->setOutput($this->load->view('account/account', $data));
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function deactivate() {

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/account/deactivate', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->language('account/deactivate');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('account/customer');

        $data['action'] = $this->url->link('account/account/deactivate', '', true);
        //todo: post submit data
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->session->data['success'] = $this->language->get('text_success');
            $this->model_account_customer->deactivateAccount($this->customer->getId());
            $this->response->redirect($this->url->link('account/login', '', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['reason'])) {
            $data['error_reason'] = $this->error['reason'];
        } else {
            $data['error_reason'] = '';
        }

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $data['profile_column_left'] = $this->load->controller('common/profile_column_left');

        $this->response->setOutput($this->load->view('account/deactivate', $data));
    }

    public function addDeliveryInfo()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        $this->load->language('account/delivery_partner');

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/account', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->model('account/customer');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');

        $customer_id = $this->customer->getId();
        $customer_info = $this->model_account_customer->getCustomer($customer_id);
        if($customer_info['role'] != 'delivery-partner') {
            $this->response->redirect($this->url->link('account/account', '', true));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $this->model_account_customer->updateDeliveryInfos($this->customer->getId(), $this->request->post);
        }

        $data['back'] = $this->url->link('account/account', '', true);
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $data['profile_column_left'] = $this->load->controller('common/profile_column_left');

        $deliveryInfos = $this->model_account_customer->getDeliveryInfo($customer_id);
        $delivery = [];
        foreach ($deliveryInfos as $info) {
            $deliveryArray = array();
            $deliveryArray['country_id'] = $info['country_id'];
            $deliveryArray['zone_id'] = $info['zone_id'];
            $deliveryArray['zones'] = $this->model_localisation_zone->getZonesByCountryId($info['country_id']);
            //echo '<pre>';print_r($deliveryArray['zones']);exit('aaa');
            $deliveryArray['days'] = $info['days'];
            $deliveryArray['rate'] = $info['rate'];
            $delivery[] = $deliveryArray;
        }
        $data['deliveryInfos'] = $delivery;
        $data['countries'] = $this->model_localisation_country->getCountries();

        $partnerInfos = $this->model_account_customer->getDocumentsInfo($customer_id);
        $data['vehicle_type'] = $partnerInfos['vehicle_type'];

        $data['action'] = $this->url->link('account/account/addDeliveryInfo', '', true);
        //echo '<pre>';print_r($data);exit('aaa');
        $this->response->setOutput($this->load->view('account/delivery-info-form', $data));
    }

    protected function validate()
    {
        if ((utf8_strlen(trim($this->request->post['reason'])) < 1)) {
            $this->error['reason'] = $this->language->get('error_reason');
        }

        return !$this->error;
    }
}
