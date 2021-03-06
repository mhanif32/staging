<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}




        $data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

		$data['contact'] = $this->url->link('information/contact');
		$data['about'] = $this->url->link('information/about');
		$data['privacy_policy'] = $this->url->link('information/privacy_policy');
		$data['faq'] = $this->url->link('information/faq');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
        $data['register'] = $this->url->link('account/register', '', true);
        $data['login'] = $this->url->link('account/login', '', true);
        $data['logout'] = $this->url->link('account/logout', '', true);
        $data['logged'] = $this->customer->isLogged();

        $data['seller_register_link'] = $this->url->link('information/become_seller_info', true);
        $data['affiliate_info_href'] = $this->url->link('information/affiliate_info', '', true);
        $data['sellers_href'] = $this->url->link('mpmultivendor/mv_seller', '', true);
        $data['delivery_info_href'] = $this->url->link('information/delivery_info', '', true);
        $data['return_policy_href'] = $this->url->link('information/return_policy', '', true);
        $data['become_delivery_partner_info_href'] = $this->url->link('information/become_delivery_partner_info', '', true);
        $data['become_executive_member_info_href'] = $this->url->link('information/become_executive_member_info', '', true);
        $data['become_our_partner_href'] = $this->url->link('information/become_our_partner_info', '', true);

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['scripts'] = $this->document->getScripts('footer');

        $this->load->model('account/customer');

        $affiliate_info = $this->model_account_customer->getAffiliate($this->customer->getId());

        if (!$affiliate_info) {
            $data['affiliate'] = $this->url->link('account/affiliate/add', '', true);
        } else {
            $data['affiliate'] = $this->url->link('account/affiliate/edit', '', true);
        }

        if ($affiliate_info) {
            $data['tracking'] = $this->url->link('account/tracking', '', true);
        } else {
            $data['tracking'] = '';
        }
		
		return $this->load->view('common/footer', $data);
	}
}
