<?php
class ControllerCommonProfileColumnLeft extends Controller {
    public function index() {

        $this->load->language('common/profile_column_left');

        $customer_info = [];
        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $this->load->model('account/customer');
            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
        }

        $data = [];
        //my account list
        $data['account'] = $this->url->link('account/account', '', true);
        $data['edit'] = $this->url->link('account/edit', '', true);
        $data['change_password'] = $this->url->link('account/password', '', true);
        $data['address'] = $this->url->link('account/address', '', true);

        //order list
        $data['order'] = $this->url->link('account/order', '', true);
        $data['download'] = $this->url->link('account/download', '', true);
        $data['reward'] = $this->url->link('account/reward', '', true);
        $data['return'] = $this->url->link('account/return', '', true);
        $data['transaction'] = $this->url->link('account/transaction', '', true);
        $data['recurring'] = $this->url->link('account/recurring', '', true);

        $data['my_wishlist'] = $this->url->link('account/wishlist', '', true);
        $data['logout'] = $this->url->link('account/logout', '', true);

        //multivender links and text
        $data['text_seller_menus'] = $this->language->get('text_seller_menus');
        $data['text_enabled_seller'] = $this->language->get('text_enabled_seller');
        $data['text_profile'] = $this->language->get('text_profile');
        $data['text_dashboard'] = $this->language->get('text_dashboard');
        $data['text_profile'] = $this->language->get('text_profile');
        $data['text_product'] = $this->language->get('text_product');
        $data['text_service'] = $this->language->get('text_service');
        $data['text_orders'] = $this->language->get('text_orders');
        $data['text_reviews'] = $this->language->get('text_reviews');
        $data['text_commission'] = $this->language->get('text_commission');
        $data['text_payment'] = $this->language->get('text_payment');
        $data['text_information_section'] = $this->language->get('text_information_section');
        $data['text_store_information'] = $this->language->get('text_store_information');
        $data['text_store_setting'] = $this->language->get('text_store_setting');
        $data['text_visit_store'] = $this->language->get('text_visit_store');
        $data['text_enquiries'] = $this->language->get('text_enquiries');

        $data['seller_dashboard'] = $this->url->link('account/mpmultivendor/dashboard', '', true);
        $data['seller_profile'] = $this->url->link('account/edit', '', true);
        $data['seller_product'] = $this->url->link('account/mpmultivendor/product', '', true);
        $data['seller_service'] = $this->url->link('account/mpmultivendor/service', '', true);
        $data['seller_orders'] = $this->url->link('account/mpmultivendor/orders', '', true);
        $data['seller_reviews'] = $this->url->link('account/mpmultivendor/reviews', '', true);
        $data['seller_commission'] = $this->url->link('account/mpmultivendor/commission', '', true);
        $data['seller_payment'] = $this->url->link('account/mpmultivendor/payment', '', true);
        $data['seller_information_section'] = $this->url->link('account/mpmultivendor/information_section', '', true);
        $data['seller_store_info'] = $this->url->link('account/mpmultivendor/store_info', '', true);
        $data['seller_store_setting'] = $this->url->link('account/mpmultivendor/store_setting', '', true);
        $data['seller_enquiries'] = $this->url->link('account/mpmultivendor/enquiries', '', true);
        $data['seller_messages'] = $this->url->link('account/mpmultivendor/message', '', true);
        $data['customer_messages'] = $this->url->link('account/message', '', true);

        //end
        $data = array_merge($data, $customer_info);

        $this->load->model('tool/image');
//        if ($customer_info['image']) {
//            $image = $this->model_tool_image->resize($customer_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
//        } else {
//            $image = $this->model_tool_image->resize('no-avatar.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
//        }
//        echo $customer_info['image'];exit('aaaaaaa');
//        $data['image_url'] = $image;

        $file = !empty($customer_info['image']) ? $customer_info['image'] : 'no-avatar.png';
        $data['image_url'] = '/storage/upload/' . $file;
        return $this->load->view('common/profile_column_left', $data);
    }
}
