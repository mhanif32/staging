<?php
class ControllerAccountMpmultivendorDashboard extends Controller
{
    public function index()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/account', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        if (!$this->config->get('mpmultivendor_status')) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }

        $this->load->language('account/mpmultivendor/dashboard');

        $this->load->model('account/mpmultivendor/dashboard');

        $this->load->model('account/mpmultivendor/seller');

        $this->load->model('account/mpmultivendor/orders');

        $this->load->language('account/edit');


        $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

        if (strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')) {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
        }

        $this->document->setTitle($this->language->get('heading_title'));

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
            'text' => $this->language->get('text_dashboard'),
            'href' => $this->url->link('account/mpmultivendor/dashboard', '', true)
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_product'] = $this->language->get('text_product');
        $data['text_service'] = $this->language->get('text_service');
        $data['text_sales'] = $this->language->get('text_sales');
        $data['text_orders'] = $this->language->get('text_orders');
        $data['text_commission'] = $this->language->get('text_commission');
        $data['text_recevied_payment'] = $this->language->get('text_recevied_payment');
        $data['text_available_balance'] = $this->language->get('text_available_balance');
        $data['text_reviews'] = $this->language->get('text_reviews');
        $data['text_products'] = $this->language->get('text_products');
        $data['text_services'] = $this->language->get('text_services');
        $data['text_viewmore'] = $this->language->get('text_viewmore');
        $data['text_all'] = $this->language->get('text_all');
        $data['text_total'] = $this->language->get('text_total');

        $data['button_new_product'] = $this->language->get('button_new_product');
        $data['button_new_service'] = $this->language->get('button_new_service');
        $data['button_viewmore'] = $this->language->get('button_viewmore');

        $data['new_product'] = $this->url->link('account/mpmultivendor/product/add', '', true);

        $data['new_service'] = $this->url->link('account/mpmultivendor/service/add', '', true);

        $data['all_reviews'] = $this->url->link('account/mpmultivendor/reviews', '', true);

        $data['all_orders'] = $this->url->link('account/mpmultivendor/orders', '', true);

        $seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

        if (!$seller_info) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }

        if (!empty($seller_info)) {
            $mpseller_id = $seller_info['mpseller_id'];
        } else {
            $mpseller_id = 0;
        }

        $filter_data = array(
            'filter_mpseller_id' => $mpseller_id,
        );

        $total_commission = $this->model_account_mpmultivendor_dashboard->getCommissionTotal($filter_data);
        $data['total_commission'] = $this->currency->format($total_commission, $this->config->get('config_currency'));

        $recevied_payment = $this->model_account_mpmultivendor_dashboard->getPaymentTotal($filter_data);
        $data['recevied_payment'] = $this->currency->format($recevied_payment, $this->config->get('config_currency'));

        $available_balance = $this->model_account_mpmultivendor_dashboard->getAvailableBalance($filter_data);
        $data['available_balance'] = $this->currency->format($available_balance, $this->config->get('config_currency'));

        $data['total_orders'] = $this->model_account_mpmultivendor_dashboard->getTotalOrders($filter_data);

        $total_sales = $this->model_account_mpmultivendor_dashboard->getTotalSales($filter_data);
        $data['total_sales'] = $this->currency->format($total_sales, $this->config->get('config_currency'));
        $data['total_products'] = $this->model_account_mpmultivendor_dashboard->getTotalProucts($filter_data);

        $data['total_services'] = $this->model_account_mpmultivendor_dashboard->getTotalServices($filter_data);

        $order_statuses = $this->model_account_mpmultivendor_orders->getOrderStatuses();
        $data['order_statuses'] = array();
        foreach ($order_statuses as $order_status) {
            $filter_status_data = array(
                'filter_mpseller_id' => $mpseller_id,
                'filter_order_status_id' => $order_status['order_status_id'],
            );

            $total_orders = $this->model_account_mpmultivendor_dashboard->getTotalOrders($filter_status_data);

            if ($total_orders) {
                $data['order_statuses'][] = array(
                    'name' => $order_status['name'],
                    'total_orders' => $total_orders,
                    'href' => $this->url->link('account/mpmultivendor/orders', '&filter_order_status_id=' . $order_status['order_status_id'], true),
                );
            }
        }

        $data['total_reviews'] = $this->model_account_mpmultivendor_dashboard->getTotalReviews($filter_data);

        $data['mpseller_links'] = $this->load->controller('account/mpmultivendor/mpseller_links');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $data['profile_column_left'] = $this->load->controller('common/profile_column_left');
        /* Theme Work Starts */
        if ($this->config->get('config_theme')) {
            $custom_themename = $this->config->get('config_theme');
        }
        if ($this->config->get('theme_default_directory')) {
            $custom_themename = $this->config->get('theme_default_directory');
        }
        if ($this->config->get('config_template')) {
            $custom_themename = $this->config->get('config_template');
        }
        // else{
        // 	$custom_themename = 'default';
        // }

        if (defined('JOURNAL3_ACTIVE')) {
            $custom_themename = 'journal3';
        }

        if (strpos($this->config->get('config_template'), 'journal2') === 0) {
            $custom_themename = 'journal2';
        }

        if (empty($custom_themename)) {
            $custom_themename = 'default';
        }

        $data['custom_themename'] = $custom_themename;
        /* Theme Work Ends */

        //my latest 5 orders
        $this->load->model('account/mpmultivendor/orders');
        $data['orders'] = array();
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }
        $filter_data = array(
            'mpseller_id'		=> $mpseller_id,
            'start'         	=> ($page - 1) * $this->config->get('mpmultivendor_seller_list'),
            'limit'         	=> 5,
        );

        $results = $this->model_account_mpmultivendor_orders->getOrders($filter_data);

        foreach ($results as $result) {
            $data['orders'][] = array(
                'order_id'   => $result['order_id'],
                'name'       => $result['firstname'] . ' ' . $result['lastname'],
                'status'     => $result['status'],
                'by_admin_status'     => $result['by_admin_status'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'view'       => $this->url->link('account/mpmultivendor/orders/info', 'order_id=' . $result['order_id'], true),
            );
        }
        //end my orders

        //my products
        $this->load->model('account/mpmultivendor/product');
        $data['products'] = array();
        $filter_data = array(
            'filter_mpseller_id' => $mpseller_id,
            'start' => ($page - 1) * $this->config->get('mpmultivendor_seller_list'),
            'limit' => 5
        );
        $resultsProducts = $this->model_account_mpmultivendor_product->getProducts($filter_data);
        foreach ($resultsProducts as $result) {

            $data['products'][] = array(
                'product_id' => $result['product_id'],
                'name' => $result['name'],
                'model' => $result['model'],
                'price' => $result['price'],
                'quantity' => $result['quantity'],
                'status' => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit' => $this->url->link('account/mpmultivendor/product/edit', 'product_id=' . $result['product_id'], true)
            );
        }
        $data['view_all_orders_link'] = $this->url->link('account/mpmultivendor/orders', '', true);
        $data['view_all_products_link'] = $this->url->link('account/mpmultivendor/product', '', true);
        $data['addnew_product_link'] = $this->url->link('account/mpmultivendor/product/add', '', true);


        if (VERSION < '2.2.0.0') {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/mpmultivendor/dashboard.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/mpmultivendor/dashboard.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/account/mpmultivendor/dashboard.tpl', $data));
            }
        } else {
            $this->response->setOutput($this->load->view('account/mpmultivendor/dashboard', $data));
        }
    }
}