<?php
class ModelAccountMpmultivendorImportProduct extends Model {
	public function getProductById($product_id, $mpseller_id) {
		$query = $this->db->query("SELECT product_id, model FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "' AND p.mpseller_id = '" . (int)$mpseller_id . "'");

		return $query->row;
	}
	
	public function getProductByModel($model, $mpseller_id) {
		$query = $this->db->query("SELECT product_id, model FROM " . DB_PREFIX . "product p WHERE p.model = '" . $this->db->escape($model) . "' AND p.mpseller_id = '" . (int)$mpseller_id . "'");
		
		return $query->row;
	}
	
	public function getProductByName($name, $language_id, $mpseller_id) {
		$query = $this->db->query("SELECT pd.product_id, pd.name FROM " . DB_PREFIX . "product_description pd WHERE pd.name = '" . $this->db->escape($name) . "' AND pd.language_id = '". (int)$language_id ."' AND p.mpseller_id = '" . (int)$mpseller_id . "'");
		
		return $query->row;
	}
	
	public function addProduct($data, $find_data) {
		$sql = "INSERT INTO " . DB_PREFIX . "product SET ";

		$implode = array();

		$implode[] = " mpseller_id = '" . $this->db->escape($find_data['mpseller_id']) . "'";

		if(!empty($find_data['cell_operations']['model'])) {
			$implode[] = " model = '" . $this->db->escape($data['model']) . "'";
		}

		if(!empty($find_data['cell_operations']['sku'])) {
			$implode[] = " sku = '" . $this->db->escape($data['sku']) . "'";
		}

		if(!empty($find_data['cell_operations']['upc'])) {
			$implode[] = " upc = '" . $this->db->escape($data['upc']) . "'";
		}
		if(!empty($find_data['cell_operations']['ean'])) {
			$implode[] = " ean = '" . $this->db->escape($data['ean']) . "'";
		}
		if(!empty($find_data['cell_operations']['jan'])) {
			$implode[] = " jan = '" . $this->db->escape($data['jan']) . "'";
		}
		if(!empty($find_data['cell_operations']['isbn'])) {
			$implode[] = " isbn = '" . $this->db->escape($data['isbn']) . "'";
		}
		if(!empty($find_data['cell_operations']['mpn'])) {
			$implode[] = " mpn = '" . $this->db->escape($data['mpn']) . "'";
		}
		if(!empty($find_data['cell_operations']['location'])) {
			$implode[] = " location = '" . $this->db->escape($data['location']) . "'";
		}
		if(!empty($find_data['cell_operations']['product_image'])) {
			$implode[] = " image = '" . $this->db->escape(($data['product_image'])) . "'";
		}
		if(!empty($find_data['cell_operations']['quantity'])) {
			$implode[] = " quantity = '" . (int)$data['quantity'] . "'";
		}
		if(!empty($find_data['cell_operations']['minimum_quantity'])) {
			$implode[] = " minimum = '" . (int)$data['minimum_quantity'] . "'";
		}
		if(!empty($find_data['cell_operations']['date_available'])) {
			$implode[] = " date_available = '" . $this->db->escape($data['date_available']) . "'";
		}

		if(!empty($find_data['cell_operations']['shipping_required'])) {
			$implode[] = " shipping = '" . (int)$data['shipping_required'] . "'";
		}
		if(!empty($find_data['cell_operations']['price'])) {
			$implode[] = " price = '" . (float)str_replace(',', '', $data['price']) . "'";
		}
		
		if(!empty($data['product_id'])) {
			$implode[] = " product_id = '" . (int)$data['product_id'] . "'";
		}
		
		$implode[] = " status = 1";
		$implode[] = " date_added = NOW()";
		$implode[] = " date_modified = NOW()";

		if($implode) {			
			$sql .= implode(', ', $implode);
		}

		$this->db->query($sql);
		$product_id = $this->db->getLastId();

		// Product Description
		$sql = "INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$find_data['language_id']. "'";

		$implode = array();
		if(!empty($find_data['cell_operations']['product_name'])) {
			$implode[] = " name = '" . $this->db->escape(($data['product_name'])) . "'";
		}

		if(!empty($find_data['cell_operations']['description'])) {
			$implode[] = " description = '" . $this->db->escape(($data['description'])) . "'";
		}

		if(!empty($find_data['cell_operations']['meta_tag'])) {
			$implode[] = " tag = '" . $this->db->escape(($data['meta_tag'])) . "'";
		}
		
		if(!empty($find_data['cell_operations']['meta_title'])) {
			$implode[] = " meta_title = '" . $this->db->escape(($data['meta_title'])) . "'";
		}

		if(!empty($find_data['cell_operations']['meta_description'])) {
			$implode[] = " meta_description = '" . $this->db->escape(($data['meta_description'])) . "'";
		}
		if(!empty($find_data['cell_operations']['meta_keyword'])) {
			$implode[] = " meta_keyword = '" . $this->db->escape(($data['meta_keyword'])) . "'";
		}

		if($implode) {
			$sql .= ", ";
		}

		$sql .= implode(', ', $implode);
		$this->db->query($sql);
		
		// Product Store
		if (isset($find_data['store']) && !empty($find_data['cell_operations']['store'])) {
			foreach ($find_data['store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
	}
	
	public function editProduct($product_id, $data, $find_data) {
		$sql = "UPDATE " . DB_PREFIX . "product SET product_id = '". (int)$product_id ."'";

		$implode = array();

		if(!empty($find_data['cell_operations']['model'])) {
			$implode[] = " model = '" . $this->db->escape($data['model']) . "'";
		}

		if(!empty($find_data['cell_operations']['sku'])) {
			$implode[] = " sku = '" . $this->db->escape($data['sku']) . "'";
		}

		if(!empty($find_data['cell_operations']['upc'])) {
			$implode[] = " upc = '" . $this->db->escape($data['upc']) . "'";
		}
		if(!empty($find_data['cell_operations']['ean'])) {
			$implode[] = " ean = '" . $this->db->escape($data['ean']) . "'";
		}
		if(!empty($find_data['cell_operations']['jan'])) {
			$implode[] = " jan = '" . $this->db->escape($data['jan']) . "'";
		}
		if(!empty($find_data['cell_operations']['isbn'])) {
			$implode[] = " isbn = '" . $this->db->escape($data['isbn']) . "'";
		}
		if(!empty($find_data['cell_operations']['mpn'])) {
			$implode[] = " mpn = '" . $this->db->escape($data['mpn']) . "'";
		}
		if(!empty($find_data['cell_operations']['location'])) {
			$implode[] = " location = '" . $this->db->escape($data['location']) . "'";
		}
		if(!empty($find_data['cell_operations']['product_image'])) {
			$implode[] = " image = '" . $this->db->escape(($data['product_image'])) . "'";
		}
		if(!empty($find_data['cell_operations']['quantity'])) {
			$implode[] = " quantity = '" . (int)$data['quantity'] . "'";
		}
		if(!empty($find_data['cell_operations']['minimum_quantity'])) {
			$implode[] = " minimum = '" . (int)$data['minimum_quantity'] . "'";
		}
		if(!empty($find_data['cell_operations']['subtract'])) {
			$implode[] = " subtract = '" . (int)$data['subtract'] . "'";
		}
		if(!empty($find_data['cell_operations']['date_available'])) {
			$implode[] = " date_available = '" . $this->db->escape($data['date_available']) . "'";
		}

		if(!empty($find_data['cell_operations']['shipping_required'])) {
			$implode[] = " shipping = '" . (int)$data['shipping_required'] . "'";
		}
		if(!empty($find_data['cell_operations']['price'])) {
			$implode[] = " price = '" . (float)str_replace(',', '', $data['price']) . "'";
		}

		$implode[] = " date_modified = NOW()";

		if($implode) {
			$sql .= ", ";
		}

		$sql .= implode(', ', $implode);

	 	$sql .= " WHERE product_id = '". (int)$product_id ."' AND mpseller_id = '" . $this->db->escape($find_data['mpseller_id']) . "'";
		
		$this->db->query($sql);
		
		
		// Product Description
		$product_description_query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$find_data['language_id'] . "'");
		
		if($product_description_query->row['total'] > 0) {
			// Product Description
			$sql = "UPDATE " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$find_data['language_id']. "'";

			$implode = array();
			if(!empty($find_data['cell_operations']['product_name'])) {
				$implode[] = " name = '" . $this->db->escape(($data['product_name'])) . "'";
			}

			if(!empty($find_data['cell_operations']['description'])) {
				$implode[] = " description = '" . $this->db->escape(($data['description'])) . "'";
			}

			if(!empty($find_data['cell_operations']['meta_tag'])) {
				$implode[] = " tag = '" . $this->db->escape(($data['meta_tag'])) . "'";
			}
			
			if(!empty($find_data['cell_operations']['meta_title'])) {
				$implode[] = " meta_title = '" . $this->db->escape(($data['meta_title'])) . "'";
			}

			if(!empty($find_data['cell_operations']['meta_description'])) {
				$implode[] = " meta_description = '" . $this->db->escape(($data['meta_description'])) . "'";
			}
			if(!empty($find_data['cell_operations']['meta_keyword'])) {
				$implode[] = " meta_keyword = '" . $this->db->escape(($data['meta_keyword'])) . "'";
			}

			if($implode) {
				$sql .= ", ";
			}

			$sql .= implode(', ', $implode);

			$sql .= " WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$find_data['language_id']. "'";

			$this->db->query($sql);
		} else {
			// Product Description
			$sql = "INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$find_data['language_id']. "'";

			$implode = array();
			if(!empty($find_data['cell_operations']['product_name'])) {
				$implode[] = " name = '" . $this->db->escape(($data['product_name'])) . "'";
			}

			if(!empty($find_data['cell_operations']['description'])) {
				$implode[] = " description = '" . $this->db->escape(($data['description'])) . "'";
			}

			if(!empty($find_data['cell_operations']['meta_tag'])) {
				$implode[] = " tag = '" . $this->db->escape(($data['meta_tag'])) . "'";
			}
			
			if(!empty($find_data['cell_operations']['meta_title'])) {
				$implode[] = " meta_title = '" . $this->db->escape(($data['meta_title'])) . "'";
			}

			if(!empty($find_data['cell_operations']['meta_description'])) {
				$implode[] = " meta_description = '" . $this->db->escape(($data['meta_description'])) . "'";
			}
			if(!empty($find_data['cell_operations']['meta_keyword'])) {
				$implode[] = " meta_keyword = '" . $this->db->escape(($data['meta_keyword'])) . "'";
			}

			if($implode) {
				$sql .= ", ";
			}

			$sql .= implode(', ', $implode);
			$this->db->query($sql);
		}

		if(!empty($find_data['cell_operations']['store'])) {
			// Product Store
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

			if (isset($find_data['store'])) {
				foreach ($find_data['store'] as $store_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
				}
			}
		}
	}
}