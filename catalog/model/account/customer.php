<?php

class ModelAccountCustomer extends Model
{
    public function addCustomer($data)
    {
        if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
            $customer_group_id = $data['customer_group_id'];
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        if (isset($this->request->get['role'])) {
            $data['role'] = $this->request->get['role'];
        } else {
            $data['role'] = 'buyer';
        }

        $this->load->model('account/customer_group');
        $customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

        $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$customer_group_id . "', store_id = '" . (int)$this->config->get('config_store_id') . "', language_id = '" . (int)$this->config->get('config_language_id') . "', role = '" . $this->db->escape($data['role']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', newsletter = '" . (isset($data['newsletter']) ? (int)$data['newsletter'] : 0) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = '0', date_added = NOW()");

        $customer_id = $this->db->getLastId();

        if ($customer_group_info['approval']) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_approval` SET customer_id = '" . (int)$customer_id . "', type = 'customer', date_added = NOW()");
        }

        //delivery partner
        if ($data['role'] == 'delivery-partner') {
            $sqlDelInfo = "INSERT INTO " . DB_PREFIX . "delivery_partner_info SET delivery_type = '" . $this->db->escape($data['delivery_type']) . "', customer_id = '" . (int)$customer_id . "'";
            $this->db->query($sqlDelInfo);
        }
        return $customer_id;
    }

    public function editCustomer($customer_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', gender = '" . $this->db->escape($data['gender']) . "', date_of_birth = '" . $this->db->escape($data['date_of_birth']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");
    }

    public function editAvatar($customer_id, $file, $code)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET image = '" . $file . "', code = '" . $code . "' WHERE customer_id = '" . (int)$customer_id . "'");
    }

    public function editPassword($email, $password)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', code = '' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
    }

    public function editAddressId($customer_id, $address_id)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
    }

    public function editCode($email, $code)
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET code = '" . $this->db->escape($code) . "' WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
    }

    public function editNewsletter($newsletter)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET newsletter = '" . (int)$newsletter . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
    }

    public function getCustomer($customer_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

        return $query->row;
    }

    public function saveCountries($customer_id, $data)
    {
        if (!empty($data['countries'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "delivery_partner_countries WHERE customer_id = '" . (int)$customer_id . "'");
            foreach ($data['countries'] as $country) {

                $this->db->query("INSERT INTO `" . DB_PREFIX . "delivery_partner_countries` SET customer_id = '" . (int)$customer_id . "', country_id = '" . $this->db->escape($country) . "'");
            }
        }
    }

    public function getCustomerByEmail($email)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

        return $query->row;
    }

    public function getCustomerByCode($code)
    {
        $query = $this->db->query("SELECT customer_id, firstname, lastname, email FROM `" . DB_PREFIX . "customer` WHERE code = '" . $this->db->escape($code) . "' AND code != ''");

        return $query->row;
    }

    public function getCustomerByToken($token)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE token = '" . $this->db->escape($token) . "' AND token != ''");

        $this->db->query("UPDATE " . DB_PREFIX . "customer SET token = ''");

        return $query->row;
    }

    public function getTotalCustomersByEmail($email)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

        return $query->row['total'];
    }

    public function addTransaction($customer_id, $description, $amount = '', $order_id = 0)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$customer_id . "', order_id = '" . (float)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");
    }

    public function deleteTransactionByOrderId($order_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
    }

    public function getTransactionTotal($customer_id)
    {
        $query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

        return $query->row['total'];
    }

    public function getTotalTransactionsByOrderId($order_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");

        return $query->row['total'];
    }

    public function getRewardTotal($customer_id)
    {
        $query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

        return $query->row['total'];
    }

    public function getIps($customer_id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = '" . (int)$customer_id . "'");

        return $query->rows;
    }

    public function addLoginAttempt($email)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_login WHERE email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");

        if (!$query->num_rows) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_login SET email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', total = 1, date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
        } else {
            $this->db->query("UPDATE " . DB_PREFIX . "customer_login SET total = (total + 1), date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE customer_login_id = '" . (int)$query->row['customer_login_id'] . "'");
        }
    }

    public function getLoginAttempts($email)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");

        return $query->row;
    }

    public function deleteLoginAttempts($email)
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");
    }

    public function addAffiliate($customer_id, $data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "customer_affiliate SET `customer_id` = '" . (int)$customer_id . "', `company` = '" . $this->db->escape($data['company']) . "', `website` = '" . $this->db->escape($data['website']) . "', `tracking` = '" . $this->db->escape(token(64)) . "', `commission` = '" . (float)$this->config->get('config_affiliate_commission') . "', `tax` = '" . $this->db->escape($data['tax']) . "', `payment` = '" . $this->db->escape($data['payment']) . "', `cheque` = '" . $this->db->escape($data['cheque']) . "', `paypal` = '" . $this->db->escape($data['paypal']) . "', `bank_name` = '" . $this->db->escape($data['bank_name']) . "', `bank_branch_number` = '" . $this->db->escape($data['bank_branch_number']) . "', `bank_swift_code` = '" . $this->db->escape($data['bank_swift_code']) . "', `bank_account_name` = '" . $this->db->escape($data['bank_account_name']) . "', `bank_account_number` = '" . $this->db->escape($data['bank_account_number']) . "', `status` = '" . (int)!$this->config->get('config_affiliate_approval') . "'");

        if ($this->config->get('config_affiliate_approval')) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_approval` SET customer_id = '" . (int)$customer_id . "', type = 'affiliate', date_added = NOW()");
        }
    }

    public function editAffiliate($customer_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer_affiliate SET `company` = '" . $this->db->escape($data['company']) . "', `website` = '" . $this->db->escape($data['website']) . "', `commission` = '" . (float)$this->config->get('config_affiliate_commission') . "', `tax` = '" . $this->db->escape($data['tax']) . "', `payment` = '" . $this->db->escape($data['payment']) . "', `cheque` = '" . $this->db->escape($data['cheque']) . "', `paypal` = '" . $this->db->escape($data['paypal']) . "', `bank_name` = '" . $this->db->escape($data['bank_name']) . "', `bank_branch_number` = '" . $this->db->escape($data['bank_branch_number']) . "', `bank_swift_code` = '" . $this->db->escape($data['bank_swift_code']) . "', `bank_account_name` = '" . $this->db->escape($data['bank_account_name']) . "', `bank_account_number` = '" . $this->db->escape($data['bank_account_number']) . "' WHERE `customer_id` = '" . (int)$customer_id . "'");
    }

    public function getAffiliate($customer_id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_affiliate` WHERE `customer_id` = '" . (int)$customer_id . "'");

        return $query->row;
    }

    public function getAffiliateByTracking($tracking)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_affiliate` WHERE `tracking` = '" . $this->db->escape($tracking) . "'");

        return $query->row;
    }

    public function deactivateAccount($customer_id)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET `status` = '2' WHERE `customer_id` = '" . (int)$customer_id . "'");
    }

    public function checkIsSellerApproved($customer_id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mpseller` WHERE customer_id = '" . (int)$customer_id . "' and  status = '1' and approved  = '1'");
        return $query->row;
    }

    public function getDeliveryInfo($customer_id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "delivery_partner_countries` WHERE customer_id = '" . (int)$customer_id . "'");
        return $query->rows;
    }

    public function getDocumentsInfo($customer_id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "delivery_partner_info` WHERE customer_id = '" . (int)$customer_id . "'");
        return $query->row;
    }

    public function updateDeliveryInfos($customer_id, $data, $dataFile)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "delivery_partner_info WHERE `customer_id`='" . $customer_id . "'";
        $result2 = $this->db->query($sql);

        $fileSql = '';
        if (!empty($dataFile['id_proof'])) {
            $fileSql .= " id_proof = '" . $this->db->escape($dataFile['id_proof']) . "', ";
        }
        if (!empty($dataFile['address_proof'])) {
            $fileSql .= " address_proof = '" . $this->db->escape($dataFile['address_proof']) . "', ";
        }
        if (!empty($dataFile['travel_license'])) {
            $fileSql .= " travel_license = '" . $this->db->escape($dataFile['travel_license']) . "', ";
        }
        if (!empty($dataFile['vehicle_insurance'])) {
            $fileSql .= " vehicle_insurance = '" . $this->db->escape($dataFile['vehicle_insurance']) . "', ";
        }

        if ($result2->num_rows > 0) {
            $sql = "UPDATE " . DB_PREFIX . "delivery_partner_info SET ";
            $sql .= $fileSql;
            $sql .= "`vehicle_type` = '" . $this->db->escape($data['vehicle_type']) . "', `delivery_type` = '" . $this->db->escape($data['delivery_type']) . "' WHERE `customer_id` = '" . (int)$customer_id . "'";
            $this->db->query($sql);
        } else {

            $sql = "INSERT INTO " . DB_PREFIX . "delivery_partner_info SET vehicle_type = '" . $this->db->escape($data['vehicle_type']) . "', delivery_type = '" . $this->db->escape($data['delivery_type']) . "',";
            $sql .= $fileSql;
            $sql .= "customer_id = '" . (int)$customer_id . "'";
            $this->db->query($sql);
        }

        //multilevel location
        $this->db->query("DELETE FROM `" . DB_PREFIX . "delivery_partner_countries` WHERE customer_id = '" . (int)$customer_id . "'");
        if (!empty($data['delivery_info'])) {
            foreach ($data['delivery_info'] as $info) {

                $area = !empty($info['area_id']) ? $this->db->escape($info['area_id']) : '';
                $areaData = $this->getArea($area);

                if(!empty($areaData['name'])) {
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "delivery_partner_countries` SET customer_id = '" . (int)$customer_id . "', 
            country_id = '" . $this->db->escape($info['country_id']) . "', 
            zone_id = '" . $this->db->escape($info['zone_id']) . "',
            area_id = '" . $area . "',
            area_name = '" . $areaData['name'] . "',
            added_date = NOW()
            ");
                }
            }
        }
    }

    public function getArea($area_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "area WHERE area_id = '" . (int)$area_id . "'");

        return $query->row;
    }

    public function getStripeCustomerId($customer_id)
    {
        $query = $this->db->query("SELECT stripe_customer_id, email, role, subscription_plan, subscription_plan_id FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

        return $query->row;
    }

    public function getSellerPlan($customer_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "subscription_plan sp LEFT JOIN " . DB_PREFIX . "subscription_user_plan sup ON (sp.plan_id = sup.subscription_plan_id) WHERE stripe_status = 'active' and customer_id = '" . (int)$customer_id . "'");

        return $query->row;
    }

    public function getFreePlanId()
    {
        $query = $this->db->query("SELECT plan_id FROM " . DB_PREFIX . "subscription_plan sp WHERE name = 'Free'");

        return $query->row;
    }

    public function updateCustomerToFree($data, $customer_id)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET `subscription_plan_id` = '".(int)$data['plan_id']."' WHERE customer_id = '" . (int)$customer_id . "'");

        $this->db->query("INSERT INTO " . DB_PREFIX . "subscription_user_plan SET `subscription_plan_id` = '".(int)$data['plan_id']."', customer_id = '" . (int)$customer_id . "', stripe_subscription_id = 'NULL', stripe_customer_id = 'NULL', stripe_status = 'active', amount = '0.00', start_date = NOW(), end_date = NOW()");
    }
}