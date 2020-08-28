<?php

class ModelAccountRequest extends Model
{
    public function getDeliveryRequest($delivery_partner_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "delivery_partner_request dpr WHERE dpr.delivery_partner_id = '" . (int)$delivery_partner_id . "' ORDER BY requested_date DESC");
        return $query->rows;
    }

    public function sendRequestToDeliveryPartner($orderId, $shippingAddress, $mpseller_id, $delivery_type) // send Request To Delivery Partner
    {
        //Select delivery partner's location (For Same Country)
        $query = $this->db->query("SELECT dpc.customer_id, dpc.area_name, c.email FROM " . DB_PREFIX . "delivery_partner_countries dpc LEFT JOIN " . DB_PREFIX . "customer c ON (dpc.customer_id = c.customer_id) LEFT JOIN " . DB_PREFIX . "delivery_partner_info dpi ON (dpc.customer_id = dpi.customer_id)  WHERE dpc.country_id = '" . $shippingAddress['country_id'] . "' and dpc.zone_id = '" . $shippingAddress['zone_id'] . "' and dpc.area_name = '" . $shippingAddress['city'] . "' and dpi.is_approved = '1' and dpi.delivery_type = '" . $delivery_type . "'");
        $deliveryPartners = $query->rows;
//        echo $shippingAddress['city'].' - '. $delivery_type.' - '. $shippingAddress['zone_id'].' - '. $shippingAddress['country_id'];
//        echo '<pre>'; print_r($deliveryPartners);exit('asd');
        //Generate delivery partner requests
        foreach ($deliveryPartners as $deliveryPartner) {

            $this->db->query("INSERT INTO " . DB_PREFIX . "delivery_partner_request SET                             
                        delivery_partner_id = '" . (int)$deliveryPartner['customer_id'] . "', 
                        mpseller_id = '" . (int)$mpseller_id . "',
                        customer_id = '" . (int)$this->customer->getId() . "',
                        order_id = '" . (int)$orderId . "', 
                        requested_date = NOW()");
        }
        return $deliveryPartners;
    }

    public function getMpSellerdata($mpseller_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpseller WHERE mpseller_id = '" . (int)$mpseller_id . "'");

        return $query->row;
    }

    public function getRequestData($id)
    {
        $delivery_partner_id = $this->customer->getId();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "delivery_partner_request WHERE request_id = '" . (int)$id . "' and delivery_partner_id = '" . (int)$delivery_partner_id . "'");

        return $query->row;
    }

    public function getOtherRequest($order_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "delivery_partner_request WHERE order_id = '" . (int)$order_id . "' and is_accept = 1");

        return $query->row;
    }

    public function getOrderData($order_id)
    {
        $order_query = $this->db->query("SELECT order_id, invoice_no, invoice_prefix, firstname, lastname, email, telephone, shipping_address_1, shipping_address_2, shipping_postcode, shipping_city, shipping_zone, shipping_country, total FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND customer_id != '0' AND order_status_id > '0'");

        return $order_query->row;
    }

    public function updateRequest($requestId, $isAccept)
    {
        $delivery_partner_id = $this->customer->getId();
        $this->db->query("UPDATE " . DB_PREFIX . "delivery_partner_request SET is_accept = '" . (int)$isAccept . "' WHERE request_id = '" . (int)$requestId . "' and delivery_partner_id = '" . (int)$delivery_partner_id . "'");
        return true;
    }

    public function getAssignedOrders($customerId)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "delivery_partner_request dpr LEFT JOIN `" . DB_PREFIX . "order` o ON dpr.order_id = o.order_id WHERE dpr.delivery_partner_id = '" . (int)$customerId . "' and dpr.is_accept = 1");

        return $query->rows;
    }

    public function updateStatus($requestId, $data)
    {
        $delivery_partner_id = $this->customer->getId();
        $this->db->query("UPDATE " . DB_PREFIX . "delivery_partner_request SET status = '" . $this->db->escape($data['selectStatus']) . "', other_status_specification = '".$this->db->escape($data['inputOtherSpecification'])."' WHERE request_id = '" . (int)$requestId . "' and delivery_partner_id = '" . (int)$delivery_partner_id . "'");
        return true;
    }
}