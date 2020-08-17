<?php
class ModelMpmultivendorSubscription extends Model {
    public function getSubscriptionPlans() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "subscription_plan ORDER BY sort_order ASC");
        return $query->rows;
    }

    public function createUserSubscription($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "subscription_user_plan SET customer_id = '" . (int)$data['customer_id'] . "', subscription_plan_id = '" . (int)$data['subscription_plan_id'] . "', stripe_subscription_id = '" . $this->db->escape($data['stripe_subscription_id']) . "', amount = '" . (float)$data['amount'] . "', stripe_customer_id = '" . $this->db->escape($data['stripe_customer_id']) . "', stripe_status = '" . $this->db->escape($data['stripe_status']) ."', start_date = '" . $this->db->escape($data['start_date']) ."', end_date = '" . $this->db->escape($data['end_date']) ."'");
    }
}