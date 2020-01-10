<?php
class ModelMpmultivendorEnquiries extends Model {
	public function deleteEnquiry($mpseller_enquiry_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpseller_enquiry WHERE mpseller_enquiry_id = '" . (int)$mpseller_enquiry_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpseller_enquiry_chat WHERE mpseller_enquiry_id = '" . (int)$mpseller_enquiry_id . "'");
	}

	public function getEnquiry($mpseller_enquiry_id) {
		$query = $this->db->query("SELECT e.*, (SELECT mps.store_owner FROM " . DB_PREFIX . "mpseller mps WHERE mps.mpseller_id = e.mpseller_id) AS store_owner FROM " . DB_PREFIX . "mpseller_enquiry e WHERE e.mpseller_enquiry_id = '" . (int)$mpseller_enquiry_id . "'");

		return $query->row;
	}

	public function getEnquiries($data = array()) {
		$sql = "SELECT e.*, (SELECT mps.store_owner FROM " . DB_PREFIX . "mpseller mps WHERE mps.mpseller_id = e.mpseller_id) AS store_owner FROM " . DB_PREFIX . "mpseller_enquiry e WHERE e.mpseller_enquiry_id > 0";

		if (!empty($data['filter_store_owner'])) {
			$sql .= " AND mpseller_id IN (SELECT mps.mpseller_id FROM " . DB_PREFIX . "mpseller mps WHERE mps.store_owner LIKE '" . $this->db->escape($data['filter_store_owner']) . "%')";
		}

		if (!empty($data['filter_mpseller_id'])) {
			$sql .= " AND e.mpseller_id = '" . $this->db->escape($data['filter_mpseller_id']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND e.name LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_customer_email'])) {
			$sql .= " AND e.email LIKE '" . $this->db->escape($data['filter_customer_email']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(e.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$sort_data = array(
			'e.date_added',
			'e.date_modified',
			'store_owner',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY e.date_modified";
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

	public function getTotalEnquiries($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpseller_enquiry e WHERE e.mpseller_enquiry_id > 0";

		if (!empty($data['filter_store_owner'])) {
			$sql .= " AND mpseller_id IN (SELECT mps.mpseller_id FROM " . DB_PREFIX . "mpseller mps WHERE mps.store_owner LIKE '" . $this->db->escape($data['filter_store_owner']) . "%')";
		}

		if (!empty($data['filter_mpseller_id'])) {
			$sql .= " AND e.mpseller_id = '" . $this->db->escape($data['filter_mpseller_id']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND e.name LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_customer_email'])) {
			$sql .= " AND e.email LIKE '" . $this->db->escape($data['filter_customer_email']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(e.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getEnquiryChats($enquiry_id) {
		$sql = "SELECT c.*, e.name FROM " . DB_PREFIX . "mpseller_enquiry_chat c LEFT JOIN " . DB_PREFIX . "mpseller_enquiry e ON(c.mpseller_enquiry_id = e.mpseller_enquiry_id) WHERE e.mpseller_enquiry_id = '" . (int)$enquiry_id . "' ORDER BY c.mpseller_enquiry_chat_id ASC";

		$query = $this->db->query($sql);

		return $query->rows;
	}
}