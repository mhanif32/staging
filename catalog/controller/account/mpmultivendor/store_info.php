<?php
class ControllerAccountMpmultivendorStoreInfo extends Controller {
	private $error = array();
	
	public function index() {

        error_reporting(E_ALL);
        ini_set("display_errors", 1);

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if(!$this->config->get('mpmultivendor_status')) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		if(!$this->config->get('mpmultivendor_applyseller_page')) {
			$this->response->redirect($this->url->link('account/account', '', true));	
		}

		$this->load->language('account/mpmultivendor/store_info');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

		if(strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')){
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');			
		}
		
		$this->document->addScript('catalog/view/javascript/mpseller/seller.js');
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/mpmultivendor/seller');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $dataFile = array();
            $uploads_dir = 'image/mpseller/customer-'.$this->customer->getId().'/'; // set you upload path here
            if (is_uploaded_file($this->request->files['id_proof']['tmp_name'])) {

                move_uploaded_file($this->request->files['id_proof']['tmp_name'],$uploads_dir.$this->request->files['id_proof']['name']);
                $dataFile['id_proof'] = $this->request->files['id_proof']['name'];
            }
            if (is_uploaded_file($this->request->files['address_proof']['tmp_name'])) {

                move_uploaded_file($this->request->files['address_proof']['tmp_name'],$uploads_dir.$this->request->files['address_proof']['name']);
                $dataFile['address_proof'] = $this->request->files['address_proof']['name'];
            }

            $this->model_account_mpmultivendor_seller->addSellerStoreInfo($this->request->post, $dataFile);

			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('account/mpmultivendor/store_info'));
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		} 

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = '';
		}
		
		if (isset($this->error['meta_description'])) {
			$data['error_meta_description'] = $this->error['meta_description'];
		} else {
			$data['error_meta_description'] = '';
		}
		
		if (isset($this->error['meta_keyword'])) {
			$data['error_meta_keyword'] = $this->error['meta_keyword'];
		} else {
			$data['error_meta_keyword'] = '';
		}

		if (isset($this->error['store_owner'])) {
			$data['error_store_owner'] = $this->error['store_owner'];
		} else {
			$data['error_store_owner'] = '';
		}
		
		if (isset($this->error['store_name'])) {
			$data['error_store_name'] = $this->error['store_name'];
		} else {
			$data['error_store_name'] = '';
		}
		
		if (isset($this->error['address'])) {
			$data['error_address'] = $this->error['address'];
		} else {
			$data['error_address'] = '';
		}
		
		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

        if (isset($this->error['telephone'])) {
            $data['error_telephone'] = $this->error['telephone'];
        } else {
            $data['error_telephone'] = '';
        }

        if (isset($this->error['alternate_telephone'])) {
            $data['error_alternate_telephone'] = $this->error['alternate_telephone'];
        } else {
            $data['error_alternate_telephone'] = '';
        }
		
		if (isset($this->error['fax'])) {
			$data['error_fax'] = $this->error['fax'];
		} else {
			$data['error_fax'] = '';
		}
		
		if (isset($this->error['seo_keyword'])) {
			$data['error_seo_keyword'] = $this->error['seo_keyword'];
		} else {
			$data['error_seo_keyword'] = '';
		}

		if (isset($this->error['review_seo_keyword'])) {
			$data['error_review_seo_keyword'] = $this->error['review_seo_keyword'];
		} else {
			$data['error_review_seo_keyword'] = '';
		}

		if (isset($this->error['city'])) {
			$data['error_city'] = $this->error['city'];
		} else {
			$data['error_city'] = '';
		}

		if (isset($this->error['country'])) {
			$data['error_country'] = $this->error['country'];
		} else {
			$data['error_country'] = '';
		}

		if (isset($this->error['zone'])) {
			$data['error_zone'] = $this->error['zone'];
		} else {
			$data['error_zone'] = '';
		}

		//for idproof file validation
        if (isset($this->error['filename_id_proof'])) {
            $data['error_filename_id_proof'] = $this->error['filename_id_proof'];
        } else {
            $data['error_filename_id_proof'] = '';
        }
        if (isset($this->error['filetype_id_proof'])) {
            $data['error_filetype_id_proof'] = $this->error['filetype_id_proof'];
            //print_r($data);exit('aaaaa');
        } else {
            $data['error_filetype_id_proof'] = '';
        }
        if (isset($this->error['filesize_id_proof'])) {
            $data['error_filesize_id_proof'] = $this->error['filesize_id_proof'];
        } else {
            $data['error_filesize_id_proof'] = '';
        }

        //for addressproof file validation
        if (isset($this->error['filename_address_proof'])) {
            $data['error_filename_address_proof'] = $this->error['filename_address_proof'];
        } else {
            $data['error_filename_address_proof'] = '';
        }
        if (isset($this->error['filetype_address_proof'])) {
            $data['error_filetype_address_proof'] = $this->error['filetype_address_proof'];
        } else {
            $data['error_filetype_address_proof'] = '';
        }
        if (isset($this->error['filesize_address_proof'])) {
            $data['error_filesize_address_proof'] = $this->error['filesize_address_proof'];
        } else {
            $data['error_filesize_address_proof'] = '';
        }

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_store_info'),
			'href' => $this->url->link('account/store_info', '', true)
		);

		$data['action'] = $this->url->link('account/mpmultivendor/store_info', '', true);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_keyword'] = $this->language->get('text_keyword');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_store'] = $this->language->get('tab_store');
		$data['tab_local'] = $this->language->get('tab_local');
		$data['tab_image'] = $this->language->get('tab_image');
		$data['tab_social_profiles'] = $this->language->get('tab_social_profiles');
		$data['tab_seo'] = $this->language->get('tab_seo');

		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_store_owner'] = $this->language->get('entry_store_owner');
		$data['entry_store_name'] = $this->language->get('entry_store_name');
		$data['entry_address'] = $this->language->get('entry_address');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_alternate_telephone'] = $this->language->get('entry_alternate_telephone');
		$data['entry_fax'] = $this->language->get('entry_fax');
		$data['entry_seo_keyword'] = $this->language->get('entry_seo_keyword');
		$data['entry_review_seo_keyword'] = $this->language->get('entry_review_seo_keyword');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_state'] = $this->language->get('entry_state');
		$data['entry_logo'] = $this->language->get('entry_logo');
		$data['entry_banner'] = $this->language->get('entry_banner');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_facebook'] = $this->language->get('entry_facebook');
		$data['entry_google_plus'] = $this->language->get('entry_google_plus');
		$data['entry_twitter'] = $this->language->get('entry_twitter');
		$data['entry_pinterest'] = $this->language->get('entry_pinterest');
		$data['entry_linkedin'] = $this->language->get('entry_linkedin');
		$data['entry_youtube'] = $this->language->get('entry_youtube');
		$data['entry_instagram'] = $this->language->get('entry_instagram');
		$data['entry_flickr'] = $this->language->get('entry_flickr');
		$data['entry_shipping_type'] = $this->language->get('entry_shipping_type');
		$data['entry_keyword'] = $this->language->get('entry_keyword');

		$data['help_banner'] = sprintf($this->language->get('help_banner'), $this->config->get('mpmultivendor_main_banner_width'), $this->config->get('mpmultivendor_main_banner_height'));
		$data['help_logo'] = sprintf($this->language->get('help_logo'), $this->config->get('mpmultivendor_store_logo_width'), $this->config->get('mpmultivendor_store_logo_height'));
		$data['help_image'] = sprintf($this->language->get('help_image'), $this->config->get('mpmultivendor_profile_image_width'), $this->config->get('mpmultivendor_profile_image_height'));

		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$data['countries'] = $this->model_localisation_country->getCountries();

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerInfo($this->customer->getId());

		if(isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} else if($seller_info) {
			$data['description'] = $seller_info['description'];
		} else {
			$data['description'] = '';
		}

		if(isset($this->request->post['meta_description'])) {
			$data['meta_description'] = $this->request->post['meta_description'];
		} else if($seller_info) {
			$data['meta_description'] = $seller_info['meta_description'];
		} else {
			$data['meta_description'] = '';
		}
		
		if(isset($this->request->post['meta_keyword'])) {
			$data['meta_keyword'] = $this->request->post['meta_keyword'];
		} else if($seller_info) {
			$data['meta_keyword'] = $seller_info['meta_keyword'];
		} else {
			$data['meta_keyword'] = '';
		}

		if(isset($this->request->post['store_owner'])) {
			$data['store_owner'] = $this->request->post['store_owner'];
		} else if($seller_info) {
			$data['store_owner'] = $seller_info['store_owner'];
		} else {
			$data['store_owner'] = '';
		}

		if(isset($this->request->post['store_name'])) {
			$data['store_name'] = $this->request->post['store_name'];
		} else if($seller_info) {
			$data['store_name'] = $seller_info['store_name'];
		} else {
			$data['store_name'] = '';
		}

		if(isset($this->request->post['address'])) {
			$data['address'] = $this->request->post['address'];
		} else if($seller_info) {
			$data['address'] = $seller_info['address'];
		} else {
			$data['address'] = '';
		}

		if(isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else if($seller_info) {
			$data['email'] = $seller_info['email'];
		} else {
			$data['email'] = $this->customer->getEmail();
		}

		if(isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} else if($seller_info) {
			$data['telephone'] = $seller_info['telephone'];
		} else {
			$data['telephone'] = $this->customer->getTelephone();
		}

        if(isset($this->request->post['alternate_telephone'])) {
            $data['alternate_telephone'] = $this->request->post['alternate_telephone'];
        } else if($seller_info) {
            $data['alternate_telephone'] = $seller_info['alternate_telephone'];
        } else {
            //below commented not working on server
            //$data['alternate_telephone'] = $this->customer->getAlternateTelephone();
            $data['alternate_telephone'] = '';
        }

		if(isset($this->request->post['fax'])) {
			$data['fax'] = $this->request->post['fax'];
		} else if($seller_info) {
			$data['fax'] = $seller_info['fax'];
		} else {
			$data['fax'] = '';
		}

		if(isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		} else if($seller_info) {
			$data['city'] = $seller_info['city'];
		} else {
			$data['city'] = '';
		}
		
		if(isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} else if($seller_info) {
			$data['country_id'] = $seller_info['country_id'];
		} else {
			$data['country_id'] = '';
		}

        $uploads_dir = 'image/mpseller/customer-'.$this->customer->getId().'/';
		if(isset($this->request->post['id_proof'])) {
			$data['link_id_proof'] = $this->request->post['id_proof'];
		} else if($seller_info['id_proof']) {
			$data['link_id_proof'] =$this->config->get('config_ssl').$uploads_dir.$seller_info['id_proof'];
		} else {
			$data['link_id_proof'] = '';
		}

        if(isset($this->request->post['address_proof'])) {
            $data['link_address_proof'] = $this->request->post['address_proof'];
        } else if($seller_info['address_proof']) {
            $data['link_address_proof'] = $this->config->get('config_ssl').$uploads_dir.$seller_info['address_proof'];
        } else {
            $data['link_address_proof'] = '';
        }

        if(isset($this->request->post['zone_id'])) {
            $data['zone_id'] = $this->request->post['zone_id'];
            if(!empty($data['country_id'])) {
                $data['zonesData'] = $this->model_localisation_zone->getZonesByCountryId($data['country_id']);
            }
        } else if($seller_info) {
            $data['zone_id'] = $seller_info['zone_id'];
            if(!empty($data['country_id'])) {
                $data['zonesData'] = $this->model_localisation_zone->getZonesByCountryId($data['country_id']);
            }
        } else {
            $data['zone_id'] = '';
            $data['zonesData'] = [];
        }

		if(isset($this->request->post['facebook_url'])) {
			$data['facebook_url'] = $this->request->post['facebook_url'];
		} else if($seller_info) {
			$data['facebook_url'] = $seller_info['facebook_url'];
		} else {
			$data['facebook_url'] = '';
		}

		if(isset($this->request->post['google_plus_url'])) {
			$data['google_plus_url'] = $this->request->post['google_plus_url'];
		} else if($seller_info) {
			$data['google_plus_url'] = $seller_info['google_plus_url'];
		} else {
			$data['google_plus_url'] = '';
		}
		
		if(isset($this->request->post['twitter_url'])) {
			$data['twitter_url'] = $this->request->post['twitter_url'];
		} else if($seller_info) {
			$data['twitter_url'] = $seller_info['twitter_url'];
		} else {
			$data['twitter_url'] = '';
		}
		
		if(isset($this->request->post['pinterest_url'])) {
			$data['pinterest_url'] = $this->request->post['pinterest_url'];
		} else if($seller_info) {
			$data['pinterest_url'] = $seller_info['pinterest_url'];
		} else {
			$data['pinterest_url'] = '';
		}

		if(isset($this->request->post['linkedin_url'])) {
			$data['linkedin_url'] = $this->request->post['linkedin_url'];
		} else if($seller_info) {
			$data['linkedin_url'] = $seller_info['linkedin_url'];
		} else {
			$data['linkedin_url'] = '';
		}
		
		if(isset($this->request->post['youtube_url'])) {
			$data['youtube_url'] = $this->request->post['youtube_url'];
		} else if($seller_info) {
			$data['youtube_url'] = $seller_info['youtube_url'];
		} else {
			$data['youtube_url'] = '';
		}

		if(isset($this->request->post['instagram_url'])) {
			$data['instagram_url'] = $this->request->post['instagram_url'];
		} else if($seller_info) {
			$data['instagram_url'] = $seller_info['instagram_url'];
		} else {
			$data['instagram_url'] = '';
		}

		if(isset($this->request->post['flickr_url'])) {
			$data['flickr_url'] = $this->request->post['flickr_url'];
		} else if($seller_info) {
			$data['flickr_url'] = $seller_info['flickr_url'];
		} else {
			$data['flickr_url'] = '';
		}

		if (isset($this->request->post['seller_seo_url'])) {
			$data['seller_seo_url'] = $this->request->post['seller_seo_url'];
		} else if($seller_info) {
			$data['seller_seo_url'] = $this->model_account_mpmultivendor_seller->getSellerSeoUrls($seller_info['mpseller_id']);
		} else {
			$data['seller_seo_url'] = array();
		}

		if (isset($this->request->post['review_seo_url'])) {
			$data['review_seo_url'] = $this->request->post['review_seo_url'];
		} else if($seller_info) {
			$data['review_seo_url'] = $this->model_account_mpmultivendor_seller->getReviewSeoUrls($seller_info['mpseller_id']);
		} else {
			$data['review_seo_url'] = array();
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['logo'])) {
			$data['logo'] = $this->request->post['logo'];
		} elseif (!empty($seller_info)) {
			$data['logo'] = $seller_info['logo'];
		} else {
			$data['logo'] = '';
		}

		if (isset($this->request->post['logo']) && is_file(DIR_IMAGE . $this->request->post['logo'])) {
			$data['thumb_logo'] = $this->model_tool_image->resize($this->request->post['logo'], 100, 100);
		} elseif (!empty($seller_info) && is_file(DIR_IMAGE . $seller_info['logo'])) {
			$data['thumb_logo'] = $this->model_tool_image->resize($seller_info['logo'], 100, 100);
		} else {
			$data['thumb_logo'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		if (isset($this->request->post['banner'])) {
			$data['banner'] = $this->request->post['banner'];
		} elseif (!empty($seller_info)) {
			$data['banner'] = $seller_info['banner'];
		} else {
			$data['banner'] = '';
		}

		if (isset($this->request->post['banner']) && is_file(DIR_IMAGE . $this->request->post['banner'])) {
			$data['thumb_banner'] = $this->model_tool_image->resize($this->request->post['banner'], 100, 100);
		} elseif (!empty($seller_info) && is_file(DIR_IMAGE . $seller_info['banner'])) {
			$data['thumb_banner'] = $this->model_tool_image->resize($seller_info['banner'], 100, 100);
		} else {
			$data['thumb_banner'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($seller_info)) {
			$data['image'] = $seller_info['image'];
		} else {
			$data['image'] = '';
		}

        if (isset($this->request->post['id_proof'])) {
            $data['id_proof'] = $this->request->post['id_proof'];
        } elseif (!empty($seller_info)) {
            $data['id_proof'] = $seller_info['id_proof'];
        } else {
            $data['id_proof'] = '';
        }

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb_image'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($seller_info) && is_file(DIR_IMAGE . $seller_info['image'])) {
			$data['thumb_image'] = $this->model_tool_image->resize($seller_info['image'], 100, 100);
		} else {
			$data['thumb_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (!empty($seller_info)) {
			$data['status'] = $seller_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['approved'])) {
			$data['approved'] = $this->request->post['approved'];
		} elseif (!empty($seller_info)) {
			$data['approved'] = $seller_info['approved'];
		} else {
			$data['approved'] = '';
		}


		$data['error_warning_message'] = '';
		if($seller_info) {
			if($data['status']) {
				if($data['approved']) {
					$data['error_warning_message'] = '';
				} else {
					$data['error_warning_message'] = $this->language->get('message_need_approval');
				}
			} else {
				$data['error_warning_message'] = $this->language->get('message_status_disabled');
			}
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('setting/store');
		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}
		
		$data['mpseller_links'] = $this->load->controller('account/mpmultivendor/mpseller_links');
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		/* Theme Work Starts */
		if($this->config->get('config_theme')) {			
     		$custom_themename = $this->config->get('config_theme');
    	} if($this->config->get('theme_default_directory')) {    		
			$custom_themename = $this->config->get('theme_default_directory');
		} if($this->config->get('config_template')) {			
			$custom_themename = $this->config->get('config_template');
		} 
		// else{
		// 	$custom_themename = 'default';
		// }

		if (defined('JOURNAL3_ACTIVE')) {
			$custom_themename = 'journal3';
		}

		if(strpos($this->config->get('config_template'), 'journal2') === 0){
			$custom_themename = 'journal2';
		}

		if(empty($custom_themename)) {
			$custom_themename = 'default';
		}

		$data['custom_themename'] = $custom_themename;
		/* Theme Work Ends */

		if(VERSION < '2.2.0.0') {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/mpmultivendor/store_info.tpl')) {
		    	$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/mpmultivendor/store_info.tpl', $data));
		   } else {
		   		$this->response->setOutput($this->load->view('default/template/account/mpmultivendor/store_info.tpl', $data));
		   }
	  	} else {
		   $this->response->setOutput($this->load->view('account/mpmultivendor/store_info', $data));
		}
	}

	private function validate() {
		$mpseller_info = $this->model_account_mpmultivendor_seller->getSellerInfo($this->customer->getId());
		if ($mpseller_info && empty($mpseller_info['status'])) {
			$this->error['warning'] = $this->language->get('error_status_disabled');
		}

		if (!$this->request->post['description']) {
			$this->error['description'] = $this->language->get('error_description');
		}

		if (!$this->request->post['meta_description']) {
			$this->error['meta_description'] = $this->language->get('error_meta_description');
		}

		if (!$this->request->post['meta_keyword']) {
			$this->error['meta_keyword'] = $this->language->get('error_meta_keyword');
		}

		if ((utf8_strlen($this->request->post['store_owner']) < 3) || (utf8_strlen($this->request->post['store_owner']) > 64)) {
			$this->error['store_owner'] = $this->language->get('error_store_owner');
		}

		if ((utf8_strlen($this->request->post['store_name']) < 3) || (utf8_strlen($this->request->post['store_name']) > 255)) {
			$this->error['store_name'] = $this->language->get('error_store_name');
		}

		if ((utf8_strlen($this->request->post['address']) < 3) || (utf8_strlen($this->request->post['address']) > 128)) {
			$this->error['address'] = $this->language->get('error_address');
		}

		if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
			$this->error['city'] = $this->language->get('error_city');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

		/*if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
			$this->error['postcode'] = $this->language->get('error_postcode');
		}*/

		if ($this->request->post['country_id'] == '') {
			$this->error['country'] = $this->language->get('error_country');
		}

		if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
			$this->error['zone'] = $this->language->get('error_zone');
		}

		$seller_info = $this->model_account_mpmultivendor_seller->getSellerInfo($this->customer->getId());

		if($seller_info) {
			$mpseller_id = $seller_info['mpseller_id'];
		}

		if($seller_info) {
			if (isset($this->request->post['seller_seo_url'])) {
				foreach ($this->request->post['seller_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							if (count(array_keys($language, $keyword)) > 1) {
								$this->error['seo_keyword'][$store_id][$language_id] = $this->language->get('error_unique');
							}						
							
							$seo_urls = $this->model_account_mpmultivendor_seller->getSeoUrlsByKeyword($keyword);
							
							foreach ($seo_urls as $seo_url) {
								if (($seo_url['store_id'] == $store_id) && (!isset($seller_info['mpseller_id']) || (($seo_url['query'] != 'mpseller_id=' . $seller_info['mpseller_id'])))) {
									$this->error['seo_keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
									
									break;
								}
							}
						}
					}
				}
			}

			if (isset($this->request->post['review_seo_url'])) {
				foreach ($this->request->post['review_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							if (count(array_keys($language, $keyword)) > 1) {
								$this->error['review_seo_keyword'][$store_id][$language_id] = $this->language->get('error_unique');
							}						
							
							$seo_urls = $this->model_account_mpmultivendor_seller->getSeoUrlsByKeyword($keyword);
							
							foreach ($seo_urls as $seo_url) {
								if (($seo_url['store_id'] == $store_id) && (!isset($seller_info['mpseller_id']) || (($seo_url['query'] != 'review_mpseller_id=' . $seller_info['mpseller_id'])))) {
									$this->error['review_seo_keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
									
									break;
								}
							}
						}
					}
				}
			}
		}

		//if file found
        $this->validateFile('id_proof');
        $this->validateFile('address_proof');

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

    private function validateFile($inputFileName)
    {
        if (!empty($this->request->files[$inputFileName]['name']))
        {
            $filename = html_entity_decode($this->request->files[$inputFileName]['name'], ENT_QUOTES, 'UTF-8');

            if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 128))
            {
                $this->error['filename_'.$inputFileName]  = $this->language->get('error_filename_'.$inputFileName);
            }

            $allowed = array("doc","pdf","jpg");
            if (!in_array(utf8_substr(strrchr($filename, '.'), 1), $allowed)) {
                $this->error['filetype_'.$inputFileName]  = $this->language->get('error_filetype_'.$inputFileName);
            }

            if($this->request->files[$inputFileName]['size']>500000)
            {
                $this->error['filesize_'.$inputFileName]  = $this->language->get('error_filesize_'.$inputFileName);
            }

            if ($this->request->files[$inputFileName]['error'] != UPLOAD_ERR_OK) {
                $this->error[$inputFileName] = $this->language->get('error_upload_' . $this->request->files['upload']['error']);
            }
            //print_r($this->error[$inputFileName]);exit();
        }
    }
}