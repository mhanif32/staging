<?php

class ControllerAccountEnquiries extends Controller
{
    public function index()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/enquiries', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        if (!$this->config->get('mpmultivendor_status')) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }

        $this->load->language('account/enquiries');

        $this->load->model('account/enquiries');

        $this->load->model('account/mpmultivendor/orders');

        $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

        if (strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')) {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
        }

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'date_modified';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . urlencode(html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8'));
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
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('account/enquiries', '' . $url, true)
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_no_results'] = $this->language->get('text_no_results');

        $data['entry_store'] = $this->language->get('entry_store');
        $data['entry_message'] = $this->language->get('entry_message');
        $data['entry_date_added'] = $this->language->get('entry_date_added');
        $data['entry_date_modified'] = $this->language->get('entry_date_modified');
        $data['entry_action'] = $this->language->get('entry_action');

        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_view'] = $this->language->get('button_view');
        $data['button_view_store'] = $this->language->get('button_view_store');

        $filter_data = array(
            'filter_customer_id' => $this->customer->getId(),
            'filter_date_added' => $filter_date_added,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('mpmultivendor_seller_list'),
            'limit' => $this->config->get('mpmultivendor_seller_list'),
        );

        $total_enquiries = $this->model_account_enquiries->getTotalEnquiries($filter_data);

        $enquiries = $this->model_account_enquiries->getEnquiries($filter_data);

        $data['enquiries'] = array();

        foreach ($enquiries as $enquiry) {
            $seller_info = $this->model_account_mpmultivendor_orders->getMpseller($enquiry['mpseller_id']);
            if ($seller_info) {
                $store_name = $seller_info['store_name'];
                $view_store = $this->url->link('mpmultivendor/store', 'mpseller_id=' . $seller_info['mpseller_id'], true);
            } else {
                $store_name = '';
                $view_store = '';
            }
            $data['enquiries'][] = array(
                'store_name' => $store_name,
                'message' => utf8_substr(strip_tags($enquiry['message']), 0, 150) . '..',
                'date_added' => date('d M Y h:i A', strtotime($enquiry['date_added'])),
                'date_modified' => date('d M Y h:i A', strtotime($enquiry['date_modified'])),
                'view_store' => $view_store,
                'view' => $this->url->link('account/enquiries/view', 'enquiry_id=' . $enquiry['mpseller_enquiry_id'], true),
            );
        }

        $url = '';

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $total_enquiries;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('mpmultivendor_seller_list');
        $pagination->url = $this->url->link('account/enquiries', '' . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($total_enquiries) ? (($page - 1) * $this->config->get('mpmultivendor_seller_list')) + 1 : 0, ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) > ($total_enquiries - $this->config->get('mpmultivendor_seller_list'))) ? $total_enquiries : ((($page - 1) * $this->config->get('mpmultivendor_seller_list')) + $this->config->get('mpmultivendor_seller_list')), $total_enquiries, ceil($total_enquiries / $this->config->get('mpmultivendor_seller_list')));

        $data['filter_date_added'] = $filter_date_added;

        $data['mpseller_links'] = $this->load->controller('account/mpmultivendor/mpseller_links');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $data['custom_themename'] = $this->model_account_mpmultivendor_seller->getactiveTheme();

        $this->load->model('mpmultivendor/mv_seller');
        $data['sellers'] = $this->model_mpmultivendor_mv_seller->getSellers();

        if (VERSION < '2.2.0.0') {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/enquiries.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/enquiries.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/account/enquiries.tpl', $data));
            }
        } else {
            $this->response->setOutput($this->load->view('account/enquiries', $data));
        }
    }

    public function view()
    {
        if (isset($this->request->get['enquiry_id'])) {
            $data['enquiry_id'] = $enquiry_id = $this->request->get['enquiry_id'];
        } else {
            $data['enquiry_id'] = $enquiry_id = 0;
        }

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/enquiries/view', 'enquiry_id=' . $enquiry_id, true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        if (!$this->config->get('mpmultivendor_status')) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }

        $this->load->language('account/enquiries_chat');

        $this->load->model('account/enquiries');

        $this->load->model('account/mpmultivendor/orders');

        $this->load->model('tool/image');

        $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor.css');

        if (strpos($this->config->get('config_template'), 'journal2') === 0 || defined('JOURNAL3_ACTIVE')) {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/mpmultivendor-journal.css');
        }

        $this->document->setTitle($this->language->get('heading_title'));


        $page_list = 30;

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
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
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('account/enquiries', '', true)
        );

        $customer_id = $this->customer->getId();

        $enquiry_info = $this->model_account_enquiries->getEnquiry($customer_id, $enquiry_id);

        if ($enquiry_info) {
            $data['text_contact_details'] = $this->language->get('text_contact_details');
            $data['text_enquiry_details'] = $this->language->get('text_enquiry_details');
            $data['text_name'] = $this->language->get('text_name');
            $data['text_email'] = $this->language->get('text_email');
            $data['text_date_added'] = $this->language->get('text_date_added');
            $data['text_date_modified'] = $this->language->get('text_date_modified');
            $data['text_message'] = $this->language->get('text_message');

            $data['entry_message'] = $this->language->get('entry_message');

            $data['button_submit'] = $this->language->get('button_submit');

            $data['customer_name'] = $enquiry_info['name'];
            $data['customer_email'] = $enquiry_info['email'];
            $data['customer_message'] = $enquiry_info['message'];
            $data['date_added'] = date('d M Y h:i A', strtotime($enquiry_info['date_added']));
            $data['date_modified'] = date('d M Y h:i A', strtotime($enquiry_info['date_modified']));

            $seller_info = $this->model_account_mpmultivendor_orders->getMpseller($enquiry_info['mpseller_id']);
            if ($this->config->get('mpmultivendor_seller_name')) {
                $seller_to_store = $seller_info['store_owner'];
            } else {
                $seller_to_store = $seller_info['store_name'];
            }

            $data['text_enquiryby'] = sprintf($this->language->get('text_enquiryby'), $seller_to_store);

            $total_chats = $this->model_account_enquiries->getTotalEnquiryChats($customer_id, $enquiry_id);
            $chats = $this->model_account_enquiries->getEnquiryChats($customer_id, $enquiry_id, ($page - 1) * $page_list, $page_list);

            $data['chats'] = array();
            foreach ($chats as $chat) {
                if ($chat['from'] == 'seller') {
                    if ($this->config->get('mpmultivendor_seller_name')) {
                        $from_name = $seller_info['store_owner'];
                        $from_store = $seller_info['store_name'];
                    } else {
                        $from_name = $seller_info['store_name'];
                        $from_store = '';
                    }

                    if ($seller_info['image']) {
                        $from_image = $this->model_tool_image->resize($seller_info['image'], 40, 40);
                    } else {
                        $from_image = $this->model_tool_image->resize('nouserpic.png', 40, 40);
                    }

                    $from_type_name = $this->language->get('text_seller_type');
                } else {
                    $from_name = $chat['name'];
                    $from_store = '';
                    $from_image = $this->model_tool_image->resize('nouserpic.png', 40, 40);
                    $from_type_name = $this->language->get('text_customer_type');
                }

                $data['chats'][] = array(
                    'message' => $chat['message'],
                    'from' => $chat['from'],
                    'from_name' => $from_name,
                    'from_store' => $from_store,
                    'from_type_name' => $from_type_name,
                    'from_image' => $from_image,
                    'date_added' => date('d M Y h:i A', strtotime($chat['date_added'])),
                );
            }

            $data['mpseller_links'] = $this->load->controller('account/mpmultivendor/mpseller_links');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $data['custom_themename'] = $this->model_account_mpmultivendor_seller->getactiveTheme();

            $pagination = new Pagination();
            $pagination->total = $total_chats;
            $pagination->page = $page;
            $pagination->limit = $page_list;
            $pagination->url = $this->url->link('account/enquiries/view', 'enquiry_id=' . $enquiry_id . '&page={page}', true);

            $data['pagination'] = $pagination->render();

            $data['results'] = sprintf($this->language->get('text_pagination'), ($total_chats) ? (($page - 1) * $page_list) + 1 : 0, ((($page - 1) * $page_list) > ($total_chats - $page_list)) ? $total_chats : ((($page - 1) * $page_list) + $page_list), $total_chats, ceil($total_chats / $page_list));

            if (VERSION < '2.2.0.0') {
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/enquiries_chat.tpl')) {
                    $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/enquiries_chat.tpl', $data));
                } else {
                    $this->response->setOutput($this->load->view('default/template/account/enquiries_chat.tpl', $data));
                }
            } else {
                $this->response->setOutput($this->load->view('account/enquiries_chat', $data));
            }
        } else {
            $this->document->setTitle($this->language->get('text_enquiry'));

            $data['heading_title'] = $this->language->get('text_enquiry');

            $data['text_error'] = $this->language->get('text_error');

            $data['button_continue'] = $this->language->get('button_continue');

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
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('account/enquiries', '', true)
            );

            $data['continue'] = $this->url->link('account/enquiries', '', true);

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            if (VERSION < '2.2.0.0') {
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
                    $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/error/not_found.tpl', $data));
                } else {
                    $this->response->setOutput($this->load->view('default/template/error/not_found.tpl', $data));
                }
            } else {
                $this->response->setOutput($this->load->view('error/not_found', $data));
            }
        }
    }

    public function Submitreply()
    {
        $json = array();

        $this->load->language('account/enquiries_chat');

        $this->load->model('account/enquiries');

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['enquiry_id'])) {
            $enquiry_id = $this->request->get['enquiry_id'];
        } else {
            $enquiry_id = 0;
        }

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/enquiries/view', 'enquiry_id=' . $enquiry_id, true);

            $json['redirect'] = $this->url->link('account/login', '', true);
        }

        $customer_id = $this->customer->getId();

        $enquiry_info = $this->model_account_enquiries->getEnquiry($customer_id, $enquiry_id);
        if (!$enquiry_info) {
            $json['warning'] = $this->language->get('error_enquirynot_found');
        }

        if (utf8_strlen($this->request->post['message']) < 2) {
            $json['warning'] = $this->language->get('error_message');
        }

        if (!$json) {
            $add_data = array(
                'enquiry_id' => $enquiry_id,
                'mpseller_id' => $enquiry_info['mpseller_id'],
                'customer_id' => $enquiry_info['customer_id'],
                'customer_name' => $enquiry_info['name'],
                'customer_email' => $enquiry_info['email'],
                'from' => 'customer',
                'message' => isset($this->request->post['message']) ? $this->request->post['message'] : '',
            );

            $this->model_account_enquiries->addEnquiryChat($add_data);

            $json['success'] = true;

            $json['redirect'] = str_replace('&amp;', '&', $this->url->link('account/enquiries/view', '&enquiry_id=' . $enquiry_id . $url, true));
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function create()
    {
        if (!$this->customer->isLogged()) {
            //$this->session->data['redirect'] = $this->url->link('account/enquiries/view', 'enquiry_id=' . $enquiry_id, true);

            $json['redirect'] = $this->url->link('account/login', '', true);
        }

        $json = array();

        $this->load->model('mpmultivendor/mv_seller');

        $this->load->language('mpmultivendor/store_profile');
//print_r($this->request->post['seller_id']);exit();
        if (empty($this->request->post['seller_id'])) {
            $json['error'] = $this->language->get('error_seller_id');
        }

        if (utf8_strlen(trim($this->request->post['message'])) < 10) {
            $json['error'] = $this->language->get('error_message');
        }

        if (!$json) {
            $enquiry_data = array(
                'mpseller_id' => $this->request->post['seller_id'],
                'customer_id' => $this->customer->getId(),
                'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
                'email' => $this->customer->getEmail(),
                'message' => $this->request->post['message'],
            );
            $this->model_mpmultivendor_mv_seller->sendEnquiry($enquiry_data);

            $json['success'] = $this->language->get('success_send_enquiry');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}