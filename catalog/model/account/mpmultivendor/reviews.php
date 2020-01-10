<?php
class ModelAccountMpmultivendorReviews extends Model {
	public function getReviews($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "mpseller_review WHERE mpseller_review_id > 0";

		if (!empty($data['filter_mpseller_id'])) {
			$sql .= " AND mpseller_id = '" . (int)$data['filter_mpseller_id'] . "'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_rating']) && !is_null($data['filter_rating'])) {
			$sql .= " AND rating = '" . (int)$data['filter_rating'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$sort_data = array(
			'author',
			'title',
			'description',
			'rating',
			'status',
			'date_added',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date_added";
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
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpseller_review WHERE mpseller_review_id > 0";

		if (!empty($data['filter_mpseller_id'])) {
			$sql .= " AND mpseller_id = '" . (int)$data['filter_mpseller_id'] . "'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_rating']) && !is_null($data['filter_rating'])) {
			$sql .= " AND rating = '" . (int)$data['filter_rating'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function updatedReviewStatus($mpseller_review_id, $mpseller_id, $status) {
		$this->db->query("UPDATE ". DB_PREFIX ."mpseller_review SET status = '". (int)$status ."', date_modified = NOW() WHERE mpseller_review_id = '". (int)$mpseller_review_id ."' AND mpseller_id = '". (int)$mpseller_id ."'");
	}
}