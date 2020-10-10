<?php
class ModelMpmultivendorMpseller extends Model {
	public function editMpseller($mpseller_id, $data) {
		$sql = "UPDATE " . DB_PREFIX . "mpseller SET description = '". $this->db->escape($data['description']) ."', meta_description = '". $this->db->escape($data['meta_description']) ."', meta_keyword = '". $this->db->escape($data['meta_keyword']) ."', store_owner = '". $this->db->escape($data['store_owner']) ."', store_name = '". $this->db->escape($data['store_name']) ."', address = '". $this->db->escape($data['address']) ."', email = '". $this->db->escape($data['email']) ."', telephone = '". $this->db->escape($data['telephone']) ."', fax = '". $this->db->escape($data['fax']) ."', city = '". $this->db->escape($data['city']) ."', zone_id = '". $this->db->escape($data['zone_id']) ."', country_id = '". (int)$data['country_id'] ."', logo = '". $this->db->escape($data['logo']) ."', banner = '". $this->db->escape($data['banner']) ."', image = '". $this->db->escape($data['image']) ."', facebook_url = '". $this->db->escape($data['facebook_url']) ."', google_plus_url = '". $this->db->escape($data['google_plus_url']) ."', twitter_url = '". $this->db->escape($data['twitter_url']) ."', pinterest_url = '". $this->db->escape($data['pinterest_url']) ."', linkedin_url = '". $this->db->escape($data['linkedin_url']) ."', youtube_url = '". $this->db->escape($data['youtube_url']) ."', instagram_url = '". $this->db->escape($data['instagram_url']) ."', flickr_url = '". $this->db->escape($data['flickr_url']) ."', shipping_type = '". $this->db->escape($data['shipping_type']) ."', shipping_amount = '". (float)$data['shipping_amount'] ."', payment_type = '". $this->db->escape($data['payment_type']) ."', paypal_email = '". $this->db->escape($data['paypal_email']) ."', bank_details = '". $this->db->escape($data['bank_details']) ."', cheque_payee_name = '". $this->db->escape($data['cheque_payee_name']) ."', commission_rate = '". (int)$data['commission_rate'] ."', status = '" . (int)$data['status'] . "', approved = '" . (int)$data['approved'] . "', approved_for_delivery = '" . (int)$data['approved_for_delivery'] . "' WHERE mpseller_id = '" . (int)$mpseller_id . "'";
		$this->db->query($sql);

		// SEO URL
			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mpseller_id=" . (int)$mpseller_id . "'");
			
			if (isset($data['seller_seo_url'])) {
				foreach ($data['seller_seo_url']as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mpseller_id=" . (int)$mpseller_id . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}

			// SEO URL
			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'review_mpseller_id=" . (int)$mpseller_id . "'");
			
			if (isset($data['review_seo_url'])) {
				foreach ($data['review_seo_url']as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'review_mpseller_id=" . (int)$mpseller_id . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}
	}

	public function deleteMpseller($mpseller_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpseller WHERE mpseller_id = '" . (int)$mpseller_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpseller_enquiry WHERE mpseller_id = '" . (int)$mpseller_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpseller_information_section WHERE mpseller_id = '" . (int)$mpseller_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpseller_review WHERE mpseller_id = '" . (int)$mpseller_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mpseller_id=" . (int)$mpseller_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'review_mpseller_id=" . (int)$mpseller_id . "'");
	}

	public function getMpseller($mpseller_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mpseller WHERE mpseller_id = '" . (int)$mpseller_id . "'");

		return $query->row;
	}

	public function getMpsellerByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mpseller WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getMpsellers($data = array()) {
		$sql = "SELECT *, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product p WHERE p.mpseller_id = mps.mpseller_id) AS total_products FROM " . DB_PREFIX . "mpseller mps WHERE mps.mpseller_id > 0";

		$implode = array();

		if (!empty($data['filter_store_owner'])) {
			$implode[] = "mps.store_owner LIKE '%" . $this->db->escape($data['filter_store_owner']) . "%'";
		}

		if (!empty($data['filter_store_name'])) {
			$implode[] = "mps.store_name LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "mps.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "mps.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "mps.approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(mps.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'mps.store_owner',
			'mps.email',
			'mps.status',
			'mps.approved',
			'mps.date_added',
			'total_products',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY mps.store_owner";
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

    public function getPendingMpsellers($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "customer c WHERE c.customer_id NOT IN (select customer_id from ".DB_PREFIX."mpseller mps where mps.mpseller_id > 0) and c.role='seller'";

        //echo $sql;exit('aaa');

        $implode = array();

//        if (!empty($data['filter_store_owner'])) {
//            $implode[] = "mps.store_owner LIKE '%" . $this->db->escape($data['filter_store_owner']) . "%'";
//        }
//
//        if (!empty($data['filter_store_name'])) {
//            $implode[] = "mps.store_name LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
//        }

//        if (!empty($data['filter_email'])) {
//            $implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
//        }
//
//        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
//            $implode[] = "mps.status = '" . (int)$data['filter_status'] . "'";
//        }
//
//        if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
//            $implode[] = "mps.approved = '" . (int)$data['filter_approved'] . "'";
//        }
//
//        if (!empty($data['filter_date_added'])) {
//            $implode[] = "DATE(mps.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
//        }
//
//        if ($implode) {
//            $sql .= " AND " . implode(" AND ", $implode);
//        }
//
        $sort_data = array(
            //'mps.store_owner',
            'mps.email',
            'mps.status',
            //'mps.approved',
            'mps.date_added',
            //'total_products',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY c.firstname";
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

	public function approve($mpseller_id) {
		$mpseller_info = $this->getMpseller($mpseller_id);

		if ($mpseller_info) {
			$this->db->query("UPDATE " . DB_PREFIX . "mpseller SET approved = '1' WHERE mpseller_id = '" . (int)$mpseller_id . "'");

			/* Email Work Starts */
			$this->load->model('setting/store');	
			$store_info = $this->model_setting_store->getStore($mpseller_info['store_id']);
	
			if ($store_info) {
				$store_name = html_entity_decode($store_info['name'], ENT_QUOTES, 'UTF-8');
				$store_url = $store_info['url'] . 'index.php?route=account/login';
			} else {
				$store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
				$store_url = HTTP_CATALOG . 'index.php?route=account/login';
			}
			
			$this->load->model('localisation/language');			
			$language_info = $this->model_localisation_language->getLanguage($mpseller_info['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}
			
			$language = new Language($language_code);
			$language->load($language_code);
			$language->load('mpmultivendor_mail/seller_approve');
						
			$subject = sprintf($language->get('text_subject'), $store_name);
								
			$data['text_welcome'] = sprintf($language->get('text_welcome'), $store_name);
			
			$data['text_login'] = $language->get('text_login');
			
			$data['text_service'] = $language->get('text_service');
				
			$data['text_thanks'] = $language->get('text_thanks');

			$data['login'] = $store_url;
			$data['store'] = $store_name;
	
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
			//$mail->setText($this->load->view('mpmultivendor_mail/seller_approve', $data));
            $mailText = $this->load->view('mpmultivendor_mail/seller_approve', $data);
            $mail->setHtml($mailText);
            $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
            $mail->send();
			/* Email Work Ends */
		}
	}

	public function getTotalMpsellers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpseller mps WHERE mps.mpseller_id > 0";

		$implode = array();

		if (!empty($data['filter_store_owner'])) {
			$implode[] = "mps.store_owner LIKE '%" . $this->db->escape($data['filter_store_owner']) . "%'";
		}

		if (!empty($data['filter_store_name'])) {
			$implode[] = "mps.store_name LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "mps.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "mps.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "mps.approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(mps.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

    public function getTotalPendingSeller($data = array()) {
//        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "mpseller mps ON c.customer_id = c.customer_id WHERE c.customer_id > 0 AND mps.mpseller_id = 0";

        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer c WHERE c.customer_id NOT IN (select customer_id from ".DB_PREFIX."mpseller mps where mps.mpseller_id > 0) and c.role = 'seller'";

        $implode = array();

        if (!empty($data['filter_email'])) {
            $implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
        }

//        if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
//            $implode[] = "mps.approved = '" . (int)$data['filter_approved'] . "'";
//        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if ($implode) {
            $sql .= " AND " . implode(" AND ", $implode);
        }
        $sql .= " AND c.role = 'seller'";
        $query = $this->db->query($sql);

        return $query->row['total'];
    }

	public function getTransactions($mpseller_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpseller_commission WHERE mpseller_id = '" . (int)$mpseller_id . "' AND type = 'withdraw' AND status = '1' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalTransactions($mpseller_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "mpseller_commission WHERE mpseller_id = '" . (int)$mpseller_id . "' AND type = 'withdraw' AND status = '1'");

		return $query->row['total'];
	}

	public function addTransaction($mpseller_id, $amount = '') {
		$mpseller_info = $this->getMpseller($mpseller_id);

		if ($mpseller_info) {
			if ($amount != '') {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpseller_commission SET mpseller_id = '" . (int)$mpseller_id . "', amount = '" . (float)$amount . "', date_added = NOW(), date_modified = NOW(), type = 'withdraw', status = '1'");

				/* Email Work Starts */
				$this->load->model('setting/store');	
				$store_info = $this->model_setting_store->getStore($mpseller_info['store_id']);
		
				if ($store_info) {
					$store_name = html_entity_decode($store_info['name'], ENT_QUOTES, 'UTF-8');
					$store_url = $store_info['url'] . 'index.php?route=account/mpmultivendor/payment';
				} else {
					$store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
					$store_url = HTTP_CATALOG . 'index.php?route=account/mpmultivendor/payment';
				}
				
				$this->load->model('localisation/language');			
				$language_info = $this->model_localisation_language->getLanguage($mpseller_info['language_id']);

				if ($language_info) {
					$language_code = $language_info['code'];
				} else {
					$language_code = $this->config->get('config_language');
				}
				
				$language = new Language($language_code);
				$language->load($language_code);
				$language->load('mpmultivendor_mail/seller_transaction');
							
				$subject = sprintf($language->get('text_subject'), $store_name, $this->currency->format($amount, $this->config->get('config_currency')));
									
				$data['text_welcome'] = sprintf($language->get('text_welcome'), $store_name, $this->currency->format($amount, $this->config->get('config_currency')));
				
				$data['text_thanks'] = $language->get('text_thanks');
					
				$data['transaction_url'] = $store_url;
				$data['store'] = $store_name;

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
				$mail->setText($this->load->view('mpmultivendor_mail/seller_transaction', $data));
				$mail->send(); 
				/* Email Work Ends */
			}
		}
	}

	public function getTransactionTotal($mpseller_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "mpseller_commission WHERE mpseller_id = '" . (int)$mpseller_id . "' AND type = 'withdraw' AND status = '1'");

		return $query->row['total'];
	}

	public function getCommissionTotal($mpseller_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "mpseller_commission WHERE mpseller_id = '" . (int)$mpseller_id . "' AND type = 'sale' AND status = '1'");

		return $query->row['total'];
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

	public function getCustomerPlan($customer_id)
    {
        $sql = "SELECT sp.name as plan_name, sup.* FROM `" . DB_PREFIX . "subscription_user_plan` sup
        LEFT JOIN `" . DB_PREFIX . "subscription_plan` sp ON sp.plan_id = sup.subscription_plan_id 
        WHERE sup.customer_id = '" . (int)$customer_id . "' ORDER BY sup.subscription_user_plan_id DESC";
        //echo $sql;exit('huhuh');
        $query = $this->db->query($sql);
        return $query->rows;
    }
}