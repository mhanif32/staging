<?php
class ModelAccountMpmultivendorInformationSection extends Model {
	public function addInformationSection($data, $mpseller_id) {
		$data['description'] = strip_tags(html_entity_decode($data['description'], ENT_QUOTES, 'UTF-8'));

		$this->db->query("INSERT INTO " . DB_PREFIX . "mpseller_information_section SET mpseller_id = '" . (int)$mpseller_id . "', title = '" . $this->db->escape($data['title']) . "', description = '" . $this->db->escape($data['description']) . "', meta_title = '" . $this->db->escape($data['meta_title']) . "', meta_description = '" . $this->db->escape($data['meta_description']) . "', meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "'");

		$mpseller_information_section_id = $this->db->getLastId();

		// SEO URL
		if (isset($data['information_seo_url'])) {
			foreach ($data['information_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'page_id=" . (int)$mpseller_information_section_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

		return $mpseller_information_section_id;
	}

	public function editInformationSection($mpseller_information_section_id, $data, $mpseller_id) {
		$data['description'] = strip_tags(html_entity_decode($data['description'], ENT_QUOTES, 'UTF-8'));
		
		$this->db->query("UPDATE " . DB_PREFIX . "mpseller_information_section SET mpseller_id = '" . (int)$mpseller_id . "', title = '" . $this->db->escape($data['title']) . "', description = '" . $this->db->escape($data['description']) . "', meta_title = '" . $this->db->escape($data['meta_title']) . "', meta_description = '" . $this->db->escape($data['meta_description']) . "', meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "' WHERE mpseller_information_section_id = '" . (int)$mpseller_information_section_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'page_id=" . (int)$mpseller_information_section_id . "'");

		if (isset($data['information_seo_url'])) {
			foreach ($data['information_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (trim($keyword)) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'page_id=" . (int)$mpseller_information_section_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
	}

	public function deleteInformationSection($mpseller_information_section_id, $mpseller_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpseller_information_section WHERE mpseller_id = '" . (int)$mpseller_id . "' AND mpseller_information_section_id = '" . (int)$mpseller_information_section_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'page_id=" . (int)$mpseller_information_section_id . "'");
	}

	public function getInformationSection($mpseller_information_section_id, $mpseller_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mpseller_information_section WHERE mpseller_id = '" . (int)$mpseller_id . "' AND mpseller_information_section_id = '" . (int)$mpseller_information_section_id . "'");

		return $query->row;
	}

	public function getInformationSections($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "mpseller_information_section WHERE mpseller_id = '" . (int)$data['mpseller_id'] . "'";

		$sql .= " ORDER BY title ASC";

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

	public function getTotalInformationSections($data = array()) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpseller_information_section WHERE mpseller_id = '" . (int)$data['mpseller_id'] . "'");

		return $query->row['total'];
	}

	public function getInformationSeoUrls($mpseller_information_section_id) {
		$information_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'page_id=" . (int)$mpseller_information_section_id . "'");

		foreach ($query->rows as $result) {
			$information_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $information_seo_url_data;
	}

	public function getSeoUrlsByKeyword($keyword) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE keyword = '" . $this->db->escape($keyword) . "'");

		return $query->rows;
	}
}