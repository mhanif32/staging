<?php
class ModelAccountMpmultivendorSeller extends Model {
	public function addSellerStoreInfo($data, $dataFile = NULL) {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."mpseller WHERE customer_id = '". (int)$this->customer->isLogged() ."'");
		if($query->row) {
			$sql = "UPDATE ". DB_PREFIX ."mpseller SET description = '". $this->db->escape($data['description']) ."', meta_description = '". $this->db->escape($data['meta_description']) ."', meta_keyword = '". $this->db->escape($data['meta_keyword']) ."', store_owner = '". $this->db->escape($data['store_owner']) ."', store_name = '". $this->db->escape($data['store_name']) ."', address = '". $this->db->escape($data['address']) ."', email = '". $this->db->escape($data['email']) ."', telephone = '". $this->db->escape($data['telephone']) ."', fax = '". $this->db->escape($data['fax']) ."', city = '". $this->db->escape($data['city']) ."', zone_id = '". $this->db->escape($data['zone_id']) ."', country_id = '". (int)$data['country_id'] ."', logo = '". $this->db->escape($data['logo']) ."', banner = '". $this->db->escape($data['banner']) ."', image = '". $this->db->escape($data['image']) ."', facebook_url = '". $this->db->escape($data['facebook_url']) ."', google_plus_url = '". $this->db->escape($data['google_plus_url']) ."', twitter_url = '". $this->db->escape($data['twitter_url']) ."', pinterest_url = '". $this->db->escape($data['pinterest_url']) ."', linkedin_url = '". $this->db->escape($data['linkedin_url']) ."', youtube_url = '". $this->db->escape($data['youtube_url']) ."', instagram_url = '". $this->db->escape($data['instagram_url']) ."', flickr_url = '". $this->db->escape($data['flickr_url']) ."', store_id = '" . (int)$this->config->get('config_store_id') . "',";

			if(!empty($dataFile['id_proof'])) {
			    $sql.=" id_proof = '".$this->db->escape($dataFile['id_proof'])."', ";
            }
            if(!empty($dataFile['address_proof'])) {
                $sql.=" address_proof = '".$this->db->escape($dataFile['address_proof'])."', ";
            }

			$sql.=" language_id = '" . (int)$this->config->get('config_language_id') . "' WHERE mpseller_id = '". (int)$query->row['mpseller_id'] ."' AND customer_id = '". (int)$this->customer->isLogged() ."' AND status = '1'";
			
			$this->db->query($sql);

			// SEO URL
			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mpseller_id=" . (int)$query->row['mpseller_id'] . "'");
			
			if (isset($data['seller_seo_url'])) {
				foreach ($data['seller_seo_url']as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mpseller_id=" . (int)$query->row['mpseller_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}

			// SEO URL
			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'review_mpseller_id=" . (int)$query->row['mpseller_id'] . "'");
			
			if (isset($data['review_seo_url'])) {
				foreach ($data['review_seo_url']as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'review_mpseller_id=" . (int)$query->row['mpseller_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}
		} else {
			if($this->config->get('mpmultivendor_autoapproved_seller')) {
				$approved = 1;
			} else {				
				$approved = 0;
			}

			$sql = "INSERT INTO ". DB_PREFIX ."mpseller SET customer_id = '". (int)$this->customer->isLogged() ."', description = '". $this->db->escape($data['description']) ."', meta_description = '". $this->db->escape($data['meta_description']) ."', meta_keyword = '". $this->db->escape($data['meta_keyword']) ."', store_owner = '". $this->db->escape($data['store_owner']) ."', store_name = '". $this->db->escape($data['store_name']) ."', address = '". $this->db->escape($data['address']) ."', email = '". $this->db->escape($data['email']) ."', telephone = '". $this->db->escape($data['telephone']) ."', fax = '". $this->db->escape($data['fax']) ."', city = '". $this->db->escape($data['city']) ."', zone_id = '". $this->db->escape($data['zone_id']) ."', country_id = '". (int)$data['country_id'] ."', logo = '". $this->db->escape($data['logo']) ."', banner = '". $this->db->escape($data['banner']) ."', image = '". $this->db->escape($data['image']) ."', facebook_url = '". $this->db->escape($data['facebook_url']) ."', google_plus_url = '". $this->db->escape($data['google_plus_url']) ."', twitter_url = '". $this->db->escape($data['twitter_url']) ."', pinterest_url = '". $this->db->escape($data['pinterest_url']) ."', linkedin_url = '". $this->db->escape($data['linkedin_url']) ."', youtube_url = '". $this->db->escape($data['youtube_url']) ."', instagram_url = '". $this->db->escape($data['instagram_url']) ."', flickr_url = '". $this->db->escape($data['flickr_url']) ."', store_id = '" . (int)$this->config->get('config_store_id') . "', language_id = '" . (int)$this->config->get('config_language_id') . "', date_added = NOW(), commission_rate = '". (int)$this->config->get('mpmultivendor_commission_rate') ."', status = '1', approved = '". (int)$approved ."'";
			
			$this->db->query($sql);

			$mpseller_id = $this->db->getLastId();
			
			// SEO URL			
			if (isset($data['seller_seo_url'])) {
				foreach ($data['seller_seo_url']as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mpseller_id=" . (int)$mpseller_id . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}

			if (isset($data['review_seo_url'])) {
				foreach ($data['review_seo_url']as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'review_mpseller_id=" . (int)$mpseller_id . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}

			/* Email Work Starts */
			// Send email to admin when seller account is created
			$this->load->language('mpmultivendor_mail/register_alert');

			$data['text_signup'] = $this->language->get('text_signup');
			$data['text_store_owner'] = $this->language->get('text_store_owner');
			$data['text_store_name'] = $this->language->get('text_store_name');
			$data['text_email'] = $this->language->get('text_email');
			$data['text_telephone'] = $this->language->get('text_telephone');
			$data['text_address'] = $this->language->get('text_address');
			
			$data['store_name'] = $data['store_name'];
			$data['store_owner'] = $data['store_owner'];
			$data['email'] = $data['email'];
			$data['telephone'] = $data['telephone'];
			$data['address'] = $data['address'];

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
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($this->language->get('text_new_seller'), ENT_QUOTES, 'UTF-8'));
			$mail->setText($this->load->view('mpmultivendor_mail/register_alert', $data));
			$mail->send();
			/* Email Work Ends */
		}
	}

	public function addSellerStoreSetting($data, $mpseller_id) {
		$sql = "UPDATE ". DB_PREFIX ."mpseller SET shipping_type = '". $this->db->escape($data['shipping_type']) ."', shipping_amount = '". (float)$data['shipping_amount'] ."', payment_type = '". $this->db->escape($data['payment_type']) ."', paypal_email = '". $this->db->escape($data['paypal_email']) ."', bank_details = '". $this->db->escape($data['bank_details']) ."', cheque_payee_name = '". $this->db->escape($data['cheque_payee_name']) ."' WHERE mpseller_id = '". (int)$mpseller_id ."' AND customer_id = '". (int)$this->customer->isLogged() ."'";
			
		$this->db->query($sql);
	}

	public function getSellerStoreInfo($customer_id) {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."mpseller WHERE customer_id = '". (int)$customer_id ."' AND status = '1' AND approved = '1'");

		return $query->row;
	}

	public function getSellerInfo($customer_id) {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."mpseller WHERE customer_id = '". (int)$customer_id ."'");

		return $query->row;
	}

	public function getSellerSeoUrls($mpseller_id) {
		$seller_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'mpseller_id=" . (int)$mpseller_id . "'");

		foreach ($query->rows as $result) {
			$seller_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $seller_seo_url_data;
	}

	public function getReviewSeoUrls($mpseller_id) {
		$review_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'review_mpseller_id=" . (int)$mpseller_id . "'");

		foreach ($query->rows as $result) {
			$review_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $review_seo_url_data;
	}

	public function getSeoUrlsByKeyword($keyword) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE keyword = '" . $this->db->escape($keyword) . "'");

		return $query->rows;
	}

	public function getactiveTheme(){
		/* Theme Work Starts */
		if($this->config->get('config_theme')) {			
     		$custom_themename = $this->config->get('config_theme');
    	} else if($this->config->get('theme_default_directory')) {    		
			$custom_themename = $this->config->get('theme_default_directory');
		} else if($this->config->get('config_template')) {			
			$custom_themename = $this->config->get('config_template');
		} else{
			$custom_themename = 'default';
		}

		if(strpos($this->config->get('config_template'), 'journal2') === 0) {
			$custom_themename = 'journal2';
		}
		if (defined('JOURNAL3_ACTIVE')) {
			$custom_themename = 'journal3';
		}
		
		if(empty($custom_themename)) {
			$custom_themename = 'default';
		}

		return $custom_themename;
		/* Theme Work Ends */
	}
}