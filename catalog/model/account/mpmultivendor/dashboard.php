<?php
class ModelAccountMpmultivendorDashboard extends Model {
	public function getCommissionTotal($data = array()) {
		$sql = "SELECT SUM(amount) AS total FROM " . DB_PREFIX . "mpseller_commission mc WHERE mc.type = 'sale' AND mc.status = '1'";

		$sql .= " AND mc.mpseller_id = '" . $this->db->escape($data['filter_mpseller_id']) . "'";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getPaymentTotal($data = array()) {
		$sql = "SELECT SUM(amount) AS total FROM " . DB_PREFIX . "mpseller_commission mc WHERE mc.type = 'withdraw' AND mc.status = '1'";

		$sql .= " AND mc.mpseller_id = '" . $this->db->escape($data['filter_mpseller_id']) . "'";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getAvailableBalance($data = array()) {
		$available_balance = $this->getCommissionTotal($data) - $this->getPaymentTotal($data);

		return $available_balance;
	}

	public function getTotalProucts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p WHERE p.mpseller_id = '". (int)$data['filter_mpseller_id'] ."' AND p.status = 1";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

    public function getTotalServices($data = array()) {
        $sql = "SELECT COUNT(DISTINCT s.service_id) AS total FROM " . DB_PREFIX . "service s WHERE s.mpseller_id = '". (int)$data['filter_mpseller_id'] ."' AND s.status = 1";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

	public function getTotalOrders($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "order` o LEFT JOIN `". DB_PREFIX ."mpseller_order_product` mpo ON (o.order_id=mpo.order_id) WHERE mpo.mpseller_id = '" . (int)$data['filter_mpseller_id'] . "' AND o.order_status_id > '0' AND mpo.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if(!empty($data['filter_order_status_id'])) {
			$sql .= " AND mpo.order_status_id = '". (int)$data['filter_order_status_id'] ."'";
		}

		$sql .= " GROUP BY mpo.order_id";

		$query = $this->db->query($sql);

		return $query->num_rows;
	}

	public function getTotalSales($data = array()) {
		$sql = "SELECT SUM(total) as total FROM " . DB_PREFIX . "mpseller_order_product mpo WHERE mpo.order_status_id > 0";

		$sql .= " AND mpo.mpseller_id = '" . $this->db->escape($data['filter_mpseller_id']) . "'";

		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sql .= " AND mpo.order_status_id IN (" . implode(",", $implode) . ") ";
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalReviews($data = array()) {
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpseller_review WHERE mpseller_review_id > 0";

		if (!empty($data['filter_mpseller_id'])) {
			$sql .= " AND mpseller_id = '" . (int)$data['filter_mpseller_id'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}
		
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}