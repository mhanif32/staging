<?php
class ModelLocalisationCountry extends Model {
	public function getCountry($country_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "' AND status = '1'");

		return $query->row;
	}

	public function getCountries() {
		$country_data = $this->cache->get('country.catalog');

		if (!$country_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE status = '1' ORDER BY name ASC");

			$country_data = $query->rows;

			$this->cache->set('country.catalog', $country_data);
		}

		return $country_data;
	}

    public function getDeliveryPartnerCountries() {

            $query = $this->db->query("SELECT ct.* FROM " . DB_PREFIX . "delivery_partner_countries dpc
            LEFT JOIN " . DB_PREFIX . "country ct ON ct.country_id = dpc.country_id
            WHERE status = '1' 
            GROUP BY dpc.country_id
            ORDER BY name ASC");

            return $query->rows;
    }

	public function getSavedCountries($customer_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "delivery_partner_countries WHERE customer_id = '" . (int)$customer_id . "'");
        return $query->rows;
    }

    public function getProductLocation($product_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_location WHERE product_id = '" . (int)$product_id . "'");
        return $query->rows;
    }

    public function getCountryIdFromName($name)
    {
        $query = $this->db->query("SELECT country_id FROM " . DB_PREFIX . "country WHERE name = '" .  $this->db->escape($name) . "'");
        return $query->row['country_id'];
    }

    public function getCountriesFilter($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "country cd2 WHERE status = '1' ";

        if (!empty($data['filter_name'])) {
            $sql .= " AND cd2.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sql .= " GROUP BY cd2.country_id";

        $sort_data = array(
            'name'
        );

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
        //echo  $sql;exit('aaaa');
        $query = $this->db->query($sql);

        return $query->rows;
    }
}