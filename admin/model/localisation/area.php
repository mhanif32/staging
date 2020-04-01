<?php
class ModelLocalisationArea extends Model {
	public function addArea($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "area SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "'");

		$this->cache->delete('area');
		
		return $this->db->getLastId();
	}

	public function editArea($area_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "area SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "' WHERE area_id = '" . (int)$area_id . "'");

		$this->cache->delete('area');
	}

	public function deleteArea($area_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "area WHERE area_id = '" . (int)$area_id . "'");

		$this->cache->delete('area');
	}

	public function getArea($area_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "area WHERE area_id = '" . (int)$area_id . "'");

		return $query->row;
	}

	public function getAreas($data = array()) {
		$sql = "SELECT area_id, a.name AS area, a.code AS code, c.name AS country, z.name AS state FROM " . DB_PREFIX . "area a LEFT JOIN " . DB_PREFIX . "country c ON (a.country_id = c.country_id) LEFT JOIN " . DB_PREFIX . "zone z ON (a.zone_id = z.zone_id)";

		$sort_data = array(
			'c.name',
			'z.name',
			'a.name',
			'a.code'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY c.name";
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
//echo $sql;exit('aaa');
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getAreasByCountryId($country_id) {
		$area_data = $this->cache->get('area.' . (int)$country_id);

		if (!$area_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "area WHERE country_id = '" . (int)$country_id . "' AND status = '1' ORDER BY name");

			$area_data = $query->rows;

			$this->cache->set('area.' . (int)$country_id, $area_data);
		}

		return $area_data;
	}

	public function getTotalAreas() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "area");

		return $query->row['total'];
	}

	public function getTotalAreasByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "area WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}
}