<?php
class ControllerAccountRequest extends Controller {
    private $error = array();

    public function index() {

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/request', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->language('account/request');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $this->load->model('account/request');
        $this->load->model('account/customer');
        $this->load->model('account/mpmultivendor/seller');

        $requests = $this->model_account_request->getDeliveryRequest($this->customer->getId());
        $requestSection = [];
        foreach ($requests as $request) {

            $orderData = $this->model_account_request->getOrderData($request['order_id']);



            $requestArray = array();
            $requestArray['request_id'] = $request['request_id'];
            $customer = $this->model_account_customer->getCustomer($request['customer_id']);
            $requestArray['customer_name'] = $customer['firstname'].' '.$customer['lastname'];
            $requestArray['order_id'] = $request['order_id'];
            $requestArray['delivery_location'] = $orderData['shipping_address_1'].', '.$orderData['shipping_city'].', '.$orderData['shipping_zone'].', '.$orderData['shipping_country'];
            $seller = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($request['mpseller_id']);
            $requestArray['mpseller_name'] = $seller['store_owner'];
            $requestArray['requested_date'] = $request['requested_date'];
            $requestArray['view_link'] = $this->url->link('account/request/view', '&id='.$request['request_id'], true);
            $requestSection[] = $requestArray;
        }

        $data['requests'] = $requestSection;
        $data['heading_title'] = $this->language->get('heading_title');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('account/requests', $data));
    }

    public function view() {

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/request', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->language('account/request');
        $this->document->setTitle($this->language->get('heading_title_view'));
        $this->load->model('account/request');
        $this->load->model('account/mpmultivendor/seller');
        $this->load->model('localisation/zone');
        $this->load->model('localisation/country');

        $requestData = $this->model_account_request->getRequestData($this->request->get['id']);
        $seller = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($requestData['mpseller_id']);

        $data['seller'] = array(
            'store_owner' => $seller['store_owner'],
            'store_name' => $seller['store_name'],
            'address' => $seller['address'],
            'city' => $seller['city'],
            'zone' => $this->model_localisation_zone->getZone($seller['zone_id']),
            'country' => $this->model_localisation_country->getCountry($seller['country_id']),
            'email' => $seller['email'],
            'telephone' => $seller['telephone']
        );
        $data['is_accept'] = $requestData['is_accept'];
        $data['request_id'] = $requestData['request_id'];
        $data['order'] = $this->model_account_request->getOrderData($requestData['order_id']);
        $data['heading_title_view'] = $this->language->get('heading_title_view');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('account/request_view', $data));
    }

    public function acceptRequest()
    {
        $json = array();
        $this->load->model('account/request');
        if($this->request->post['request_id']) {
            $requestId = $this->request->post['request_id'];
            $this->model_account_request->updateRequest($requestId, $isAccept = 1);

            $json['success'] = 'Your request has been successfully accepted.';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function declineRequest()
    {
        $json = array();
        $this->load->model('account/request');
        if($this->request->post['request_id']) {
            $requestId = $this->request->post['request_id'];
            $this->model_account_request->updateRequest($requestId, $isAccept = 2);

            $json['success'] = 'Your request has been successfully declined.';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}