<?php

class ControllerCheckoutSuccess extends Controller
{
    public function index()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        $this->load->language('checkout/success');

        $this->load->model('account/request');
        $this->load->model('account/customer');
        $this->load->model('account/order');
        $this->load->model('localisation/zone');
        $this->load->model('localisation/country');

        if (isset($this->session->data['order_id'])) {

            $orderId = $this->session->data['order_id'];

            //TODO Update Order : Set order delivery date

            //Generated alphanumeric order number
            $invoice_no = $this->model_account_order->createInvoiceNo($orderId);

            //START : send request to delivery partner
            if (!empty($this->session->data['shipping_address'])) {

                $shippingAddress = $this->session->data['shipping_address'];

                //get total seller
                $cartProducts = $this->cart->getProducts();
                $sellerList = array();
                foreach ($cartProducts as $product) {
                    $sellerList[] = $product['mpseller_id'];
                }
                $totalSellers = array_unique($sellerList);
                //For Single Seller
                if (count($totalSellers) == 1) {
                    $mpSellerData = $this->model_account_request->getMpSellerdata($totalSellers[0]);

                    //Same City
                    if (!empty($mpSellerData['city']) && strtolower($mpSellerData['city']) == strtolower($shippingAddress['city']) && $shippingAddress['zone_id'] == $mpSellerData['zone_id']) {

                        $deliveryPartners = $this->model_account_request->sendRequestToDeliveryPartner($orderId, $shippingAddress, $mpSellerData['mpseller_id'], 1);
                    }
                    //Same State
                    if (!empty($mpSellerData['city']) && strtolower($mpSellerData['city']) != strtolower($shippingAddress['city']) && $shippingAddress['zone_id'] == $mpSellerData['zone_id']) {

                        $deliveryPartners = $this->model_account_request->sendRequestToDeliveryPartner($orderId, $shippingAddress, $mpSellerData['mpseller_id'], 2);
                    }
                    //Same Country
                    if (!empty($mpSellerData['city']) && strtolower($mpSellerData['city']) != strtolower($shippingAddress['city']) && $shippingAddress['zone_id'] != $mpSellerData['zone_id'] && $shippingAddress['country_id'] == $mpSellerData['country_id']) {

                        $deliveryPartners = $this->model_account_request->sendRequestToDeliveryPartner($orderId, $shippingAddress, $mpSellerData['mpseller_id'], 3);
                    }

                    if ($this->request->server['HTTPS']) {
                        $server = $this->config->get('config_ssl');
                    } else {
                        $server = $this->config->get('config_url');
                    }
                    //Mail send to delivery partners
//                    $dataMail = [];
//                    $mail = new Mail($this->config->get('config_mail_engine'));
//                    $mail->parameter = $this->config->get('config_mail_parameter');
//                    $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
//                    $mail->smtp_username = $this->config->get('config_mail_smtp_username');
//                    $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
//                    $mail->smtp_port = $this->config->get('config_mail_smtp_port');
//                    $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                    if (!empty($deliveryPartners)) {
                        foreach ($deliveryPartners as $deliveryPartner) {
                            $dataDp = [];
                            $mail = new Mail($this->config->get('config_mail_engine'));
                            $mail->parameter = $this->config->get('config_mail_parameter');
                            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                            $mail->setTo($deliveryPartner['email']);
                            $mail->setFrom($this->config->get('config_email'));
                            $mail->setSender(html_entity_decode($mpSellerData['store_name'], ENT_QUOTES, 'UTF-8'));
                            $mail->setSubject(html_entity_decode(sprintf('The Champion Mall : Delivery Request', $this->config->get('config_name'), $orderId), ENT_QUOTES, 'UTF-8'));
                            $dataDp['logo'] = $server . 'image/' . $this->config->get('config_logo');
                            $dataDp['store'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
                            $delPartnerData = $this->model_account_customer->getCustomer($deliveryPartner['customer_id']);

                            if(!empty($delPartnerData)) { // if delivery partner not found
                                $dataDp['delivery_partner_name'] = $delPartnerData['firstname'] . ' ' . $delPartnerData['lastname'];
                                $dataDp['orderId'] = '#' . $orderId;
                                $dataDp['delivery_address'] = $shippingAddress['address_1'] . ', ' . $shippingAddress['city'] . ', ' . $shippingAddress['zone'] . ', ' . $shippingAddress['country'];
                                $dataDp['seller_name'] = $mpSellerData['store_owner'];

                                //echo '<pre>';print_r($mpSellerData);exit('asd');
                                $zone = $this->model_localisation_zone->getZone($mpSellerData['zone_id']);
                                $country = $this->model_localisation_country->getCountry($mpSellerData['country_id']);

                                $dataDp['seller_address'] = $mpSellerData['address'] . ', ' . $mpSellerData['city'] . ', ' . $zone['name'] . ', ' . $country['name'];
                                $dataDp['request_view_link'] = $this->url->link('account/request/index', '', true);
                                $mailText = $this->load->view('mail/order_delivery_alert', $dataDp);
                                $mail->setHtml($mailText);
                                $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                                $mail->send();
                            }
                        }
                    }

                    //send mail to admin
                    //commented : because already sending mail to admin : order_alertd
                    /*$dataAdmin = [];
                    $mail->setTo($this->config->get('config_email'));
                    $mail->setFrom($this->config->get('config_email'));
                    $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                    $mail->setSubject(html_entity_decode(sprintf('The Champion Mall : Customer New Order ', $this->config->get('config_name'), $orderId), ENT_QUOTES, 'UTF-8'));
                    $dataAdmin['logo'] = $server . 'image/' . $this->config->get('config_logo');
                    $dataAdmin['store'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
                    $dataAdmin['order_id'] =  '#' . $orderId;
                    $dataAdmin['customer_address'] = $shippingAddress['address_1'] . ', ' . $shippingAddress['city'] . ', ' . $shippingAddress['zone'] . ', ' . $shippingAddress['country'];
                    $dataAdmin['customer_name'] = $this->customer->getFirstName().' '.$this->customer->getLastName();
                    $dataAdmin['seller_name'] = $mpSellerData['store_owner'];
                    $dataAdmin['order_link'] = $this->config->get('config_url') . '/admin/index.php?route=sale/order&user_token=' . $this->session->data['user_token'];

                    $mailText = $this->load->view('mail/order_admin_alert', $dataAdmin);
                    $mail->setHtml($mailText);
                    $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                    $mail->send();*/
                }
            }
            //END

            $this->cart->clear();
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['guest']);
            unset($this->session->data['comment']);
            unset($this->session->data['order_id']);
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);
            unset($this->session->data['totals']);
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        if ($this->customer->isLogged()) {
            $data['text_message'] = sprintf($this->language->get('text_customer'),
                $this->url->link('account/account', '', true),
                $this->url->link('account/order', '', true),
                $this->url->link('account/download', '', true),
                $orderId,
                $invoice_no,
                $this->url->link('information/contact')
            );
        } else {
            $data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
        }

        $data['continue'] = $this->url->link('account/edit');
//		$data['column_left'] = $this->load->controller('common/column_left');
//		$data['column_right'] = $this->load->controller('common/column_right');
//		$data['content_top'] = $this->load->controller('common/content_top');
//		$data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('common/success', $data));
    }
}