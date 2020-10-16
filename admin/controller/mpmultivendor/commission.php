<?php

class ControllerMpmultivendorCommission extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('mpmultivendor/commission');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('mpmultivendor/commission');

        $this->getList();
    }

    protected function getList()
    {
        if (isset($this->request->get['filter_mpseller_id'])) {
            $filter_mpseller_id = $this->request->get['filter_mpseller_id'];
        } else {
            $filter_mpseller_id = null;
        }

        if (isset($this->request->get['filter_store_owner'])) {
            $filter_store_owner = $this->request->get['filter_store_owner'];
        } else {
            $filter_store_owner = null;
        }

        if (isset($this->request->get['filter_date_start'])) {
            $filter_date_start = $this->request->get['filter_date_start'];
        } else {
            $filter_date_start = null;
        }

        if (isset($this->request->get['filter_date_end'])) {
            $filter_date_end = $this->request->get['filter_date_end'];
        } else {
            $filter_date_end = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'mc.date_added';
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

        if (isset($this->request->get['filter_mpseller_id'])) {
            $url .= '&filter_mpseller_id=' . $this->request->get['filter_mpseller_id'];
        }

        if (isset($this->request->get['filter_store_owner'])) {
            $url .= '&filter_store_owner=' . $this->request->get['filter_store_owner'];
        }

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
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
            'href' => $this->url->link('mpmultivendor/commission', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );


        $data['commissions'] = array();

        $filter_data = array(
            'filter_mpseller_id' => $filter_mpseller_id,
            'filter_store_owner' => $filter_store_owner,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $filter_data_all = array(
            'filter_mpseller_id' => $filter_mpseller_id,
            'filter_store_owner' => $filter_store_owner,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order
        );

        $final_commission = $this->model_mpmultivendor_commission->getCommissionTotal($filter_data);
        $data['final_commission'] = $this->currency->format($final_commission, $this->config->get('config_currency'));

        $commission_total = $this->model_mpmultivendor_commission->getTotalCommissions($filter_data);

        $results = $this->model_mpmultivendor_commission->getCommissions($filter_data);
        $results_all = $this->model_mpmultivendor_commission->getCommissions($filter_data_all);

        foreach ($results as $result) {
//            if (!empty($result['admin_fee'])) {
//                $data['admin_fees'][$result['currency_code']][] = $this->currency->format($result['admin_fee'], $result['currency_code'], $result['currency_value']);
//            }

            $data['commissions'][] = array(
                'store_owner' => $result['store_owner'],
                'store_name' => $result['store_name'],
                'order_id' => $result['order_id'],
                'name' => $result['name'],
                'price' => $this->currency->format($result['price'], $result['currency_code'], $result['currency_value']),
                'quantity' => $result['quantity'],
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'amount' => $this->currency->format($result['amount'], $result['currency_code'], $result['currency_value']),
                'admin_fee' => $this->currency->format($result['admin_fee'], $result['currency_code'], $result['currency_value']),
                'currency_code' => $result['currency_code'],
                'currency_value' => $result['currency_value'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
            );
        }

        //commission for all total
        foreach ($results_all as $result_all) {
            if (!empty($result_all['admin_fee'])) {
                $data['admin_fees'][$result_all['currency_code']][] = $this->currency->format($result_all['admin_fee'], $result_all['currency_code'], $result_all['currency_value']);
            }
        }
        foreach ($data['admin_fees'] as $key => $fee) {

            $feeAmt = preg_replace("/[^0-9.]/", "", $fee);

            $data['data'.$key]  = $this->currency->getSymbolLeft($key) . round(array_sum($feeAmt), 2) . $this->currency->getSymbolRight($key);
        }
//echo '<pre>';print_r($feeAmt);exit('okoko');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_final_commission'] = $this->language->get('text_final_commission');

        $data['entry_date_start'] = $this->language->get('entry_date_start');
        $data['entry_date_end'] = $this->language->get('entry_date_end');
        $data['entry_store_owner'] = $this->language->get('entry_store_owner');

        $data['button_filter'] = $this->language->get('button_filter');

        $data['column_store_owner'] = $this->language->get('column_store_owner');
        $data['column_store_name'] = $this->language->get('column_store_name');
        $data['column_amount'] = $this->language->get('column_amount');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_product_name'] = $this->language->get('column_product_name');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_total'] = $this->language->get('column_total');

        $data['button_view'] = $this->language->get('button_view');
        $data['button_delete'] = $this->language->get('button_delete');

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

        if (isset($this->request->get['filter_mpseller_id'])) {
            $url .= '&filter_mpseller_id=' . $this->request->get['filter_mpseller_id'];
        }

        if (isset($this->request->get['filter_store_owner'])) {
            $url .= '&filter_store_owner=' . $this->request->get['filter_store_owner'];
        }

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_store_owner'] = $this->url->link('mpmultivendor/commission', 'user_token=' . $this->session->data['user_token'] . '&sort=store_owner' . $url, true);
        $data['sort_store_name'] = $this->url->link('mpmultivendor/commission', 'user_token=' . $this->session->data['user_token'] . '&sort=store_name' . $url, true);
        $data['sort_order_id'] = $this->url->link('mpmultivendor/commission', 'user_token=' . $this->session->data['user_token'] . '&sort=mc.order_id' . $url, true);
        $data['sort_name'] = $this->url->link('mpmultivendor/commission', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
        $data['sort_price'] = $this->url->link('mpmultivendor/commission', 'user_token=' . $this->session->data['user_token'] . '&sort=price' . $url, true);
        $data['sort_quantity'] = $this->url->link('mpmultivendor/commission', 'user_token=' . $this->session->data['user_token'] . '&sort=quantity' . $url, true);
        $data['sort_total'] = $this->url->link('mpmultivendor/commission', 'user_token=' . $this->session->data['user_token'] . '&sort=total' . $url, true);
        $data['sort_amount'] = $this->url->link('mpmultivendor/commission', 'user_token=' . $this->session->data['user_token'] . '&sort=mc.amount' . $url, true);
        $data['sort_date_added'] = $this->url->link('mpmultivendor/commission', 'user_token=' . $this->session->data['user_token'] . '&sort=mc.date_added' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_mpseller_id'])) {
            $url .= '&filter_mpseller_id=' . $this->request->get['filter_mpseller_id'];
        }

        if (isset($this->request->get['filter_store_owner'])) {
            $url .= '&filter_store_owner=' . $this->request->get['filter_store_owner'];
        }

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $commission_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('mpmultivendor/commission', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($commission_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($commission_total - $this->config->get('config_limit_admin'))) ? $commission_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $commission_total, ceil($commission_total / $this->config->get('config_limit_admin')));

        $data['filter_mpseller_id'] = $filter_mpseller_id;
        $data['filter_store_owner'] = $filter_store_owner;
        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->config->set('template_engine', 'template');
        $this->response->setOutput($this->load->view('mpmultivendor/commission_list', $data));
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'mpmultivendor/commission')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}