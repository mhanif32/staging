<?php
class ModelSubscriptionExecutiveMemberPlan extends Model {
	public function addSubscriptionPlan($data, $stripe_plan_id = NULL) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "subscription_plan_em SET sort_order = '" . (int)$data['sort_order'] . "', `name` = '" . $this->db->escape($data['name']) . "', amount = '" . $this->db->escape($data['amount']) . "', currency = '" . $this->db->escape($data['currency']) . "', `interval` = '" . $this->db->escape($data['interval']) . "', interval_count = '" . (int)$data['interval_count'] . "', stripe_plan_id = '" . $this->db->escape($stripe_plan_id) . "', rent_percentage = '" . $data['rent_percentage'] . "', date_added = NOW()");

		$customer_group_id = $this->db->getLastId();
		return $customer_group_id;
	}

	public function editSubscriptionPlan($plan_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "subscription_plan_em SET rent_percentage = '" . (float)$data['rent_percentage'] . "' WHERE plan_id = '" . (int)$plan_id . "'");
	}

	public function deleteCustomerGroup($customer_group_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_group_description WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "tax_rate_to_customer_group WHERE customer_group_id = '" . (int)$customer_group_id . "'");
	}

	public function getSubscriptionPlan($plan_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "subscription_plan p WHERE p.plan_id = '" . (int)$plan_id . "'");

		return $query->row;
	}

	public function getSubscriptionPlans($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "subscription_plan_em cg ";

		$sort_data = array(
			'cg.name',
			'cg.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cg.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getCustomerGroupDescriptions($customer_group_id) {
		$customer_group_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group_description WHERE customer_group_id = '" . (int)$customer_group_id . "'");

		foreach ($query->rows as $result) {
			$customer_group_data[$result['language_id']] = array(
				'name'        => $result['name'],
				'description' => $result['description']
			);
		}

		return $customer_group_data;
	}

	public function getTotalSubscriptionPlans() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "subscription_plan_em");

		return $query->row['total'];
	}
}