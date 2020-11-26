<?php

class ModelExtensionShippingPartnerShipping extends Model
{
    function getQuote($address)
    {
        //echo '<pre>'; print_r($address); exit('iook');

        $this->load->language('extension/shipping/partner_shipping');
        /**
         * Query for finding if the customer is from the same zone as selected by the admin in the backend
         * @var [type]
         */
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('partner_shipping_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('partner_shipping_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        /**
         * This is the cost you want to set for using your shipping service
         * Instead of this, you can use the APIs to calculate your shipping rate
         * You can also make use of $address array as it contains the address of the customer
         * @var integer
         */

        //START : Champion Mall Delivery Charges Algorithm
        $cartProducts = $this->cart->getProducts();
        $deliveryValues = array();
        foreach ($cartProducts as $product) {

            if (!isset($deliveryValues[$product['mpseller_id']])) {
                $deliveryValues[$product['mpseller_id']] = 0;
            }
            $deliveryValues[$product['mpseller_id']] += $product['total'];
        }

        //$deliveryCharges = array_values($deliveryValues);
        $totalDeliveryCharges = 0;
        foreach ($deliveryValues as $key => $deliveryCharge) {

            $totalDeliveryAmt = 0; $tempAmt = 0;
            //check seller and customer locations are same
            $mpSellerId = $key;
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpseller m WHERE m.mpseller_id = '" . (int)$mpSellerId . "' AND m.status = '1'");
            $mpSellerData = $query->row;

            //echo '<pre>'; print_r($address); /*exit('iook');*/
            //echo '<pre>'; print_r($mpSellerData); exit('iook');

            if (($mpSellerData['country_id'] == $address['country_id']) && ($mpSellerData['zone_id'] == $address['zone_id']) && ($mpSellerData['city'] == $address['city']) && $address['country'] != 'Nigeria') {
                //exit('here');

                $sellerZoneData = $this->getZone($mpSellerData['zone_id']);
                $sellerCountryData = $this->getCountry($mpSellerData['country_id']);

                $googleKey = $this->config->get('config_google_distance_api_key');
                $sellerAddress = [
                    $mpSellerData['address'],
                    $mpSellerData['city'],
                    $sellerZoneData['name'],
                    $sellerCountryData['name']
                ];
                $sellerAddress = implode(', ', $sellerAddress);
                $customerArray = [
                    $address['address_1'],
                    $address['address_2'],
                    $address['city'],
                    $address['zone'],
                    $address['country']
                ];
                $customerAddress = implode(', ', array_filter($customerArray));

                $addressFrom = $sellerAddress;
                $addressTo = $customerAddress;
                $distance = $this->getDistanceOLD($addressFrom, $addressTo, "K", $googleKey);
                // my change
                $sellerIDs = $this->getSellers();
                $distance = $this->getTotalDistance($sellerIDs, $customerArray);

                $configFlatCharge = (float) $this->config->get('config_flat_delivery_charges');
                $configFlatChargeDistance = (float) $this->config->get('config_delivery_charge_per_distance');
                $delivery_charge = $configFlatCharge + ($configFlatChargeDistance * $distance);
                //$dataCharges = $this->currency->addCurrencySymbol($delivery_charge, $this->session->data['currency']);
                $totalDeliveryAmt = $delivery_charge;

            } else {
                //if seller and customer locations are not the same
                $deliveryChargeNew = $this->currency->formatExceptSymbol($this->tax->calculate($deliveryCharge, $this->config->get('partner_shipping_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']);

                if ($this->session->data['currency'] == 'NGN') {
                    //for NGN Currency only
                    if ($deliveryChargeNew <= 20000) {

                        $tempAmt += 4000;
                    } else if ($deliveryChargeNew > 20000 && $deliveryChargeNew <= 30000) {

                        $tempAmt += 4600;
                    } else if ($deliveryChargeNew > 30000 && $deliveryChargeNew <= 35000) {

                        $tempAmt += 4800;
                    } else if ($deliveryChargeNew > 35000 && $deliveryChargeNew <= 40000) {

                        $tempAmt += 4950;
                    } else if ($deliveryChargeNew > 40000) {
                        $tempAmt += $deliveryChargeNew * 13 / 100;
                    }

                    //get USD rate from the currency
                    $NGN_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code = 'NGN'");
                    $nigeria_data = $NGN_query->row;
                    $usdAmt = $tempAmt / $nigeria_data['value'];
                    $totalDeliveryAmt = number_format($usdAmt, 4);
                } else {

                    //for currency USD
                    if ($this->session->data['currency'] != 'USD') {
                        $deliveryChargeNew = $this->currency->formatExceptSymbol($this->tax->calculate($deliveryCharge, $this->config->get('partner_shipping_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']);
                    }

                    if ($deliveryChargeNew <= 50) {
                        $tempAmt += 19.20;
                    } else if ($deliveryChargeNew > 50 && $deliveryChargeNew <= 80) {
                        $tempAmt += 20;
                    } else if ($deliveryChargeNew > 80 && $deliveryChargeNew <= 100) {
                        $tempAmt += 22;
                    } else if ($deliveryChargeNew > 100 && $deliveryChargeNew <= 150) {
                        $tempAmt += 24.50;
                    } else if ($deliveryChargeNew > 150 && $deliveryChargeNew <= 250) {
                        $tempAmt += 25;
                    } else if ($deliveryChargeNew > 250 && $deliveryChargeNew <= 500) {
                        $tempAmt += 30;
                    } else if ($deliveryChargeNew > 500) {
                        $tempAmt += $deliveryChargeNew * 10 / 100;
                    }

                    if ($this->session->data['currency'] == 'USD') {

                        $totalDeliveryAmt = $tempAmt;
                    } else {

                        $NGN_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code = '" . $this->session->data['currency'] . "'");
                        $nigeria_data = $NGN_query->row;
                        $usdAmt = $tempAmt / $nigeria_data['value'];
                        $totalDeliveryAmt = number_format($usdAmt, 4);
                    }
                }
            }
            $totalDeliveryCharges += $totalDeliveryAmt;
        }

        //END : Champion Mall Delivery Charges Algorithm
        $method_data = array();
        if ($status) {
            $quote_data = array();
            $quote_data['partner_shipping'] = array(
                'code' => 'partner_shipping.partner_shipping',
                'title' => $this->language->get('text_description'),
                'cost' => $totalDeliveryCharges,
                'tax_class_id' => $this->config->get('partner_shipping_tax_class_id'),
                'text' => $this->currency->format($this->tax->calculate($totalDeliveryCharges, $this->config->get('partner_shipping_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
                //'text' => $this->currency->addCurrencySymbol($totalDeliveryCharges, $this->session->data['currency'])
            );

            $method_data = array(
                'code' => 'partner_shipping',
                'title' => $this->language->get('text_title'),
                'quote' => $quote_data,
                'sort_order' => $this->config->get('partner_shipping_sort_order'),
                'error' => false
            );
        }
        return $method_data;
    }

    /**
     * Get all the unique sellers of products in the cart.
     * @return array
     */
    protected function getSellers()
    {
        //$cartProducts = $this->cart->getProducts();
        //$sellers =  new \Ds\Set();
        $sellers = [];

        foreach($this->cart->getProducts() as $product){
            //echo '<pre>'; print_r($product) . '</pre>';
            if (!in_array($product['mpseller_id'], $sellers))
                $sellers[] = $product['mpseller_id'];
            //$sellers->add();
        }
        return array_unique($sellers);
    }

    /**
     * Get the address in a format expected by google geo - code.
     * @param $sellerID
     * @return array|string
     */
    protected function getGoogleFormattedAddress($sellerID)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpseller m WHERE m.mpseller_id = '" . (int)$sellerID . "' AND m.status = '1'");
        $sellerData = $query->row;

        //echo '<pre>'; print_r($sellerData) . '</pre>'; exit('here');

        $sellerZoneData = $this->getZone($sellerData['zone_id']);
        $sellerCountryData = $this->getCountry($sellerData['country_id']);

        $sellerAddress = [
            $sellerData['address'],
            $sellerData['city'],
            $sellerZoneData['name'],
            $sellerCountryData['name']
        ];
        $sellerAddress = implode(', ', $sellerAddress);

        return $sellerAddress;
    }

    /**
     * Returns the total distance, following the format that if more than one seller,
     * we calculate distance from first seller to next seller and then from last seller
     * to customer.
     * @param $sellerIDs
     * @param $customerAddress
     * @param string $unit
     * @return float|int|string
     */
    protected function getTotalDistance($sellerIDs, $customerAddress, $unit = "K")
    {
        $googleKey = $this->config->get('config_google_distance_api_key');
        $totalDistance = 0;
        // distance from last seller to customer
        $customerAddress = implode(', ', array_filter($customerAddress));
        $prevSellerAddress = null;

        // handle type of seller quickly
        if (count($sellerIDs) <= 1){
            $lastSellerAddress = $this->getGoogleFormattedAddress($sellerIDs[0]);
            $totalDistance += $this->getDistance($lastSellerAddress, $customerAddress, $unit, $googleKey);
            return $totalDistance;
        }

        // handle 2 type seller
        if (count($sellerIDs) <= 2){
            $prevSellerAddress = $this->getGoogleFormattedAddress($sellerIDs[0]);
            $lastSellerAddress = $this->getGoogleFormattedAddress($sellerIDs[1]);
            // get distance btw seller 1 and seller 2
            $totalDistance += $this->getDistance($prevSellerAddress, $lastSellerAddress, $unit, $googleKey);
            // get distance btw last seller (2) and customer
            $totalDistance += $this->getDistance($lastSellerAddress, $customerAddress, $unit, $googleKey);
            return $totalDistance;
        }

        // handles all cases above 2
        for($i = 0; $i < count($sellerIDs) - 1; $i++){
            /*
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpseller m WHERE m.mpseller_id = '" . (int)$sellerIDs[$i] . "' AND m.status = '1'");
            $sellerData = $query->row;

            $sellerAddress = [
                $sellerData['address'],
                $sellerData['city'],
                $sellerData['name'],
                $sellerData['name']
            ];
            $sellerAddress = implode(', ', $sellerAddress);

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpseller m WHERE m.mpseller_id = '" . (int)$sellerIDs[$i+1] . "' AND m.status = '1'");
            $nextSellerData = $query->row;

            $nextSellerAddress = [
                $nextSellerData['address'],
                $nextSellerData['city'],
                $nextSellerData['name'],
                $nextSellerData['name']
            ];
            $nextSellerAddress = implode(', ', $nextSellerAddress);
            */

            $sellerAddress = $this->getGoogleFormattedAddress($sellerIDs[$i]);
            $nextSellerAddress = $this->getGoogleFormattedAddress($sellerIDs[$i+1]);
            $totalDistance += $this->getDistance($sellerAddress, $nextSellerAddress, $unit, $googleKey);

            $prevSellerAddress = $nextSellerAddress;
        }

        // get details of the last seller and add the address
        /*
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpseller m WHERE m.mpseller_id = '" . (int)$sellerIDs[array_key_last($sellerIDs)] . "' AND m.status = '1'");
        $nextSellerData = $query->row;

        $lastSellerAddress = [
            $nextSellerData['address'],
            $nextSellerData['city'],
            $nextSellerData['name'],
            $nextSellerData['name']
        ];
        $lastSellerAddress = implode(', ', $lastSellerAddress);
        */
        $lastSellerAddress = $this->getGoogleFormattedAddress($sellerIDs[count($sellerIDs) - 1]);
        $totalDistance += $this->getDistance($prevSellerAddress, $lastSellerAddress, $unit, $googleKey);

        // distance from last seller to customer
        $totalDistance += $this->getDistance($lastSellerAddress, $customerAddress, $unit, $googleKey);

        return $totalDistance;
    }

    protected function getDistance($addressFrom, $addressTo, $unit, $apiKey)
    {
        $endpoint = "https://maps.googleapis.com/maps/api/distancematrix/json?";
        $query_data = [
            //'units' => 'imperial', // returns it in km
            'origins' => $addressFrom,
            'destinations' => $addressTo,
            'key' => $apiKey
        ];

        $endpoint .= http_build_query($query_data);
        $distanceMatrixAPI = file_get_contents($endpoint);
        $outputReturned = json_decode($distanceMatrixAPI);
        if ($outputReturned->status != "OK" || count($outputReturned->rows) == 0){
            return $outputReturned->status;
        }

        // good to take info
        $response = $outputReturned->rows[0];
        $distance = $response->elements[0]->distance->text;
        $value = explode(" ", $distance);
        return $value[0];
    }

    protected function getDistanceOLD($addressFrom, $addressTo, $unit = '', $apiKey)
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
        if(!empty($outputFrom->results)) {
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
                return round($miles * 1.609344, 2);
            } elseif ($unit == "M") {
                return round($miles * 1609.344, 2) . ' meters';
            } else {
                return round($miles, 2) . ' miles';
            }
        } else {
            return 0;
        }
    }

    public function getCountry($country_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "' AND status = '1'");

        return $query->row;
    }

    public function getZone($zone_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");

        return $query->row;
    }

    public function getCurrencyByCode($currency)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "currency WHERE code = '" . $this->db->escape($currency) . "'");

        return $query->row;
    }
}
