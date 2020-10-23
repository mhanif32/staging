<?php

class ControllerAccountRequest extends Controller
{
    private $error = array();

    public function index()
    {
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
            $isRequestAcceptedByOther = $this->model_account_request->getRequestAcceptedByOther($request['order_id']);
            $requestArray = array();
            if (!empty($orderData['firstname']) && empty($isRequestAcceptedByOther)) {
                $requestArray['request_id'] = $request['request_id'];
                //$customer = $this->model_account_customer->getCustomer($request['customer_id']);
                $requestArray['customer_name'] = @$orderData['firstname'] . ' ' . @$orderData['lastname'];
                $requestArray['order_id'] = $request['order_id'];
                $requestArray['delivery_location'] = $orderData['shipping_address_1'] . ', ' . $orderData['shipping_city'] . ', ' . $orderData['shipping_zone'] . ', ' . $orderData['shipping_country'];
                $seller = $this->model_account_request->getMpSellerdata($request['mpseller_id']);

                if (empty($seller)) {
                    continue;
                }
                $requestArray['mpseller_name'] = !empty($seller) ? $seller['store_owner'] : '';
                $requestArray['requested_date'] = $request['requested_date'];
                $requestArray['is_accept'] = $request['is_accept'];
                $requestArray['view_link'] = $this->url->link('account/request/view', '&id=' . $request['request_id'], true);
                $requestSection[] = $requestArray;
            }
        }

