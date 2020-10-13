<?php
class ModelMpmultivendorSubscription extends Model {
    public function getSubscriptionPlan($plan_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "subscription_plan WHERE plan_id = '" . (int)$plan_id . "'");
        return $query->row;
    }

    public function getSubscriptionPlans() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "subscription_plan WHERE name != 'Free' ORDER BY sort_order ASC");
        return $query->rows;
    }

    public function updateUserSubscription($data, $oldData)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "subscription_user_plan SET customer_id = '" . (int)$data['customer_id'] . "', subscription_plan_id = '" . (int)$data['subscription_plan_id'] . "', stripe_subscription_id = '" . $this->db->escape($data['stripe_subscription_id']) . "', amount = '" . (float)$data['amount'] . "', stripe_customer_id = '" . $this->db->escape($data['stripe_customer_id']) . "', stripe_status = '" . $this->db->escape($data['stripe_status']) ."', start_date = '" . $this->db->escape($data['start_date']) ."', end_date = '" . $this->db->escape($data['end_date']) ."', old_subscription_plan_id = '" . (int)$oldData['subscription_plan_id'] ."', old_end_date ='" . $this->db->escape($oldData['end_date']) ."', old_stripe_data = '" . $this->db->escape($oldData['stripe_data']) ."' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
    }

    public function updateCustomer($data, $customerId) {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET subscription_plan_id = '" . $data['subscription_plan_id'] . "', stripe_customer_id ='".$data['stripe_customer_id']."', subscription_plan = '" . $data['subscription_plan'] . "' WHERE customer_id = '" . (int)$customerId . "'");
    }

    public function removeCustomerSubscription($data, $customerId) {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET subscription_plan_id = '" . $data['subscription_plan_id'] . "', stripe_customer_id ='".$data['stripe_customer_id']."' WHERE customer_id = '" . (int)$customerId . "'");
    }

    public function saveStripeCard($data, $customer_id)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "subscription_user_stripe_card SET 
        customer_id = '" . (int)$customer_id . "', 
        stripe_customer_id = '" . $this->db->escape($data['customer']) . "', 
        stripe_card_id = '" . $this->db->escape($data['id']) . "', 
        object = '" . $this->db->escape($data['object']) . "', 
        address_city = '" . $this->db->escape($data['address_city']) . "', 
        address_country = '" . $this->db->escape($data['address_country']) . "', 
        address_line1 = '" . $this->db->escape($data['address_line1']) . "', 
        address_line1_check = '" . $this->db->escape($data['address_line1_check']) . "', 
        address_line2 = '" . $this->db->escape($data['address_line2']) . "', 
        address_state = '" . $this->db->escape($data['address_state']) . "', 
        address_zip = '" . $this->db->escape($data['address_zip']) . "', 
        address_zip_check = '" . $this->db->escape($data['address_zip_check']) . "', 
        brand = '" . $this->db->escape($data['brand']) . "', 
        country = '" . $this->db->escape($data['country']) . "', 
        cvc_check = '" . $this->db->escape($data['cvc_check']) . "', 
        dynamic_last4 = '" . $this->db->escape($data['dynamic_last4']) . "', 
        exp_month = '" . $this->db->escape($data['exp_month']) . "', 
        exp_year = '" . $this->db->escape($data['exp_year']) . "', 
        fingerprint = '" . $this->db->escape($data['fingerprint']) . "', 
        funding = '" . $this->db->escape($data['funding']) . "', 
        last4 = '" . $this->db->escape($data['last4']) . "', 
        metadata = '" . $this->db->escape($data['metadata']) . "', 
        name = '" . $this->db->escape($data['name']) . "', 
        tokenization_method = '" . $this->db->escape($data['tokenization_method']) . "', 
        created_at = NOW(), 
        updated_at = NOW()");
    }

    public function getSavedCards($customer_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "subscription_user_stripe_card WHERE customer_id = '" . (int)$customer_id . "'");

        return $query->rows;
    }

    public function getUserCard($user_card_id, $customer_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "subscription_user_stripe_card WHERE customer_id = '" . (int)$customer_id . "' and user_stripe_card_id = '". (int)$user_card_id ."'");

        return $query->row;
    }

    public function getUserSubscription($customer_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "subscription_user_plan WHERE customer_id = '" . (int)$customer_id . "'");

        return $query->row;
    }

    public function removeCard($user_card_id, $customer_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "subscription_user_stripe_card WHERE user_stripe_card_id = '" . (int)$user_card_id . "' AND customer_id = '" . (int)$customer_id . "'");
    }

    public function createUserSubscription($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "subscription_user_plan SET customer_id = '" . (int)$data['customer_id'] . "', subscription_plan_id = '" . (int)$data['subscription_plan_id'] . "', stripe_subscription_id = '" . $this->db->escape($data['stripe_subscription_id']) . "', amount = '" . (float)$data['amount'] . "', stripe_customer_id = '" . $this->db->escape($data['stripe_customer_id']) . "', stripe_status = '" . $this->db->escape($data['stripe_status']) ."', start_date = '" . $this->db->escape($data['start_date']) ."', end_date = '" . $this->db->escape($data['end_date']) ."', stripe_data = '" . $this->db->escape($data['end_date']) ."'");
    }

    public function removeUserSubscription($subscription_plan_id)
    {
        //$this->db->query("DELETE FROM " . DB_PREFIX . "subscription_user_plan WHERE customer_id = '" . (int)$this->customer->getId() . "'");
        $this->db->query("UPDATE " . DB_PREFIX . "subscription_user_plan SET stripe_status = 'cancelled' WHERE customer_id = '" . (int)$this->customer->getId() . "' AND subscription_plan_id = '".$subscription_plan_id."'");

        $this->db->query("UPDATE " . DB_PREFIX . "customer SET stripe_customer_id = NULL, subscription_plan_id = '', subscription_plan = NULL WHERE customer_id = '" . (int)$this->customer->getId() . "'");
    }

    public function checkIsFreePlan($customer_id)
    {
//        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer cus LEFT JOIN " . DB_PREFIX . "subscription_plan sp ON (sp.plan_id = cus.subscription_plan_id) WHERE customer_id = '" . (int)$customer_id . "'");
//
//        return $query->row;
    }
}