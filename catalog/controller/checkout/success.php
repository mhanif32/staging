<?php

class ControllerCheckoutSuccess extends Controller
{
    public function index()
    {

        $this->load->language('checkout/success');

        if (isset($this->session->data['order_id'])) {
            $orderId = $this->session->data['order_id'];

            //START : send request to delivery partner
            $this->load->model('account/request');
            $this->load->model('account/customer');
            if (!empty($this->session->data['shipping_address'])) {

                $shippingAddress = $this->session->data['shipping_address'];
                //echo '<pre>';print_r($shippingAddress);exit('aaa');

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
                    $dataMail = [];
                    $mail = new Mail($this->config->get('config_mail_engine'));
                    $mail->parameter = $this->config->get('config_mail_parameter');
                    $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                    $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                    $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                    $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                    $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

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

                            $dataDp['delivery_partner_name'] = $delPartnerData['firstname'].' '. $delPartnerData['lastname'];
                            $dataDp['orderId'] = '#'.$orderId;
                            $dataDp['delivery_address'] = $shippingAddress['address_1'].', '.$shippingAddress['city'].', '.$shippingAddress['zone'].', '.$shippingAddress['country'];
                            $dataDp['seller_name'] = $mpSellerData['store_owner'];
                            $dataDp['seller_address'] = $mpSellerData['address_1'].', '.$mpSellerData['city'].', '.$mpSellerData['zone'].', '.$mpSellerData['country'];
                            $dataDp['request_view_link'] = $this->url->link('account/request/index', '', true);
                            $mailText = $this->load->view('mail/order_delivery_alert', $dataDp);
                            $mail->setHtml($mailText);
                            $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                            $mail->send();
                        }
                    }

                    //send mail to admin
                    $dataAdmin = [];
                    $mail->setTo($this->config->get('config_email'));
                    $mail->setFrom($this->config->get('config_email'));
                    $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                    $mail->setSubject(html_entity_decode(sprintf('The Champion Mall : Customer New Order ', $this->config->get('config_name'), $orderId), ENT_QUOTES, 'UTF-8'));
                    $dataAdmin['logo'] = $server . 'image/' . $this->config->get('config_logo');
                    $dataAdmin['store'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
                    $mailText = $this->load->view('mail/order_admin_alert', $dataAdmin);
                    $mail->setHtml($mailText);
                    $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                    $mail->send();
                }
            }

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