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
            $requestData = $this->model_account_request->getRequestData($requestId);
            if(!empty($requestData)) {
                $this->model_account_request->updateRequest($requestId, $isAccept = 1);
                $json['success'] = 'Your request has been successfully accepted.';

                $this->load->model('account/customer');
                $customerData = $this->model_account_customer->getCustomer($this->customer->getId());

                //Send Mail to Delivery Partner
                $dataMail = [];
                $mail = new Mail($this->config->get('config_mail_engine'));
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
                $mail->setTo($customerData['email']);
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                $mail->setSubject(html_entity_decode(sprintf('The Champion Mall : Accepted Delivery Request', $this->config->get('config_name'), $requestData['order_id']), ENT_QUOTES, 'UTF-8'));
                $mailText = $this->load->view('mail/dp_request_accept_alert', $dataMail);
                $mail->setHtml($mailText);
                $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                $mail->send();

                //Send Mail for Request Accept to Admin
                $dataAdminMail = [];
                $mail = new Mail($this->config->get('config_mail_engine'));
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                $mail->setTo($this->config->get('config_email'));
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                $mail->setSubject(html_entity_decode(sprintf('The Champion Mall : Accepted Delivery Request', $this->config->get('config_name'), $requestData['order_id']), ENT_QUOTES, 'UTF-8'));
                $mailText = $this->load->view('mail/adm_request_accept_alert', $dataAdminMail);
                $mail->setHtml($mailText);
                $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                $mail->send();
            }
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
            $requestId = $this->request->post['request_id'];
            $requestData = $this->model_account_request->getRequestData($requestId);
            if(!empty($requestData)) {

                $this->model_account_request->updateRequest($requestId, $isAccept = 2);
                $json['success'] = 'Your request has been successfully declined.';

                //Send Mail for Request Accept to Admin
                $data = [];
                $mail = new Mail($this->config->get('config_mail_engine'));
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                $mail->setTo($this->config->get('config_email'));
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                $mail->setSubject(html_entity_decode(sprintf('The Champion Mall : Declined Delivery Request', $this->config->get('config_name'), $requestData['order_id']), ENT_QUOTES, 'UTF-8'));
                $mailText = $this->load->view('mail/adm_request_decline_alert', $data);
                $mail->setHtml($mailText);
                $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                $mail->send();
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}