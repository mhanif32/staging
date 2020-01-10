<?php
class ModelExtensionShippingMpseller extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/mpseller');
		
		$this->load->model('mpmultivendor/mv_seller');
		
		$this->load->model('catalog/product');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_mpseller_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('shipping_mpseller_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		$mpseller_cost = 0;
		$mpseller_ids = array();

		if ($status) {
			foreach ($this->cart->getProducts() as $product) {
				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
				
				if($product_info && $product['shipping']) {
					$mpseller_info = $this->model_mpmultivendor_mv_seller->getSellerStoreInfo($product_info['mpseller_id']);
					if($mpseller_info) {
						if($mpseller_info['shipping_type'] == 'order_wise') {
							if($mpseller_info['shipping_amount']) {
								$mpseller_ids[$product_info['mpseller_id']] = $mpseller_info['shipping_amount'];
							}
						}

						if($mpseller_info['shipping_type'] == 'product_wise') {
							if($mpseller_info['shipping_amount']) {
								$mpseller_cost += $mpseller_info['shipping_amount'];
							}
						}
					}
				}
			}

			if($mpseller_ids) {
				foreach ($mpseller_ids as $mpseller_id_key => $mpseller_id_amount) {
					$mpseller_cost += $mpseller_id_amount;
				}
			}

			$quote_data = array();

			if($mpseller_cost) {
				$quote_data['mpseller'] = array(
					'code'         => 'mpseller.mpseller',
					'title'        => $this->language->get('text_description'),
					'cost'         => $mpseller_cost,
					'tax_class_id' => $this->config->get('shipping_mpseller_tax_class_id'),
					'text'         => $this->currency->format($this->tax->calculate($mpseller_cost, $this->config->get('shipping_mpseller_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
				);

				$method_data = array(
					'code'       => 'mpseller',
					'title'      => $this->language->get('text_title'),
					'quote'      => $quote_data,
					'sort_order' => $this->config->get('shipping_mpseller_sort_order'),
					'error'      => false
				);
			}
		}

		return $method_data;
	}
}