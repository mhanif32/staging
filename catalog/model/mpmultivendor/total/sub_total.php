<?php
class ModelMpmultivendorTotalSubTotal extends Model {
	public function getTotal($total, $mpseller_id) {
		$this->load->language('extension/total/sub_total');

		$this->load->model('mpmultivendor/cart');

		$sub_total = $this->model_mpmultivendor_cart->getSubTotal($mpseller_id);

		$total['totals'][] = array(
			'code'       => 'sub_total',
			'title'      => $this->language->get('text_sub_total'),
			'value'      => $sub_total,
			'sort_order' => $this->config->get('total_sub_total_sort_order')
		);

		$total['total'] += $sub_total;
	}
}