<?php
class ModelAccountMpmultivendorMessage extends Model {
	public function addMessage($data) {
		$this->db->query("INSERT INTO ". DB_PREFIX ."mpseller_message SET mpseller_id = '". (int)$data['mpseller_id'] ."', `from` = '". $this->db->escape($data['from']) ."', message = '". $this->db->escape($data['message']) ."', subject = '". $this->db->escape($data['subject']) ."', date_added = NOW()");

		// Seller Add a Message, Send Email To Admin

		$this->load->language('mpmultivendor_mail/send_message_to_admin');

		$this->load->model('tool/image');

		$this->load->model('mpmultivendor/mv_seller');

		$seller_info = $this->model_mpmultivendor_mv_seller->getSellerStoreInfo($data['mpseller_id']);

		if($seller_info) {
			$subject = sprintf($this->language->get('text_mail_subject'), $seller_info['store_owner']);
			$sender = sprintf($this->language->get('text_mail_sender'), $seller_info['store_owner'], $this->config->get('config_name'));

			$mail_data = array();

			$mail_data['title'] = $subject;

			if($seller_info['image']) {
				$mail_data['seller_image'] = $this->model_tool_image->resize($seller_info['image'], 40, 40);
			} else {
				$mail_data['seller_image'] = $this->model_tool_image->resize('nouserpic.png', 40, 40);
			}

			$mail_data['text_mail_details'] = $this->language->get('text_mail_details');

			$mail_data['text_mail_name'] = $this->language->get('text_mail_name') . $seller_info['store_owner'];
			$mail_data['text_mail_store_name'] = $this->language->get('text_mail_store_name') . $seller_info['store_name'];

			$mail_data['text_mail_email'] = $this->language->get('text_mail_email') . $seller_info['email'];

			$mail_data['seller_name'] = $seller_info['store_owner'];

			$mail_data['date_added'] = date('M d, h:i');

			$mail_data['text_mail_enquiry'] = $this->language->get('text_mail_enquiry');

			$mail_data['message'] = $data['message'];

			if(VERSION < '2.2.0.0') {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mpmultivendor_mail/send_message_to_admin.tpl')) {
			    	$message = $this->load->view($this->config->get('config_template') . '/template/mpmultivendor_mail/send_message_to_admin.tpl', $data);
			   } else {
			   		$message = $this->load->view('default/template/mpmultivendor_mail/send_message_to_admin.tpl', $data);
			   }
		  	} else {
			   $message = $this->load->view('mpmultivendor_mail/send_message_to_admin', $mail_data);
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
			$mail->setReplyTo($seller_info['email']);
			$mail->setSender(html_entity_decode($sender, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setHtml(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}

	public function getTotalUnreadMessages($mpseller_id) {
		$query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpseller_message WHERE mpseller_id = '" . (int)$mpseller_id . "' AND `from` = 'admin' AND read_status = '0'");

		return $query->row['total'];
	}

	public function MarkAsReadMessage($mpseller_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpseller_message SET  read_status = '1' WHERE mpseller_id = '" . (int)$mpseller_id . "' AND `from` = 'admin'");
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
}