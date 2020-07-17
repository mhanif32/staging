<?php
class ModelDeliverypartnerMessage extends Model {
	public function SendMessage($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "delivery_partner_message SET delivery_partner_id = '" . (int)$data['delivery_partner_id'] . "', `from` = '" . $this->db->escape($data['from']) . "', message = '" . $this->db->escape($data['message']) . "', date_added = NOW()");

		// Admin Add a Message, Send Email To Seller
		/*$this->load->model('customer/customer');

		$this->load->model('localisation/language');
		$deliverypartner_info = $this->model_customer_customer->getCustomer($data['delivery_partner_id']);
		if($deliverypartner_info) {
			$language_info = $this->model_localisation_language->getLanguage($deliverypartner_info['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}

			$this->load->model('setting/store');
			$store_info = $this->model_setting_store->getStore($deliverypartner_info['store_id']);

			if ($store_info) {
				$store_name = html_entity_decode($store_info['name'], ENT_QUOTES, 'UTF-8');
			} else {
				$store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
			}


			$language = new Language($language_code);
			$language->load($language_code);
			$language->load('mpmultivendor_mail/send_message_to_seller');

			$subject = sprintf($language->get('text_subject'), $store_name);

			$mail_data = array();

			$mail_data['text_welcome'] = sprintf($language->get('text_welcome'), $store_name);

			$mail_data['title'] = $subject;

			$mail_data['text_thanks'] = $language->get('text_thanks');

			$mail_data['text_message'] = $language->get('text_message');

			$store_url = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

			$seller_message_link = $store_url . 'index.php?route=account/mpmultivendor/message';

			$contact_link = $store_url . 'index.php?route=information/contact';

			$mail_data['text_mail_view'] = sprintf($language->get('text_mail_view'), $seller_message_link);

			$mail_data['text_mail_dontreply'] = sprintf($language->get('text_mail_dontreply'), $contact_link);

			$mail_data['message'] = $data['message'];

			$mail_data['store'] = $store_name;

			$message = $this->load->view('mpmultivendor_mail/send_message_to_seller', $mail_data);

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

			$mail->setTo($deliverypartner_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($store_name);
			$mail->setSubject($subject);
			$mail->setHtml(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}*/
	}

	public function deleteSellerMessage($delivery_partner_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "delivery_partner_message WHERE delivery_partner_id = '" . (int)$delivery_partner_id . "'");
	}

	public function getTotalMessageChats($data = array()) {
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "delivery_partner_message WHERE delivery_partner_id = '". (int)$data['delivery_partner_id'] ."'";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getMessageChats($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "delivery_partner_message WHERE delivery_partner_id = '". (int)$data['delivery_partner_id'] ."' ORDER BY delivery_partner_message_id ASC";

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

	public function MarkAsReadMessage($delivery_partner_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "delivery_partner_message SET  read_status = '1' WHERE delivery_partner_id = '" . (int)$delivery_partner_id . "' AND `from` = 'delivery-partner'");
	}

	public function getTotalUnreadMessages($delivery_partner_id) {
		$query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "delivery_partner_message WHERE delivery_partner_id = '" . (int)$delivery_partner_id . "' AND `from` = 'delivery-partner' AND read_status = '0'");

		return $query->row['total'];
	}

	public function getTotalAllUnreadMessages() {
		$query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "delivery_partner_message WHERE `from` = 'seller' AND read_status = '0'");

		return $query->row['total'];
	}

	public function getSellerMessages($data = array()) {
		$sql = "SELECT c.customer_id, c.firstname, c.lastname, (SELECT message FROM " . DB_PREFIX . "delivery_partner_message dpms WHERE dpms.delivery_partner_id = dpm.delivery_partner_id ORDER BY dpm.date_added DESC LIMIT 0, 1) AS message, (SELECT date_added FROM " . DB_PREFIX . "delivery_partner_message dpm2 WHERE dpm2.delivery_partner_id = dpm.delivery_partner_id ORDER BY dpm2.date_added DESC LIMIT 0, 1) AS date_added FROM ". DB_PREFIX  ."delivery_partner_message dpm LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id = dpm.delivery_partner_id) WHERE c.customer_id = dpm.delivery_partner_id";

		if (!empty($data['filter_firstname'])) {
			$sql .= " AND c.firstname LIKE '%" . $this->db->escape($data['filter_firstname']) . "%'";
		}

		if (!empty($data['filter_lastname'])) {
			$sql .= " AND c.lastname LIKE '%" . $this->db->escape($data['filter_lastname']) . "%'";
		}

		$sql .= " GROUP BY dpm.delivery_partner_id";

		$sort_data = array(
			'c.firstname',
			'c.lastname',
			'message',
			'date_added',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY dpm.delivery_partner_message_id";
		}

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
//echo $sql;exit('aa');
		$query = $this->db->query($sql);

		return $query->rows;

	}

	public function getTotalSellerMessages($data = array()) {

        $sql = "SELECT c.customer_id, c.firstname, c.lastname, (SELECT message FROM " . DB_PREFIX . "delivery_partner_message dpms WHERE dpms.delivery_partner_id = dpm.delivery_partner_id ORDER BY dpm.date_added DESC LIMIT 0, 1) AS message, (SELECT date_added FROM " . DB_PREFIX . "delivery_partner_message dpm2 WHERE dpm2.delivery_partner_id = dpm.delivery_partner_id ORDER BY dpm2.date_added DESC LIMIT 0, 1) AS date_added FROM ". DB_PREFIX  ."delivery_partner_message dpm LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id = dpm.delivery_partner_id) WHERE c.customer_id = dpm.delivery_partner_id";

        if (!empty($data['filter_firstname'])) {
            $sql .= " AND c.firstname LIKE '%" . $this->db->escape($data['filter_firstname']) . "%'";
        }

        if (!empty($data['filter_lastname'])) {
            $sql .= " AND c.lastname LIKE '%" . $this->db->escape($data['filter_lastname']) . "%'";
        }

        $sql .= " GROUP BY dpm.delivery_partner_id";

		$query = $this->db->query($sql);

		return $query->num_rows;
	}
}