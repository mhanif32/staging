<?php
class ModelAccountMpmultivendorEnquiries extends Model {
	public function getEnquiries($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "mpseller_enquiry WHERE mpseller_enquiry_id > 0";

		if (!empty($data['filter_mpseller_id'])) {
			$sql .= " AND mpseller_id = '" . (int)$data['filter_mpseller_id'] . "'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$sort_data = array(
			'name',
			'email',
			'date_added',
			'date_modified',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date_modified";
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

	public function getTotalEnquiries($data = array()) {
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpseller_enquiry WHERE mpseller_enquiry_id > 0";

		if (!empty($data['filter_mpseller_id'])) {
			$sql .= " AND mpseller_id = '" . (int)$data['filter_mpseller_id'] . "'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getEnquiry($mpseller_id, $enquiry_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "mpseller_enquiry WHERE mpseller_enquiry_id > 0";

		$sql .= " AND mpseller_id = '" . (int)$mpseller_id . "'";

		$sql .= " AND mpseller_enquiry_id = '" . (int)$enquiry_id . "'";


		$query = $this->db->query($sql);

		return $query->row;
	}

	public function getEnquiryChats($mpseller_id, $enquiry_id, $start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

		$sql = "SELECT c.*, e.name FROM " . DB_PREFIX . "mpseller_enquiry_chat c LEFT JOIN " . DB_PREFIX . "mpseller_enquiry e ON(c.mpseller_enquiry_id = e.mpseller_enquiry_id) WHERE e.mpseller_id = '" . (int)$mpseller_id . "' AND e.mpseller_enquiry_id = '" . (int)$enquiry_id . "' ORDER BY c.mpseller_enquiry_chat_id ASC LIMIT " . (int)$start . "," . (int)$limit;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalEnquiryChats($mpseller_id, $enquiry_id) {
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpseller_enquiry_chat c LEFT JOIN " . DB_PREFIX . "mpseller_enquiry e ON(c.mpseller_enquiry_id = e.mpseller_enquiry_id) WHERE e.mpseller_id = '" . (int)$mpseller_id . "' AND e.mpseller_enquiry_id = '" . (int)$enquiry_id . "'";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function addEnquiryChat($data) {
		// Show Pending Conversations Starts
		$pending_conversations = $this->getRecentReplies($data['enquiry_id']);
		// Show Pending Conversations Ends

		$this->db->query("INSERT INTO " . DB_PREFIX . "mpseller_enquiry_chat SET mpseller_enquiry_id = '". (int)$data['enquiry_id'] ."', mpseller_id = '". (int)$data['mpseller_id'] ."', customer_id = '". (int)$data['customer_id'] ."', `from` = '". $this->db->escape($data['from']) ."', message = '". $this->db->escape($data['message']) ."', date_added = NOW()");

		$this->db->query("UPDATE " . DB_PREFIX . "mpseller_enquiry SET date_modified = NOW() WHERE mpseller_enquiry_id = '". (int)$data['enquiry_id'] ."'");

		$this->load->language('mpmultivendor_mail/chat_reply_customer_mail');

		$this->load->model('account/mpmultivendor/seller');

		$this->load->model('tool/image');

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());
		if($seller_info) {
			if($this->config->get('mpmultivendor_seller_name')) {
				$seller_or_store = $seller_info['store_owner'];
			} else {
				$seller_or_store = $seller_info['store_name'];
			}

			// Seller add Reply, Send Email To Customer

			$subject = sprintf($this->language->get('text_mail_subject'), $seller_or_store, $data['enquiry_id']);
			$sender = sprintf($this->language->get('text_mail_sender'), $this->config->get('config_name'));

			$mail_data = array();

			$mail_data['title'] = $subject;

			if($seller_info['image']) {
				$mail_data['customer_image'] = $this->model_tool_image->resize($seller_info['image'], 40, 40);
			} else {
				$mail_data['customer_image'] = $this->model_tool_image->resize('nouserpic.png', 40, 40);
			}



			$mail_data['text_mail_name'] = $seller_or_store;

			$mail_data['date_added'] = date('M d, h:i');

			$mail_data['message'] = $data['message'];

			$link = $this->url->link('account/enquiries/view', 'enquiry_id='. $data['enquiry_id'], true);

			$mail_data['text_mail_view'] = sprintf($this->language->get('text_mail_view'), $link);

			$mail_data['text_mail_dontreply'] = sprintf($this->language->get('text_mail_dontreply'), $this->url->link('information/contact', '', true));


			// Show Pending Conversations Starts
			$mail_data['pending_conversations'] = array();
			foreach($pending_conversations as $pending_conversation) {
				if($pending_conversation['from'] == 'seller') {
					$from_name = $seller_or_store;

					if($seller_info['image']) {
						$image = $this->model_tool_image->resize($seller_info['image'], 40, 40);
					} else {
						$image = $this->model_tool_image->resize('nouserpic.png', 40, 40);
					}

				} else if($pending_conversation['from'] == 'customer') {
					$from_name = $data['customer_name'];

					$image = $this->model_tool_image->resize('nouserpic.png', 40, 40);
				}

				$mail_data['pending_conversations'][] = array(
					'name'					=> $from_name,
					'image'					=> $image,
					'date_added'			=> date('M d, h:i', strtotime($pending_conversation['date_added'])),
					'message'				=> $pending_conversation['message'],
				);
			}
			// Show Pending Conversations Ends

			$message = $this->load->view('mpmultivendor_mail/chat_reply_customer_mail', $mail_data);

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

			$mail->setTo($data['customer_email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($message);
			$mail->send();
		}
	}

	public function getRecentReplies($mpseller_enquiry_id) {
		$recentreplies = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpseller_enquiry_chat WHERE mpseller_enquiry_id = '" . (int)$mpseller_enquiry_id . "' ORDER BY mpseller_enquiry_chat_id DESC")->rows;

		return $recentreplies;
	}
}