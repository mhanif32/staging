<?php

class ControllerAccountMpmultivendorProduct extends Controller
{
    private $error = array();

    public function index()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/account', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        if (!$this->config->get('mpmultivendor_status')) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }
        $this->load->language('account/edit');

        $this->load->language('account/mpmultivendor/product');

        $this->load->model('account/mpmultivendor/seller');

        $this->load->model('account/mpmultivendor/product');

        $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

        if (strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')) {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->getList();
    }

    public function special()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/account', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        if (!$this->config->get('mpmultivendor_status')) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }
        $this->load->language('account/edit');

        $this->load->language('account/mpmultivendor/product');

        $this->load->model('account/mpmultivendor/seller');

        $this->load->model('account/mpmultivendor/product');

        $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

        if (strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')) {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->getSpecialList();
    }

    public function add()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/account', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        if (!$this->config->get('mpmultivendor_status')) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }

        $this->load->language('account/mpmultivendor/product');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('account/mpmultivendor/seller');

        $this->load->model('account/mpmultivendor/product');
        $this->load->model('localisation/country');

        $seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

        if (!empty($seller_info)) {
            $mpseller_id = $seller_info['mpseller_id'];
        } else {
            $mpseller_id = 0;
        }

        if (!$seller_info) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_account_mpmultivendor_product->addProduct($this->request->post, $mpseller_id);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_model'])) {
                $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_price'])) {
                $url .= '&filter_price=' . $this->request->get['filter_price'];
            }

            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('account/mpmultivendor/product', '' . $url, true));
        }

        $this->getForm();
    }

    public function edit()
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

        $this->load->language('account/mpmultivendor/product');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('account/mpmultivendor/seller');

        $this->load->model('account/mpmultivendor/product');

        $seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

        if (!$seller_info) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }

        if (!empty($seller_info)) {
            $mpseller_id = $seller_info['mpseller_id'];
        } else {
            $mpseller_id = 0;
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            if ($this->model_account_mpmultivendor_product->getProductBySellerID($this->request->get['product_id'], $mpseller_id)) {

                $this->model_account_mpmultivendor_product->editProduct($this->request->get['product_id'], $this->request->post, $mpseller_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_model'])) {
                $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_price'])) {
                $url .= '&filter_price=' . $this->request->get['filter_price'];
            }

            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('account/mpmultivendor/product', '' . $url, true));
        }

        $this->getForm();
    }

    public function delete()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/account', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        if (!$this->config->get('mpmultivendor_status')) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }

        $this->load->language('account/mpmultivendor/product');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('account/mpmultivendor/seller');

        $this->load->model('account/mpmultivendor/product');

        $seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

        if (!$seller_info) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }

        if (!empty($seller_info)) {
            $mpseller_id = $seller_info['mpseller_id'];
        } else {
            $mpseller_id = 0;
        }

        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $product_id) {
                if ($this->model_account_mpmultivendor_product->getProductBySellerID($product_id, $mpseller_id)) {
                    $this->model_account_mpmultivendor_product->deleteProduct($product_id);
                }
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_model'])) {
                $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_price'])) {
                $url .= '&filter_price=' . $this->request->get['filter_price'];
            }

            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('account/mpmultivendor/product', '' . $url, true));
        }

        $this->getList();
    }

    protected function getList()
    {

        if (strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')) {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
        }

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['filter_model'])) {
            $filter_model = $this->request->get['filter_model'];
        } else {
            $filter_model = null;
        }

        if (isset($this->request->get['filter_price'])) {
            $filter_price = $this->request->get['filter_price'];
        } else {
            $filter_price = null;
        }

        if (isset($this->request->get['filter_quantity'])) {
            $filter_quantity = $this->request->get['filter_quantity'];
        } else {
            $filter_quantity = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['filter_image'])) {
            $filter_image = $this->request->get['filter_image'];
        } else {
            $filter_image = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'pd.name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_image'])) {
            $url .= '&filter_image=' . $this->request->get['filter_image'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['add'] = $this->url->link('account/mpmultivendor/product/add', '' . $url, true);

        $data['delete'] = $this->url->link('account/mpmultivendor/product/delete', '' . $url, true);

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
            'text' => $this->language->get('text_product'),
            'href' => $this->url->link('account/mpmultivendor/product', '', true)
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_image'] = $this->language->get('column_image');
        $data['column_name'] = $this->language->get('column_name');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_model'] = $this->language->get('entry_model');
        $data['entry_price'] = $this->language->get('entry_price');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_image'] = $this->language->get('entry_image');

        $data['button_import_products'] = $this->language->get('button_import_products');
        $data['button_export_products'] = $this->language->get('button_export_products');
        $data['import_products'] = $this->url->link('account/mpmultivendor/import_product');
        $data['export_products'] = $this->url->link('account/mpmultivendor/export_product');

        $data['button_copy'] = $this->language->get('button_copy');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');

        $data['products'] = array();

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
            'filter_name' => $filter_name,
            'filter_model' => $filter_model,
            'filter_price' => $filter_price,
            'filter_quantity' => $filter_quantity,
            'filter_status' => $filter_status,
            'filter_image' => $filter_image,
            'filter_mpseller_id' => $mpseller_id,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('mpmultivendor_seller_list'),
            'limit' => $this->config->get('mpmultivendor_seller_list')
        );

        $this->load->model('tool/image');

        $product_total = $this->model_account_mpmultivendor_product->getTotalProducts($filter_data);

        $results = $this->model_account_mpmultivendor_product->getProducts($filter_data);

        foreach ($results as $result) {
            if (is_file(DIR_IMAGE . $result['image'])) {
                $image = $this->model_tool_image->resize($result['image'], 40, 40);
            } else {
                $image = $this->model_tool_image->resize('no_image.png', 40, 40);
            }

            $special = false;

            $product_specials = $this->model_account_mpmultivendor_product->getProductSpecials($result['product_id']);

            foreach ($product_specials as $product_special) {
                if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
                    $special = $product_special['price'];

                    break;
                }
            }

            $local_price = 0;
            if (!empty($result['price'])) {
                if(!empty($result['currency'])) {
                    $currData = $this->model_localisation_currency->getCurrencyByCode($result['currency']);
                    $dataValue = $result['price'] * $currData['value'];
                    $local_price = ($result['currency'] == 'USD') ? $result['local_price'] : $dataValue;
                    $local_price = round($local_price, 2);
                }
            }

            $data['products'][] = array(
                'product_id' => $result['product_id'],
                'image' => $image,
                'name' => $result['name'],
                'model' => $result['model'],
                'price' => $result['price'],
                'local_price' => $this->currency->getSymbolLeft($result['currency']) . $local_price . $this->currency->getSymbolRight($result['currency']),
                'currency' => $result['currency'],
                'special' => $special,
                'quantity' => $result['quantity'],
                'status' => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit' => $this->url->link('account/mpmultivendor/product/edit', 'product_id=' . $result['product_id'] . $url, true)
            );
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_image'])) {
            $url .= '&filter_image=' . $this->request->get['filter_image'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('account/mpmultivendor/product', 'sort=pd.name' . $url, true);
        $data['sort_model'] = $this->url->link('account/mpmultivendor/product', 'sort=p.model' . $url, true);
        $data['sort_price'] = $this->url->link('account/mpmultivendor/product', 'sort=p.price' . $url, true);
        $data['sort_quantity'] = $this->url->link('account/mpmultivendor/product', 'sort=p.quantity' . $url, true);
        $data['sort_status'] = $this->url->link('account/mpmultivendor/product', 'sort=p.status' . $url, true);
        $data['sort_order'] = $this->url->link('account/mpmultivendor/product', 'sort=p.sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_image'])) {
            $url .= '&filter_image=' . $this->request->get['filter_image'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('mpmultivendor_seller_list');
        $pagination->url = $this->url->link('account/mpmultivendor/product', '' . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('mpmultivendor_seller_list')) + 1 : 0, ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) > ($product_total - $this->config->get('mpmultivendor_seller_list'))) ? $product_total : ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) + $this->config->get('mpmultivendor_seller_list')), $product_total, ceil($product_total / $this->config->get('mpmultivendor_seller_list')));

        $data['filter_name'] = $filter_name;
        $data['filter_model'] = $filter_model;
        $data['filter_price'] = $filter_price;
        $data['filter_quantity'] = $filter_quantity;
        $data['filter_status'] = $filter_status;
        $data['filter_image'] = $filter_image;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['mpseller_links'] = $this->load->controller('account/mpmultivendor/mpseller_links');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

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

        //for profile right column
        $data['profile_column_left'] = $this->load->controller('common/profile_column_left');
        $this->response->setOutput($this->load->view('account/mpmultivendor/product_list', $data));
    }

    protected function getSpecialList()
    {
        if (strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')) {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
        }

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['filter_model'])) {
            $filter_model = $this->request->get['filter_model'];
        } else {
            $filter_model = null;
        }

        if (isset($this->request->get['filter_price'])) {
            $filter_price = $this->request->get['filter_price'];
        } else {
            $filter_price = null;
        }

        if (isset($this->request->get['filter_quantity'])) {
            $filter_quantity = $this->request->get['filter_quantity'];
        } else {
            $filter_quantity = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['filter_image'])) {
            $filter_image = $this->request->get['filter_image'];
        } else {
            $filter_image = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'pd.name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_image'])) {
            $url .= '&filter_image=' . $this->request->get['filter_image'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['add'] = $this->url->link('account/mpmultivendor/product/add', '' . $url, true);

        $data['delete'] = $this->url->link('account/mpmultivendor/product/delete', '' . $url, true);

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
            'text' => $this->language->get('text_product'),
            'href' => $this->url->link('account/mpmultivendor/product', '', true)
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_image'] = $this->language->get('column_image');
        $data['column_name'] = $this->language->get('column_name');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_model'] = $this->language->get('entry_model');
        $data['entry_price'] = $this->language->get('entry_price');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_image'] = $this->language->get('entry_image');

        $data['button_import_products'] = $this->language->get('button_import_products');
        $data['button_export_products'] = $this->language->get('button_export_products');
        $data['import_products'] = $this->url->link('account/mpmultivendor/import_product');
        $data['export_products'] = $this->url->link('account/mpmultivendor/export_product');

        $data['button_copy'] = $this->language->get('button_copy');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');

        $data['products'] = array();

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
            'filter_name' => $filter_name,
            'filter_model' => $filter_model,
            'filter_price' => $filter_price,
            'filter_quantity' => $filter_quantity,
            'filter_status' => $filter_status,
            'filter_image' => $filter_image,
            'filter_mpseller_id' => $mpseller_id,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('mpmultivendor_seller_list'),
            'limit' => $this->config->get('mpmultivendor_seller_list')
        );

        $this->load->model('tool/image');

        $product_total = $this->model_account_mpmultivendor_product->getTotalSpecialProducts($filter_data);

        $results = $this->model_account_mpmultivendor_product->getSpecialProducts($filter_data);

        foreach ($results as $result) {
            if (is_file(DIR_IMAGE . $result['image'])) {
                $image = $this->model_tool_image->resize($result['image'], 40, 40);
            } else {
                $image = $this->model_tool_image->resize('no_image.png', 40, 40);
            }

            $special = false;

            $product_specials = $this->model_account_mpmultivendor_product->getProductSpecials($result['product_id']);

            foreach ($product_specials as $product_special) {
                if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
                    $special = $product_special['price'];

                    break;
                }
            }

            $data['products'][] = array(
                'product_id' => $result['product_id'],
                'image' => $image,
                'name' => $result['name'],
                'model' => $result['model'],
                'price' => $result['price'],
                'special' => $special,
                'quantity' => $result['quantity'],
                'status' => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit' => $this->url->link('account/mpmultivendor/product/edit', 'product_id=' . $result['product_id'] . $url, true)
            );
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_image'])) {
            $url .= '&filter_image=' . $this->request->get['filter_image'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('account/mpmultivendor/product', 'sort=pd.name' . $url, true);
        $data['sort_model'] = $this->url->link('account/mpmultivendor/product', 'sort=p.model' . $url, true);
        $data['sort_price'] = $this->url->link('account/mpmultivendor/product', 'sort=p.price' . $url, true);
        $data['sort_quantity'] = $this->url->link('account/mpmultivendor/product', 'sort=p.quantity' . $url, true);
        $data['sort_status'] = $this->url->link('account/mpmultivendor/product', 'sort=p.status' . $url, true);
        $data['sort_order'] = $this->url->link('account/mpmultivendor/product', 'sort=p.sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_image'])) {
            $url .= '&filter_image=' . $this->request->get['filter_image'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('mpmultivendor_seller_list');
        $pagination->url = $this->url->link('account/mpmultivendor/product/special', '' . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('mpmultivendor_seller_list')) + 1 : 0, ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) > ($product_total - $this->config->get('mpmultivendor_seller_list'))) ? $product_total : ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) + $this->config->get('mpmultivendor_seller_list')), $product_total, ceil($product_total / $this->config->get('mpmultivendor_seller_list')));

        $data['filter_name'] = $filter_name;
        $data['filter_model'] = $filter_model;
        $data['filter_price'] = $filter_price;
        $data['filter_quantity'] = $filter_quantity;
        $data['filter_status'] = $filter_status;
        $data['filter_image'] = $filter_image;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['mpseller_links'] = $this->load->controller('account/mpmultivendor/mpseller_links');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

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

        //for profile right column
        $data['profile_column_left'] = $this->load->controller('common/profile_column_left');
        $this->response->setOutput($this->load->view('account/mpmultivendor/special_product_list', $data));
    }

    protected function getForm()
    {
        $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');
        if (strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')) {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
        }

        $seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

        if (!empty($seller_info)) {
            $mpseller_id = $seller_info['mpseller_id'];
        } else {
            $mpseller_id = 0;
        }

        $this->document->addScript('catalog/view/javascript/mpseller/seller.js');
        $this->load->model('localisation/currency');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $this->load->model('localisation/area');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_plus'] = $this->language->get('text_plus');
        $data['text_minus'] = $this->language->get('text_minus');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_option'] = $this->language->get('text_option');
        $data['text_option_value'] = $this->language->get('text_option_value');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_percent'] = $this->language->get('text_percent');
        $data['text_amount'] = $this->language->get('text_amount');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_description'] = $this->language->get('entry_description');
        $data['entry_meta_title'] = $this->language->get('entry_meta_title');
        $data['entry_meta_description'] = $this->language->get('entry_meta_description');
        $data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
        $data['entry_keyword'] = $this->language->get('entry_keyword');
        $data['entry_model'] = $this->language->get('entry_model');
        $data['entry_sku'] = $this->language->get('entry_sku');
        $data['entry_upc'] = $this->language->get('entry_upc');
        $data['entry_ean'] = $this->language->get('entry_ean');
        $data['entry_jan'] = $this->language->get('entry_jan');
        $data['entry_isbn'] = $this->language->get('entry_isbn');
        $data['entry_mpn'] = $this->language->get('entry_mpn');
        $data['entry_location'] = $this->language->get('entry_location');
        $data['entry_minimum'] = $this->language->get('entry_minimum');
        $data['entry_shipping'] = $this->language->get('entry_shipping');
        $data['entry_date_available'] = $this->language->get('entry_date_available');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_stock_status'] = $this->language->get('entry_stock_status');
        $data['entry_price'] = $this->language->get('entry_price');
        $data['entry_tax_class'] = $this->language->get('entry_tax_class');
        $data['entry_points'] = $this->language->get('entry_points');
        $data['entry_option_points'] = $this->language->get('entry_option_points');
        $data['entry_subtract'] = $this->language->get('entry_subtract');
        $data['entry_weight_class'] = $this->language->get('entry_weight_class');
        $data['entry_weight'] = $this->language->get('entry_weight');
        $data['entry_dimension'] = $this->language->get('entry_dimension');
        $data['entry_length_class'] = $this->language->get('entry_length_class');
        $data['entry_length'] = $this->language->get('entry_length');
        $data['entry_width'] = $this->language->get('entry_width');
        $data['entry_height'] = $this->language->get('entry_height');
        $data['entry_image'] = $this->language->get('entry_image');
        $data['entry_additional_image'] = $this->language->get('entry_additional_image');
        $data['entry_store'] = $this->language->get('entry_store');
        $data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
        $data['entry_download'] = $this->language->get('entry_download');
        $data['entry_category'] = $this->language->get('entry_category');
        $data['entry_filter'] = $this->language->get('entry_filter');
        $data['entry_related'] = $this->language->get('entry_related');
        $data['entry_attribute'] = $this->language->get('entry_attribute');
        $data['entry_text'] = $this->language->get('entry_text');
        $data['entry_option'] = $this->language->get('entry_option');
        $data['entry_option_value'] = $this->language->get('entry_option_value');
        $data['entry_required'] = $this->language->get('entry_required');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_date_start'] = $this->language->get('entry_date_start');
        $data['entry_date_end'] = $this->language->get('entry_date_end');
        $data['entry_priority'] = $this->language->get('entry_priority');
        $data['entry_tag'] = $this->language->get('entry_tag');
        $data['entry_customer_group'] = $this->language->get('entry_customer_group');
        $data['entry_reward'] = $this->language->get('entry_reward');
        $data['entry_layout'] = $this->language->get('entry_layout');
        $data['entry_recurring'] = $this->language->get('entry_recurring');

        $data['help_keyword'] = $this->language->get('help_keyword');
        $data['help_sku'] = $this->language->get('help_sku');
        $data['help_upc'] = $this->language->get('help_upc');
        $data['help_ean'] = $this->language->get('help_ean');
        $data['help_jan'] = $this->language->get('help_jan');
        $data['help_isbn'] = $this->language->get('help_isbn');
        $data['help_mpn'] = $this->language->get('help_mpn');
        $data['help_minimum'] = $this->language->get('help_minimum');
        $data['help_manufacturer'] = $this->language->get('help_manufacturer');
        $data['help_stock_status'] = $this->language->get('help_stock_status');
        $data['help_points'] = $this->language->get('help_points');
        $data['help_category'] = $this->language->get('help_category');
        $data['help_filter'] = $this->language->get('help_filter');
        $data['help_download'] = $this->language->get('help_download');
        $data['help_related'] = $this->language->get('help_related');
        $data['help_tag'] = $this->language->get('help_tag');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_attribute_add'] = $this->language->get('button_attribute_add');
        $data['button_option_add'] = $this->language->get('button_option_add');
        $data['button_option_value_add'] = $this->language->get('button_option_value_add');
        $data['button_discount_add'] = $this->language->get('button_discount_add');
        $data['button_special_add'] = $this->language->get('button_special_add');
        $data['button_image_add'] = $this->language->get('button_image_add');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_recurring_add'] = $this->language->get('button_recurring_add');

        $data['tab_general'] = $this->language->get('tab_general');
        $data['tab_data'] = $this->language->get('tab_data');
        $data['tab_attribute'] = $this->language->get('tab_attribute');
        $data['tab_option'] = $this->language->get('tab_option');
        $data['tab_discount'] = $this->language->get('tab_discount');
        $data['tab_special'] = $this->language->get('tab_special');
        $data['tab_image'] = $this->language->get('tab_image');
        $data['tab_links'] = $this->language->get('tab_links');
        $data['tab_reward'] = $this->language->get('tab_reward');
        $data['tab_seo'] = $this->language->get('tab_seo');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = array();
        }

        if (isset($this->error['meta_title'])) {
            $data['error_meta_title'] = $this->error['meta_title'];
        } else {
            $data['error_meta_title'] = array();
        }

        if (isset($this->error['model'])) {
            $data['error_model'] = $this->error['model'];
        } else {
            $data['error_model'] = '';
        }

        if (isset($this->error['keyword'])) {
            $data['error_keyword'] = $this->error['keyword'];
        } else {
            $data['error_keyword'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', '', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('account/mpmultivendor/product', '' . $url, true)
        );

        if (!isset($this->request->get['product_id'])) {
            $data['action'] = $this->url->link('account/mpmultivendor/product/add', '' . $url, true);
        } else {
            $data['action'] = $this->url->link('account/mpmultivendor/product/edit', '' . '&product_id=' . $this->request->get['product_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('account/mpmultivendor/product', '' . $url, true);

        if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $product_info = $this->model_account_mpmultivendor_product->getProduct($this->request->get['product_id'], $mpseller_id);
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['product_description'])) {
            $data['product_description'] = $this->request->post['product_description'];
        } elseif (isset($this->request->get['product_id']) && !empty($product_info)) {
            $data['product_description'] = $this->model_account_mpmultivendor_product->getProductDescriptions($this->request->get['product_id']);
        } else {
            $data['product_description'] = array();
        }

        if (isset($this->request->post['model'])) {
            $data['model'] = $this->request->post['model'];
        } elseif (!empty($product_info)) {
            $data['model'] = $product_info['model'];
        } else {
            $data['model'] = '';
        }

        if (isset($this->request->post['sku'])) {
            $data['sku'] = $this->request->post['sku'];
        } elseif (!empty($product_info)) {
            $data['sku'] = $product_info['sku'];
        } else {
            $data['sku'] = '';
        }

        if (isset($this->request->post['upc'])) {
            $data['upc'] = $this->request->post['upc'];
        } elseif (!empty($product_info)) {
            $data['upc'] = $product_info['upc'];
        } else {
            $data['upc'] = '';
        }

        if (isset($this->request->post['ean'])) {
            $data['ean'] = $this->request->post['ean'];
        } elseif (!empty($product_info)) {
            $data['ean'] = $product_info['ean'];
        } else {
            $data['ean'] = '';
        }

        if (isset($this->request->post['jan'])) {
            $data['jan'] = $this->request->post['jan'];
        } elseif (!empty($product_info)) {
            $data['jan'] = $product_info['jan'];
        } else {
            $data['jan'] = '';
        }

        if (isset($this->request->post['isbn'])) {
            $data['isbn'] = $this->request->post['isbn'];
        } elseif (!empty($product_info)) {
            $data['isbn'] = $product_info['isbn'];
        } else {
            $data['isbn'] = '';
        }

        if (isset($this->request->post['mpn'])) {
            $data['mpn'] = $this->request->post['mpn'];
        } elseif (!empty($product_info)) {
            $data['mpn'] = $product_info['mpn'];
        } else {
            $data['mpn'] = '';
        }

        if (isset($this->request->post['location'])) {
            $data['location'] = $this->request->post['location'];
        } elseif (!empty($product_info)) {
            $data['location'] = $product_info['location'];
        } else {
            $data['location'] = '';
        }

        $this->load->model('setting/store');

        $data['stores'] = array();

        $data['stores'][] = array(
            'store_id' => 0,
            'name' => $this->language->get('text_default')
        );

        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            $data['stores'][] = array(
                'store_id' => $store['store_id'],
                'name' => $store['name']
            );
        }

        if (isset($this->request->post['product_store'])) {
            $data['product_store'] = $this->request->post['product_store'];
        } elseif (isset($this->request->get['product_id']) && !empty($product_info)) {
            $data['product_store'] = $this->model_account_mpmultivendor_product->getProductStores($this->request->get['product_id']);
        } else {
            $data['product_store'] = array(0);
        }

        if (isset($this->request->post['shipping'])) {
            $data['shipping'] = $this->request->post['shipping'];
        } elseif (!empty($product_info)) {
            $data['shipping'] = $product_info['shipping'];
        } else {
            $data['shipping'] = 1;
        }

        if (isset($this->request->post['currency'])) {
            $data['currency'] = $this->request->post['currency'];
        } elseif (!empty($product_info)) {
            $data['currency'] = $product_info['currency'];
        } else {
            $data['currency'] = '';
        }

        $this->load->model('localisation/currency');
        if (isset($this->request->post['price'])) {
            $data['price'] = $this->request->post['price'];
//            //load price
            if (!empty($this->request->post['price'])) {

                if(!empty($this->request->post['currency'])) {
                    if($this->request->post['currency'] == 'USD') {
                        $data['price'] = $this->request->post['local_price'];
                    } else {
                        $currData = $this->model_localisation_currency->getCurrencyByCode($this->request->post['currency']);
                        //$usdData = $this->model_localisation_currency->getCurrencyByCode('USD');
                        $dataValue = $this->request->post['local_price'] * $currData['value'];
                        $data['price'] = $dataValue;
                    }
                } else {
                    $data['price'] = $this->request->post['local_price'];
                }
            }
        } elseif (!empty($product_info)) {

            $data['price'] = $product_info['price'];

        } else {
            $data['price'] = '';
        }

        if (isset($this->request->post['local_price'])) {
            $data['local_price'] = $this->request->post['local_price'];
        } elseif (!empty($product_info)) {

            if (!empty($product_info['price'])) {
                if(!empty($product_info['currency'])) {
                    $currData = $this->model_localisation_currency->getCurrencyByCode($product_info['currency']);
                    $dataValue = $product_info['price'] * $currData['value'];
                    $data['local_price'] = ($product_info['currency'] == 'USD') ? $product_info['local_price'] : round($dataValue, 2);
                } else {
                    $data['local_price'] = $this->request->post['local_price'];
                }
            }
            //$data['local_price'] = $product_info['local_price'];
        } else {
            $data['local_price'] = '';
        }

        $data['tax_classes'] = $this->model_account_mpmultivendor_product->getTaxClasses();

        if (isset($this->request->post['tax_class_id'])) {
            $data['tax_class_id'] = $this->request->post['tax_class_id'];
        } elseif (!empty($product_info)) {
            $data['tax_class_id'] = $product_info['tax_class_id'];
        } else {
            $data['tax_class_id'] = 0;
        }

        if (isset($this->request->post['date_available'])) {
            $data['date_available'] = $this->request->post['date_available'];
        } elseif (!empty($product_info)) {
            $data['date_available'] = ($product_info['date_available'] != '0000-00-00') ? $product_info['date_available'] : '';
        } else {
            $data['date_available'] = date('Y-m-d');
        }

        if (isset($this->request->post['show_size_chart'])) {
            $data['show_size_chart'] = $this->request->post['show_size_chart'];
        } elseif (!empty($product_info)) {
            $data['show_size_chart'] = $product_info['show_size_chart'];
        } else {
            $data['show_size_chart'] = '';
        }

        if (isset($this->request->post['quantity'])) {
            $data['quantity'] = $this->request->post['quantity'];
        } elseif (!empty($product_info)) {
            $data['quantity'] = $product_info['quantity'];
        } else {
            $data['quantity'] = 1;
        }

        if (isset($this->request->post['minimum'])) {
            $data['minimum'] = $this->request->post['minimum'];
        } elseif (!empty($product_info)) {
            $data['minimum'] = $product_info['minimum'];
        } else {
            $data['minimum'] = 1;
        }

        if (isset($this->request->post['subtract'])) {
            $data['subtract'] = $this->request->post['subtract'];
        } elseif (!empty($product_info)) {
            $data['subtract'] = $product_info['subtract'];
        } else {
            $data['subtract'] = 1;
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($product_info)) {
            $data['sort_order'] = $product_info['sort_order'];
        } else {
            $data['sort_order'] = 1;
        }

        $data['stock_statuses'] = $this->model_account_mpmultivendor_product->getStockStatuses();

        if (isset($this->request->post['stock_status_id'])) {
            $data['stock_status_id'] = $this->request->post['stock_status_id'];
        } elseif (!empty($product_info)) {
            $data['stock_status_id'] = $product_info['stock_status_id'];
        } else {
            $data['stock_status_id'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($product_info)) {
            $data['status'] = $product_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['weight'])) {
            $data['weight'] = $this->request->post['weight'];
        } elseif (!empty($product_info)) {
            $data['weight'] = $product_info['weight'];
        } else {
            $data['weight'] = '';
        }

        $data['weight_classes'] = $this->model_account_mpmultivendor_product->getWeightClasses();

        if (isset($this->request->post['weight_class_id'])) {
            $data['weight_class_id'] = $this->request->post['weight_class_id'];
        } elseif (!empty($product_info)) {
            $data['weight_class_id'] = $product_info['weight_class_id'];
        } else {
            $data['weight_class_id'] = $this->config->get('config_weight_class_id');
        }

        if (isset($this->request->post['length'])) {
            $data['length'] = $this->request->post['length'];
        } elseif (!empty($product_info)) {
            $data['length'] = $product_info['length'];
        } else {
            $data['length'] = '';
        }

        if (isset($this->request->post['width'])) {
            $data['width'] = $this->request->post['width'];
        } elseif (!empty($product_info)) {
            $data['width'] = $product_info['width'];
        } else {
            $data['width'] = '';
        }

        if (isset($this->request->post['height'])) {
            $data['height'] = $this->request->post['height'];
        } elseif (!empty($product_info)) {
            $data['height'] = $product_info['height'];
        } else {
            $data['height'] = '';
        }

        $data['length_classes'] = $this->model_account_mpmultivendor_product->getLengthClasses();

        if (isset($this->request->post['length_class_id'])) {
            $data['length_class_id'] = $this->request->post['length_class_id'];
        } elseif (!empty($product_info)) {
            $data['length_class_id'] = $product_info['length_class_id'];
        } else {
            $data['length_class_id'] = $this->config->get('config_length_class_id');
        }

        if (isset($this->request->post['manufacturer_id'])) {
            $data['manufacturer_id'] = $this->request->post['manufacturer_id'];
        } elseif (!empty($product_info)) {
            $data['manufacturer_id'] = $product_info['manufacturer_id'];
        } else {
            $data['manufacturer_id'] = 0;
        }

        if (isset($this->request->post['manufacturer'])) {
            $data['manufacturer'] = $this->request->post['manufacturer'];
        } elseif (!empty($product_info)) {
            $manufacturer_info = $this->model_account_mpmultivendor_product->getManufacturer($product_info['manufacturer_id']);

            if ($manufacturer_info) {
                $data['manufacturer'] = $manufacturer_info['name'];
            } else {
                $data['manufacturer'] = '';
            }
        } else {
            $data['manufacturer'] = '';
        }

        // Categories
        if (isset($this->request->post['product_category'])) {
            $categories = $this->request->post['product_category'];
        } elseif (isset($this->request->get['product_id']) && !empty($product_info)) {
            $categories = $this->model_account_mpmultivendor_product->getProductCategories($this->request->get['product_id']);
        } else {
            $categories = array();
        }

        $data['product_categories'] = array();

        foreach ($categories as $category_id) {
            $category_info = $this->model_account_mpmultivendor_product->getCategory($category_id);

            if ($category_info) {
                $data['product_categories'][] = array(
                    'category_id' => $category_info['category_id'],
                    'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
                );
            }
        }

        // Filters

        if (isset($this->request->post['product_filter'])) {
            $filters = $this->request->post['product_filter'];
        } elseif (isset($this->request->get['product_id']) && !empty($product_info)) {
            $filters = $this->model_account_mpmultivendor_product->getProductFilters($this->request->get['product_id']);
        } else {
            $filters = array();
        }

        $data['product_filters'] = array();

        foreach ($filters as $filter_id) {
            $filter_info = $this->model_account_mpmultivendor_product->getFilter($filter_id);

            if ($filter_info) {
                $data['product_filters'][] = array(
                    'filter_id' => $filter_info['filter_id'],
                    'name' => $filter_info['group'] . ' &gt; ' . $filter_info['name']
                );
            }
        }

        // Attributes
        if (isset($this->request->post['product_attribute'])) {
            $product_attributes = $this->request->post['product_attribute'];
        } elseif (isset($this->request->get['product_id']) && !empty($product_info)) {
            $product_attributes = $this->model_account_mpmultivendor_product->getProductAttributes($this->request->get['product_id']);
        } else {
            $product_attributes = array();
        }

        $data['product_attributes'] = array();

        foreach ($product_attributes as $product_attribute) {
            $attribute_info = $this->model_account_mpmultivendor_product->getAttribute($product_attribute['attribute_id']);

            if ($attribute_info) {
                $data['product_attributes'][] = array(
                    'attribute_id' => $product_attribute['attribute_id'],
                    'name' => $attribute_info['name'],
                    'product_attribute_description' => $product_attribute['product_attribute_description']
                );
            }
        }

        // Options
        if (isset($this->request->post['product_option'])) {
            $product_options = $this->request->post['product_option'];
        } elseif (isset($this->request->get['product_id']) && !empty($product_info)) {
            $product_options = $this->model_account_mpmultivendor_product->getProductOptions($this->request->get['product_id']);
        } else {
            $product_options = array();
        }

        $data['product_options'] = array();
        $this->load->model('tool/image');
        foreach ($product_options as $product_option) {
            $product_option_value_data = array();

            if (isset($product_option['product_option_value'])) {
                foreach ($product_option['product_option_value'] as $product_option_value) {

                    //option color images
                    if (!empty($product_option_value['color_image']) && isset($product_option_value['color_image']) && is_file(DIR_IMAGE . $product_option_value['color_image'])) {
                        $product_option_value_image = $this->model_tool_image->resize($product_option_value['color_image'], 100, 100);
                        //$product_option_value_image = $product_option_value['color_image'];
                    } else {
                        $product_option_value_image = $this->model_tool_image->resize('no_image.png', 100, 100);;
                    }

                    $product_option_value_data[] = array(
                        'product_option_value_id' => $product_option_value['product_option_value_id'],
                        'option_value_id' => $product_option_value['option_value_id'],
                        'quantity' => $product_option_value['quantity'],
                        'subtract' => $product_option_value['subtract'],
                        'price' => $product_option_value['price'],
                        'price_prefix' => $product_option_value['price_prefix'],
                        'points' => $product_option_value['points'],
                        'points_prefix' => $product_option_value['points_prefix'],
                        'weight' => $product_option_value['weight'],
                        'color_image_thumb' => $product_option_value_image,
                        'color_image' => $product_option_value['color_image'],
                        'weight_prefix' => $product_option_value['weight_prefix']
                    );
                }
            }

            $data['product_options'][] = array(
                'product_option_id' => $product_option['product_option_id'],
                'product_option_value' => $product_option_value_data,
                'option_id' => $product_option['option_id'],
                'name' => $product_option['name'],
                'type' => $product_option['type'],
                'value' => isset($product_option['value']) ? $product_option['value'] : '',
                'required' => $product_option['required']
            );
        }

        $data['option_values'] = array();

        foreach ($data['product_options'] as $product_option) {
            if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                if (!isset($data['option_values'][$product_option['option_id']])) {
                    $data['option_values'][$product_option['option_id']] = $this->model_account_mpmultivendor_product->getOptionValues($product_option['option_id']);
                }
            }
        }

        $data['customer_groups'] = $this->model_account_mpmultivendor_product->getCustomerGroups();

        if (isset($this->request->post['product_discount'])) {
            $product_discounts = $this->request->post['product_discount'];
        } elseif (isset($this->request->get['product_id']) && !empty($product_info)) {
            $product_discounts = $this->model_account_mpmultivendor_product->getProductDiscounts($this->request->get['product_id']);
        } else {
            $product_discounts = array();
        }

        $data['product_discounts'] = array();

        //echo '<pre>'; print_r($product_discounts); exit('okoko');

        foreach ($product_discounts as $product_discount) {
            $data['product_discounts'][] = array(
                'customer_group_id' => $product_discount['customer_group_id'],
                'quantity' => $product_discount['quantity'],
                'priority' => $product_discount['priority'],
                'local_price' => $product_discount['local_price'],
                'price' => $product_discount['price'],
                'date_start' => ($product_discount['date_start'] != '0000-00-00') ? $product_discount['date_start'] : '',
                'date_end' => ($product_discount['date_end'] != '0000-00-00') ? $product_discount['date_end'] : ''
            );
        }

        if (isset($this->request->post['product_special'])) {
            $product_specials = $this->request->post['product_special'];
        } elseif (isset($this->request->get['product_id']) && !empty($product_info)) {
            $product_specials = $this->model_account_mpmultivendor_product->getProductSpecials($this->request->get['product_id']);
        } else {
            $product_specials = array();
        }

        $data['product_specials'] = array();

        foreach ($product_specials as $product_special) {
            $data['product_specials'][] = array(
                'customer_group_id' => $product_special['customer_group_id'],
                'priority' => $product_special['priority'],
                'price' => $product_special['price'],
                'local_price' => $product_special['local_price'],
                'date_start' => ($product_special['date_start'] != '0000-00-00') ? $product_special['date_start'] : '',
                'date_end' => ($product_special['date_end'] != '0000-00-00') ? $product_special['date_end'] : ''
            );
        }

        // Image
        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($product_info)) {
            $data['image'] = $product_info['image'];
        } else {
            $data['image'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($product_info) && is_file(DIR_IMAGE . $product_info['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        // Images
        if (isset($this->request->post['product_image'])) {
            $product_images = $this->request->post['product_image'];
        } elseif (isset($this->request->get['product_id']) && !empty($product_info)) {
            $product_images = $this->model_account_mpmultivendor_product->getProductImages($this->request->get['product_id']);
        } else {
            $product_images = array();
        }

        $data['product_images'] = array();

        foreach ($product_images as $product_image) {
            if (is_file(DIR_IMAGE . $product_image['image'])) {
                $image = $product_image['image'];
                $thumb = $product_image['image'];
            } else {
                $image = '';
                $thumb = 'no_image.png';
            }

            $data['product_images'][] = array(
                'image' => $image,
                'thumb' => $this->model_tool_image->resize($thumb, 100, 100),
                'sort_order' => $product_image['sort_order']
            );
        }

        // Downloads
        if (isset($this->request->post['product_download'])) {
            $product_downloads = $this->request->post['product_download'];
        } elseif (isset($this->request->get['product_id']) && !empty($product_info)) {
            $product_downloads = $this->model_account_mpmultivendor_product->getProductDownloads($this->request->get['product_id']);
        } else {
            $product_downloads = array();
        }

        $data['product_downloads'] = array();

        foreach ($product_downloads as $download_id) {
            $download_info = $this->model_account_mpmultivendor_product->getDownload($download_id, $mpseller_id);

            if ($download_info) {
                $data['product_downloads'][] = array(
                    'download_id' => $download_info['download_id'],
                    'name' => $download_info['name']
                );
            }
        }

        if (isset($this->request->post['points'])) {
            $data['points'] = $this->request->post['points'];
        } elseif (!empty($product_info)) {
            $data['points'] = $product_info['points'];
        } else {
            $data['points'] = '';
        }

        if (isset($this->request->post['product_reward'])) {
            $data['product_reward'] = $this->request->post['product_reward'];
        } elseif (isset($this->request->get['product_id']) && !empty($product_info)) {
            $data['product_reward'] = $this->model_account_mpmultivendor_product->getProductRewards($this->request->get['product_id']);
        } else {
            $data['product_reward'] = array();
        }

//        if (isset($this->request->post['product_seo_url'])) {
//            $data['product_seo_url'] = $this->request->post['product_seo_url'];
//        } elseif (isset($this->request->get['product_id'])) {
//            $data['product_seo_url'] = $this->model_account_mpmultivendor_product->getProductSeoUrls($this->request->get['product_id']);
//        } else {
//            $data['product_seo_url'] = array();
//        }


        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $data['currencies'] = $this->model_localisation_currency->getCurrencies();

        /*$data['countries'] = $this->model_localisation_country->getCountries();
        if(!empty($this->request->get['product_id'])) {
            $data['my_countries'] = array();
            $my_countries = $this->model_localisation_country->getProductLocation($this->request->get['product_id']);
            foreach ($my_countries as $country_id) {
                $country_info = $this->model_localisation_country->getCountry($country_id['country_id']);
                if ($country_info) {
                    $data['my_countries'][] = array(
                        'country_id' => $country_info['country_id'],
                        'name' => $country_info['name']
                    );
                }
            }
        }*/

        $locationArray = [];
        if (!empty($this->request->get['product_id'])) {
            $productLocations = $this->model_localisation_country->getProductLocation($this->request->get['product_id']);
            foreach ($productLocations as $location) {
                $dataArray = array();
                $dataArray['country_id'] = $location['country_id'];
                $dataArray['zone_id'] = $location['zone_id'];
                $dataArray['zones'] = $this->model_localisation_zone->getZonesByCountryId($location['country_id']);
                $locationArray[] = $dataArray;
            }
        }
        $data['productLocations'] = $locationArray;
        //echo '<pre>';print_r(count($data['productLocations']));exit('okok');
        $data['countries'] = $this->model_localisation_country->getCountries();

//        $delivery = [];
//        if (isset($this->request->get['product_id'])) {
//            $deliveryInfos = $this->model_account_mpmultivendor_product->getDeliveryLocations($this->request->get['product_id']);
//            foreach ($deliveryInfos as $info) {
//                $deliveryArray = array();
//                $deliveryArray['country_id'] = $info['country_id'];
//                $deliveryArray['zone_id'] = $info['zone_id'];
//                $deliveryArray['zones'] = $this->model_localisation_zone->getZonesByCountryId($info['country_id']);
//                $deliveryArray['area_id'] = $info['area_id'];
//                $deliveryArray['areas'] = $this->model_localisation_area->getAreasByZoneId($info['zone_id']);
//                $deliveryArray['days'] = $info['days'];
//                $deliveryArray['rate_per_hour'] = $info['rate_per_hour'];
//                $delivery[] = $deliveryArray;
//            }
//        }
//        $data['deliveryInfos'] = $delivery;

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

        //for profile right column
        $data['profile_column_left'] = $this->load->controller('common/profile_column_left');
        if (VERSION < '2.2.0.0') {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/mpmultivendor/product_form.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/mpmultivendor/product_form.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/account/mpmultivendor/product_form.tpl', $data));
            }
        } else {
            $this->response->setOutput($this->load->view('account/mpmultivendor/product_form', $data));
        }
    }

    protected function validateForm()
    {
        foreach ($this->request->post['product_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 255)) {
                $this->error['name'][$language_id] = $this->language->get('error_name');
            }

            if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
                $this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
            }
        }

//        if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
//            $this->error['model'] = $this->language->get('error_model');
//        }

//        if ($this->request->post['product_seo_url']) {
//            foreach ($this->request->post['product_seo_url'] as $store_id => $language) {
//                foreach ($language as $language_id => $keyword) {
//                    if (!empty($keyword)) {
//                        if (count(array_keys($language, $keyword)) > 1) {
//                            $this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
//                        }
//
//                        $seo_urls = $this->model_account_mpmultivendor_product->getSeoUrlsByKeyword($keyword);
//
//                        foreach ($seo_urls as $seo_url) {
//                            if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['product_id']) || (($seo_url['query'] != 'product_id=' . $this->request->get['product_id'])))) {
//                                $this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
//
//                                break;
//                            }
//                        }
//                    }
//                }
//            }
//        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    public function autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
            $this->load->model('account/mpmultivendor/product');
            $this->load->model('account/mpmultivendor/seller');

            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }

            if (isset($this->request->get['filter_model'])) {
                $filter_model = $this->request->get['filter_model'];
            } else {
                $filter_model = '';
            }

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = 5;
            }

            $seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

            if (!empty($seller_info)) {
                $mpseller_id = $seller_info['mpseller_id'];
            } else {
                $mpseller_id = 0;
            }

            $filter_data = array(
                'filter_name' => $filter_name,
                'filter_model' => $filter_model,
                'filter_mpseller_id' => $mpseller_id,
                'start' => 0,
                'limit' => $limit
            );

            $results = $this->model_account_mpmultivendor_product->getProducts($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'product_id' => $result['product_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                    'model' => $result['model'],
                    'price' => $result['price']
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function manufacturer_autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('account/mpmultivendor/product');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_account_mpmultivendor_product->getManufacturers($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'manufacturer_id' => $result['manufacturer_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function category_autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('account/mpmultivendor/product');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'sort' => 'name',
                'order' => 'ASC',
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_account_mpmultivendor_product->getCategories($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'category_id' => $result['category_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function filter_autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('account/mpmultivendor/product');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start' => 0,
                'limit' => 5
            );

            $filters = $this->model_account_mpmultivendor_product->getFilters($filter_data);

            foreach ($filters as $filter) {
                $json[] = array(
                    'filter_id' => $filter['filter_id'],
                    'name' => strip_tags(html_entity_decode($filter['group'] . ' &gt; ' . $filter['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function download_autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('account/mpmultivendor/seller');

            $this->load->model('account/mpmultivendor/download');

            $seller_info = $this->model_account_mpmultivendor_seller->getSellerStoreInfo($this->customer->isLogged());

            if (!empty($seller_info)) {
                $mpseller_id = $seller_info['mpseller_id'];
            } else {
                $mpseller_id = 0;
            }

            $filter_data = array(
                'mpseller_id' => $mpseller_id,
                'filter_name' => $this->request->get['filter_name'],
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_account_mpmultivendor_download->getDownloads($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'download_id' => $result['download_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function attribute_autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('account/mpmultivendor/product');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_account_mpmultivendor_product->getAttributes($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'attribute_id' => $result['attribute_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                    'attribute_group' => $result['attribute_group']
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function option_autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->language('account/mpmultivendor/product');

            $this->load->model('account/mpmultivendor/product');

            $this->load->model('tool/image');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start' => 0,
                'limit' => 5
            );

            $options = $this->model_account_mpmultivendor_product->getOptions($filter_data);

            foreach ($options as $option) {
                $option_value_data = array();

                if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                    $option_values = $this->model_account_mpmultivendor_product->getOptionValues($option['option_id']);

                    foreach ($option_values as $option_value) {
                        if (is_file(DIR_IMAGE . $option_value['image'])) {
                            $image = $this->model_tool_image->resize($option_value['image'], 50, 50);
                        } else {
                            $image = $this->model_tool_image->resize('no_image.png', 50, 50);
                        }

                        $option_value_data[] = array(
                            'option_value_id' => $option_value['option_value_id'],
                            'name' => strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8')),
                            'image' => $image
                        );
                    }

                    $sort_order = array();

                    foreach ($option_value_data as $key => $value) {
                        $sort_order[$key] = $value['name'];
                    }

                    array_multisort($sort_order, SORT_ASC, $option_value_data);
                }

                $type = '';

                if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
                    $type = $this->language->get('text_choose');
                }

                if ($option['type'] == 'text' || $option['type'] == 'textarea') {
                    $type = $this->language->get('text_input');
                }

                if ($option['type'] == 'file') {
                    $type = $this->language->get('text_file');
                }

                if ($option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                    $type = $this->language->get('text_date');
                }

                $json[] = array(
                    'option_id' => $option['option_id'],
                    'name' => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
                    'category' => $type,
                    'type' => $option['type'],
                    'option_value' => $option_value_data
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getAllPrices()
    {

        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            $post = $this->request->post;
            $currencyCode = $post['currencyCode'];
            $currencyAmt = str_replace( ',', '', $post['currencyAmt']);
            $json = [];

            $this->load->model('localisation/currency');
            $currencies = $this->model_localisation_currency->getCurrencies();
            $fetchData = $this->model_localisation_currency->getCurrencyByCode($currencyCode);

            foreach ($currencies as $currency) {
                //echo $currency['value'].'<br>';
                $data = [];
                //$data['value'] = $currency['value'];
                $data['code'] = $currency['code'];

                if ($currencyCode == $currency['code']) {
                    $data['amount'] = round($currencyAmt, 4);
                } else {
                    if ($currencyCode == 'USD') {
                        $amount = $currency['value'] * $currencyAmt;
                        $data['amount'] = round($amount, 4);
                    } else {
                        //$data['amount'] = ($currencyAmt * $currency['value']) / $fetchData['value'];
//                        $data['currencyAmt'] =  $currencyAmt;
//                        $data['currencyValue'] =  $currency['value'];
//                        $data['fetchData'] =  $fetchData['value'];

                        $amount = $currencyAmt * $currency['value'] / $fetchData['value'];
                        $data['amount'] = round($amount, 4);
                    }
                }
                $json[] = $data;
            }
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }
    }
}