<?php
class ModelMpmultivendorCart extends Model {
	public function getSubTotal($mpseller_id) {
		$total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if($product['mpseller_id'] == $mpseller_id) {
				$total += $product['total'];
			}
		}

		return $total;
	}

	public function getTaxes($mpseller_id) {
		$tax_data = array();

		foreach ($this->cart->getProducts() as $product) {
			if($product['mpseller_id'] == $mpseller_id) {
				if ($product['tax_class_id']) {
					$tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

					foreach ($tax_rates as $tax_rate) {
						if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
							$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
						} else {
							$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
						}
					}
				}
			}
		}

		return $tax_data;
	}

	public function getTotal($mpseller_id) {
		$total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if($product['mpseller_id'] == $mpseller_id) {
				$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
			}
		}

		return $total;
	}

	public function assignSellerOrderTotal() {
		$mpseller_able_totals = array(
			'total',
			'shipping',
			'tax',
			'sub_total',
		);

		$order_data_mpseller_total = array();

		$this->load->model('mpmultivendor/cart');
		foreach ($this->cart->getProducts() as $product) {
			if(!empty($product['mpseller_id'])) {
				$mpseller_total = array();
				$mpseller_total[$product['mpseller_id']]['totals'] = array();
				$mpseller_total[$product['mpseller_id']]['taxes'] = $this->model_mpmultivendor_cart->getTaxes($product['mpseller_id']);
				$mpseller_total[$product['mpseller_id']]['total'] = 0;

				$total_data = array(
					'totals' => &$mpseller_total[$product['mpseller_id']]['totals'],
					'taxes'  => &$mpseller_total[$product['mpseller_id']]['taxes'],
					'total'  => &$mpseller_total[$product['mpseller_id']]['total'],
				);

				$this->load->model('setting/extension');

				$sort_order = array();

				$mpseller_results = $this->model_setting_extension->getExtensions('total');

				foreach ($mpseller_results as $key => $value) {
					$sort_order[$key] = $this->config->get('total_'. $value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $mpseller_results);

				foreach ($mpseller_results as $mpseller_result) {
					if ($this->config->get('total_'. $mpseller_result['code'] . '_status')) {
						if (in_array($mpseller_result['code'], $mpseller_able_totals)) {
							$this->load->model('mpmultivendor/total/' . $mpseller_result['code']);
							$this->{'model_mpmultivendor_total_' . $mpseller_result['code']}->getTotal($total_data, $product['mpseller_id']);

						}
					}
				}

				$sort_order = array();

				foreach ($mpseller_total[$product['mpseller_id']]['totals'] as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $mpseller_total[$product['mpseller_id']]['totals']);

				$order_data_mpseller_total[$product['mpseller_id']] = $mpseller_total[$product['mpseller_id']]['totals'];
			}
		}

		return $order_data_mpseller_total;
	}

	public function getMpsellerOrderProducts($order_id) {
		return $this->db->query("SELECT * FROM ". DB_PREFIX ."mpseller_order_product WHERE order_id = '". (int)$order_id ."'")->rows;
	}

	public function getMpsellerLatestOrderHistory($order_id, $mpseller_id) {
		return $this->db->query("SELECT * FROM ". DB_PREFIX ."mpseller_order_history WHERE order_id = '" . (int)$order_id . "' AND mpseller_id = '". (int)$mpseller_id ."' ORDER BY date_added DESC LIMIT 0, 1")->row;
	}

	/* Email Work Starts */
	public function SendOrderEmailToSeller($order_id, $order_status_id, $comment) {
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);

		if ($order_info && $order_status_id) {
			$this->load->language('mpmultivendor_mail/order_alert');
			
			$sellers = $this->db->query("SELECT mpseller_id FROM ". DB_PREFIX ."mpseller_order_product WHERE order_id = '". (int)$order_id ."' GROUP BY mpseller_id")->rows;
			foreach($sellers as $seller) {
				$seller_info = $this->db->query("SELECT * FROM ". DB_PREFIX ."mpseller m WHERE m.mpseller_id = '". (int)$seller['mpseller_id'] ."'")->row;
				if($seller_info) {
					// HTML Mail
					$data['text_received'] = $this->language->get('text_received');
					$data['text_order_id'] = $this->language->get('text_order_id');
					$data['text_date_added'] = $this->language->get('text_date_added');
					$data['text_order_status'] = $this->language->get('text_order_status');
					$data['text_product'] = $this->language->get('text_product');
					$data['text_total'] = $this->language->get('text_total');
					$data['text_comment'] = $this->language->get('text_comment');
					
					$data['order_id'] = $order_info['order_id'];
					$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

					$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($order_status_query->num_rows) {
						$data['order_status'] = $order_status_query->row['name'];
					} else {
						$data['order_status'] = '';
					}

					$this->load->model('tool/upload');
					
					$data['products'] = array();

					$order_products = $this->db->query("SELECT mpo.*, op.name, op.model FROM " . DB_PREFIX . "mpseller_order_product mpo LEFT JOIN ". DB_PREFIX ."order_product op ON(op.order_product_id=mpo.order_product_id) WHERE mpo.order_id = '" . (int)$order_id . "' AND mpo.mpseller_id = '". (int)$seller['mpseller_id'] ."'")->rows;

					foreach ($order_products as $order_product) {
						$option_data = array();
						
						$order_options = $this->getOrderOptions($order_info['order_id'], $order_product['order_product_id']);
						
						foreach ($order_options as $order_option) {
							if ($order_option['type'] != 'file') {
								$value = $order_option['value'];
							} else {
								$upload_info = $this->model_tool_upload->getUploadByCode($order_option['value']);
			
								if ($upload_info) {
									$value = $upload_info['name'];
								} else {
									$value = '';
								}
							}

							$option_data[] = array(
								'name'  => $order_option['name'],
								'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
							);					
						}
							
						$data['products'][] = array(
							'name'     => $order_product['name'],
							'model'    => $order_product['model'],
							'quantity' => $order_product['quantity'],
							'option'   => $option_data,
							'total'    => html_entity_decode($this->currency->format($order_product['total'] + ($this->config->get('config_tax') ? ($order_product['tax'] * $order_product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8')
						);
					}

					$data['totals'] = array();			

					$order_totals = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpseller_order_total WHERE order_id = '" . (int)$order_id . "' AND mpseller_id = '". (int)$seller['mpseller_id'] ."' ORDER BY sort_order ASC")->rows;
					foreach ($order_totals as $order_total) {
						$data['totals'][] = array(
							'title' => $order_total['title'],
							'value' => html_entity_decode($this->currency->format($order_total['value'], $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8')
						);
					}

					$data['comment'] = strip_tags($order_info['comment']);
                    $data['store_owner'] = $seller_info['store_owner'];
					if(VERSION >= '3.0.0.0') {
						$mail = new Mail($this->config->get('config_mail_engine'));
						$mail->parameter = $this->config->get('config_mail_parameter');
						$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
						$mail->smtp_username = $this->config->get('config_mail_smtp_username');
						$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
						$mail->smtp_port = $this->config->get('config_mail_smtp_port');
						$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
					} else if(VERSION <= '2.0.1.1') {
				     	$mail = new Mail($this->config->get('config_mail'));
				    } else {
						$mail = new Mail();
						$mail->protocol = $this->config->get('config_mail_protocol');
						$mail->parameter = $this->config->get('config_mail_parameter');
						$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
						$mail->smtp_username = $this->config->get('config_mail_smtp_username');
						$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
						$mail->smtp_port = $this->config->get('config_mail_smtp_port');
						$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
					}

					$mail->setTo($seller_info['email']);
					$mail->setFrom($this->config->get('config_email'));
					$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
					$mail->setSubject(html_entity_decode(sprintf($this->language->get('text_subject'), $this->config->get('config_name'), $order_info['order_id']), ENT_QUOTES, 'UTF-8'));
					$mailText = $this->load->view('mpmultivendor_mail/order_alert', $data);
                    $mail->setHtml($mailText);
                    $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
                    $mail->send();
				}
			}
		}
	}
	/* Email Work Ends */

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}
}