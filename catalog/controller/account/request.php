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
            //$customer = $this->model_account_customer->getCustomer($request['customer_id']);
            $requestArray['customer_name'] = $orderData['firstname'].' '.$orderData['lastname'];
            $requestArray['order_id'] = $request['order_id'];
            $requestArray['delivery_location'] = $orderData['shipping_address_1'].', '.$orderData['shipping_city'].', '.$orderData['shipping_zone'].', '.$orderData['shipping_country'];
            $seller = $this->model_account_request->getMpSellerdata($request['mpseller_id']);
            $requestArray['mpseller_name'] = $seller['store_owner'];
            $requestArray['requested_date'] = $request['requested_date'];
            $requestArray['is_accept'] = $request['is_accept'];
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

        error_reporting(E_ALL);
        ini_set("display_errors", 1);

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
        $seller = $this->model_account_request->getMpSellerdata($requestData['mpseller_id']);

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
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        $json = array();
        $this->load->model('account/request');
        $this->load->model('account/order');
        $this->load->model('account/customer');

        if($this->request->post['request_id']) {
            $requestId = $this->request->post['request_id'];
            $requestData = $this->model_account_request->getRequestData($requestId);

            $customer = $this->model_account_customer->getCustomer($requestData['customer_id']);
            if(!empty($requestData)) {
                $this->model_account_request->updateRequest($requestId, $isAccept = 1);
                $json['success'] = 'Your request has been successfully accepted.';

                $this->load->model('account/customer');
                $customerData = $this->model_account_customer->getCustomer($this->customer->getId());

                $deliveryPartData = $this->model_account_customer->getCustomer($requestData['delivery_partner_id']);

                $sellerData = $this->model_account_request->getMpSellerdata($requestData['mpseller_id']);

                $orderData = $this->model_account_order->getOrder($requestData['order_id']);

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
                if ($this->request->server['HTTPS']) {
                    $server = $this->config->get('config_ssl');
                } else {
                    $server = $this->config->get('config_url');
                }
                $dataMail['logo'] = $server . 'image/' . $this->config->get('config_logo');
                $dataMail['order_id'] = $requestData['order_id'];
                $dataMail['deliveryPartnerName'] = $deliveryPartData['firstname'] . ' ' .$deliveryPartData['lastname'];
                $dataMail['store_owner'] = $sellerData['store_owner'];
                $dataMail['seller_address'] = $sellerData['address'];
                $dataMail['customerName'] = $customerData['firstname'] . ' ' . $customerData['lastname'];
                $dataMail['customer_address'] = $orderData['shipping_address_1'].' '. $orderData['shipping_city'].' '. $orderData['shipping_zone'].' '. $orderData['shipping_country'];
                $dataMail['view_request_link'] = $this->url->link('account/request/view', '&id='.$requestData['request_id'], true);
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

                $dataAdminMail['delivery_partner_name'] = $this->customer->getFirstName() .' '. $this->customer->getLastName();
                $dataAdminMail['customer_name'] = $customer['firstname'] .' '. $customer['lastname'];
                $dataAdminMail['orderId'] = $requestData['order_id'];
                $dataAdminMail['store_owner'] = $sellerData['store_owner'];
                $dataAdminMail['order_link'] = $this->url->link('account/order/info', '&order_id='.$requestData['order_id'], true);

                $dataAdminMail['customer_address'] = $orderData['shipping_address_1'].' '. $orderData['shipping_city'].' '. $orderData['shipping_zone'].' '. $orderData['shipping_country'];

                $mailTextAdmin = $this->load->view('mail/adm_request_accept_alert', $dataAdminMail);
                $mail->setHtml($mailTextAdmin);
                $mail->setText(html_entity_decode($mailTextAdmin, ENT_QUOTES, 'UTF-8'));
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
        $this->load->model('account/customer');
        if($this->request->post['request_id']) {

            $requestId = $this->request->post['request_id'];
            $requestData = $this->model_account_request->getRequestData($requestId);

            $customer = $this->model_account_customer->getCustomer($requestData['customer_id']);
            $deliveryPartner = $this->model_account_customer->getCustomer($requestData['delivery_partner_id']);
            if(!empty($requestData)) {

                $this->model_account_request->updateRequest($requestId, $isAccept = 2);
                $json['success'] = 'Your request has been successfully declined.';
                if ($this->request->server['HTTPS']) {
                    $server = $this->config->get('config_ssl');
                } else {
                    $server = $this->config->get('config_url');
                }

                //Send Mail for declining Request to Delivery Partner
                $dataMail = [];
                $mail = new Mail($this->config->get('config_mail_engine'));
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
                $mail->setTo($deliveryPartner['email']);
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                $mail->setSubject(html_entity_decode(sprintf('The Champion Mall : Declined Delivery Request', $this->config->get('config_name'), $requestData['order_id']), ENT_QUOTES, 'UTF-8'));

                $dataMail['logo'] = $server . 'image/' . $this->config->get('config_logo');
                $dataMail['order_id'] = $requestData['order_id'];
                $dataMail['deliveryPartnerName'] = $deliveryPartner['firstname'] .' '. $deliveryPartner['lastname'];
//                $dataMail['store_owner'] = $sellerData['store_owner'];
//                $dataMail['seller_address'] = $sellerData['address'];
                $dataMail['customerName'] = $customer['firstname'] . ' ' . $customer['lastname'];

                $mailText = $this->load->view('mail/dp_request_accept_alert', $dataMail);
                $mail->setHtml($mailText);
                $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                $mail->send();

                //Send Mail for declining Request to Admin
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

                $data['delivery_partner_name'] = $this->customer->getFirstName() .' '. $this->customer->getLastName();
                $data['customer_name'] = $customer['firstname'] .' '. $customer['lastname'];
                $data['orderId'] = $requestData['order_id'];
                $data['logo'] = $server . 'image/' . $this->config->get('config_logo');

                $mailText = $this->load->view('mail/adm_request_decline_alert', $data);
                $mail->setHtml($mailText);
                $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                $mail->send();
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function orders() {
//        error_reporting(E_ALL);
//        ini_set("display_errors", 1);

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/request/orders', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->language('account/request');
        $this->document->setTitle('Assigned Orders');

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

        $assignedOrders = $this->model_account_request->getAssignedOrders($this->customer->getId());
        //echo '<pre>';print_r($assignedOrders);exit('assd');

        $orderSection = [];
        foreach ($assignedOrders as $order) {

            $orderData = $this->model_account_request->getOrderData($order['order_id']);

            $orderArray = array();
            $orderArray['request_id'] = $order['request_id'];
            $orderArray['customer_name'] = $order['firstname'].' '.$order['lastname'];
            $orderArray['order_id'] = $order['order_id'];
            $orderArray['delivery_location'] = $order['shipping_address_1'].', '.$order['shipping_city'].', '.$order['shipping_zone'].', '.$order['shipping_country'];
            $seller = $this->model_account_request->getMpSellerdata($order['mpseller_id']);
            $orderArray['mpseller_name'] = $seller['store_owner'];
//            $requestArray['requested_date'] = $request['requested_date'];
//            $requestArray['is_accept'] = $request['is_accept'];
//            $requestArray['view_link'] = $this->url->link('account/request/view', '&id='.$request['request_id'], true);
            $orderSection[] = $orderArray;
        }

        $data['orders'] = $orderSection;
        $data['heading_title'] = 'Assigned Orders';
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('account/assigned_orders', $data));
    }
}