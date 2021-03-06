<?php

class ControllerAccountEdit extends Controller
{
    private $error = array();

    public function index()
    {
//        error_reporting(E_ALL);
//        ini_set("display_errors", 1);
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/edit', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->language('account/edit');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

        $this->load->model('account/customer');
        $this->load->model('account/address');
        $this->load->model('localisation/country');

        $customer = $this->model_account_customer->getCustomer($this->customer->getId());

//breadcrumbs
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
            'text' => $this->language->get('text_edit'),
            'href' => $this->url->link('account/edit', '', true)
        );
//end
        //edit customer
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            //echo '<pre>'; print_r($this->customer->getRole());exit('aaa');

            //upload Image
            $json = array();
            if (!empty($this->request->files['avatar']['name']) && is_file($this->request->files['avatar']['tmp_name'])) {
                // Sanitize the filename
                $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['avatar']['name'], ENT_QUOTES, 'UTF-8')));

                // Validate the filename length
                if ((strlen($filename) < 3) || (strlen($filename) > 64)) {
                    $json['error'] = 'Filename is too long!';
                }

                // Allowed file extension types
                $allowed = array();
                $extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));
                $filetypes = explode("\n", $extension_allowed);
                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

                if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
                    $json['error'] = 'File Type Error!';
                }

                // Allowed file mime types
                $allowed = array();
                $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));
                $filetypes = explode("\n", $mime_allowed);
                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }
                if (!in_array($this->request->files['avatar']['type'], $allowed)) {
                    $json['error'] = 'File Type Error!';
                }
                // Return any upload error
                if ($this->request->files['avatar']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = 'File Upload Error!';
                }
            } else {
                $json['error'] = 'File Upload Error!';
            }

            if (!$json) {
                $file = $filename . '.' . token(32);
                move_uploaded_file($this->request->files['avatar']['tmp_name'], DIR_UPLOAD . $file);
                // Hide the uploaded file name so people can not link to it directly.
                $this->load->model('tool/upload');
                $json['code'] = $this->addUpload($filename, $file);

                $this->model_account_customer->editAvatar($this->customer->getId(), $file, $json['code']);
            }

            $this->model_account_customer->editCustomer($this->customer->getId(), $this->request->post);

            // save the countries for delivery partner
            if(!empty($customer['role']) && $customer['role'] == 'delivery-partner') {
                $this->model_account_customer->saveCountries($this->customer->getId(), $this->request->post);
            }
            //print_r($role);exit('aaa');

            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('account/account', '', true));
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

        if (isset($this->error['date_of_birth'])) {
            $data['error_date_of_birth'] = $this->error['date_of_birth'];
        } else {
            $data['error_date_of_birth'] = '';
        }

        if (isset($this->error['address'])) {
            $data['error_address'] = $this->error['address'];
        } else {
            $data['error_address'] = '';
        }

        //$address = $this->model_account_address->getDefaultAddress();

        if (isset($this->error['gender'])) {
            $data['error_gender'] = $this->error['gender'];
        } else {
            $data['error_gender'] = '';
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

        if (isset($this->error['custom_field'])) {
            $data['error_custom_field'] = $this->error['custom_field'];
        } else {
            $data['error_custom_field'] = array();
        }

        $data['action'] = $this->url->link('account/edit', '', true);

        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
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

        if (isset($this->request->post['date_of_birth'])) {
            $data['date_of_birth'] = $this->request->post['date_of_birth'];
        } elseif (!empty($customer_info)) {
            $data['date_of_birth'] = $customer_info['date_of_birth'];
        } else {
            $data['date_of_birth'] = '';
        }

        if (isset($this->request->post['gender'])) {
            $data['gender'] = $this->request->post['gender'];
        } elseif (!empty($customer_info)) {
            $data['gender'] = $customer_info['gender'];
        } else {
            $data['gender'] = '';
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
        $data['custom_fields'] = array();

        $this->load->model('account/custom_field');

        $custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

        foreach ($custom_fields as $custom_field) {
            if ($custom_field['location'] == 'account') {
                $data['custom_fields'][] = $custom_field;
            }
        }

        if (isset($this->request->post['custom_field']['account'])) {
            $data['account_custom_field'] = $this->request->post['custom_field']['account'];
        } elseif (isset($customer_info)) {
            $data['account_custom_field'] = json_decode($customer_info['custom_field'], true);
        } else {
            $data['account_custom_field'] = array();
        }

        $data['back'] = $this->url->link('account/account', '', true);
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $data['profile_column_left'] = $this->load->controller('common/profile_column_left');
        $data['deactivate_link'] = $this->url->link('account/account/deactivate', '', true);
        $data['manage_address'] = $this->url->link('account/address', '', true);
        $data['delivery_link'] = $this->url->link('account/account/addDeliveryInfo', '', true);

        $address = $this->model_account_address->getDefaultAddress();
        $data['home_default_address'] = $address;
        $data['role'] = $customer['role'];



//        $data['my_countries'] = array();
//        $my_countries = $this->model_localisation_country->getSavedCountries($this->customer->getId());
//        foreach ($my_countries as $country_id) {
//            $country_info = $this->model_localisation_country->getCountry($country_id['country_id']);
//            //print_r($country_info);exit();
//            if ($country_info) {
//                $data['my_countries'][] = array(
//                    'country_id' => $country_info['country_id'],
//                    'name' => $country_info['name']
//                );
//            }
//        }

        $file = !empty($customer_info['image']) ? $customer_info['image'] : 'no-avatar.png';
        $data['image_url'] = '/storage/upload/' . $file;
        $this->response->setOutput($this->load->view('account/edit', $data));
    }

    protected function addUpload($name, $filename) {
        $code = sha1(uniqid(mt_rand(), true));
        return $code;
    }

    protected function validate()
    {
        if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }

        if ((utf8_strlen(trim($this->request->post['date_of_birth'])) < 1) || (utf8_strlen(trim($this->request->post['date_of_birth'])) > 32)) {
            $this->error['date_of_birth'] = $this->language->get('error_date_of_birth');
        }

        $address = $this->model_account_address->getDefaultAddress();
        if (empty($address)) {
            $this->error['error_address'] = $this->language->get('error_address');
        }
//echo '<pre>'; print_r($address);exit('asd');
        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $this->error['warning'] = $this->language->get('error_exists');
        }

        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        // Custom field validation
        $this->load->model('account/custom_field');

        $custom_fields = $this->model_account_custom_field->getCustomFields('account', $this->config->get('config_customer_group_id'));

        foreach ($custom_fields as $custom_field) {
            if ($custom_field['location'] == 'account') {
                if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
                    $this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
                } elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
                    $this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
                }
            }
        }

        return !$this->error;
    }

    public function country_autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('localisation/country');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'sort' => 'name',
                'order' => 'ASC',
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_localisation_country->getCountriesFilter($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'country_id' => $result['country_id'],
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
}