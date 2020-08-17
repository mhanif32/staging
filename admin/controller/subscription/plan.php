<?php

class ControllerSubscriptionPlan extends Controller
{
    private $error = array();

    public function index()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        $this->load->language('subscription/plan');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('subscription/plan');

        $this->getList();
    }

    public function add()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        $this->load->language('subscription/plan');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('subscription/plan');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            //todo create a plan
            $this->load->library('stripe');
            \Stripe\Stripe::setApiKey('sk_test_51H8inyJvSOEFkXrXcFESwhvDkx08F0DI8KfkUnwO14cGKdxv36U0hWj9GSusI2ZMrd3NJaLBI3u13Q26Uj9osSTH00b6wMWu3v');

            $product = \Stripe\Product::create([
                'name' => $this->request->post['name'],
            ]);

            $this->session->data['success'] = $this->language->get('text_success');

//            $stripe = new \Stripe\StripeObject('sk_test_51H8inyJvSOEFkXrXcFESwhvDkx08F0DI8KfkUnwO14cGKdxv36U0hWj9GSusI2ZMrd3NJaLBI3u13Q26Uj9osSTH00b6wMWu3v'
//            );
//            $price = $stripe->prices->create([
//                'unit_amount' => $this->request->post['amount'],
//                'currency' => 'usd',
//                'recurring' => ['interval' => 'month'],
//                'product' => $product['id'],
//            ]);
//echo '<pre>'; print_r($price); exit('okok');
            $parameters = [
                "amount" => $this->request->post['amount'] * 100,
                "interval" => $this->request->post['interval'],
                "product" => $product['id'],
                "currency" => 'usd',
                "interval_count" => $this->request->post['interval_count']
            ];
            $response = \Stripe\Plan::create($parameters);
            $stripe_plan_id = $response['id'];
            $this->model_subscription_plan->addSubscriptionPlan($this->request->post, $stripe_plan_id);

            $this->response->redirect($this->url->link('subscription/plan', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit()
    {
        $this->load->language('customer/customer_group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer_group');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_customer_customer_group->editCustomerGroup($this->request->get['customer_group_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('customer/customer_group', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete()
    {
        $this->load->language('customer/customer_group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer_group');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $customer_group_id) {
                $this->model_customer_customer_group->deleteCustomerGroup($customer_group_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('customer/customer_group', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList()
    {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'cgd.name';
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
            'href' => $this->url->link('subscription/plan', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('subscription/plan/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('subscription/plan/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['plans'] = array();

        $filter_data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $plan_total = $this->model_subscription_plan->getTotalSubscriptionPlans();

        $results = $this->model_subscription_plan->getSubscriptionPlans($filter_data);

        foreach ($results as $result) {
            $data['plans'][] = array(
                'plan_id' => $result['plan_id'],
                'amount' => $result['amount'],
                'date_added' => $result['date_added'],
                'rent_percentage' => $result['rent_percentage'],
                'name' => $result['name'] . (($result['plan_id'] == $this->config->get('plan_id')) ? $this->language->get('text_default') : null),
                //'sort_order'        => $result['sort_order'],
                'edit' => $this->url->link('subscription/plan/edit', 'user_token=' . $this->session->data['user_token'] . '&plan_id=' . $result['plan_id'] . $url, true)
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

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('subscription/plan', 'user_token=' . $this->session->data['user_token'] . '&sort=cgd.name' . $url, true);
        $data['sort_sort_order'] = $this->url->link('subscription/plan', 'user_token=' . $this->session->data['user_token'] . '&sort=cg.sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $plan_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('subscription/plan', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($plan_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($plan_total - $this->config->get('config_limit_admin'))) ? $plan_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $plan_total, ceil($plan_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('subscription/plan_list', $data));
    }

    protected function getForm()
    {
        $data['text_form'] = !isset($this->request->get['plan_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

        if (isset($this->error['amount'])) {
            $data['error_amount'] = $this->error['amount'];
        } else {
            $data['error_amount'] = array();
        }

        if (isset($this->error['interval'])) {
            $data['error_interval'] = $this->error['interval'];
        } else {
            $data['error_interval'] = array();
        }

        if (isset($this->error['interval_count'])) {
            $data['error_interval_count'] = $this->error['interval_count'];
        } else {
            $data['error_interval_count'] = array();
        }

        $url = '';

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
            'href' => $this->url->link('subscription/plan', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['plan_id'])) {
            $data['action'] = $this->url->link('subscription/plan/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('subscription/plan/edit', 'user_token=' . $this->session->data['user_token'] . '&plan_id=' . $this->request->get['plan_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('subscription/plan', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['plan_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $plan_info = $this->model_subscription_plan->getSubscriptionPlan($this->request->get['plan_id']);
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($plan_info)) {
            $data['sort_order'] = $plan_info['sort_order'];
        } else {
            $data['sort_order'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('subscription/plan_form', $data));
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'subscription/plan')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        return !$this->error;
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'customer/customer_group')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('setting/store');
        $this->load->model('customer/customer');

        foreach ($this->request->post['selected'] as $customer_group_id) {
            if ($this->config->get('config_customer_group_id') == $customer_group_id) {
                $this->error['warning'] = $this->language->get('error_default');
            }

            $store_total = $this->model_setting_store->getTotalStoresByCustomerGroupId($customer_group_id);

            if ($store_total) {
                $this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
            }

            $customer_total = $this->model_customer_customer->getTotalCustomersByCustomerGroupId($customer_group_id);

            if ($customer_total) {
                $this->error['warning'] = sprintf($this->language->get('error_customer'), $customer_total);
            }
        }

        return !$this->error;
    }
}