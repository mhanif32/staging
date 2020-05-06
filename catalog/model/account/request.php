<?php
class ModelAccountRequest extends Model
{
    public function getDeliveryRequest($delivery_partner_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "delivery_partner_request dpr WHERE dpr.delivery_partner_id = '" . (int)$delivery_partner_id . "' ORDER BY requested_date DESC");
        return $query->rows;
    }

    public function sendRequestToDeliveryPartner($orderId, $shippingAddress, $mpseller_id)
    {
//        if(!empty($this->session->data['shipping_address'])) {
//
//            $shippingAddress = $this->session->data['shipping_address'];
//
//            //get total seller
//            $cartProducts = $this->cart->getProducts();
//            $sellerList = array();
//            foreach ($cartProducts as $product) {
//                $sellerList[] = $product['mpseller_id'];
//            }
//            $totalSellers = array_unique($sellerList);
//
//            if(count($totalSellers) == 1) { //for single seller
//
//                $mpSellerData = $this->getMpSellerdata($totalSellers[0]);
//                //check : seller & customer shipping address relates with same city
//                if($mpSellerData['city'] == $shippingAddress['city']) {

                    //select delivery partner's location
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "delivery_partner_countries dpc LEFT JOIN " . DB_PREFIX . "delivery_partner_info dpi ON (dpc.customer_id = dpi.customer_id)  WHERE dpc.area_name = '" . $shippingAddress['city'] . "' and dpi.is_approved = '1'");
                    $deliveryPartners = $query->rows;

                    //Generate delivery partner requests
                    foreach ($deliveryPartners as $deliveryPartner) {

                        $this->db->query("INSERT INTO " . DB_PREFIX . "delivery_partner_request SET                             delivery_partner_id = '" . (int)$deliveryPartner['customer_id'] . "', 
                        mpseller_id = '" . (int)$mpseller_id . "',
                        customer_id = '" . (int)$this->customer->getId() . "',
                        order_id = '" . (int)$orderId . "', 
                        requested_date = NOW()");
                    }
                    return $deliveryPartners;
//                }
//            }
//        }
    }

    public function getMpSellerdata($mpseller_id) {
        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."mpseller WHERE mpseller_id = '". (int)$mpseller_id ."'");

        return $query->row;
    }

    public function getRequestData($id)
    {
        $delivery_partner_id = $this->customer->getId();
        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."delivery_partner_request WHERE request_id = '". (int)$id ."' and delivery_partner_id = '".(int)$delivery_partner_id."'");

        return $query->row;
    }

    public function getOrderData($order_id)
    {
        $order_query = $this->db->query("SELECT shipping_firstname, shipping_lastname, email, telephone, shipping_address_1, shipping_address_2, shipping_postcode, shipping_city, shipping_zone, shipping_country, total FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND customer_id != '0' AND order_status_id > '0'");

        return $order_query->row;
    }

    public function updateRequest($requestId, $isAccept)
    {
        $delivery_partner_id = $this->customer->getId();
        $this->db->query("UPDATE " . DB_PREFIX . "delivery_partner_request SET is_accept = '" . (int)$isAccept . "' WHERE request_id = '".(int)$requestId."' and delivery_partner_id = '".(int)$delivery_partner_id."'");
        return true;
    }
}