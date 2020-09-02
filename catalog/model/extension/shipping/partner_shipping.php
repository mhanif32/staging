<?php

class ModelExtensionShippingPartnerShipping extends Model
{
    function getQuote($address)
    {
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

            if ( ! isset($deliveryValues[$product['mpseller_id']])) {
                $deliveryValues[$product['mpseller_id']] = 0;
            }
            $deliveryValues[$product['mpseller_id']]+= $product['total'];
        }

        $deliveryCharges = array_values($deliveryValues);
        $totalDeliveryAmt = 0; $tempAmt = 0;
        foreach ($deliveryCharges as $deliveryCharge) {

            $deliveryChargeNew = $this->currency->formatExceptSymbol($this->tax->calculate($deliveryCharge, $this->config->get('partner_shipping_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']);

            if($this->session->data['currency'] == 'NGN') {
                //for NGN Currency only
                if ($deliveryChargeNew <= 20000) {

                    $tempAmt+= 4000;
                } else if ($deliveryChargeNew > 20000 && $deliveryChargeNew <= 30000) {

                    $tempAmt+= 4600;
                } else if ($deliveryChargeNew > 30000 && $deliveryChargeNew <= 35000) {

                    $tempAmt+= 4800;
                } else if ($deliveryChargeNew > 35000 && $deliveryChargeNew <= 40000) {

                    $tempAmt+= 4950;
                } else if ($deliveryChargeNew > 40000) {
                    $tempAmt+= $deliveryChargeNew * 13 / 100;
                }

                //get USD rate from the currency
                $NGN_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code = 'NGN'");
                $nigeria_data = $NGN_query->row;
                $usdAmt = $tempAmt / $nigeria_data['value'];
                $totalDeliveryAmt = number_format($usdAmt, 4);
            } else {

                //for currency USD
                if($this->session->data['currency'] != 'USD') {
                    $deliveryChargeNew = $this->currency->formatExceptSymbol($this->tax->calculate($deliveryCharge, $this->config->get('partner_shipping_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']);
                }

                if ($deliveryChargeNew <= 50) {
                    $tempAmt+= 19.20;
                } else if($deliveryChargeNew > 50 && $deliveryChargeNew <= 80) {
                    $tempAmt+= 20;
                } else if($deliveryChargeNew > 80 && $deliveryChargeNew <= 100) {
                    $tempAmt+= 22;
                } else if($deliveryChargeNew > 100 && $deliveryChargeNew <= 150) {
                    $tempAmt+= 24.50;
                } else if($deliveryChargeNew > 150 && $deliveryChargeNew <= 250) {
                    $tempAmt+= 25;
                } else if($deliveryChargeNew > 250 && $deliveryChargeNew <= 500) {
                    $tempAmt+= 30;
                } else if($deliveryChargeNew > 500) {
                    $tempAmt+= $deliveryChargeNew * 10 / 100;
                }

                if($this->session->data['currency'] == 'USD') {

                    $totalDeliveryAmt = $tempAmt;
                } else {

                    $NGN_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code = '".$this->session->data['currency']."'");
                    $nigeria_data = $NGN_query->row;
                    $usdAmt = $tempAmt / $nigeria_data['value'];
                    $totalDeliveryAmt = number_format($usdAmt, 4);
                }
            }
        }
        //END : Champion Mall Delivery Charges Algorithm

        $method_data = array();
        if ($status) {
            $quote_data = array();
            $quote_data['partner_shipping'] = array(
                'code' => 'partner_shipping.partner_shipping',
                'title' => $this->language->get('text_description'),
                'cost' => $totalDeliveryAmt,
                'tax_class_id' => $this->config->get('partner_shipping_tax_class_id'),
                'text' => $this->currency->format($this->tax->calculate($totalDeliveryAmt, $this->config->get('partner_shipping_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
                //'text' => $this->currency->addCurrencySymbol($totalDeliveryAmt, $this->session->data['currency'])
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
}