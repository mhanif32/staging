<?php

class ControllerAccountDeliverypartnerPayments extends Controller
{
    public function index()
    {

//Our starting point / origin. Change this if you wish.
        $start = "Cork, Ireland";

//Our end point / destination. Change this if you wish.
        $destination = "Dublin, Ireland";

//The Google Directions API URL. Do not change this.
        $apiUrl = 'http://maps.googleapis.com/maps/api/directions/json';

//Construct the URL that we will visit with cURL.
        $url = $apiUrl . '?' . 'origin=' . urlencode($start) . '&destination=' . urlencode($destination);

//Initiate cURL.
        $curl = curl_init($url);

//Tell cURL that we want to return the data.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

//Execute the request.
        $res = curl_exec($curl);

//If something went wrong with the request.
        if(curl_errno($curl)){
            throw new Exception(curl_error($curl));
        }

//Close the cURL handle.
        curl_close($curl);

//Decode the JSON data we received.
        $json = json_decode(trim($res), true);

//Automatically select the first route that Google gave us.
        $route = $json['routes'][0];

//Loop through the "legs" in our route and add up the distances.
        $totalDistance = 0;
        foreach($route['legs'] as $leg){
            $totalDistance = $totalDistance + $leg['distance']['value'];
        }

//Divide by 1000 to get the distance in KM.
        $totalDistance = round($totalDistance / 1000);

//Print out the result.
        echo 'Total distance is ' . $totalDistance . 'km';

//var_dump the original array, for illustrative purposes.
        var_dump($json);exit('okokok');

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

        $order_total = $this->model_account_order->getTotalDeliveryPartnersOrders();

        $results = $this->model_account_order->getDeliveryPartnersOrders(($page - 1) * 10, 10);
//echo '<pre>'; print_r($results); exit('okoko');
        foreach ($results as $result) {
            $product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
            $voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

            $seller = $this->model_account_request->getMpSellerdata($result['mpseller_id']);
            $seller_address = $seller['address'];
            $seller_city = $seller['city'];
            $seller_zone = $this->model_localisation_zone->getZone($seller['zone_id']);
            $seller_country = $this->model_localisation_country->getCountry($seller['country_id']);



            $data['orders'][] = array(
                'order_id' => $result['order_id'],
                'name' => $result['firstname'] . ' ' . $result['lastname'],
                'status' => $result['status'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'products' => ($product_total + $voucher_total),
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
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
//        $data['back_url'] = $this->url->link('account/account', '', true);
//        $data['track_link'] = $this->url->link('account/order/track', '', true);


        $this->response->setOutput($this->load->view('account/deliverypartner/payment_list', $data));
    }
}