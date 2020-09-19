<?php
class ModelDeliverypartnerPayments extends Model {

    public function getTotalDeliveryPartnersOrders()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "delivery_partner_request` dpr LEFT JOIN `" . DB_PREFIX . "order` o ON dpr.order_id = o.order_id WHERE dpr.is_accept = 1 AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'");
        return $query->row['total'];
    }

    public function getDeliveryPartnersOrders($start = 0, $limit = 20)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 1;
        }

        $query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value, o.shipping_address_1, o.shipping_address_2, o.shipping_city, o.shipping_zone, o.shipping_country, o.shipping_postcode, dpr.mpseller_id FROM `" . DB_PREFIX . "delivery_partner_request` dpr LEFT JOIN `" . DB_PREFIX . "order` o ON dpr.order_id = o.order_id LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE dpr.is_accept = 1 AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);

        return $query->rows;
    }

    public function getTotalOrderProductsByOrderId($order_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

        return $query->row['total'];
    }

    public function getTotalOrderVouchersByOrderId($order_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");

        return $query->row['total'];
    }

    public function getMpSellerdata($mpseller_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpseller WHERE mpseller_id = '" . (int)$mpseller_id . "'");

        return $query->row;
    }
}