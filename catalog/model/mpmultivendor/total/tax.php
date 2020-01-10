<?php
class ModelMpmultivendorTotalTax extends Model {
	public function getTotal($total, $mpseller_id) {
		foreach ($total['taxes'] as $key => $value) {
			if ($value > 0) {
				$total['totals'][] = array(
					'code'       => 'tax',
					'title'      => $this->tax->getRateName($key),
					'value'      => $value,
					'sort_order' => $this->config->get('total_tax_sort_order')
				);

				$total['total'] += $value;
			}
		}
	}
}