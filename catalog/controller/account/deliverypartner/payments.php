<?php

class ControllerAccountDeliverypartnerPayments extends Controller
{
    public function index()
    {
//        $key = 'AIzaSyA1KlkW09_TLutu_Pg85h2YhU3jCRLqK1w';
//        $addressFrom = 'Wardha';
//        $addressTo = 'Nagpur';
//
//        // Get distance in km
//        $distance = $this->getDistance($addressFrom, $addressTo, "K", $key);
//        echo '<pre>';
//        print_r($distance);
//        exit('okoko');

        /*$address = str_replace(" ", "+", 'Mahal, Nagpur, India'); // replace all the white space with "+" sign to match with google search pattern

        $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address=$address&key=$key";

        $response = file_get_contents($url);

        $json = json_decode($response,TRUE); //generate array object from the response from the web
        print_r($json);
        echo ($json['results'][0]['geometry']['location']['lat'].",".$json['results'][0]['geometry']['location']['lng']);*/

        /*$address = "Kathmandu, Nepal";
        $url = "https://maps.google.com/maps/api/geocode/json?address=".urlencode($address)."&key=".$key;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $responseJson = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($responseJson);

        if ($response->status == 'OK') {
            $latitude = $response->results[0]->geometry->location->lat;
            $longitude = $response->results[0]->geometry->location->lng;

            echo 'Latitude: ' . $latitude;
            echo '<br />';
            echo 'Longitude: ' . $longitude;
        } else {
            echo $response->status;
            var_dump($response);
        }
        exit('okoko');*/

        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/order', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->language('account/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $url = '';

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
            'text' => 'Delivery Partner Order Payments',
            'href' => $this->url->link('account/deliverypartner/payments', $url, true)
        );

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['orders'] = array();

        $this->load->model('account/order');
        $this->load->model('account/request');
        $this->load->model('localisation/zone');
        $this->load->model('localisation/country');

        $order_total = $this->model_account_order->getTotalDeliveryPartnersOrders();

        $results = $this->model_account_order->getDeliveryPartnersOrders(($page - 1) * 10, 10);
        foreach ($results as $result) {
            $product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
            $voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

            /*$seller = $this->model_account_request->getMpSellerdata($result['mpseller_id']);
            $seller_address = $seller['address'];
            $seller_city = $seller['city'];
            $seller_zone = $this->model_localisation_zone->getZone($seller['zone_id']);
            $seller_country = $this->model_localisation_country->getCountry($seller['country_id']);*/

            $data['orders'][] = array(
                'order_id' => $result['order_id'],
                'name' => $result['firstname'] . ' ' . $result['lastname'],
                'status' => $result['status'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'products' => ($product_total + $voucher_total),
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'delivery_charges' => !empty($result['delivery_charges']) ? $this->currency->format($result['delivery_charges'], $result['currency']) : 0,
                'view' => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], true),
            );
        }

        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = $this->url->link('account/order', 'page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));

        $data['continue'] = $this->url->link('account/account', '', true);
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('account/deliverypartner/payment_list', $data));
    }
}