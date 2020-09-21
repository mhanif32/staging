<?php

class ControllerAccountDeliverypartnerPayments extends Controller
{
    public function index()
    {
//        $key = 'AIzaSyB65K6J8mCTzwU8hhfUtCMDS5t_Uq351iA';
//        $addressFrom = 'Nandanvan, Nagpur, Maharashtra, India';
//        $addressTo = 'Rakesh Layout, Nagpur, Maharashtra, India';
//
//// Get distance in km
//        $distance = $this->getDistance($addressFrom, $addressTo, "K", $key);
//
//
//        echo '<pre>';
//        print_r($distance);
//        exit('okoko');
//        exit('okokok');
//        $going='Nandanvan, Nagpur, Maharashtra, India';
//
//        $address =$going; // Google HQ
//        $prepAddr = str_replace(' ','+',$address);
//        $apiKey = 'AIzaSyB65K6J8mCTzwU8hhfUtCMDS5t_Uq351iA'; // Google maps now requires an API key.
//
//        $geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?
//   address='.urlencode($address).'&sensor=false&key='.$apiKey);
//
//        //print_r($geocode);
//
//        $output= json_decode($geocode);
//        $latitude = $output->results[0]->geometry->location->lat;
//        $longitude = $output->results[0]->geometry->location->lng;
//        echo '<pre>';
//        print_r($latitude);
//        exit('okoko');



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
                'delivery_charges' => 0.00,
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
        $latitudeFrom = $outputFrom->results[0]->geometry->location->lat;
        $longitudeFrom = $outputFrom->results[0]->geometry->location->lng;
        $latitudeTo = $outputTo->results[0]->geometry->location->lat;
        $longitudeTo = $outputTo->results[0]->geometry->location->lng;

        // Calculate distance between latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) + cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        // Convert unit and return distance
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return round($miles * 1.609344, 2) . ' km';
        } elseif ($unit == "M") {
            return round($miles * 1609.344, 2) . ' meters';
        } else {
            return round($miles, 2) . ' miles';
        }
    }
}