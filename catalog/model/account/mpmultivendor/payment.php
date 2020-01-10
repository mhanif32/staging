<?php
class ModelAccountMpmultivendorPayment extends Model {
	public function getPayments($data = array()) {
		$sql = "SELECT mc.* FROM " . DB_PREFIX . "mpseller_commission mc WHERE mc.mpseller_commission_id > 0 AND type = 'withdraw' AND mc.status = '1'";

		$sql .= " AND mc.mpseller_id = '" . $this->db->escape($data['filter_mpseller_id']) . "'";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(mc.date_added) >= DATE('" . $this->db->escape($data['filter_date_start']) . "')";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(mc.date_added) <= DATE('" . $this->db->escape($data['filter_date_end']) . "')";
		}

		$sort_data = array(
			'store_owner',
			'store_name',
			'price',
			'name',
			'quantity',
			'total',
			'mc.order_id',
			'mc.amount',
			'mc.date_added',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY mc.date_added";
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

	public function getTotalPayments($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpseller_commission mc WHERE mc.mpseller_commission_id > 0 AND type = 'withdraw' AND mc.status = '1'";

		$sql .= " AND mc.mpseller_id = '" . $this->db->escape($data['filter_mpseller_id']) . "'";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(mc.date_added) >= DATE('" . $this->db->escape($data['filter_date_start']) . "')";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(mc.date_added) <= DATE('" . $this->db->escape($data['filter_date_end']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getPaymentTotal($data = array()) {
		$sql = "SELECT SUM(amount) AS total FROM " . DB_PREFIX . "mpseller_commission mc WHERE mc.type = 'withdraw' AND mc.status = '1'";

		$sql .= " AND mc.mpseller_id = '" . $this->db->escape($data['filter_mpseller_id']) . "'";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(mc.date_added) >= DATE('" . $this->db->escape($data['filter_date_start']) . "')";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(mc.date_added) <= DATE('" . $this->db->escape($data['filter_date_end']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}