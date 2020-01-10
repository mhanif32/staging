<?php
class ModelMpmultivendorTotalShipping extends Model {
	public function getTotal($total, $mpseller_id) {
		
		$this->load->model('catalog/product');
		$this->load->model('mpmultivendor/mv_seller');

		if ($this->cart->hasShipping() && isset($this->session->data['shipping_method'])) {
			if($this->session->data['shipping_method']['code'] == 'mpseller.mpseller') {
				$mpseller_cost = 0;
				$mpseller_ids = array();
				foreach ($this->cart->getProducts() as $product) {
					if($product['mpseller_id'] == $mpseller_id) {
						if($product['shipping']) {
							$mpseller_info = $this->model_mpmultivendor_mv_seller->getSellerStoreInfo($product['mpseller_id']);
							if($mpseller_info) {
								if($mpseller_info['shipping_type'] == 'order_wise') {
									if($mpseller_info['shipping_amount']) {
										$mpseller_ids[$product['mpseller_id']] = $mpseller_info['shipping_amount'];
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
				}

				if($mpseller_ids) {
					foreach ($mpseller_ids as $mpseller_id_key => $mpseller_id_amount) {
						$mpseller_cost += $mpseller_id_amount;
					}
				}
			
				$total['totals'][] = array(
					'code'       => 'shipping',
					'title'      => $this->session->data['shipping_method']['title'],
					'value'      => $mpseller_cost,
					'sort_order' => $this->config->get('total_shipping_sort_order')
				);

				if ($this->session->data['shipping_method']['tax_class_id']) {
					$tax_rates = $this->tax->getRates($mpseller_cost, $this->session->data['shipping_method']['tax_class_id']);

					foreach ($tax_rates as $tax_rate) {
						if (!isset($total['taxes'][$tax_rate['tax_rate_id']])) {
							$total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
						} else {
							$total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
						}
					}
				}

				$total['total'] += $mpseller_cost;
			}
		}
	}
}