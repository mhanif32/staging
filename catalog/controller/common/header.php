<?php

class ControllerCommonHeader extends Controller
{
    public function index()
    {
        //LogOut after 30 min of inactivity
        if (isset($this->session->data['last']) && (time() - $this->session->data['last'] > 30 * 60)) {
            $this->customer->logout();
            unset($this->session->data['last']);
            $this->response->redirect($this->url->link('common/home', '', true));
        }
        $this->session->data['last'] = time();
        //end of : LogOut

        // Analytics
        $this->load->model('setting/extension');
        $this->load->model('account/address');
        $this->load->model('catalog/information');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $this->load->model('account/wishlist');
        $this->load->model('tool/image');

        $data['analytics'] = array();

        $analytics = $this->model_setting_extension->getExtensions('analytics');

        foreach ($analytics as $analytic) {
            if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
                $data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
            }
        }

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
            $this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
        }

        $data['title'] = $this->document->getTitle();

        $data['base'] = $server;
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        $data['links'] = $this->document->getLinks();
        $data['styles'] = $this->document->getStyles();
        $data['scripts'] = $this->document->getScripts('header');
        $data['lang'] = $this->language->get('code');
        $data['direction'] = $this->language->get('direction');

        $data['name'] = $this->config->get('config_name');

        if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
            $data['logo'] = $server . 'image/' . $this->config->get('config_logo');
        } else {
            $data['logo'] = '';
        }

        $this->load->language('common/header');

        // Wishlist
        if ($this->customer->isLogged()) {

            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
            $data['firstname'] = $this->customer->getFirstName();
            $data['lastname'] = $this->customer->getLastName();

            $this->load->model('account/customer');
            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
            $data['loggedInRole'] = (!empty($customer_info['role']) && $customer_info['role'] != 'buyer') ? 'You are a <b>' . ucfirst(str_replace("-", " ", $customer_info['role'])) . '</b>' : '';

        } else {
            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
        }

        $data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

        $data['home'] = $this->url->link('common/home');
        $data['wishlist'] = $this->url->link('account/wishlist', '', true);
        $data['logged'] = $this->customer->isLogged();
        $data['account'] = $this->url->link('account/account', '', true);
        $data['my_profile'] = $this->url->link('account/edit', '', true);
        $data['register'] = $this->url->link('account/register', '', true);
        $data['affiliate_register_info_link'] = $this->url->link('information/become_franchisee_info', '', true);

        $data['seller_register_link'] = $this->url->link('information/become_seller_info', true);
        $data['affiliate_info_href'] = $this->url->link('information/affiliate_info', '', true);
        $data['sellers_href'] = $this->url->link('mpmultivendor/mv_seller', '', true);
        $data['delivery_info_href'] = $this->url->link('information/delivery_info', '', true);
        $data['return_policy_href'] = $this->url->link('information/return_policy', '', true);
        $data['become_delivery_partner_info_href'] = $this->url->link('information/become_delivery_partner_info', '', true);
        $data['become_executive_member_info_href'] = $this->url->link('information/become_executive_member_info', '', true);
        $data['become_our_partner_href'] = $this->url->link('information/become_our_partner_info', '', true);

        $data['login'] = $this->url->link('account/login', '', true);
        $data['order'] = $this->url->link('account/order', '', true);
        $data['transaction'] = $this->url->link('account/transaction', '', true);
        $data['download'] = $this->url->link('account/download', '', true);
        $data['logout'] = $this->url->link('account/logout', '', true);
        $data['shopping_cart'] = $this->url->link('checkout/cart');
        $data['checkout'] = $this->url->link('checkout/checkout', '', true);
        $data['contact'] = $this->url->link('information/contact');
        $data['track_your_order'] = $this->url->link('account/order/track');
        $data['manage_address_link'] = $this->url->link('account/address');
        $data['telephone'] = $this->config->get('config_telephone');

        $data['language'] = $this->load->controller('common/language');
        $data['currency'] = $this->load->controller('common/currency');
        $data['search'] = $this->load->controller('common/search');
        $data['cart'] = $this->load->controller('common/cart');
        $data['menu'] = $this->load->controller('common/menu');
        $data['show_top_bar'] = (empty($this->request->get['route']) || $this->request->get['route'] == 'common/home') ? true : false;
        $data['show_top_menu'] = (isset($this->request->get['route']) && $this->request->get['route'] == 'account/account') ? false : true;

        $data['countries'] = $this->model_localisation_country->getCountries();

        $fillAddressMessage = $this->model_catalog_information->getInfoMessage(7);
        $data['popupLogo'] = $this->model_tool_image->resize('/' . $this->config->get('config_logo'), 450, 100);
        $data['fillAddressMsg'] = html_entity_decode($fillAddressMessage['description'], ENT_QUOTES, 'UTF-8');

        //check logged in user country
        //if (!$this->customer->isLogged()) { //before login

        if (empty($this->session->data['loggedInCountry'])) {

            $ip = $_SERVER['REMOTE_ADDR'];
            $dataArray = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            $this->session->data['loggedInCity'] = !empty($dataArray->geoplugin_city) ? $dataArray->geoplugin_city : '';
            $this->session->data['loggedInState'] = !empty($dataArray->geoplugin_regionName) ? $dataArray->geoplugin_regionName : '';
            $this->session->data['loggedInCountry'] = !empty($dataArray->geoplugin_countryName) ? $dataArray->geoplugin_countryName : '';

            $data['loggedInCity'] = $this->session->data['loggedInCity'];
            $data['loggedInState'] = $this->session->data['loggedInState'];
            $data['loggedInCountry'] = $this->session->data['loggedInCountry'];
        } else {

            $data['loggedInCountry'] = isset($this->session->data['loggedInCountry']) ? $this->session->data['loggedInCountry'] : '';
            $data['loggedInState'] = isset($this->session->data['loggedInState']) ? $this->session->data['loggedInState'] : '';
            $data['loggedInCity'] = isset($this->session->data['loggedInCity']) ? $this->session->data['loggedInCity'] : '';
        }
        /*} else { //after login
            // Default Shipping Address
            $defaultAddress = $this->model_account_address->getDefaultAddress();
            if (!empty($defaultAddress['country'])) {

                $data['loggedInCountry'] = $this->session->data['loggedInCountry'] = !empty($defaultAddress['country']) ? $defaultAddress['country'] : '';
                $data['loggedInState'] = $this->session->data['loggedInState'] = !empty($defaultAddress['zone']) ? $defaultAddress['zone'] : '';
                $data['loggedInCity'] = $this->session->data['loggedInCity'] = !empty($defaultAddress['city']) ? $defaultAddress['city'] : '';
            } else {
                if (empty($this->session->data['loggedInCountry'])) {
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $dataArray = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
                    $this->session->data['loggedInState'] = $dataArray->geoplugin_regionName;
                    $this->session->data['loggedInCountry'] = $dataArray->geoplugin_countryName;
                } else {
                    $data['loggedInCountry'] = isset($this->session->data['loggedInCountry']) ? $this->session->data['loggedInCountry'] : '';

                }
            }
        }*/

        $country = $this->model_localisation_country->getCountryIdFromName($data['loggedInCountry']);
        $zone = $this->model_localisation_zone->getZoneIdFromName($data['loggedInState']);
        if (!empty($country)) {
            $this->session->data['session_country_id'] = !empty($country['country_id']) ? $country['country_id'] : '';
            $this->session->data['session_state_id'] = !empty($zone['zone_id']) ? $zone['zone_id'] : '';
        }
        return $this->load->view('common/header', $data);
    }
}
