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

            //Update Order : Set order delivery date
//            if($this->session->data['isSuperMarketProduct'] == true) {
//                $estDeliveryDays = (int) $this->config->get('config_maxGroceryDays');
//            } else {
//                $this->session->data['shipping_address']['']
//
//                $estDeliveryDays = (int) $this->config->get('config_maxGeneralDays');
//            }




            //Generated alphanumeric order number
            $invoice_no = $this->model_account_order->createInvoiceNo($orderId);

            //START : send request to delivery partner
            $shippingMethod = $this->session->data['shipping_method'];
            if (!empty($this->session->data['shipping_address']) && $shippingMethod['code'] == 'partner_shipping.partner_shipping') {

                $shipAdd = $this->session->data['shipping_address'];

                //get total seller
                $cartProducts = $this->cart->getProducts();
                $sellerList = array();
                foreach ($cartProducts as $product) {
                    $sellerList[] = $product['mpseller_id'];
                }
                $totalSellers = array_unique($sellerList);
                foreach ($totalSellers as $sellerId) {

                    $seller = $this->model_account_request->getMpSellerdata($sellerId);

                    //Same City
                    if (!empty($seller['city']) && strtolower($seller['city']) == strtolower($shipAdd['city']) && $shipAdd['zone_id'] == $seller['zone_id']) {

                        $deliveryPartners = $this->model_account_request->sendRequestToDeliveryPartner($orderId, $shipAdd, $seller['mpseller_id'], 1);
                    }

                    //Same State
                    //if (!empty($seller['city']) && strtolower($seller['city']) != strtolower($shipAdd['city']) && $shipAdd['zone_id'] == $seller['zone_id']) {
                    if (!empty($seller['city']) && $shipAdd['zone_id'] == $seller['zone_id']) {
                        $deliveryPartners = $this->model_account_request->sendRequestToDeliveryPartner($orderId, $shipAdd, $seller['mpseller_id'], 2);
                    }

                    //Same Country
                    if (!empty($seller['city']) && $shipAdd['country_id'] == $seller['country_id']) {

                        $deliveryPartners = $this->model_account_request->sendRequestToDeliveryPartner($orderId, $shipAdd, $seller['mpseller_id'], 3);
                    }

                    if ($this->request->server['HTTPS']) {
                        $server = $this->config->get('config_ssl');
                    } else {
                        $server = $this->config->get('config_url');
                    }

                    //Mail send to delivery partners
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
                            $mail->setSender(html_entity_decode($seller['store_name'], ENT_QUOTES, 'UTF-8'));
                            $mail->setSubject(html_entity_decode(sprintf('The Champion Mall : Delivery Request', $this->config->get('config_name'), $orderId), ENT_QUOTES, 'UTF-8'));
                            $dataDp['logo'] = $server . 'image/' . $this->config->get('config_logo');
                            $dataDp['store'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
                            $delPartnerData = $this->model_account_customer->getCustomer($deliveryPartner['customer_id']);

                            if(!empty($delPartnerData)) { // if delivery partner not found
                                $dataDp['delivery_partner_name'] = $delPartnerData['firstname'] . ' ' . $delPartnerData['lastname'];
                                $dataDp['orderId'] = '#' . $orderId;
                                $dataDp['delivery_address'] = $shipAdd['address_1'] . ', ' . $shipAdd['city'] . ', ' . $shipAdd['zone'] . ', ' . $shipAdd['country'];
                                $dataDp['seller_name'] = $seller['store_owner'];
                                $zone = $this->model_localisation_zone->getZone($seller['zone_id']);
                                $country = $this->model_localisation_country->getCountry($seller['country_id']);

                                $dataDp['seller_address'] = $seller['address'] . ', ' . $seller['city'] . ', ' . $zone['name'] . ', ' . $country['name'];
                                $dataDp['request_view_link'] = $this->url->link('account/request/index', '', true);
                                $mailText = $this->load->view('mail/order_delivery_alert', $dataDp);
                                $mail->setHtml($mailText);
                                $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                                $mail->send();
                            }
                        }
                    }
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
                $orderId,
                $invoice_no,
                $this->url->link('account/download', '', true)
            );
        } else {
            $data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
        }

        $data['continue'] = $this->url->link('account/edit');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('common/success', $data));
    }
}