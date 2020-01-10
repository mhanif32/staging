<?php
class ModelAccountMpmultivendorExportProduct extends Model {
	public function getProducts($data = array()) {
		// Find Product Data
		$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";

		// Find Store Left Join
		if (isset($data['find_store_id']) && $data['find_store_id'] != '') {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) ";
		}
		
		// Find Language
		$sql .= " WHERE pd.language_id = '" . (int)$data['find_language_id'] . "'";
		$sql .= " AND p.mpseller_id = '" . (int)$data['filter_mpseller_id'] . "'";
		
		// Find Store
		if (isset($data['find_store_id']) && $data['find_store_id'] != '') {
			$sql .= " AND p2s.store_id = '" . (int)$data['find_store_id'] . "'";
		}

		// Find Quantity Range
		if ((isset($data['find_quantity_start']) && $data['find_quantity_start'] != '') && (isset($data['find_quantity_limit']) && $data['find_quantity_limit'] != '')) {
			// BETWEEN
			$sql .= " AND p.quantity BETWEEN '" . (int)$data['find_quantity_start'] . "' AND '" . (int)$data['find_quantity_limit'] . "'";
		}else if((isset($data['find_quantity_start']) && $data['find_quantity_start'] != '') && (isset($data['find_quantity_limit']) && $data['find_quantity_limit'] == '')) {
			// START FROM LIMIT (EMPTY)
			$sql .= " AND p.quantity >= '" . (int)$data['find_quantity_start'] . "'";
		}else if((isset($data['find_quantity_start']) && $data['find_quantity_start'] == '') && (isset($data['find_quantity_limit']) && $data['find_quantity_limit'] != '')) {
			// START (EMPTY) LIMIT TO
			$sql .= " AND p.quantity <= '" . (int)$data['find_quantity_limit'] . "'";
		}
				
		// Find Price Range
		if ((isset($data['find_price_start']) && $data['find_price_start'] != '') && (isset($data['find_price_limit']) && $data['find_price_limit'] != '')) {
			// BETWEEN
			$sql .= " AND p.price BETWEEN '" . $this->db->escape($data['find_price_start']) . "' AND '" . $this->db->escape($data['find_price_limit']) . "'";
		}else if((isset($data['find_price_start']) && $data['find_price_start'] != '') && (isset($data['find_price_limit']) && $data['find_price_limit'] == '')) {
			// START FROM LIMIT (EMPTY)
			$sql .= " AND p.price >= '" . $this->db->escape($data['find_price_start']) . "'";
		}else if((isset($data['find_price_start']) && $data['find_price_start'] == '') && (isset($data['find_price_limit']) && $data['find_price_limit'] != '')) {
			// START (EMPTY) LIMIT TO
			$sql .= " AND p.price <= '" . $this->db->escape($data['find_price_limit']) . "'";
		}
		
		// Find Stock Status
		if (isset($data['find_stock_status_id']) && !is_null($data['find_stock_status_id'])) {
			$sql .= " AND p.stock_status_id = '" . (int)$data['find_stock_status_id'] . "'";
		}
		
		// Find Products
		if (!empty($data['find_product'])) {
			$sql .= " AND p.product_id IN('". implode("','", $data['find_product']) ."')";
		}
		
		// Find Manufacturer
		if (!empty($data['find_manufacturer'])) {
			$sql .= " AND p.manufacturer_id IN('". implode("','", $data['find_manufacturer']) ."')";
		}
				
		// Group Product Id
		$sql .= " GROUP BY p.product_id";

		// Sort ORDER
		$sql .= " ORDER BY pd.name ASC";

		// Find Limit Range
		if ((isset($data['find_product_start']) && $data['find_product_start'] != '') && (isset($data['find_product_limit']) && $data['find_product_limit'] != '')) {
			// Limit 0, 100;
			$sql .= " LIMIT " . (int)$data['find_product_start'] . "," . (int)$data['find_product_limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	
	public function getProductStores($product_id, $store_id = '') {
		$product_store_data = array();

		$sql = "SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'";

		if (!empty($store_id) || $store_id == '0') {
			$sql .= " AND store_id = '" . (int)$store_id . "'";
		}
		
		$query = $this->db->query($sql);
		
		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}

	
	public function getLanguage($language_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "language WHERE language_id = '" . (int)$language_id . "'");

		return $query->row;
	}	
}