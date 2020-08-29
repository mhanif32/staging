<?php

class ControllerMpmultivendorMpseller extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('mpmultivendor/mpseller');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('mpmultivendor/mpseller');

        $this->getList();
    }

    public function pendingUsers()
    {

        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        $this->load->language('mpmultivendor/mpseller');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('mpmultivendor/mpseller');

        $this->getPendingUserList();
    }

    public function edit()
    {
        $this->load->language('mpmultivendor/mpseller');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('mpmultivendor/mpseller');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_mpmultivendor_mpseller->editMpseller($this->request->get['mpseller_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_store_owner'])) {
                $url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_store_name'])) {
                $url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_approved'])) {
                $url .= '&filter_approved=' . $this->request->get['filter_approved'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

            $this->response->redirect($this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete()
    {
        $this->load->language('mpmultivendor/mpseller');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('mpmultivendor/mpseller');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $mpseller_id) {
                $this->model_mpmultivendor_mpseller->deleteMpseller($mpseller_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_store_owner'])) {
                $url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_store_name'])) {
                $url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_approved'])) {
                $url .= '&filter_approved=' . $this->request->get['filter_approved'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

            $this->response->redirect($this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    public function approve()
    {
        $this->load->language('mpmultivendor/mpseller');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('mpmultivendor/mpseller');

        $mpsellers = array();

        if (isset($this->request->post['selected'])) {
            $mpsellers = $this->request->post['selected'];
        } elseif (isset($this->request->get['mpseller_id'])) {
            $mpsellers[] = $this->request->get['mpseller_id'];
        }

        if ($mpsellers && $this->validateApprove()) {
            $this->model_mpmultivendor_mpseller->approve($this->request->get['mpseller_id']);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_store_owner'])) {
                $url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_store_name'])) {
                $url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_approved'])) {
                $url .= '&filter_approved=' . $this->request->get['filter_approved'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

            $this->response->redirect($this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList()
    {
        $this->document->addStyle('view/stylesheet/mpmultivendor/mpmultivendor.css');

        if (isset($this->request->get['filter_store_owner'])) {
            $filter_store_owner = $this->request->get['filter_store_owner'];
        } else {
            $filter_store_owner = null;
        }

        if (isset($this->request->get['filter_store_name'])) {
            $filter_store_name = $this->request->get['filter_store_name'];
        } else {
            $filter_store_name = null;
        }

        if (isset($this->request->get['filter_email'])) {
            $filter_email = $this->request->get['filter_email'];
        } else {
            $filter_email = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['filter_approved'])) {
            $filter_approved = $this->request->get['filter_approved'];
        } else {
            $filter_approved = null;
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
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

        if (isset($this->request->get['filter_store_owner'])) {
            $url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_store_name'])) {
            $url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_approved'])) {
            $url .= '&filter_approved=' . $this->request->get['filter_approved'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['delete'] = $this->url->link('mpmultivendor/mpseller/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['mpsellers'] = array();

        $filter_data = array(
            'filter_store_owner' => $filter_store_owner,
            'filter_store_name' => $filter_store_name,
            'filter_email' => $filter_email,
            'filter_status' => $filter_status,
            'filter_approved' => $filter_approved,
            'filter_date_added' => $filter_date_added,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $seller_total = $this->model_mpmultivendor_mpseller->getTotalMpsellers($filter_data);

        $results = $this->model_mpmultivendor_mpseller->getMpsellers($filter_data);

        foreach ($results as $result) {
            if (!$result['approved']) {
                $approve = $this->url->link('mpmultivendor/mpseller/approve', 'user_token=' . $this->session->data['user_token'] . '&mpseller_id=' . $result['mpseller_id'] . $url, true);
            } else {
                $approve = '';
            }

            $data['mpsellers'][] = array(
                'mpseller_id' => $result['mpseller_id'],
                'customer_id' => $result['customer_id'],
                'store_owner' => $result['store_owner'],
                'store_name' => $result['store_name'],
                'total_products' => $result['total_products'],
                'email' => $result['email'],
                'status' => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'approve' => $approve,
                'message_link' => $this->url->link('mpmultivendor/mpseller_message/view', 'user_token=' . $this->session->data['user_token'] . '&mpseller_id=' . $result['mpseller_id'] . $url, true),
                'edit' => $this->url->link('mpmultivendor/mpseller/edit', 'user_token=' . $this->session->data['user_token'] . '&mpseller_id=' . $result['mpseller_id'] . $url, true)
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_store_owner'] = $this->language->get('column_store_owner');
        $data['column_store_name'] = $this->language->get('column_store_name');
        $data['column_products'] = $this->language->get('column_products');
        $data['column_email'] = $this->language->get('column_email');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_approved'] = $this->language->get('column_approved');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_store_name'] = $this->language->get('entry_store_name');
        $data['entry_store_owner'] = $this->language->get('entry_store_owner');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_approved'] = $this->language->get('entry_approved');
        $data['entry_date_added'] = $this->language->get('entry_date_added');

        $data['button_message'] = $this->language->get('button_message');
        $data['button_approve'] = $this->language->get('button_approve');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_login'] = $this->language->get('button_login');

        $data['user_token'] = $this->session->data['user_token'];

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

        if (isset($this->request->get['filter_store_owner'])) {
            $url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_store_name'])) {
            $url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_approved'])) {
            $url .= '&filter_approved=' . $this->request->get['filter_approved'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_store_owner'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.store_owner' . $url, true);
        $data['sort_store_name'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.store_name' . $url, true);
        $data['sort_total_products'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.total_products' . $url, true);
        $data['sort_email'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.email' . $url, true);
        $data['sort_status'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.status' . $url, true);
        $data['sort_date_added'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.date_added' . $url, true);
        $data['sort_products'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=total_products' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_store_owner'])) {
            $url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_store_name'])) {
            $url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_approved'])) {
            $url .= '&filter_approved=' . $this->request->get['filter_approved'];
        }

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
        $pagination->total = $seller_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($seller_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($seller_total - $this->config->get('config_limit_admin'))) ? $seller_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $seller_total, ceil($seller_total / $this->config->get('config_limit_admin')));

        $data['filter_store_owner'] = $filter_store_owner;
        $data['filter_store_name'] = $filter_store_name;
        $data['filter_email'] = $filter_email;
        $data['filter_status'] = $filter_status;
        $data['filter_approved'] = $filter_approved;
        $data['filter_date_added'] = $filter_date_added;

        $this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->config->set('template_engine', 'template');
        $this->response->setOutput($this->load->view('mpmultivendor/mpseller_list', $data));
    }

    protected function getPendingUserList()
    {
        $this->document->addStyle('view/stylesheet/mpmultivendor/mpmultivendor.css');

        if (isset($this->request->get['filter_store_owner'])) {
            $filter_store_owner = $this->request->get['filter_store_owner'];
        } else {
            $filter_store_owner = null;
        }

        if (isset($this->request->get['filter_store_name'])) {
            $filter_store_name = $this->request->get['filter_store_name'];
        } else {
            $filter_store_name = null;
        }

        if (isset($this->request->get['filter_email'])) {
            $filter_email = $this->request->get['filter_email'];
        } else {
            $filter_email = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['filter_approved'])) {
            $filter_approved = $this->request->get['filter_approved'];
        } else {
            $filter_approved = null;
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
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

        if (isset($this->request->get['filter_store_owner'])) {
            $url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_store_name'])) {
            $url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_approved'])) {
            $url .= '&filter_approved=' . $this->request->get['filter_approved'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => 'Sellers Registration Pending Approval',
            'href' => $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['delete'] = $this->url->link('mpmultivendor/mpseller/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['mpsellers'] = array();

        $filter_data = array(
            'filter_store_owner' => $filter_store_owner,
            'filter_store_name' => $filter_store_name,
            'filter_email' => $filter_email,
            'filter_status' => $filter_status,
            'filter_approved' => $filter_approved,
            'filter_date_added' => $filter_date_added,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $seller_total = $this->model_mpmultivendor_mpseller->getTotalPendingSeller($filter_data);

        $results = $this->model_mpmultivendor_mpseller->getPendingMpsellers($filter_data);

        foreach ($results as $result) {
//            if (!$result['approved']) {
//                $approve = $this->url->link('mpmultivendor/mpseller/approve', 'user_token=' . $this->session->data['user_token'] . '&mpseller_id=' . $result['mpseller_id'] . $url, true);
//            } else {
//                $approve = '';
//            }

            $data['mpsellers'][] = array(
                'customer_id' => $result['customer_id'],
                'firstname' => $result['firstname'],
                'lastname' => $result['lastname'],
                'total_products' => 0,
                'email' => $result['email'],
                'status' => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                //'approve'        => $approve,
                //'message_link'           => $this->url->link('mpmultivendor/mpseller_message/view', 'user_token=' . $this->session->data['user_token'] . '&mpseller_id=' . $result['mpseller_id'] . $url, true),
                //'edit'           => $this->url->link('mpmultivendor/mpseller/edit', 'user_token=' . $this->session->data['user_token'] . '&mpseller_id=' . $result['mpseller_id'] . $url, true)
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_store_owner'] = $this->language->get('column_store_owner');
        $data['column_store_name'] = $this->language->get('column_store_name');
        $data['column_products'] = $this->language->get('column_products');
        $data['column_email'] = $this->language->get('column_email');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_approved'] = $this->language->get('column_approved');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_store_name'] = $this->language->get('entry_store_name');
        $data['entry_store_owner'] = $this->language->get('entry_store_owner');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_approved'] = $this->language->get('entry_approved');
        $data['entry_date_added'] = $this->language->get('entry_date_added');

        $data['button_message'] = $this->language->get('button_message');
        $data['button_approve'] = $this->language->get('button_approve');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_login'] = $this->language->get('button_login');

        $data['user_token'] = $this->session->data['user_token'];

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

        if (isset($this->request->get['filter_store_owner'])) {
            $url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_store_name'])) {
            $url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_approved'])) {
            $url .= '&filter_approved=' . $this->request->get['filter_approved'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_store_owner'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.store_owner' . $url, true);
        $data['sort_store_name'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.store_name' . $url, true);
        $data['sort_total_products'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.total_products' . $url, true);
        $data['sort_email'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.email' . $url, true);
        $data['sort_status'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.status' . $url, true);
        $data['sort_date_added'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=mps.date_added' . $url, true);
        $data['sort_products'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . '&sort=total_products' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_store_owner'])) {
            $url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_store_name'])) {
            $url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_approved'])) {
            $url .= '&filter_approved=' . $this->request->get['filter_approved'];
        }

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
        $pagination->total = $seller_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($seller_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($seller_total - $this->config->get('config_limit_admin'))) ? $seller_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $seller_total, ceil($seller_total / $this->config->get('config_limit_admin')));

        $data['filter_store_owner'] = $filter_store_owner;
        $data['filter_store_name'] = $filter_store_name;
        $data['filter_email'] = $filter_email;
        $data['filter_status'] = $filter_status;
        $data['filter_approved'] = $filter_approved;
        $data['filter_date_added'] = $filter_date_added;

        $this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->config->set('template_engine', 'template');
        $this->response->setOutput($this->load->view('mpmultivendor/mpseller_pending_list', $data));
    }

    protected function getForm()
    {
        $this->document->addStyle('view/stylesheet/mpmultivendor/mpmultivendor.css');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_keyword'] = $this->language->get('text_keyword');

        $data['text_form'] = !isset($this->request->get['mpseller_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['text_paypal'] = $this->language->get('text_paypal');
        $data['text_bank_transfer'] = $this->language->get('text_bank_transfer');
        $data['text_cheque'] = $this->language->get('text_cheque');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_order_wise'] = $this->language->get('text_order_wise');
        $data['text_product_wise'] = $this->language->get('text_product_wise');
        $data['text_total_commission'] = $this->language->get('text_total_commission');
        $data['text_total_paid'] = $this->language->get('text_total_paid');
        $data['text_total_balance'] = $this->language->get('text_total_balance');

        $data['entry_description'] = $this->language->get('entry_description');
        $data['entry_meta_description'] = $this->language->get('entry_meta_description');
        $data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
        $data['entry_store_owner'] = $this->language->get('entry_store_owner');
        $data['entry_store_name'] = $this->language->get('entry_store_name');
        $data['entry_address'] = $this->language->get('entry_address');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_telephone'] = $this->language->get('entry_telephone');
        $data['entry_fax'] = $this->language->get('entry_fax');
        $data['entry_seo_keyword'] = $this->language->get('entry_seo_keyword');
        $data['entry_review_seo_keyword'] = $this->language->get('entry_review_seo_keyword');
        $data['entry_city'] = $this->language->get('entry_city');
        $data['entry_zone'] = $this->language->get('entry_zone');
        $data['entry_country'] = $this->language->get('entry_country');
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
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_approved'] = $this->language->get('entry_approved');
        $data['entry_payment_type'] = $this->language->get('entry_payment_type');
        $data['entry_paypal_email'] = $this->language->get('entry_paypal_email');
        $data['entry_bank_details'] = $this->language->get('entry_bank_details');
        $data['entry_cheque_payee'] = $this->language->get('entry_cheque_payee');
        $data['entry_shipping_type'] = $this->language->get('entry_shipping_type');
        $data['entry_shipping_amount'] = $this->language->get('entry_shipping_amount');
        $data['entry_commission_rate'] = $this->language->get('entry_commission_rate');
        $data['entry_amount'] = $this->language->get('entry_amount');
        $data['entry_store'] = $this->language->get('entry_store');

        $data['button_transaction_add'] = $this->language->get('button_transaction_add');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->request->get['mpseller_id'])) {
            $data['button_products'] = $this->language->get('button_products');
            $data['button_enquiries'] = $this->language->get('button_enquiries');
            $data['button_reviews'] = $this->language->get('button_reviews');
            $data['button_orders'] = $this->language->get('button_orders');
            $data['button_commission'] = $this->language->get('button_commission');
            $data['button_transaction'] = $this->language->get('button_transaction');


            $data['seller_products'] = $this->url->link('mpmultivendor/product', 'user_token=' . $this->session->data['user_token'] . '&filter_mpseller_id=' . $this->request->get['mpseller_id'], true);
            $data['seller_enquiries'] = $this->url->link('mpmultivendor/enquiries', 'user_token=' . $this->session->data['user_token'] . '&filter_mpseller_id=' . $this->request->get['mpseller_id'], true);
            $data['seller_reviews'] = $this->url->link('mpmultivendor/review', 'user_token=' . $this->session->data['user_token'] . '&filter_mpseller_id=' . $this->request->get['mpseller_id'], true);
            $data['seller_orders'] = $this->url->link('mpmultivendor/order', 'user_token=' . $this->session->data['user_token'] . '&filter_mpseller_id=' . $this->request->get['mpseller_id'], true);
            $data['seller_commission'] = $this->url->link('mpmultivendor/commission', 'user_token=' . $this->session->data['user_token'] . '&filter_mpseller_id=' . $this->request->get['mpseller_id'], true);
            $data['seller_transaction'] = $this->url->link('mpmultivendor/payment', 'user_token=' . $this->session->data['user_token'] . '&filter_mpseller_id=' . $this->request->get['mpseller_id'], true);
        }

        $data['tab_general'] = $this->language->get('tab_general');
        $data['tab_store'] = $this->language->get('tab_store');
        $data['tab_local'] = $this->language->get('tab_local');
        $data['tab_image'] = $this->language->get('tab_image');
        $data['tab_social_profiles'] = $this->language->get('tab_social_profiles');
        $data['tab_shipping'] = $this->language->get('tab_shipping');
        $data['tab_payment'] = $this->language->get('tab_payment');
        $data['tab_commission'] = $this->language->get('tab_commission');
        $data['tab_transaction'] = $this->language->get('tab_transaction');
        $data['tab_seo'] = $this->language->get('tab_seo');

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->get['mpseller_id'])) {
            $data['mpseller_id'] = $this->request->get['mpseller_id'];
        } else {
            $data['mpseller_id'] = 0;
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

        if (isset($this->error['payment_type'])) {
            $data['error_payment_type'] = $this->error['payment_type'];
        } else {
            $data['error_payment_type'] = '';
        }

        if (isset($this->error['paypal_email'])) {
            $data['error_paypal_email'] = $this->error['paypal_email'];
        } else {
            $data['error_paypal_email'] = '';
        }

        if (isset($this->error['bank_details'])) {
            $data['error_bank_details'] = $this->error['bank_details'];
        } else {
            $data['error_bank_details'] = '';
        }

        if (isset($this->error['cheque_payee_name'])) {
            $data['error_cheque_payee_name'] = $this->error['cheque_payee_name'];
        } else {
            $data['error_cheque_payee_name'] = '';
        }

        if (isset($this->error['shipping_amount'])) {
            $data['error_shipping_amount'] = $this->error['shipping_amount'];
        } else {
            $data['error_shipping_amount'] = '';
        }

        if (isset($this->error['commission_rate'])) {
            $data['error_commission_rate'] = $this->error['commission_rate'];
        } else {
            $data['error_commission_rate'] = '';
        }

        $url = '';

        if (isset($this->request->get['filter_store_owner'])) {
            $url .= '&filter_store_owner=' . urlencode(html_entity_decode($this->request->get['filter_store_owner'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_store_name'])) {
            $url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_approved'])) {
            $url .= '&filter_approved=' . $this->request->get['filter_approved'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['mpseller_id'])) {
            $data['action'] = '';
        } else {
            $data['action'] = $this->url->link('mpmultivendor/mpseller/edit', 'user_token=' . $this->session->data['user_token'] . '&mpseller_id=' . $this->request->get['mpseller_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('mpmultivendor/mpseller', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['mpseller_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $mpseller_info = $this->model_mpmultivendor_mpseller->getMpseller($this->request->get['mpseller_id']);
        }

        if (isset($this->request->post['description'])) {
            $data['description'] = $this->request->post['description'];
        } else if (!empty($mpseller_info)) {
            $data['description'] = $mpseller_info['description'];
        } else {
            $data['description'] = '';
        }

        if (isset($this->request->post['meta_description'])) {
            $data['meta_description'] = $this->request->post['meta_description'];
        } else if (!empty($mpseller_info)) {
            $data['meta_description'] = $mpseller_info['meta_description'];
        } else {
            $data['meta_description'] = '';
        }

        if (isset($this->request->post['meta_keyword'])) {
            $data['meta_keyword'] = $this->request->post['meta_keyword'];
        } else if (!empty($mpseller_info)) {
            $data['meta_keyword'] = $mpseller_info['meta_keyword'];
        } else {
            $data['meta_keyword'] = '';
        }

        if (isset($this->request->post['store_owner'])) {
            $data['store_owner'] = $this->request->post['store_owner'];
        } else if (!empty($mpseller_info)) {
            $data['store_owner'] = $mpseller_info['store_owner'];
        } else {
            $data['store_owner'] = '';
        }

        if (isset($this->request->post['store_name'])) {
            $data['store_name'] = $this->request->post['store_name'];
        } else if (!empty($mpseller_info)) {
            $data['store_name'] = $mpseller_info['store_name'];
        } else {
            $data['store_name'] = '';
        }

        if (isset($this->request->post['address'])) {
            $data['address'] = $this->request->post['address'];
        } else if (!empty($mpseller_info)) {
            $data['address'] = $mpseller_info['address'];
        } else {
            $data['address'] = '';
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else if (!empty($mpseller_info)) {
            $data['email'] = $mpseller_info['email'];
        } else {
            $data['email'] = $this->customer->getEmail();
        }

        if (isset($this->request->post['telephone'])) {
            $data['telephone'] = $this->request->post['telephone'];
        } else if (!empty($mpseller_info)) {
            $data['telephone'] = $mpseller_info['telephone'];
        } else {
            $data['telephone'] = $this->customer->getTelephone();
        }

        if (isset($this->request->post['fax'])) {
            $data['fax'] = $this->request->post['fax'];
        } else if (!empty($mpseller_info)) {
            $data['fax'] = $mpseller_info['fax'];
        } else {
            $data['fax'] = '';
        }


        if (isset($this->request->post['city'])) {
            $data['city'] = $this->request->post['city'];
        } else if (!empty($mpseller_info)) {
            $data['city'] = $mpseller_info['city'];
        } else {
            $data['city'] = '';
        }

        if (isset($this->request->post['country_id'])) {
            $data['country_id'] = $this->request->post['country_id'];
        } else if (!empty($mpseller_info)) {
            $data['country_id'] = $mpseller_info['country_id'];
        } else {
            $data['country_id'] = '';
        }

//        echo '<pre>';
//        print_r($this->config->get('config_ssl'));
//        exit('plpl');
        $uploads_dir = '/image/mpseller/customer-' . $mpseller_info['customer_id'] . '/';
        if (isset($this->request->post['id_proof'])) {
            $data['link_id_proof'] = $this->request->post['id_proof'];
        } else if (isset($mpseller_info['id_proof'])) {
            $data['link_id_proof'] = $uploads_dir . $mpseller_info['id_proof'];
        } else {
            $data['link_id_proof'] = '';
        }
        if (isset($this->request->post['address_proof'])) {
            $data['link_address_proof'] = $this->request->post['address_proof'];
        } else if (isset($mpseller_info['address_proof'])) {
            $data['link_address_proof'] = $uploads_dir . $mpseller_info['address_proof'];
        } else {
            $data['link_address_proof'] = '';
        }

        if (isset($this->request->post['zone_id'])) {
            $data['zone_id'] = $this->request->post['zone_id'];
        } else if (!empty($mpseller_info)) {
            $data['zone_id'] = $mpseller_info['zone_id'];
        } else {
            $data['zone_id'] = '';
        }

        if (isset($this->request->post['facebook_url'])) {
            $data['facebook_url'] = $this->request->post['facebook_url'];
        } else if (!empty($mpseller_info)) {
            $data['facebook_url'] = $mpseller_info['facebook_url'];
        } else {
            $data['facebook_url'] = '';
        }

        if (isset($this->request->post['google_plus_url'])) {
            $data['google_plus_url'] = $this->request->post['google_plus_url'];
        } else if (!empty($mpseller_info)) {
            $data['google_plus_url'] = $mpseller_info['google_plus_url'];
        } else {
            $data['google_plus_url'] = '';
        }

        if (isset($this->request->post['twitter_url'])) {
            $data['twitter_url'] = $this->request->post['twitter_url'];
        } else if (!empty($mpseller_info)) {
            $data['twitter_url'] = $mpseller_info['twitter_url'];
        } else {
            $data['twitter_url'] = '';
        }

        if (isset($this->request->post['pinterest_url'])) {
            $data['pinterest_url'] = $this->request->post['pinterest_url'];
        } else if (!empty($mpseller_info)) {
            $data['pinterest_url'] = $mpseller_info['pinterest_url'];
        } else {
            $data['pinterest_url'] = '';
        }

        if (isset($this->request->post['linkedin_url'])) {
            $data['linkedin_url'] = $this->request->post['linkedin_url'];
        } else if (!empty($mpseller_info)) {
            $data['linkedin_url'] = $mpseller_info['linkedin_url'];
        } else {
            $data['linkedin_url'] = '';
        }

        if (isset($this->request->post['youtube_url'])) {
            $data['youtube_url'] = $this->request->post['youtube_url'];
        } else if (!empty($mpseller_info)) {
            $data['youtube_url'] = $mpseller_info['youtube_url'];
        } else {
            $data['youtube_url'] = '';
        }

        if (isset($this->request->post['instagram_url'])) {
            $data['instagram_url'] = $this->request->post['instagram_url'];
        } else if (!empty($mpseller_info)) {
            $data['instagram_url'] = $mpseller_info['instagram_url'];
        } else {
            $data['instagram_url'] = '';
        }

        if (isset($this->request->post['flickr_url'])) {
            $data['flickr_url'] = $this->request->post['flickr_url'];
        } else if (!empty($mpseller_info)) {
            $data['flickr_url'] = $mpseller_info['flickr_url'];
        } else {
            $data['flickr_url'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($mpseller_info)) {
            $data['status'] = $mpseller_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['approved'])) {
            $data['approved'] = $this->request->post['approved'];
        } elseif (!empty($mpseller_info)) {
            $data['approved'] = $mpseller_info['approved'];
        } else {
            $data['approved'] = true;
        }

        if (isset($this->request->post['approved_for_delivery'])) {
            $data['approved_for_delivery'] = $this->request->post['approved_for_delivery'];
        } elseif (!empty($mpseller_info)) {
            $data['approved_for_delivery'] = $mpseller_info['approved_for_delivery'];
        } else {
            $data['approved_for_delivery'] = true;
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['logo'])) {
            $data['logo'] = $this->request->post['logo'];
        } elseif (!empty($mpseller_info)) {
            $data['logo'] = $mpseller_info['logo'];
        } else {
            $data['logo'] = '';
        }

        if (isset($this->request->post['logo']) && is_file(DIR_IMAGE . $this->request->post['logo'])) {
            $data['thumb_logo'] = $this->model_tool_image->resize($this->request->post['logo'], 100, 100);
        } elseif (!empty($mpseller_info) && is_file(DIR_IMAGE . $mpseller_info['logo'])) {
            $data['thumb_logo'] = $this->model_tool_image->resize($mpseller_info['logo'], 100, 100);
        } else {
            $data['thumb_logo'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        if (isset($this->request->post['banner'])) {
            $data['banner'] = $this->request->post['banner'];
        } elseif (!empty($mpseller_info)) {
            $data['banner'] = $mpseller_info['banner'];
        } else {
            $data['banner'] = '';
        }

        if (isset($this->request->post['banner']) && is_file(DIR_IMAGE . $this->request->post['banner'])) {
            $data['thumb_banner'] = $this->model_tool_image->resize($this->request->post['banner'], 100, 100);
        } elseif (!empty($mpseller_info) && is_file(DIR_IMAGE . $mpseller_info['banner'])) {
            $data['thumb_banner'] = $this->model_tool_image->resize($mpseller_info['banner'], 100, 100);
        } else {
            $data['thumb_banner'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($mpseller_info)) {
            $data['image'] = $mpseller_info['image'];
        } else {
            $data['image'] = '';
        }

        if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
            $data['thumb_image'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($mpseller_info) && is_file(DIR_IMAGE . $mpseller_info['image'])) {
            $data['thumb_image'] = $this->model_tool_image->resize($mpseller_info['image'], 100, 100);
        } else {
            $data['thumb_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);


        if (isset($this->request->post['shipping_type'])) {
            $data['shipping_type'] = $this->request->post['shipping_type'];
        } else if (!empty($mpseller_info['shipping_type'])) {
            $data['shipping_type'] = $mpseller_info['shipping_type'];
        } else {
            $data['shipping_type'] = 'order_wise';
        }

        if (isset($this->request->post['shipping_amount'])) {
            $data['shipping_amount'] = $this->request->post['shipping_amount'];
        } else if (!empty($mpseller_info)) {
            $data['shipping_amount'] = $mpseller_info['shipping_amount'];
        } else {
            $data['shipping_amount'] = 0.00;
        }

        if (isset($this->request->post['payment_type'])) {
            $data['payment_type'] = $this->request->post['payment_type'];
        } else if (!empty($mpseller_info['payment_type'])) {
            $data['payment_type'] = $mpseller_info['payment_type'];
        } else {
            $data['payment_type'] = 'paypal';
        }

        if (isset($this->request->post['paypal_email'])) {
            $data['paypal_email'] = $this->request->post['paypal_email'];
        } else if (!empty($mpseller_info)) {
            $data['paypal_email'] = $mpseller_info['paypal_email'];
        } else {
            $data['paypal_email'] = '';
        }

        if (isset($this->request->post['bank_details'])) {
            $data['bank_details'] = $this->request->post['bank_details'];
        } else if (!empty($mpseller_info)) {
            $data['bank_details'] = $mpseller_info['bank_details'];
        } else {
            $data['bank_details'] = '';
        }

        if (isset($this->request->post['cheque_payee_name'])) {
            $data['cheque_payee_name'] = $this->request->post['cheque_payee_name'];
        } else if (!empty($mpseller_info)) {
            $data['cheque_payee_name'] = $mpseller_info['cheque_payee_name'];
        } else {
            $data['cheque_payee_name'] = '';
        }

        if (isset($this->request->post['commission_rate'])) {
            $data['commission_rate'] = $this->request->post['commission_rate'];
        } else if (!empty($mpseller_info)) {
            $data['commission_rate'] = $mpseller_info['commission_rate'];
        } else {
            $data['commission_rate'] = '';
        }


        if (isset($this->request->post['seller_seo_url'])) {
            $data['seller_seo_url'] = $this->request->post['seller_seo_url'];
        } else if (!empty($mpseller_info)) {
            $data['seller_seo_url'] = $this->model_mpmultivendor_mpseller->getSellerSeoUrls($mpseller_info['mpseller_id']);
        } else {
            $data['seller_seo_url'] = array();
        }

        if (isset($this->request->post['review_seo_url'])) {
            $data['review_seo_url'] = $this->request->post['review_seo_url'];
        } else if (!empty($mpseller_info)) {
            $data['review_seo_url'] = $this->model_mpmultivendor_mpseller->getReviewSeoUrls($mpseller_info['mpseller_id']);
        } else {
            $data['review_seo_url'] = array();
        }

        $this->load->model('localisation/country');
        $data['countries'] = $this->model_localisation_country->getCountries();

        $total_paid = $this->model_mpmultivendor_mpseller->getTransactionTotal($this->request->get['mpseller_id']);

        $total_commission = $this->model_mpmultivendor_mpseller->getCommissionTotal($this->request->get['mpseller_id']);

        $data['total_paid'] = $this->currency->format($total_paid, $this->config->get('config_currency'));

        $data['total_commission'] = $this->currency->format($total_commission, $this->config->get('config_currency'));

        $data['total_balance'] = $this->currency->format($total_commission - $total_paid, $this->config->get('config_currency'));

        $this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();

        $data['seo_stores'] = array();
        $data['seo_stores'][] = array(
            'store_id' => 0,
            'name' => $this->language->get('text_default')
        );

        $stores = $this->model_setting_store->getStores();
        foreach ($stores as $store) {
            $data['seo_stores'][] = array(
                'store_id' => $store['store_id'],
                'name' => $store['name']
            );
        }

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->config->set('template_engine', 'template');

        $this->response->setOutput($this->load->view('mpmultivendor/mpseller_form', $data));
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'mpmultivendor/mpseller')) {
            $this->error['warning'] = $this->language->get('error_permission');
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

        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = $this->language->get('error_email');
        }

        $mpseller_info = $this->model_mpmultivendor_mpseller->getMpsellerByEmail($this->request->post['email']);


        if (isset($this->request->get['mpseller_id'])) {
            if ($mpseller_info && ($this->request->get['mpseller_id'] != $mpseller_info['mpseller_id'])) {
                $this->error['warning'] = $this->language->get('error_exists');
                $this->error['email'] = $this->language->get('error_exists');
            }
        }

        /*if (!empty($this->request->post['payment_type'])) {
            if ($this->request->post['payment_type'] == 'paypal') {
                if ((utf8_strlen($this->request->post['paypal_email']) > 96) || !filter_var($this->request->post['paypal_email'], FILTER_VALIDATE_EMAIL)) {
                    $this->error['paypal_email'] = $this->language->get('error_paypal_email');
                }
            }

            if ($this->request->post['payment_type'] == 'bank') {
                if (empty($this->request->post['bank_details'])) {
                    $this->error['bank_details'] = $this->language->get('error_bank_details');
                }
            }

            if ($this->request->post['payment_type'] == 'cheque') {
                if (empty($this->request->post['cheque_payee_name'])) {
                    $this->error['cheque_payee_name'] = $this->language->get('error_cheque_payee_name');
                }
            }
        } else {
            $this->error['payment_type'] = $this->language->get('error_payment_type');
        }*/

        if ((int)$this->request->post['shipping_amount'] < 0) {
            $this->error['shipping_amount'] = $this->language->get('error_shipping_amount');
        }

        if ((int)$this->request->post['commission_rate'] < 0) {
            $this->error['commission_rate'] = $this->language->get('error_commission_rate');
        }

        if (isset($this->request->post['seller_seo_url'])) {
            foreach ($this->request->post['seller_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        if (count(array_keys($language, $keyword)) > 1) {
                            $this->error['seo_keyword'][$store_id][$language_id] = $this->language->get('error_unique');
                        }

                        $seo_urls = $this->model_mpmultivendor_mpseller->getSeoUrlsByKeyword($keyword);

                        foreach ($seo_urls as $seo_url) {
                            if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['mpseller_id']) || (($seo_url['query'] != 'mpseller_id=' . $this->request->get['mpseller_id'])))) {
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

                        $seo_urls = $this->model_mpmultivendor_mpseller->getSeoUrlsByKeyword($keyword);

                        foreach ($seo_urls as $seo_url) {
                            if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['mpseller_id']) || (($seo_url['query'] != 'review_mpseller_id=' . $this->request->get['mpseller_id'])))) {
                                $this->error['review_seo_keyword'][$store_id][$language_id] = $this->language->get('error_keyword');

                                break;
                            }
                        }
                    }
                }
            }
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'mpmultivendor/mpseller')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateApprove()
    {
        if (!$this->user->hasPermission('modify', 'mpmultivendor/mpseller')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_store_owner']) || isset($this->request->get['filter_store_name']) || isset($this->request->get['filter_email'])) {

            if (isset($this->request->get['filter_store_owner'])) {
                $filter_store_owner = $this->request->get['filter_store_owner'];
            } else {
                $filter_store_owner = '';
            }

            if (isset($this->request->get['filter_store_name'])) {
                $filter_store_name = $this->request->get['filter_store_name'];
            } else {
                $filter_store_name = '';
            }

            if (isset($this->request->get['filter_email'])) {
                $filter_email = $this->request->get['filter_email'];
            } else {
                $filter_email = '';
            }

            $this->load->model('mpmultivendor/mpseller');

            $filter_data = array(
                'filter_store_owner' => $filter_store_owner,
                'filter_store_name' => $filter_store_name,
                'filter_email' => $filter_email,
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_mpmultivendor_mpseller->getMpsellers($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'mpseller_id' => $result['mpseller_id'],
                    'store_owner' => strip_tags(html_entity_decode($result['store_owner'], ENT_QUOTES, 'UTF-8')),
                    'store_name' => strip_tags(html_entity_decode($result['store_name'], ENT_QUOTES, 'UTF-8')),
                    'email' => $result['email'],
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['store_owner'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function transaction()
    {
        $this->load->language('mpmultivendor/mpseller');

        $this->load->model('mpmultivendor/mpseller');

        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_paid'] = $this->language->get('text_paid');

        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_amount'] = $this->language->get('column_amount');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['transactions'] = array();

        $results = $this->model_mpmultivendor_mpseller->getTransactions($this->request->get['mpseller_id'], ($page - 1) * 10, 10);

        foreach ($results as $result) {
            $data['transactions'][] = array(
                'amount' => $this->currency->format($result['amount'], $this->config->get('config_currency')),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
            );
        }

        $data['total_paid'] = $this->currency->format($this->model_mpmultivendor_mpseller->getTransactionTotal($this->request->get['mpseller_id']), $this->config->get('config_currency'));

        $transaction_total = $this->model_mpmultivendor_mpseller->getTotalTransactions($this->request->get['mpseller_id']);

        $pagination = new Pagination();
        $pagination->total = $transaction_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = $this->url->link('mpmultivendor/mpseller/transaction', 'user_token=' . $this->session->data['user_token'] . '&mpseller_id=' . $this->request->get['mpseller_id'] . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($transaction_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($transaction_total - 10)) ? $transaction_total : ((($page - 1) * 10) + 10), $transaction_total, ceil($transaction_total / 10));

        $this->config->set('template_engine', 'template');
        $this->response->setOutput($this->load->view('mpmultivendor/mpseller_transaction', $data));
    }

    public function addTransaction()
    {
        $this->load->language('mpmultivendor/mpseller');

        $json = array();

        if (!$this->user->hasPermission('modify', 'mpmultivendor/mpseller')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            $this->load->model('mpmultivendor/mpseller');

            $this->model_mpmultivendor_mpseller->addTransaction($this->request->get['mpseller_id'], $this->request->post['amount']);

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function remind()
    {
        $this->load->model('customer/customer');
        $customerId = $this->request->post['customer_id'];
        $customer = $this->model_customer_customer->getCustomer($customerId);

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        $mail = new Mail($this->config->get('config_mail_engine'));
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

        $mail->setTo($customer['email']);
        $mail->setFrom($this->config->get('config_email'));
        $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
        $mail->setSubject(html_entity_decode(sprintf('The Champion Mall : Reminder for Seller Info', $this->config->get('config_name')), ENT_QUOTES, 'UTF-8'));

        $data['logo'] = $server . 'image/' . $this->config->get('config_logo');
        $data['firstname'] = $customer['firstname'];
        $data['seller_info_link'] = $this->url->link('account/mpmultivendor/store_info', '', true);

        $mailText = $this->load->view('mpmultivendor_mail/seller_remind', $data);
        $mail->setHtml($mailText);
        $mail->setText(html_entity_decode($mailText, ENT_QUOTES, 'UTF-8'));
        $mail->send();

        $json = array();
        $json['success'] = 'Success : The reminder mail has been sent to Seller.';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}