<?php
class ModelAccountMpmultivendorOrders extends Model {
	public function getOrders($data = array()) {
		$sql = "SELECT mpo.order_id, o.firstname, o.lastname, os.name as status, osadmin.name as by_admin_status, o.date_added, o.currency_code, o.currency_value, (SELECT `value` FROM ". DB_PREFIX ."mpseller_order_total mpot WHERE mpot.order_id = mpo.order_id AND mpot.mpseller_id = '" . (int)$data['mpseller_id'] . "' AND `code` = 'sub_total') AS total FROM `" . DB_PREFIX . "order` o LEFT JOIN `". DB_PREFIX ."mpseller_order_product` mpo ON (o.order_id=mpo.order_id) LEFT JOIN " . DB_PREFIX . "order_status os ON (mpo.order_status_id = os.order_status_id) LEFT JOIN " . DB_PREFIX . "order_status osadmin ON (o.order_status_id = osadmin.order_status_id) WHERE mpo.mpseller_id = '" . (int)$data['mpseller_id'] . "' AND o.order_status_id > '0' AND mpo.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND mpo.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		}

		if (!empty($data['filter_admin_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_admin_order_status_id'] . "'";
		}

		$sql .= " GROUP BY mpo.order_id ORDER BY o.order_id DESC ";

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

    public function getPendingOrders($data = array()) {
        $sql = "SELECT mpo.order_id, o.firstname, o.lastname, os.name as status, osadmin.name as by_admin_status, o.date_added, o.currency_code, o.currency_value, (SELECT `value` FROM ". DB_PREFIX ."mpseller_order_total mpot WHERE mpot.order_id = mpo.order_id AND mpot.mpseller_id = '" . (int)$data['mpseller_id'] . "' AND `code` = 'sub_total') AS total FROM `" . DB_PREFIX . "order` o LEFT JOIN `". DB_PREFIX ."mpseller_order_product` mpo ON (o.order_id=mpo.order_id) LEFT JOIN " . DB_PREFIX . "order_status os ON (mpo.order_status_id = os.order_status_id) LEFT JOIN " . DB_PREFIX . "order_status osadmin ON (o.order_status_id = osadmin.order_status_id) WHERE mpo.mpseller_id = '" . (int)$data['mpseller_id'] . "' AND o.order_status_id > '0' AND mpo.order_status_id != '5' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if (!empty($data['filter_order_status_id'])) {
            $sql .= " AND mpo.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
        }

        if (!empty($data['filter_admin_order_status_id'])) {
            $sql .= " AND o.order_status_id = '" . (int)$data['filter_admin_order_status_id'] . "'";
        }

        $sql .= " GROUP BY mpo.order_id ORDER BY o.order_id DESC ";

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

	public function getTotalOrders($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "order` o LEFT JOIN `". DB_PREFIX ."mpseller_order_product` mpo ON (o.order_id=mpo.order_id) WHERE mpo.mpseller_id = '" . (int)$data['mpseller_id'] . "' AND o.order_status_id > '0' AND mpo.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND mpo.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		}

		if (!empty($data['filter_admin_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_admin_order_status_id'] . "'";
		}
		$sql .= " GROUP BY mpo.order_id";


		$query = $this->db->query($sql); 

		return $query->num_rows;
	}

    public function getTotalPendingOrders($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "order` o LEFT JOIN `". DB_PREFIX ."mpseller_order_product` mpo ON (o.order_id=mpo.order_id) WHERE mpo.mpseller_id = '" . (int)$data['mpseller_id'] . "' AND mpo.order_status_id != '5' AND mpo.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if (!empty($data['filter_order_status_id'])) {
            $sql .= " AND mpo.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
        }

        if (!empty($data['filter_admin_order_status_id'])) {
            $sql .= " AND o.order_status_id = '" . (int)$data['filter_admin_order_status_id'] . "'";
        }
        $sql .= " GROUP BY mpo.order_id";


        $query = $this->db->query($sql);

        return $query->num_rows;
    }

	public function getOrder($order_id, $mpseller_id) {
		$order_query = $this->db->query("SELECT o.*, (SELECT `value` FROM ". DB_PREFIX ."mpseller_order_total mpot WHERE mpot.order_id = mpo.order_id AND mpot.mpseller_id = '" . (int)$mpseller_id . "' AND `code` = 'sub_total') AS total, mpo.order_status_id AS mpseller_order_status_id FROM `" . DB_PREFIX . "order` o LEFT JOIN `". DB_PREFIX ."mpseller_order_product` mpo ON (o.order_id=mpo.order_id) WHERE o.order_id = '" . (int)$order_id . "' AND mpo.mpseller_id = '" . (int)$mpseller_id . "' AND o.order_status_id > '0' AND mpo.order_status_id > '0'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			$this->load->model('localisation/language');
			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}

			return array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'email'                   => $order_query->row['email'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_method'         => $order_query->row['shipping_method'],
				'shipping_code'           => $order_query->row['shipping_code'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'mpseller_order_status_id'=> $order_query->row['mpseller_order_status_id'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'ip'                      => $order_query->row['ip']
			);
		} else {
			return false;
		}
	}

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}

	public function getOrderProducts($order_id, $mpseller_id) {
		$query = $this->db->query("SELECT mpo.*, op.name, op.model FROM " . DB_PREFIX . "mpseller_order_product mpo LEFT JOIN ". DB_PREFIX ."order_product op ON (mpo.order_product_id=op.order_product_id) WHERE mpo.order_id = '" . (int)$order_id . "' AND mpseller_id = '". (int)$mpseller_id ."'");

		return $query->rows;
	}

	public function getOrderTotals($order_id, $mpseller_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpseller_order_total WHERE order_id = '" . (int)$order_id . "' AND mpseller_id = '". (int)$mpseller_id ."' ORDER BY sort_order");

		return $query->rows;
	}

	public function getOrderHistories($order_id, $mpseller_id) {
		$query = $this->db->query("SELECT date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "mpseller_order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' AND mpseller_id = '". (int)$mpseller_id ."' ORDER BY oh.date_added");

		return $query->rows;
	}

	public function getOrderStatuses($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sql .= " ORDER BY name";

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

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function addOrderHistory($order_id, $order_status_id, $comment = '', $notify = false, $override = false) {
		$this->load->model('account/mpmultivendor/seller');

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

		if(!empty($seller_info)) {
			$mpseller_id = $seller_info['mpseller_id'];
		} else {
			$mpseller_id = 0;
		}

		$order_info = $this->getOrder($order_id, $mpseller_id);
		if ($order_info) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpseller_order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW(), mpseller_id = '". (int)$mpseller_id ."'");

			$this->db->query("UPDATE `" . DB_PREFIX . "mpseller_order_product` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE mpseller_id = '". (int)$mpseller_id ."' AND order_id = '" . (int)$order_id . "'");

			/* Email Work Starts */
			// Send email to customer when seller update their order
			if($notify) {
				$language = new Language($order_info['language_code']);
				$language->load($order_info['language_code']);
				$language->load('mpmultivendor_mail/seller_order_edit');

				$data['text_order_id'] = $language->get('text_order_id');
				$data['text_date_added'] = $language->get('text_date_added');
				$data['text_link'] = $language->get('text_link');
				$data['text_history'] = $language->get('text_history');
				$data['text_comment'] = $language->get('text_comment');
				$data['text_thanks'] = $language->get('text_thanks');

				$data['text_order_status'] = sprintf($language->get('text_order_status'), $seller_info['store_owner'], $order_id);

				$data['order_id'] = $order_info['order_id'];
				$data['store_name'] = $order_info['store_name'];
				$data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));

				$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
		
				if ($order_status_query->num_rows) {
					$data['order_status'] = $order_status_query->row['name'];
				} else {
					$data['order_status'] = '';
				}

				if ($order_info['customer_id']) {
					$data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_info['order_id'];
				} else {
					$data['link'] = '';
				}

				$data['comment'] = strip_tags($comment);

                $data['order_id'] = $order_info['order_id'];
                $data['firstname'] = $order_info['firstname'];
                $data['lastname'] = $order_info['lastname'];
                $data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));

				$this->load->model('setting/setting');

				$from = $this->getSettingValue('config_email', $order_info['store_id']);
				
				if (!$from) {
					$from = $this->config->get('config_email');
				}

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

				$mail->setTo($order_info['email']);
				$mail->setFrom($from);
				$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(sprintf($language->get('text_subject'), $order_info['store_name'], $order_id));
				$mail->setText($this->load->view('mpmultivendor_mail/seller_order_edit', $data));
				$mail->send();
			}

			// Admin Alert
			$language = new Language($order_info['language_code']);
			$language->load($order_info['language_code']);
			$language->load('mpmultivendor_mail/seller_order_edit_alert');

			$data['text_order_id'] = $language->get('text_order_id');
			$data['text_date_added'] = $language->get('text_date_added');
			$data['text_history'] = $language->get('text_history');
			$data['text_comment'] = $language->get('text_comment');

			$data['text_order_status'] = sprintf($language->get('text_order_status'), $seller_info['store_owner'], $order_id);

			$data['order_id'] = $order_info['order_id'];
			$data['store_name'] = $order_info['store_name'];
			$data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));

			$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
	
			if ($order_status_query->num_rows) {
				$data['order_status'] = $order_status_query->row['name'];
			} else {
				$data['order_status'] = '';
			}

			$data['comment'] = strip_tags($comment);
			$this->load->model('setting/setting');

			$from = $this->getSettingValue('config_email', $order_info['store_id']);
			
			if (!$from) {
				$from = $this->config->get('config_email');
			}

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

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(sprintf($language->get('text_subject'), $order_info['store_name'], $order_id));
			$mail->setText($this->load->view('mpmultivendor_mail/seller_order_edit_alert', $data));
			$mail->send();
			/* Email Work Ends */
		}
	}

	public function getCustomerOrderHistories($order_id) {
		$query = $this->db->query("SELECT oh.order_id, oh.date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added");

		$histories = array();
		foreach($query->rows as $value) {
			$histories[] = array(
				'postby'			=> $this->language->get('text_order_admin'),
				'order_id'			=> $value['order_id'],
				'mpseller_id'		=> '',
				'type'				=> 'admin',
				'date_added'		=> $value['date_added'],
				'status'			=> $value['status'],
				'comment'			=> $value['comment'],
				'notify'			=> $value['notify'],
			);
		}

		$query = $this->db->query("SELECT moh.order_id, moh.date_added, mos.name AS status, moh.comment, moh.notify, moh.mpseller_id FROM " . DB_PREFIX . "mpseller_order_history moh LEFT JOIN " . DB_PREFIX . "order_status mos ON moh.order_status_id = mos.order_status_id WHERE moh.order_id = '" . (int)$order_id . "' AND mos.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY moh.date_added");

		foreach($query->rows as $value) {
			$seller_info = $this->db->query("SELECT store_owner, store_name FROM " . DB_PREFIX . "mpseller WHERE mpseller_id = '" . (int)$value['mpseller_id'] . "'")->row;
			if($seller_info) {
				$store_name = $seller_info['store_name'];
			} else {
				$store_name = '';
			}

			$histories[] = array(
				'postby'			=> $store_name,
				'order_id'			=> $value['order_id'],
				'mpseller_id'		=> $value['mpseller_id'],
				'type'				=> 'seller',
				'date_added'		=> $value['date_added'],
				'status'			=> $value['status'],
				'comment'			=> $value['comment'],
				'notify'			=> $value['notify'],
			);
		}

		$histories_data = array();
		foreach ($histories as $key => $value) {
			$histories_data[$key] = $value['date_added'];
		}

		array_multisort($histories_data, SORT_ASC, $histories);

		return $histories;
	}

	public function getMpsellerOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpseller_order_product WHERE order_id = '" . (int)$order_id . "' AND mpseller_id > 0 GROUP BY mpseller_id ORDER BY order_product_id ASC");

		return $query->rows;
	}

	public function getAllMpsellerOrderProducts($order_id, $mpseller_id) {
		$query = $this->db->query("SELECT mpo.*, op.name, op.model FROM " . DB_PREFIX . "mpseller_order_product mpo LEFT JOIN ". DB_PREFIX ."order_product op ON(op.order_product_id=mpo.order_product_id) WHERE mpo.order_id = '" . (int)$order_id . "' AND mpo.mpseller_id = '". (int)$mpseller_id ."'");

		return $query->rows;
	}

	public function getOrderStatus($order_status_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getAdminOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "' ORDER BY order_product_id ASC");

		return $query->rows;
	}

	public function getMpsellerOrderProduct($order_id, $order_product_id) {
		$query = $this->db->query("SELECT mpo.order_id, mpo.order_product_id, mpo.order_status_id, mp.store_owner, mp.store_name, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = mpo.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status FROM " . DB_PREFIX . "mpseller_order_product mpo LEFT JOIN ". DB_PREFIX ."mpseller mp ON(mpo.mpseller_id=mp.mpseller_id) WHERE mpo.order_id = '" . (int)$order_id . "' AND mpo.order_product_id = '". (int)$order_product_id ."'");

		return $query->row;
	}

	public function getMpseller($mpseller_id) {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."mpseller WHERE mpseller_id = '". (int)$mpseller_id ."'");

		return $query->row;
	}

	public function getProduct($product_id, $language_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$language_id . "'");

		return $query->row;
	}

	public function getProductOptionValue($product_id, $product_option_value_id, $language_id) {
		$query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND ovd.language_id = '" . (int)$language_id . "'");

		return $query->row;
	}

	public function getSettingValue($key, $store_id = 0) {
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");

		if ($query->num_rows) {
			return $query->row['value'];
		} else {
			return null;	
		}
	}
}