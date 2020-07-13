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
        }

		$data['wishlist'] = $this->url->link('account/wishlist');
		$data['delivery_partner_link'] = $this->url->link('account/account/addDeliveryInfo');
		$data['delivery_partner_request_link'] = $this->url->link('account/request');
		$data['delivery_partner_orders_link'] = $this->url->link('account/request/orders');
		$data['order'] = $this->url->link('account/order', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
        $data['edit_product_href'] = $this->url->link('account/mpmultivendor/product', '', true);
        $data['edit_special_product_href'] = $this->url->link('account/mpmultivendor/product/special/', '', true);
        $data['order_hidtory_link'] = $this->url->link('account/order', '', true);
        $data['transactions_link'] = $this->url->link('account/transaction', '', true);
        $data['customer_order_href'] = $this->url->link('account/mpmultivendor/orders', '', true);
        $data['sales_special_href'] = $this->url->link('product/special', '', true);
        $data['update_order_status_link'] = $this->url->link('account/mpmultivendor/orders/pending', '', true);
        $data['return_href'] = $this->url->link('account/return', '', true);

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

    public function zone() {
        $json = array();

        $this->load->model('localisation/zone');
        $this->load->model('localisation/area');

        $zone_info = $this->model_localisation_zone->getZone($this->request->get['zone_id']);

        if ($zone_info) {

            $json = array(
                'zone_id'           => $zone_info['zone_id'],
                'name'              => $zone_info['name'],
                'area'              => $this->model_localisation_area->getAreasByZoneId($this->request->get['zone_id']),
                'status'            => $zone_info['status']
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
//        error_reporting(E_ALL);
//        ini_set("display_errors", 1);

        $this->load->language('account/delivery_partner');

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/account', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->model('account/customer');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $this->load->model('localisation/area');

        $customer_id = $this->customer->getId();
        $customer_info = $this->model_account_customer->getCustomer($customer_id);
        if($customer_info['role'] != 'delivery-partner') {
            $this->response->redirect($this->url->link('account/account', '', true));
        }

        if (isset($this->session->data['success'])) {

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            //update documents
            $dataFile = array();
            $uploads_dir = 'image/deliverypartner/customer-' . $this->customer->getId() . '/'; // set you upload path here
            if (!file_exists($uploads_dir)) {
                mkdir($uploads_dir, 0777, true);
            }
            if (is_uploaded_file($this->request->files['id_proof']['tmp_name'])) {

                move_uploaded_file($this->request->files['id_proof']['tmp_name'], $uploads_dir . $this->request->files['id_proof']['name']);
                $dataFile['id_proof'] = $this->request->files['id_proof']['name'];
            }
            if (is_uploaded_file($this->request->files['address_proof']['tmp_name'])) {

                move_uploaded_file($this->request->files['address_proof']['tmp_name'], $uploads_dir . $this->request->files['address_proof']['name']);
                $dataFile['address_proof'] = $this->request->files['address_proof']['name'];
            }
            if (is_uploaded_file($this->request->files['travel_license']['tmp_name'])) {

                move_uploaded_file($this->request->files['travel_license']['tmp_name'], $uploads_dir . $this->request->files['travel_license']['name']);
                $dataFile['travel_license'] = $this->request->files['travel_license']['name'];
            }
            if (is_uploaded_file($this->request->files['vehicle_insurance']['tmp_name'])) {

                move_uploaded_file($this->request->files['vehicle_insurance']['tmp_name'], $uploads_dir . $this->request->files['vehicle_insurance']['name']);
                $dataFile['vehicle_insurance'] = $this->request->files['vehicle_insurance']['name'];
            }

            $this->model_account_customer->updateDeliveryInfos($this->customer->getId(), $this->request->post, $dataFile);
            $data['success'] = $this->session->data['success'] = 'Success : Your DELIVERY PARTNER INFO
 has been successfully updated.
';
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
            $deliveryArray['area_id'] = $info['area_id'];
            $deliveryArray['areas'] = $this->model_localisation_area->getAreasByZoneId($info['zone_id']);
            $deliveryArray['days'] = $info['days'];
            $deliveryArray['rate_per_hour'] = $info['rate_per_hour'];
            $delivery[] = $deliveryArray;
        }
        $data['deliveryInfos'] = $delivery;
        $data['countries'] = $this->model_localisation_country->getCountries();

        $partnerInfos = $this->model_account_customer->getDocumentsInfo($customer_id);
        $data['vehicle_type'] = !empty($partnerInfos['vehicle_type']) ? $partnerInfos['vehicle_type'] : '';
        $data['delivery_type'] = !empty($partnerInfos['delivery_type']) ? $partnerInfos['delivery_type'] : '';

        $data['action'] = $this->url->link('account/account/addDeliveryInfo', '', true);

        $uploads_dir = 'image/deliverypartner/customer-'.$this->customer->getId().'/';
        if(isset($this->request->post['id_proof'])) {
            $data['link_id_proof'] = $this->request->post['id_proof'];
        } else if(isset($partnerInfos['id_proof']) && $partnerInfos['id_proof']) {
            $data['link_id_proof'] =$this->config->get('config_ssl').$uploads_dir.$partnerInfos['id_proof'];
        } else {
            $data['link_id_proof'] = '';
        }

        if(isset($this->request->post['address_proof'])) {
            $data['link_address_proof'] = $this->request->post['address_proof'];
        } else if(isset($partnerInfos['address_proof']) && $partnerInfos['address_proof']) {
            $data['link_address_proof'] = $this->config->get('config_ssl').$uploads_dir.$partnerInfos['address_proof'];
        } else {
            $data['link_address_proof'] = '';
        }

        if(isset($this->request->post['travel_license'])) {
            $data['link_travel_license'] = $this->request->post['travel_license'];
        } else if(isset($partnerInfos['travel_license']) && $partnerInfos['travel_license']) {
            $data['link_travel_license'] =$this->config->get('config_ssl').$uploads_dir.$partnerInfos['travel_license'];
        } else {
            $data['link_travel_license'] = '';
        }

        if(isset($this->request->post['vehicle_insurance'])) {
            $data['link_vehicle_insurance'] = $this->request->post['vehicle_insurance'];
        } else if(isset($partnerInfos['vehicle_insurance']) && $partnerInfos['vehicle_insurance']) {
            $data['link_vehicle_insurance'] =$this->config->get('config_ssl').$uploads_dir.$partnerInfos['vehicle_insurance'];
        } else {
            $data['link_vehicle_insurance'] = '';
        }

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
