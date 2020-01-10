<?php
class ModelMpmultivendorMpsellerMessage extends Model {
	public function SendMessage($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mpseller_message SET mpseller_id = '" . (int)$data['mpseller_id'] . "', `from` = '" . $this->db->escape($data['from']) . "', message = '" . $this->db->escape($data['message']) . "', date_added = NOW()");

		// Admin Add a Message, Send Email To Seller
		$this->load->model('mpmultivendor/mpseller');

		$this->load->model('localisation/language');
		$mpseller_info = $this->model_mpmultivendor_mpseller->getMpseller($data['mpseller_id']);
		if($mpseller_info) {
			$language_info = $this->model_localisation_language->getLanguage($mpseller_info['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}

			$this->load->model('setting/store');
			$store_info = $this->model_setting_store->getStore($mpseller_info['store_id']);

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

			$mail->setTo($mpseller_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($store_name);
			$mail->setSubject($subject);
			$mail->setHtml(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}

	public function deleteSellerMessage($mpseller_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpseller_message WHERE mpseller_id = '" . (int)$mpseller_id . "'");
	}

	public function getTotalSellerMessageChats($data = array()) {
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpseller_message WHERE mpseller_id = '". (int)$data['mpseller_id'] ."'";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getSellerMessageChats($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "mpseller_message WHERE mpseller_id = '". (int)$data['mpseller_id'] ."' ORDER BY mpseller_message_id ASC";

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

	public function MarkAsReadMessage($mpseller_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpseller_message SET  read_status = '1' WHERE mpseller_id = '" . (int)$mpseller_id . "' AND `from` = 'seller'");
	}

	public function getTotalUnreadMessages($mpseller_id) {
		$query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpseller_message WHERE mpseller_id = '" . (int)$mpseller_id . "' AND `from` = 'seller' AND read_status = '0'");

		return $query->row['total'];
	}

	public function getTotalAllUnreadMessages() {
		$query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpseller_message WHERE `from` = 'seller' AND read_status = '0'");

		return $query->row['total'];
	}

	public function getSellerMessages($data = array()) {
		$sql = "SELECT mp.mpseller_id, mp.store_owner, mp.store_name, (SELECT message FROM " . DB_PREFIX . "mpseller_message mpss WHERE mpss.mpseller_id = mps.mpseller_id ORDER BY mpss.date_added DESC LIMIT 0, 1) AS message, (SELECT date_added FROM " . DB_PREFIX . "mpseller_message mpss WHERE mpss.mpseller_id = mps.mpseller_id ORDER BY mpss.date_added DESC LIMIT 0, 1) AS date_added FROM " . DB_PREFIX . "mpseller mp LEFT JOIN ". DB_PREFIX  ."mpseller_message mps ON (mp.mpseller_id = mps.mpseller_id) WHERE mp.mpseller_id = mps.mpseller_id";

		if (!empty($data['filter_store_owner'])) {
			$sql .= " AND mp.store_owner LIKE '%" . $this->db->escape($data['filter_store_owner']) . "%'";
		}

		if (!empty($data['filter_store_name'])) {
			$sql .= " AND mp.store_name LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}

		$sql .= " GROUP BY mps.mpseller_id";

		$sort_data = array(
			'mp.store_owner',
			'mp.store_name',
			'message',
			'date_added',
		);


		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY mps.mpseller_message_id";
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

		$query = $this->db->query($sql);

		return $query->rows;

	}

	public function getTotalSellerMessages($data = array()) {
		$sql = "SELECT mp.mpseller_id, mp.store_owner, mp.store_name, (SELECT message FROM " . DB_PREFIX . "mpseller_message mpss WHERE mpss.mpseller_id = mps.mpseller_id ORDER BY mpss.date_added DESC LIMIT 0, 1) AS message, (SELECT date_added FROM " . DB_PREFIX . "mpseller_message mpss WHERE mpss.mpseller_id = mps.mpseller_id ORDER BY mpss.date_added DESC LIMIT 0, 1) AS date_added FROM " . DB_PREFIX . "mpseller mp LEFT JOIN ". DB_PREFIX  ."mpseller_message mps ON (mp.mpseller_id = mps.mpseller_id) WHERE mp.mpseller_id = mps.mpseller_id";

		if (!empty($data['filter_store_owner'])) {
			$sql .= " AND mp.store_owner LIKE '%" . $this->db->escape($data['filter_store_owner']) . "%'";
		}

		if (!empty($data['filter_store_name'])) {
			$sql .= " AND mp.store_name LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}

		$sql .= " GROUP BY mps.mpseller_id";

		$query = $this->db->query($sql);

		return $query->num_rows;
	}
}