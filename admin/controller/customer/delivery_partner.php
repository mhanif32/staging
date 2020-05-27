<?php
class ControllerCustomerDeliveryPartner extends Controller
{
    private $error = array();

    public function index() {
        $this->load->language('customer/delivery_partner');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer');

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }

        if (isset($this->request->get['filter_email'])) {
            $filter_email = $this->request->get['filter_email'];
        } else {
            $filter_email = '';
        }

        if (isset($this->request->get['filter_customer_group_id'])) {
            $filter_customer_group_id = $this->request->get['filter_customer_group_id'];
        } else {
            $filter_customer_group_id = '';
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = '';
        }

        if (isset($this->request->get['filter_ip'])) {
            $filter_ip = $this->request->get['filter_ip'];
        } else {
            $filter_ip = '';
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = '';
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

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_customer_group_id'])) {
            $url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_ip'])) {
            $url .= '&filter_ip=' . $this->request->get['filter_ip'];
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
            'href' => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('customer/customer/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('customer/customer/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $this->load->model('setting/store');

        $stores = $this->model_setting_store->getStores();

        $data['customers'] = array();

        $filter_data = array(
            'filter_name'              => $filter_name,
            'filter_email'             => $filter_email,
            'filter_customer_group_id' => $filter_customer_group_id,
            'filter_status'            => $filter_status,
            'filter_date_added'        => $filter_date_added,
            'filter_ip'                => $filter_ip,
            'sort'                     => $sort,
            'order'                    => $order,
            'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                    => $this->config->get('config_limit_admin')
        );

        $customer_total = $this->model_customer_customer->getTotalCustomers($filter_data);

        $results = $this->model_customer_customer->getDeliveryPartners($filter_data);

        foreach ($results as $result) {
            $login_info = $this->model_customer_customer->getTotalLoginAttempts($result['email']);

            if ($login_info && $login_info['total'] >= $this->config->get('config_login_attempts')) {
                $unlock = $this->url->link('customer/customer/unlock', 'user_token=' . $this->session->data['user_token'] . '&email=' . $result['email'] . $url, true);
            } else {
                $unlock = '';
            }

            $store_data = array();

            $store_data[] = array(
                'name' => $this->config->get('config_name'),
                'href' => $this->url->link('customer/customer/login', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'] . '&store_id=0', true)
            );

            foreach ($stores as $store) {
                $store_data[] = array(
                    'name' => $store['name'],
                    'href' => $this->url->link('customer/customer/login', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'] . '&store_id=' . $result['store_id'], true)
                );
            }

            $data['customers'][] = array(
                'customer_id'    => $result['customer_id'],
                'name'           => $result['name'],
                'email'          => $result['email'],
                'customer_group' => $result['customer_group'],
                //'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'status'         =>
                    ($result['status'] == 1) ? $this->language->get('text_enabled') : (($result['status'] == 2) ? 'Deactivated' : $this->language->get('text_disabled')),
                'ip'             => $result['ip'],
                'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'unlock'         => $unlock,
                'store'          => $store_data,
                'edit'           => $this->url->link('customer/delivery_partner/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'] . $url, true)
            );
        }

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

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_customer_group_id'])) {
            $url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_ip'])) {
            $url .= '&filter_ip=' . $this->request->get['filter_ip'];
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

        $data['sort_name'] = $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
        $data['sort_email'] = $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . '&sort=c.email' . $url, true);
        $data['sort_customer_group'] = $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . '&sort=customer_group' . $url, true);
        $data['sort_status'] = $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . '&sort=c.status' . $url, true);
        $data['sort_ip'] = $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . '&sort=c.ip' . $url, true);
        $data['sort_date_added'] = $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . '&sort=c.date_added' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_customer_group_id'])) {
            $url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_ip'])) {
            $url .= '&filter_ip=' . $this->request->get['filter_ip'];
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
        $pagination->total = $customer_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($customer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($customer_total - $this->config->get('config_limit_admin'))) ? $customer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $customer_total, ceil($customer_total / $this->config->get('config_limit_admin')));

        $data['filter_name'] = $filter_name;
        $data['filter_email'] = $filter_email;
        $data['filter_customer_group_id'] = $filter_customer_group_id;
        $data['filter_status'] = $filter_status;
        $data['filter_ip'] = $filter_ip;
        $data['filter_date_added'] = $filter_date_added;

        $this->load->model('customer/customer_group');

        $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('customer/delivery_partner_list', $data));
    }

    public function edit() {
        $this->load->language('customer/delivery_partner');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $this->load->model('localisation/area');
        $this->load->model('localisation/currency');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            //echo '<pre>';print_r($this->request->post);exit('asd');
            $this->model_customer_customer->updateDeliveryInfos($this->request->get['customer_id'], $this->request->post);

            $this->model_customer_customer->editCustomer($this->request->get['customer_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_customer_group_id'])) {
                $url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_ip'])) {
                $url .= '&filter_ip=' . $this->request->get['filter_ip'];
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

            $this->response->redirect($this->url->link('customer/delivery_partner', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['customer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->get['customer_id'])) {
            $data['customer_id'] = $this->request->get['customer_id'];
        } else {
            $data['customer_id'] = 0;
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['firstname'])) {
            $data['error_firstname'] = $this->error['firstname'];
        } else {
            $data['error_firstname'] = '';
        }

        if (isset($this->error['lastname'])) {
            $data['error_lastname'] = $this->error['lastname'];
        } else {
            $data['error_lastname'] = '';
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

        if (isset($this->error['cheque'])) {
            $data['error_cheque'] = $this->error['cheque'];
        } else {
            $data['error_cheque'] = '';
        }

        if (isset($this->error['paypal'])) {
            $data['error_paypal'] = $this->error['paypal'];
        } else {
            $data['error_paypal'] = '';
        }

        if (isset($this->error['bank_account_name'])) {
            $data['error_bank_account_name'] = $this->error['bank_account_name'];
        } else {
            $data['error_bank_account_name'] = '';
        }

        if (isset($this->error['bank_account_number'])) {
            $data['error_bank_account_number'] = $this->error['bank_account_number'];
        } else {
            $data['error_bank_account_number'] = '';
        }

        if (isset($this->error['password'])) {
            $data['error_password'] = $this->error['password'];
        } else {
            $data['error_password'] = '';
        }

        if (isset($this->error['confirm'])) {
            $data['error_confirm'] = $this->error['confirm'];
        } else {
            $data['error_confirm'] = '';
        }

        if (isset($this->error['custom_field'])) {
            $data['error_custom_field'] = $this->error['custom_field'];
        } else {
            $data['error_custom_field'] = array();
        }

        if (isset($this->error['address'])) {
            $data['error_address'] = $this->error['address'];
        } else {
            $data['error_address'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_customer_group_id'])) {
            $url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_ip'])) {
            $url .= '&filter_ip=' . $this->request->get['filter_ip'];
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
            'href' => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['customer_id'])) {
            $data['action'] = $this->url->link('customer/customer/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('customer/delivery_partner/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $this->request->get['customer_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $customer_info = $this->model_customer_customer->getCustomer($this->request->get['customer_id']);
        }

        $this->load->model('customer/customer_group');

        $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

        if (isset($this->request->post['customer_group_id'])) {
            $data['customer_group_id'] = $this->request->post['customer_group_id'];
        } elseif (!empty($customer_info)) {
            $data['customer_group_id'] = $customer_info['customer_group_id'];
        } else {
            $data['customer_group_id'] = $this->config->get('config_customer_group_id');
        }

        if (isset($this->request->post['firstname'])) {
            $data['firstname'] = $this->request->post['firstname'];
        } elseif (!empty($customer_info)) {
            $data['firstname'] = $customer_info['firstname'];
        } else {
            $data['firstname'] = '';
        }

        if (isset($this->request->post['lastname'])) {
            $data['lastname'] = $this->request->post['lastname'];
        } elseif (!empty($customer_info)) {
            $data['lastname'] = $customer_info['lastname'];
        } else {
            $data['lastname'] = '';
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } elseif (!empty($customer_info)) {
            $data['email'] = $customer_info['email'];
        } else {
            $data['email'] = '';
        }

        if (isset($this->request->post['telephone'])) {
            $data['telephone'] = $this->request->post['telephone'];
        } elseif (!empty($customer_info)) {
            $data['telephone'] = $customer_info['telephone'];
        } else {
            $data['telephone'] = '';
        }

        // Custom Fields
        $this->load->model('customer/custom_field');

        $data['custom_fields'] = array();

        $filter_data = array(
            'sort'  => 'cf.sort_order',
            'order' => 'ASC'
        );

        $custom_fields = $this->model_customer_custom_field->getCustomFields($filter_data);

        foreach ($custom_fields as $custom_field) {
            $data['custom_fields'][] = array(
                'custom_field_id'    => $custom_field['custom_field_id'],
                'custom_field_value' => $this->model_customer_custom_field->getCustomFieldValues($custom_field['custom_field_id']),
                'name'               => $custom_field['name'],
                'value'              => $custom_field['value'],
                'type'               => $custom_field['type'],
                'location'           => $custom_field['location'],
                'sort_order'         => $custom_field['sort_order']
            );
        }

        if (isset($this->request->post['custom_field'])) {
            $data['account_custom_field'] = $this->request->post['custom_field'];
        } elseif (!empty($customer_info)) {
            $data['account_custom_field'] = json_decode($customer_info['custom_field'], true);
        } else {
            $data['account_custom_field'] = array();
        }

        if (isset($this->request->post['newsletter'])) {
            $data['newsletter'] = $this->request->post['newsletter'];
        } elseif (!empty($customer_info)) {
            $data['newsletter'] = $customer_info['newsletter'];
        } else {
            $data['newsletter'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($customer_info)) {
            $data['status'] = $customer_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['safe'])) {
            $data['safe'] = $this->request->post['safe'];
        } elseif (!empty($customer_info)) {
            $data['safe'] = $customer_info['safe'];
        } else {
            $data['safe'] = 0;
        }

        if (isset($this->request->post['password'])) {
            $data['password'] = $this->request->post['password'];
        } else {
            $data['password'] = '';
        }

        if (isset($this->request->post['confirm'])) {
            $data['confirm'] = $this->request->post['confirm'];
        } else {
            $data['confirm'] = '';
        }

        $this->load->model('localisation/country');

        $deliveryInfos = $this->model_customer_customer->getDeliveryInfo($this->request->get['customer_id']);
        $delivery = [];
        foreach ($deliveryInfos as $info) {
            $deliveryArray = array();
            $deliveryArray['country_id'] = $info['country_id'];
            $deliveryArray['zone_id'] = $info['zone_id'];
            $deliveryArray['zones'] = $this->model_localisation_zone->getZonesByCountryId($info['country_id']);
            $deliveryArray['area_id'] = $info['area_id'];
            $deliveryArray['areas'] = $this->model_localisation_area->getAreasByZoneId($info['zone_id']);
            $deliveryArray['days'] = $info['days'];
            $deliveryArray['rate_per_hour'] = $info['rate_per_hour'];
            $delivery[] = $deliveryArray;
        }
        $data['deliveryInfos'] = $delivery;
        $data['countries'] = $this->model_localisation_country->getCountries();

        if (isset($this->request->post['address'])) {
            $data['addresses'] = $this->request->post['address'];
        } elseif (isset($this->request->get['customer_id'])) {
            $data['addresses'] = $this->model_customer_customer->getAddresses($this->request->get['customer_id']);
        } else {
            $data['addresses'] = array();
        }

        if (isset($this->request->post['address_id'])) {
            $data['address_id'] = $this->request->post['address_id'];
        } elseif (!empty($customer_info)) {
            $data['address_id'] = $customer_info['address_id'];
        } else {
            $data['address_id'] = '';
        }

        // Affliate
        if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $affiliate_info = $this->model_customer_customer->getAffiliate($this->request->get['customer_id']);
        }

        if (isset($this->request->post['affiliate'])) {
            $data['affiliate'] = $this->request->post['affiliate'];
        } elseif (!empty($affiliate_info)) {
            $data['affiliate'] = $affiliate_info['status'];
        } else {
            $data['affiliate'] = '';
        }

        if (isset($this->request->post['company'])) {
            $data['company'] = $this->request->post['company'];
        } elseif (!empty($affiliate_info)) {
            $data['company'] = $affiliate_info['company'];
        } else {
            $data['company'] = '';
        }

        if (isset($this->request->post['website'])) {
            $data['website'] = $this->request->post['website'];
        } elseif (!empty($affiliate_info)) {
            $data['website'] = $affiliate_info['website'];
        } else {
            $data['website'] = '';
        }

        if (isset($this->request->post['tracking'])) {
            $data['tracking'] = $this->request->post['tracking'];
        } elseif (!empty($affiliate_info)) {
            $data['tracking'] = $affiliate_info['tracking'];
        } else {
            $data['tracking'] = '';
        }

        if (isset($this->request->post['commission'])) {
            $data['commission'] = $this->request->post['commission'];
        } elseif (!empty($affiliate_info)) {
            $data['commission'] = $affiliate_info['commission'];
        } else {
            $data['commission'] = $this->config->get('config_affiliate_commission');
        }

        if (isset($this->request->post['tax'])) {
            $data['tax'] = $this->request->post['tax'];
        } elseif (!empty($affiliate_info)) {
            $data['tax'] = $affiliate_info['tax'];
        } else {
            $data['tax'] = '';
        }

        if (isset($this->request->post['payment'])) {
            $data['payment'] = $this->request->post['payment'];
        } elseif (!empty($affiliate_info)) {
            $data['payment'] = $affiliate_info['payment'];
        } else {
            $data['payment'] = 'cheque';
        }

        if (isset($this->request->post['cheque'])) {
            $data['cheque'] = $this->request->post['cheque'];
        } elseif (!empty($affiliate_info)) {
            $data['cheque'] = $affiliate_info['cheque'];
        } else {
            $data['cheque'] = '';
        }

        if (isset($this->request->post['paypal'])) {
            $data['paypal'] = $this->request->post['paypal'];
        } elseif (!empty($affiliate_info)) {
            $data['paypal'] = $affiliate_info['paypal'];
        } else {
            $data['paypal'] = '';
        }

        if (isset($this->request->post['bank_name'])) {
            $data['bank_name'] = $this->request->post['bank_name'];
        } elseif (!empty($affiliate_info)) {
            $data['bank_name'] = $affiliate_info['bank_name'];
        } else {
            $data['bank_name'] = '';
        }

        if (isset($this->request->post['bank_branch_number'])) {
            $data['bank_branch_number'] = $this->request->post['bank_branch_number'];
        } elseif (!empty($affiliate_info)) {
            $data['bank_branch_number'] = $affiliate_info['bank_branch_number'];
        } else {
            $data['bank_branch_number'] = '';
        }

        if (isset($this->request->post['bank_swift_code'])) {
            $data['bank_swift_code'] = $this->request->post['bank_swift_code'];
        } elseif (!empty($affiliate_info)) {
            $data['bank_swift_code'] = $affiliate_info['bank_swift_code'];
        } else {
            $data['bank_swift_code'] = '';
        }

        if (isset($this->request->post['bank_account_name'])) {
            $data['bank_account_name'] = $this->request->post['bank_account_name'];
        } elseif (!empty($affiliate_info)) {
            $data['bank_account_name'] = $affiliate_info['bank_account_name'];
        } else {
            $data['bank_account_name'] = '';
        }

        if (isset($this->request->post['bank_account_number'])) {
            $data['bank_account_number'] = $this->request->post['bank_account_number'];
        } elseif (!empty($affiliate_info)) {
            $data['bank_account_number'] = $affiliate_info['bank_account_number'];
        } else {
            $data['bank_account_number'] = '';
        }

        if (isset($this->request->post['custom_field'])) {
            $data['affiliate_custom_field'] = $this->request->post['custom_field'];
        } elseif (!empty($affiliate_info)) {
            $data['affiliate_custom_field'] = json_decode($affiliate_info['custom_field'], true);
        } else {
            $data['affiliate_custom_field'] = array();
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        //delivery partner info
        $deliveryPartnerInfo = $this->model_customer_customer->getDocumentsInfo($this->request->get['customer_id']);
        $data['partner_approve'] = $deliveryPartnerInfo['is_approved'];
        $data['delivery_type'] = $deliveryPartnerInfo['delivery_type'];
        $data['vehicle_type'] = $deliveryPartnerInfo['vehicle_type'];
        $data['per_hour_rate'] = $deliveryPartnerInfo['per_hour_rate'];
        $data['currency_code'] = $deliveryPartnerInfo['currency_id'];
        $data['currencies'] = $this->model_localisation_currency->getCurrencies();

        $uploads_dir = '/image/deliverypartner/customer-'.$this->request->get['customer_id'].'/';
        $data['link_id_proof'] = !empty($deliveryPartnerInfo['id_proof']) ? $this->config->get('config_ssl').$uploads_dir.$deliveryPartnerInfo['id_proof'] : '';
        $data['link_address_proof'] = !empty($deliveryPartnerInfo['address_proof']) ? $this->config->get('config_ssl').$uploads_dir.$deliveryPartnerInfo['address_proof'] : '';
        $data['link_travel_license'] = !empty($deliveryPartnerInfo['travel_license']) ? $this->config->get('config_ssl').$uploads_dir.$deliveryPartnerInfo['travel_license'] : '';
        $data['link_vehicle_insurance'] = !empty($deliveryPartnerInfo['vehicle_insurance']) ? $this->config->get('config_ssl').$uploads_dir.$deliveryPartnerInfo['vehicle_insurance'] : '';

        $this->response->setOutput($this->load->view('customer/delivery_partner_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'customer/delivery_partner')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = $this->language->get('error_email');
        }

        $customer_info = $this->model_customer_customer->getCustomerByEmail($this->request->post['email']);

        if (!isset($this->request->get['customer_id'])) {
            if ($customer_info) {
                $this->error['warning'] = $this->language->get('error_exists');
            }
        } else {
            if ($customer_info && ($this->request->get['customer_id'] != $customer_info['customer_id'])) {
                $this->error['warning'] = $this->language->get('error_exists');
            }
        }

        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        // Custom field validation
        $this->load->model('customer/custom_field');

        $custom_fields = $this->model_customer_custom_field->getCustomFields(array('filter_customer_group_id' => $this->request->post['customer_group_id']));

        foreach ($custom_fields as $custom_field) {
            if (($custom_field['location'] == 'account') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
                $this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
            } elseif (($custom_field['location'] == 'account') && ($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
                $this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
            }
        }

        if ($this->request->post['password'] || (!isset($this->request->get['customer_id']))) {
            if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
                $this->error['password'] = $this->language->get('error_password');
            }

            if ($this->request->post['password'] != $this->request->post['confirm']) {
                $this->error['confirm'] = $this->language->get('error_confirm');
            }
        }

        if ($this->request->post['affiliate']) {
            if ($this->request->post['payment'] == 'cheque') {
                if ($this->request->post['cheque'] == '') {
                    $this->error['cheque'] = $this->language->get('error_cheque');
                }
            } elseif ($this->request->post['payment'] == 'paypal') {
                if ((utf8_strlen($this->request->post['paypal']) > 96) || !filter_var($this->request->post['paypal'], FILTER_VALIDATE_EMAIL)) {
                    $this->error['paypal'] = $this->language->get('error_paypal');
                }
            } elseif ($this->request->post['payment'] == 'bank') {
                if ($this->request->post['bank_account_name'] == '') {
                    $this->error['bank_account_name'] = $this->language->get('error_bank_account_name');
                }

                if ($this->request->post['bank_account_number'] == '') {
                    $this->error['bank_account_number'] = $this->language->get('error_bank_account_number');
                }
            }

            if (!$this->request->post['tracking']) {
                $this->error['tracking'] = $this->language->get('error_tracking');
            }

            $affiliate_info = $this->model_customer_customer->getAffliateByTracking($this->request->post['tracking']);

            if (!isset($this->request->get['customer_id'])) {
                if ($affiliate_info) {
                    $this->error['tracking'] = $this->language->get('error_tracking_exists');
                }
            } else {
                if ($affiliate_info && ($this->request->get['customer_id'] != $affiliate_info['customer_id'])) {
                    $this->error['tracking'] = $this->language->get('error_tracking_exists');
                }
            }

            foreach ($custom_fields as $custom_field) {
                if (($custom_field['location'] == 'affiliate') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
                    $this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
                } elseif (($custom_field['location'] == 'affiliate') && ($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
                    $this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
                }
            }
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }
}