<?php
class ModelMpmultivendorSubscription extends Model {
    public function getSubscriptionPlans() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "subscription_plan ORDER BY sort_order ASC");
        return $query->rows;
    }
}