<?php
class ModelExtensionShippingPartnerShipping extends Model {
    function getQuote($address) {
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
        $cost = 10;

        $method_data = array();

        if ($status) {
            $quote_data = array();

            $quote_data['partner_shipping'] = array(
                'code'         => 'partner_shipping.partner_shipping',
                'title'        => $this->language->get('text_description'),
                'cost'         => $cost,
                'tax_class_id' => $this->config->get('partner_shipping_tax_class_id'),
                'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('partner_shipping_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
            );

            $method_data = array(
                'code'       => 'partner_shipping',
                'title'      => $this->language->get('text_title'),
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('partner_shipping_sort_order'),
                'error'      => false
            );
        }

        return $method_data;
    }
}