<?php

class ModelAccountOrder extends Model
{
    public function getOrder($order_id)
    {
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND customer_id != '0' AND order_status_id > '0'");

        if ($order_query->num_rows) {
            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

            if ($country_query->num_rows) {
                $payment_iso_code_2 = $country_query->row['iso_code_2'];
                $payment_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $payment_iso_code_2 = '';
                $payment_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $payment_zone_code = $zone_query->row['code'];
            } else {
                $payment_zone_code = '';
            }

            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

            if ($country_query->num_rows) {
                $shipping_iso_code_2 = $country_query->row['iso_code_2'];
                $shipping_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $shipping_iso_code_2 = '';
                $shipping_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $shipping_zone_code = $zone_query->row['code'];
            } else {
                $shipping_zone_code = '';
            }

            return array(
                'order_id' => $order_query->row['order_id'],
                'invoice_no' => $order_query->row['invoice_no'],
                'invoice_prefix' => $order_query->row['invoice_prefix'],
                'store_id' => $order_query->row['store_id'],
                'store_name' => $order_query->row['store_name'],
                'store_url' => $order_query->row['store_url'],
                'customer_id' => $order_query->row['customer_id'],
                'firstname' => $order_query->row['firstname'],
                'lastname' => $order_query->row['lastname'],
                'telephone' => $order_query->row['telephone'],
                'email' => $order_query->row['email'],
                'payment_firstname' => $order_query->row['payment_firstname'],
                'payment_lastname' => $order_query->row['payment_lastname'],
                'payment_company' => $order_query->row['payment_company'],
                'payment_address_1' => $order_query->row['payment_address_1'],
                'payment_address_2' => $order_query->row['payment_address_2'],
                'payment_postcode' => $order_query->row['payment_postcode'],
                'payment_city' => $order_query->row['payment_city'],
                'payment_zone_id' => $order_query->row['payment_zone_id'],
                'payment_zone' => $order_query->row['payment_zone'],
                'payment_zone_code' => $payment_zone_code,
                'payment_country_id' => $order_query->row['payment_country_id'],
                'payment_country' => $order_query->row['payment_country'],
                'payment_iso_code_2' => $payment_iso_code_2,
                'payment_iso_code_3' => $payment_iso_code_3,
                'payment_address_format' => $order_query->row['payment_address_format'],
                'payment_method' => $order_query->row['payment_method'],
                'shipping_firstname' => $order_query->row['shipping_firstname'],
                'shipping_lastname' => $order_query->row['shipping_lastname'],
                'shipping_company' => $order_query->row['shipping_company'],
                'shipping_address_1' => $order_query->row['shipping_address_1'],
                'shipping_address_2' => $order_query->row['shipping_address_2'],
                'shipping_postcode' => $order_query->row['shipping_postcode'],
                'shipping_city' => $order_query->row['shipping_city'],
                'shipping_zone_id' => $order_query->row['shipping_zone_id'],
                'shipping_zone' => $order_query->row['shipping_zone'],
                'shipping_zone_code' => $shipping_zone_code,
                'shipping_country_id' => $order_query->row['shipping_country_id'],
                'shipping_country' => $order_query->row['shipping_country'],
                'shipping_iso_code_2' => $shipping_iso_code_2,
                'shipping_iso_code_3' => $shipping_iso_code_3,
                'shipping_address_format' => $order_query->row['shipping_address_format'],
                'shipping_method' => $order_query->row['shipping_method'],
                'comment' => $order_query->row['comment'],
                'total' => $order_query->row['total'],
                'order_status_id' => $order_query->row['order_status_id'],
                'language_id' => $order_query->row['language_id'],
                'currency_id' => $order_query->row['currency_id'],
                'currency_code' => $order_query->row['currency_code'],
                'currency_value' => $order_query->row['currency_value'],
                'date_modified' => $order_query->row['date_modified'],
                'date_added' => $order_query->row['date_added'],
                'my_delivery_date' => $order_query->row['my_delivery_date'],
                'ip' => $order_query->row['ip']
            );
        } else {
            return false;
        }
    }

    public function getOrderForDelivery($order_id)
    {
        $order_query = $this->db->query("SELECT shipping_address_1, shipping_city, shipping_zone, shipping_country FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND customer_id != '0' AND order_status_id > '0'");
        return $order_query->row;
    }