        $data['requests'] = $requestSection;
        $data['heading_title'] = $this->language->get('heading_title');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('account/requests', $data));
    }

    public function view()
    {
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
        $otherRequests = $this->model_account_request->getOtherRequest($requestData['order_id']);
        $isAcceptedByOthers = empty($otherRequests) ? true : false;
        $seller = $this->model_account_request->getMpSellerdata($requestData['mpseller_id']);
        $sellerZoneData = $this->model_localisation_zone->getZone($seller['zone_id']);
        $sellerCountryData = $this->model_localisation_country->getCountry($seller['country_id']);
        $orderData = $this->model_account_request->getOrderData($requestData['order_id']);
        $data['seller'] = array(
            'store_owner' => $seller['store_owner'],
            'store_name' => $seller['store_name'],
            'address' => $seller['address'],
            'city' => $seller['city'],
            'zone' => $sellerZoneData,
            'country' => $sellerCountryData,
            'email' => $seller['email'],
            'telephone' => $seller['telephone']
        );
        $data['isAcceptedByOthers'] = $isAcceptedByOthers;
        $data['is_accept'] = $requestData['is_accept'];
        $data['request_id'] = $requestData['request_id'];
        $data['status'] = isset($requestData['status']) ? $requestData['status'] : '';
        $data['order'] = $orderData;
        $data['heading_title_view'] = $this->language->get('heading_title_view');

        //estimated delivery fee
        $key = $this->config->get('config_google_distance_api_key');
        $sellerAddress = [
            $seller['address'],
            $seller['city'],
            $sellerZoneData['name'],
            $sellerCountryData['name']
        ];
        $sellerAddress = implode(', ', $sellerAddress);
        $customerArray = [
            $orderData['shipping_address_1'],
            $orderData['shipping_city'],
            $orderData['shipping_zone'],
            $orderData['shipping_country']
        ];
        $customerAddress = implode(', ', $customerArray);

        $addressFrom = $sellerAddress;
        $addressTo = $customerAddress;
        $distance = $this->getDistance($addressFrom, $addressTo, "K", $key);

        $configFlatCharge = (float)$this->config->get('config_flat_delivery_partner_fee');
        $configFeeDistance = (float)$this->config->get('config_delivery_partner_fee_per_distance');
        $del_fee = $configFlatCharge + ($configFeeDistance * $distance);
        $dataCharges = $this->currency->format($del_fee, $orderData['currency_code'], $orderData['currency_value']);
        $data['estm_delivery_fee'] = $dataCharges;
        $data['is_distance'] = $distance;

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

        if ($this->request->post['request_id']) {
            $requestId = $this->request->post['request_id'];
            $requestData = $this->model_account_request->getRequestData($requestId);

            //check if already accepted by others
            $otherRequests = $this->model_account_request->getOtherRequest($requestData['order_id']);
            if (empty($otherRequests)) {
                $customer = $this->model_account_customer->getCustomer($requestData['customer_id']);
                if (!empty($requestData)) {

                    $this->model_account_request->updateRequest($requestId, $isAccept = 1);
                    $json['success'] = 'You have successfully accepted the Delivery Request.';

                    $this->load->model('account/customer');
                    $customerData = $this->model_account_customer->getCustomer($this->customer->getId());
                    $deliveryPartData = $this->model_account_customer->getCustomer($requestData['delivery_partner_id']);
                    $sellerData = $this->model_account_request->getMpSellerdata($requestData['mpseller_id']);
                    $orderData = $this->model_account_order->getOrderForDelivery($requestData['order_id']);

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
                    $dataMail['deliveryPartnerName'] = $deliveryPartData['firstname'] . ' ' . $deliveryPartData['lastname'];
                    $dataMail['store_owner'] = $sellerData['store_owner'];
                    $dataMail['seller_address'] = $sellerData['address'];
                    $dataMail['customerName'] = $customerData['firstname'] . ' ' . $customerData['lastname'];
                    $dataMail['customer_address'] = $orderData['shipping_address_1'] . ' ' . $orderData['shipping_city'] . ' ' . $orderData['shipping_zone'] . ' ' . $orderData['shipping_country'];
                    $dataMail['view_request_link'] = $this->url->link('account/request/view', '&id=' . $requestData['request_id'], true);
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

                    $dataAdminMail['delivery_partner_name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
                    $dataAdminMail['customer_name'] = $customer['firstname'] . ' ' . $customer['lastname'];
                    $dataAdminMail['orderId'] = $requestData['order_id'];
                    $dataAdminMail['store_owner'] = $sellerData['store_owner'];
                    $dataAdminMail['order_link'] = $this->url->link('account/order/info', '&order_id=' . $requestData['order_id'], true);

                    $dataAdminMail['customer_address'] = $orderData['shipping_address_1'] . ' ' . $orderData['shipping_city'] . ' ' . $orderData['shipping_zone'] . ' ' . $orderData['shipping_country'];

                    $mailTextAdmin = $this->load->view('mail/adm_request_accept_alert', $dataAdminMail);
                    $mail->setHtml($mailTextAdmin);
                    $mail->setText(html_entity_decode($mailTextAdmin, ENT_QUOTES, 'UTF-8'));
                    $mail->send();

                    //send mail to the seller
                    $orderSellers = $this->model_account_order->getMpsellerFromOrder($requestData['order_id']);
                    foreach ($orderSellers as $seller) {
                        $dataSellerMail = [];
                        $mail = new Mail($this->config->get('config_mail_engine'));
                        $mail->parameter = $this->config->get('config_mail_parameter');
                        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                        $mail->setTo($seller['email']);
                        $mail->setFrom($this->config->get('config_email'));
                        $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                        $mail->setSubject(html_entity_decode(sprintf('The Champion Mall : Accepted Delivery Request By Delivery Partner', $this->config->get('config_name'), $requestData['order_id']), ENT_QUOTES, 'UTF-8'));
                        if ($this->request->server['HTTPS']) {
                            $server = $this->config->get('config_ssl');
                        } else {
                            $server = $this->config->get('config_url');
                        }
                        $dataSellerMail['logo'] = $server . 'image/' . $this->config->get('config_logo');
                        $dataSellerMail['order_id'] = $requestData['order_id'];
                        $dataSellerMail['store_owner'] = $seller['store_owner'];
                        $dataSellerMail['customerName'] = $customerData['firstname'] . ' ' . $customerData['lastname'];
                        $dataSellerMail['delivery_partner_name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
                        $dataSellerMail['view_order_link'] = $this->url->link('account/mpmultivendor/orders/info', '&order_id=' . $requestData['order_id'], true);
                        $mailTextSeller = $this->load->view('mail/request_accept_alert_seller', $dataSellerMail);
                        $mail->setHtml($mailTextSeller);
                        $mail->setText(html_entity_decode($mailTextSeller, ENT_QUOTES, 'UTF-8'));
                        $mail->send();
                    }
                }
            } else {
                $json['warning'] = 'The Delivery Request has been already accepted by other delivery partner.';
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
        if ($this->request->post['request_id']) {

            $requestId = $this->request->post['request_id'];
            $requestData = $this->model_account_request->getRequestData($requestId);

            //check if already accepted by others
            $otherRequests = $this->model_account_request->getOtherRequest($requestData['order_id']);
            if (empty($otherRequests)) {
                $customer = $this->model_account_customer->getCustomer($requestData['customer_id']);
                $deliveryPartner = $this->model_account_customer->getCustomer($requestData['delivery_partner_id']);
                if (!empty($requestData)) {

                    $this->model_account_request->updateRequest($requestId, $isAccept = 2);
                    $json['success'] = 'You have successfully declined the Delivery Request.';
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

                    $sellerData = $this->model_account_request->getMpSellerdata($requestData['mpseller_id']);
                    $dataMail['seller_name'] = $sellerData['store_owner'];
                    $dataMail['deliveryPartnerName'] = $deliveryPartner['firstname'] . ' ' . $deliveryPartner['lastname'];

                    $dataMail['customerName'] = $customer['firstname'] . ' ' . $customer['lastname'];

                    $mailText = $this->load->view('mail/dp_request_decline_alert', $dataMail);
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

                    $data['delivery_partner_name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
                    $data['customer_name'] = $customer['firstname'] . ' ' . $customer['lastname'];
                    $data['orderId'] = $requestData['order_id'];
                    $data['logo'] = $server . 'image/' . $this->config->get('config_logo');

                    $mailText = $this->load->view('mail/adm_request_decline_alert', $data);
                    $mail->setHtml($mailText);
                    $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                    $mail->send();
                }
            } else {
                $json['warning'] = 'The Delivery Request has been already accepted by other delivery partner.';
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function orders()
    {
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
            $sellerOrderHistory = $this->model_account_request->getSellerOrderStatus($order['order_id'], $order['mpseller_id']);
            $deliveryRequest = $this->model_account_request->getDeliveryOrderRequestStatus($order['order_id']);
            $seller = $this->model_account_request->getMpSellerdata($order['mpseller_id']);

            if (!empty($seller)) {
                $orderArray = array();
                $orderArray['request_id'] = $order['request_id'];
                $orderArray['customer_name'] = $order['firstname'] . ' ' . $order['lastname'];
                $orderArray['order_id'] = $order['order_id'];
                $orderArray['delivery_location'] = $order['shipping_address_1'] . ', ' . $order['shipping_city'] . ', ' . $order['shipping_zone'] . ', ' . $order['shipping_country'];

                $orderArray['mpseller_name'] = $seller['store_owner'];
                $orderArray['seller_order_status'] = !empty($sellerOrderHistory['name']) ? $sellerOrderHistory['name'] : '-';
                $orderArray['delivery_status'] = !empty($deliveryRequest['status']) ? $deliveryRequest['status'] : '-';
                $orderArray['assignedOrderView'] = $this->url->link('account/request/assignedOrderView', '&id=' . $order['request_id'], true);
                $orderSection[] = $orderArray;
            }
        }

        $data['orders'] = $orderSection;
        $data['heading_title'] = 'Assigned Orders';
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('account/assigned_orders', $data));
    }

    public function assignedOrderView()
    {
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
        $sellerOrderHistory = $this->model_account_request->getSellerOrderStatus($requestData['order_id'], $requestData['mpseller_id']);
        $sellerOrderData = $this->model_account_request->getOrderEstimatedDates($requestData['order_id']);

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
        $data['status'] = $requestData['status'];
        $data['seller_order_status'] = !empty($sellerOrderHistory['name']) ? $sellerOrderHistory['name'] : '-';
        $data['my_delivery_date'] = !empty($sellerOrderData['my_delivery_date']) ? $sellerOrderData['my_delivery_date'] : '';
        $data['estimated_date'] = !empty($sellerOrderData['estimated_date']) ? $sellerOrderData['estimated_date'] : '';
        $data['customer_comment'] = !empty($sellerOrderData['comment']) ? $sellerOrderData['comment'] : '-';
        $data['order'] = $this->model_account_request->getOrderData($requestData['order_id']);
        $data['heading_title_view'] = $this->language->get('heading_title_view');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $data['action_order_request_status'] = $this->url->link('account/request/update-status', '&id=' . $requestData['request_id'], true);
        $this->response->setOutput($this->load->view('account/assigned_order_view', $data));
    }

    public function updateStatus()
    {
        $this->load->model('account/request');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');

        $requestId = $this->request->get['id'];
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            if (!empty($this->request->post['selectStatus'])) {

                $this->model_account_request->updateStatus($requestId, $this->request->post);
                $this->session->data['success'] = 'Your order status has been successfully updated.';

                $requestData = $this->model_account_request->getRequestData($requestId);
                $orderData = $this->model_account_request->getOrderData($requestData['order_id']);
                $sellerData = $this->model_account_request->getMpSellerdata($requestData['mpseller_id']);

                $dp_status = $this->request->post['selectStatus'];
                if ($dp_status == 'Parcel delivered') {

                    //calculate distance
                    $customerArray = [
                        $orderData['shipping_address_1'],
                        $orderData['shipping_city'],
                        $orderData['shipping_zone'],
                        $orderData['shipping_country']
                    ];
                    $customerAddress = implode(', ', $customerArray);

                    $countryData = $this->model_localisation_country->getCountry($sellerData['country_id']);
                    $zoneData = $this->model_localisation_zone->getZone($sellerData['zone_id']);
                    $sellerArray = [
                        $sellerData['address'],
                        $sellerData['city'],
                        $zoneData['name'],
                        $countryData['name']
                    ];
                    $sellerAddress = implode(', ', $sellerArray);

                    //update delivery charges on the basis of distance between customer & seller
                    $key = $this->config->get('config_google_distance_api_key');
                    $addressFrom = $sellerAddress;
                    $addressTo = $customerAddress;
                    $distance = $this->getDistance($addressFrom, $addressTo, "K", $key);

                    $configFlatCharge = (float)$this->config->get('config_flat_delivery_partner_fee');
                    $configFeeDistance = (float)$this->config->get('config_delivery_partner_fee_per_distance');
                    $dataCharges = array(
                        'delivery_charges' => $configFlatCharge + ($configFeeDistance * $distance),
                        'currency' => $orderData['currency_code']
                    );
                    $this->model_account_request->updateCharges($requestId, $dataCharges);
                }

                //send mail to seller when updates
                if ($this->request->server['HTTPS']) {
                    $server = $this->config->get('config_ssl');
                } else {
                    $server = $this->config->get('config_url');
                }
                $mail = new Mail($this->config->get('config_mail_engine'));
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                $mail->setTo($sellerData['email']);
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                $mail->setSubject(html_entity_decode(sprintf('The Champion Mall : Delivery Partner Changed Status', $this->config->get('config_name'), $requestData['order_id']), ENT_QUOTES, 'UTF-8'));
                $dataDp['logo'] = $server . 'image/' . $this->config->get('config_logo');
                $dataDp['store'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
                $dataDp['seller_name'] = $sellerData['store_owner'];
                $dataDp['orderId'] = '#' . $requestData['order_id'];
//                $delPartnerData = $this->model_account_customer->getCustomer($deliveryPartner['customer_id']);
//
//                if(!empty($delPartnerData)) { // if delivery partner not found
//                    $dataDp['delivery_partner_name'] = $delPartnerData['firstname'] . ' ' . $delPartnerData['lastname'];
//                    $dataDp['orderId'] = '#' . $orderId;
//                    $dataDp['delivery_address'] = $shippingAddress['address_1'] . ', ' . $shippingAddress['city'] . ', ' . $shippingAddress['zone'] . ', ' . $shippingAddress['country'];
//                    $dataDp['seller_name'] = $mpSellerData['store_owner'];
//
//                    //echo '<pre>';print_r($mpSellerData);exit('asd');
//                    $zone = $this->model_localisation_zone->getZone($mpSellerData['zone_id']);
//                    $country = $this->model_localisation_country->getCountry($mpSellerData['country_id']);
//
//                    $dataDp['seller_address'] = $mpSellerData['address'] . ', ' . $mpSellerData['city'] . ', ' . $zone['name'] . ', ' . $country['name'];
                $dataDp['request_view_link'] = $this->url->link('account/request/index', '', true);
                $mailText = $this->load->view('mail/dp_changed_status_toseller', $dataDp);
                $mail->setHtml($mailText);
                $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                $mail->send();

            }
        }

        //$this->response->redirect($this->url->link('account/request/view', 'id='.$requestId, true));
        $this->response->redirect($this->url->link('account/request/orders', '', true));
    }

    protected function getDistance($addressFrom, $addressTo, $unit = '', $apiKey)
    {
        // Change address format
        $formattedAddrFrom = str_replace(' ', '+', $addressFrom);
        $formattedAddrTo = str_replace(' ', '+', $addressTo);

        // Geocoding API request with start address
        $geocodeFrom = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . $formattedAddrFrom . '&sensor=false&key=' . $apiKey);
        $outputFrom = json_decode($geocodeFrom);
        if (!empty($outputFrom->error_message)) {
            return $outputFrom->error_message;
        }

        // Geocoding API request with end address
        $geocodeTo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . $formattedAddrTo . '&sensor=false&key=' . $apiKey);
        $outputTo = json_decode($geocodeTo);
        if (!empty($outputTo->error_message)) {
            return $outputTo->error_message;
        }

        // Get latitude and longitude from the geodata
        if(!empty($outputFrom->results[0]) && !empty($outputTo->results[0])) {

            $latitudeFrom = isset($outputFrom->results[0]) ? $outputFrom->results[0]->geometry->location->lat : '';
            $longitudeFrom = isset($outputFrom->results[0]) ? $outputFrom->results[0]->geometry->location->lng : '';
            $latitudeTo = isset($outputTo->results[0]) ? $outputTo->results[0]->geometry->location->lat : '';
            $longitudeTo = isset($outputTo->results[0]) ? $outputTo->results[0]->geometry->location->lng : '';

            // Calculate distance between latitude and longitude
            $theta = $longitudeFrom - $longitudeTo;
            $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) + cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;

            // Convert unit and return distance
            $unit = strtoupper($unit);
            if ($unit == "K") {
                return round($miles * 1.609344, 2);
            } elseif ($unit == "M") {
                return round($miles * 1609.344, 2) . ' meters';
            } else {
                return round($miles, 2) . ' miles';
            }
        } else {
            return false;
        }
    }
}