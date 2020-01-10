<?php
class ModelMpmultivendorReview extends Model {
	public function addReview($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mpseller_review SET author = '" . $this->db->escape($data['author']) . "', mpseller_id = '" . (int)$data['mpseller_id'] . "', title = '" . $this->db->escape(strip_tags($data['title'])) . "', description = '" . $this->db->escape(strip_tags($data['description'])) . "', rating = '" . (int)$data['rating'] . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "'");

		$mpseller_review_id = $this->db->getLastId();

		return $mpseller_review_id;
	}

	public function editReview($mpseller_review_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpseller_review SET author = '" . $this->db->escape($data['author']) . "', title = '" . $this->db->escape(strip_tags($data['title'])) . "', description = '" . $this->db->escape(strip_tags($data['description'])) . "', rating = '" . (int)$data['rating'] . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW() WHERE mpseller_review_id = '" . (int)$mpseller_review_id . "'");
	}

	public function deleteReview($mpseller_review_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpseller_review WHERE mpseller_review_id = '" . (int)$mpseller_review_id . "'");
	}

	public function getReview($mpseller_review_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT mps.store_owner FROM " . DB_PREFIX . "mpseller mps WHERE mps.mpseller_id = mr.mpseller_id) AS store_owner FROM " . DB_PREFIX . "mpseller_review mr WHERE mr.mpseller_review_id = '" . (int)$mpseller_review_id . "'");

		return $query->row;
	}

	public function getReviews($data = array()) {
		$sql = "SELECT mr.*, (SELECT mps.store_owner FROM " . DB_PREFIX . "mpseller mps WHERE mps.mpseller_id = mr.mpseller_id) AS store_owner FROM " . DB_PREFIX . "mpseller_review mr WHERE mr.mpseller_review_id > 0";

		if (!empty($data['filter_store_owner'])) {
			$sql .= " AND mpseller_id IN (SELECT mps.mpseller_id FROM " . DB_PREFIX . "mpseller mps WHERE mps.store_owner LIKE '" . $this->db->escape($data['filter_store_owner']) . "%')";
		}

		if (!empty($data['filter_mpseller_id'])) {
			$sql .= " AND mr.mpseller_id = '" . $this->db->escape($data['filter_mpseller_id']) . "'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND mr.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND mr.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(mr.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$sort_data = array(
			'store_owner',
			'mr.author',
			'mr.title',
			'mr.rating',
			'mr.status',
			'mr.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY mr.date_added";
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

	public function getTotalReviews($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpseller_review mr WHERE mr.mpseller_review_id > 0";

		if (!empty($data['filter_store_owner'])) {
			$sql .= " AND mpseller_id IN (SELECT mps.mpseller_id FROM " . DB_PREFIX . "mpseller mps WHERE mps.store_owner LIKE '" . $this->db->escape($data['filter_store_owner']) . "%')";
		}

		if (!empty($data['filter_mpseller_id'])) {
			$sql .= " AND mr.mpseller_id = '" . $this->db->escape($data['filter_mpseller_id']) . "'";
		}
		
		if (!empty($data['filter_author'])) {
			$sql .= " AND mr.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND mr.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(mr.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}