    public function getOrders($start = 0, $limit = 20)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 1;
        }

        $query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);

        return $query->rows;
    }

    public function getDeliveryPartnersOrders($start = 0, $limit = 20)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 1;
        }

        $query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value, o.shipping_address_1, o.shipping_address_2, o.shipping_city, o.shipping_zone, o.shipping_country, o.shipping_postcode, dpr.mpseller_id, dpr.delivery_charges, dpr.currency FROM `" . DB_PREFIX . "delivery_partner_request` dpr LEFT JOIN `" . DB_PREFIX . "order` o ON dpr.order_id = o.order_id LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE  dpr.delivery_partner_id = '" . (int)$this->customer->getId() . "' and dpr.is_accept = 1 AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);

        return $query->rows;
    }

    public function getOrderProduct($order_id, $order_product_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

        return $query->row;
    }

    public function getOrderProducts($order_id)
    {
        $query = $this->db->query("SELECT oc.*, p.mpseller_id, mps.store_name FROM " . DB_PREFIX . "order_product oc LEFT JOIN " . DB_PREFIX . "product p ON (oc.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "mpseller mps ON (p.mpseller_id = mps.mpseller_id) WHERE order_id = '" . (int)$order_id . "'");

        return $query->rows;
    }

    public function getOrderOptions($order_id, $order_product_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

        return $query->rows;
    }

    public function getOrderVouchers($order_id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");

        return $query->rows;
    }

    public function getOrderTotals($order_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

        return $query->rows;
    }

    public function getOrderHistories($order_id)
    {
        $query = $this->db->query("SELECT date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added");

        return $query->rows;
    }

    public function getTotalOrders()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o WHERE customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'");

        return $query->row['total'];
    }

    public function getTotalDeliveryPartnersOrders()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "delivery_partner_request` dpr LEFT JOIN `" . DB_PREFIX . "order` o ON dpr.order_id = o.order_id WHERE dpr.delivery_partner_id = '" . (int)$this->customer->getId() . "' and dpr.is_accept = 1 AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'");
        return $query->row['total'];
    }

    public function getTrackTotalOrders($order_id = NULL, $email, $order_invoice)
    {
        $sql = "SELECT os.name as status, o.order_id, o.invoice_prefix, o.invoice_no FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.order_status_id > '0' AND ";

        //check for guest checkout
        $queryGuest = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$order_id . "'");
//        if ($this->customer->isLogged()) {
//            $sql .= "customer_id = '" . (int)$this->customer->getId() . "' AND ";
//        }

        if (!empty($order_id)) {
            $sql .= "o.order_id = '" . (int)$order_id . "' AND ";
            $sql .= "o.email = '" . $email . "' AND ";
            $sql .= "concat(o.invoice_prefix, '', o.invoice_no) like '".$order_invoice."' AND ";
        }

        $sql .= "o.store_id = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1";
//echo $sql;exit('asd');
        $query = $this->db->query($sql);
        return $query->row;
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

    public function cancelOrder($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$data['order_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', order_status_id = '" . (int)$data['order_status_id'] . "', notify = '" . (int)$data['notify'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_added = NOW(), cancel_reason_id = '" . (int)$data['cancel_reason_id'] . "'");

        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$data['order_status_id'] . "' WHERE order_id = '" . (int)$data['order_id'] . "'");

        return $this->db->getLastId();
    }

    public function getLatestOrderHistory($order_id, $customer_id)
    {
        $sql = "SELECT oh.order_status_id FROM `" . DB_PREFIX . "order_history` oh LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = oh.order_id) WHERE oh.order_id = '" . (int)$order_id . "' and o.customer_id = $customer_id ORDER BY oh.order_history_id DESC LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row['order_status_id'];
    }

    public function getShippedHistoryBySeller($order_id)
    {
        $sql = "SELECT oh.order_status_id FROM `" . DB_PREFIX . "mpseller_order_history` oh LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = oh.order_id) WHERE oh.order_id = '" . (int)$order_id . "' and oh.order_status_id = '3' ORDER BY oh.mpseller_order_history_id DESC LIMIT 1";
        $query = $this->db->query($sql);
        if(!empty($query->row)) {
            return $query->row['order_status_id'];
        }
        return false;
    }

    public function createInvoiceNo($order_id) {
        $order_info = $this->getOrder($order_id);

        if ($order_info && !$order_info['invoice_no']) {
            /*$query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "'");

            if ($query->row['invoice_no']) {
                $invoice_no = $query->row['invoice_no'] + 1;
            } else {
                $invoice_no = 1;
            }*/

            $query = $this->db->query("SELECT UPPER(LEFT(UUID(), 8)) as random_no");
            $invoice_no = $query->row['random_no'];

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . $invoice_no . "', invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "' WHERE order_id = '" . (int)$order_id . "'");

            return $order_info['invoice_prefix'] . $invoice_no;
        }
    }

    public function getMpsellerFromOrder($orderId)
    {
        $query = $this->db->query("SELECT mp.store_owner, mp.store_name, mp.email FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "product` p ON (p.product_id = op.product_id) LEFT JOIN `" . DB_PREFIX . "mpseller` mp ON (mp.mpseller_id = p.mpseller_id) WHERE op.order_id = '" . (int)$orderId . "'");

        return $query->rows;
    }

    public function updateOrderEstDate($order_id, $days)
    {
        $estDate = Date('Y-m-d', strtotime('+'.$days.' days'));
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET estimated_date = '" . $estDate . "' WHERE order_id = '" . (int)$order_id . "'");
    }
}