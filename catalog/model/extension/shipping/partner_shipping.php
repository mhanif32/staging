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

        $totalDeliveryAmt = 0;
        foreach ($deliveryCharges as $deliveryCharge) {

            /*if ($deliveryCharge < 500) {
                $totalDeliveryAmt+= $deliveryCharge * 20 / 100; //20%
            } else if ($deliveryCharge >= 500 && $deliveryCharge < 700) {

                $totalDeliveryAmt+= $deliveryCharge * 10 / 100; //10%
            } else if ($deliveryCharge >= 700 && $deliveryCharge < 1000) {

                $totalDeliveryAmt+= $deliveryCharge * 5 / 100; //5%
            } else if ($deliveryCharge >= 1000) {

                $totalDeliveryAmt+= $deliveryCharge * 3 / 100; //3%
            }*/

            //$deliveryChargeNew = $this->currency->formatExceptSymbol($this->tax->calculate($deliveryCharge, $this->config->get('partner_shipping_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']);

            if ($deliveryCharge <= 150) {
                $totalDeliveryAmt+= $deliveryCharge * 15 / 100; //15%
            } else if ($deliveryCharge > 150 && $deliveryCharge <= 250.00) {

                $totalDeliveryAmt+= $deliveryCharge * 14.10 / 100;
            } else if ($deliveryCharge > 250) {

                $totalDeliveryAmt+= $deliveryCharge * 14 / 100;
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