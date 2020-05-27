<?php
class ControllerLocalisationArea extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('localisation/area');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/area');

        $this->getList();
    }

    public function add() {

        $this->load->language('localisation/area');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/area');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_localisation_area->addArea($this->request->post);

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

            $this->response->redirect($this->url->link('localisation/area', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function upload() {

//        error_reporting(E_ALL);
//        ini_set("display_errors", 1);
        $this->load->model('localisation/area');
        $this->load->language('localisation/area_upload');

        $this->document->setTitle($this->language->get('heading_title'));

            if (isset($this->request->files['upload_file']) && $this->validateUpload()) {
            $this->importAreas();

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

            $this->response->redirect($this->url->link('localisation/area', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('localisation/upload', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['user_token'] = $this->session->data['user_token'];
        $data['cancel'] = $this->url->link('localisation/area', 'user_token=' . $this->session->data['user_token'], true);
        $this->response->setOutput($this->load->view('localisation/area_upload', $data));
    }

    public function edit() {
        $this->load->language('localisation/area');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/area');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_localisation_area->editArea($this->request->get['area_id'], $this->request->post);

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

            $this->response->redirect($this->url->link('localisation/area', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {

        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        $this->load->language('localisation/area');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/area');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $area_id) {
                $this->model_localisation_area->deleteArea($area_id);
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

            $this->response->redirect($this->url->link('localisation/area', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'c.name';
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
            'href' => $this->url->link('localisation/area', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('localisation/area/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['upload'] = $this->url->link('localisation/area/upload', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('localisation/area/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['areas'] = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $area_total = $this->model_localisation_area->getTotalAreas();

        $results = $this->model_localisation_area->getAreas($filter_data);

        foreach ($results as $result) {
            $data['areas'][] = array(
                'area_id' => $result['area_id'],
                'country' => $result['country'],
                'zone'    => $result['state'],
                'name'    => $result['area'] . (($result['area_id'] == $this->config->get('config_area_id')) ? $this->language->get('text_default') : null),
                'code'    => $result['code'],
                'edit'    => $this->url->link('localisation/area/edit', 'user_token=' . $this->session->data['user_token'] . '&area_id=' . $result['area_id'] . $url, true)
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

        $data['sort_country'] = $this->url->link('localisation/area', 'user_token=' . $this->session->data['user_token'] . '&sort=c.name' . $url, true);
        $data['sort_name'] = $this->url->link('localisation/area', 'user_token=' . $this->session->data['user_token'] . '&sort=z.name' . $url, true);
        $data['sort_code'] = $this->url->link('localisation/area', 'user_token=' . $this->session->data['user_token'] . '&sort=z.code' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $area_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('localisation/area', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($area_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($area_total - $this->config->get('config_limit_admin'))) ? $area_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $area_total, ceil($area_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('localisation/area_list', $data));
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['area_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
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
            'href' => $this->url->link('localisation/area', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['area_id'])) {
            $data['action'] = $this->url->link('localisation/area/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('localisation/area/edit', 'user_token=' . $this->session->data['user_token'] . '&area_id=' . $this->request->get['area_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('localisation/area', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['area_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $area_info = $this->model_localisation_area->getArea($this->request->get['area_id']);
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($area_info)) {
            $data['status'] = $area_info['status'];
        } else {
            $data['status'] = '1';
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($area_info)) {
            $data['name'] = $area_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['code'])) {
            $data['code'] = $this->request->post['code'];
        } elseif (!empty($area_info)) {
            $data['code'] = $area_info['code'];
        } else {
            $data['code'] = '';
        }

        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');

        if (isset($this->request->post['country_id'])) {
            $data['country_id'] = $this->request->post['country_id'];
        } elseif (!empty($area_info)) {
            $data['country_id'] = $area_info['country_id'];
        } else {
            $data['country_id'] = '';
        }

        if (isset($this->request->post['zone_id'])) {
            $data['zone_id'] = $this->request->post['zone_id'];
        } elseif (!empty($area_info)) {
            $data['zone_id'] = $area_info['zone_id'];
            $data['zones'] = $this->model_localisation_zone->getZonesByCountryId($data['country_id']);
        } else {
            $data['zone_id'] = '';
        }


        $data['countries'] = $this->model_localisation_country->getCountries();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['user_token'] = $this->session->data['user_token'];

        $this->response->setOutput($this->load->view('localisation/area_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'localisation/area')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        return !$this->error;
    }

    protected function validateUpload() {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        if (!$this->user->hasPermission('modify', 'localisation/area')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (empty($this->request->files['upload_file']['tmp_name'])) {
            $this->error['upload'] = $this->language->get('error_upload');
        }

        return !$this->error;
    }

    public function importAreas()
    {
        require_once(DIR_SYSTEM.'library/PHPExcel.php');

        $this->load->model('localisation/area');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $this->load->language('localisation/area_upload');

        $json = array();

        if(empty($this->request->files['upload_file'])) {
            $json['error']['file'] = $this->language->get('error_file');
            $json['error']['warning'] = $this->language->get('error_file');
        }

        // Check to see if any PHP files are trying to be uploaded
        if(!empty($this->request->files['upload_file'])) {
            $content = file_get_contents($this->request->files['upload_file']['tmp_name']);

            if (preg_match('/\<\?php/i', $content)) {
                $json['error']['file'] = $this->language->get('error_filetype');
                $json['error']['warning'] = $this->language->get('error_filetype');
            }

            // Return any upload error
            if ($this->request->files['upload_file']['error'] != UPLOAD_ERR_OK) {
                $json['error']['file'] = $this->language->get('error_upload_' . $this->request->files['upload_file']['error']);
                $json['error']['warning'] = $this->language->get('error_upload_' . $this->request->files['upload_file']['error']);
            }
        }

        if(!$json && $this->request->files) {
            $file = basename($this->request->files['upload_file']['name']);
            move_uploaded_file($this->request->files['upload_file']['tmp_name'], $file);
            $inputFileName = $file;

            $extension = pathinfo($inputFileName);

            $extension['extension'] = strtolower(strtoupper($extension['extension']));

            if(!in_array($extension['extension'], array('xls','xlsx','csv'))) {
                $json['error']['file'] = $this->language->get('error_format_diff');
                $json['error']['warning'] = $this->language->get('error_format_diff');
            }

            if($extension['extension']=='xlsx' || $extension['extension']=='xls' || $extension['extension']=='csv') {
                try{
                    $inputFileType = $extension['extension'];

                    if($extension['extension']=='csv') {
                        $objReader  = PHPExcel_IOFactory::createReader('CSV');
                        $objPHPExcel = $objReader->load($inputFileName);
                    }else{
                        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
                    }
                }catch(Exception $e){
                    $json['error']['warning'] = $this->language->get('error_loading_file') .'"'. pathinfo($inputFileName,PATHINFO_BASENAME) .'": '.$e->getMessage();
                }
            }
        }

        if(!$json) {
            /*if(isset($this->request->get['find_importon'])) {
                $find_importon = $this->request->get['find_importon'];
            } else{
                $find_importon = 'product_id';
            }

            if(isset($this->request->get['find_store'])) {
                $find_store = $this->request->get['find_store'];
            } else{
                $find_store = array();
            }

            if(isset($this->request->get['find_language_id'])) {
                $find_language_id = $this->request->get['find_language_id'];
            } else{
                $find_language_id = 0;
            }

            if(isset($this->request->get['find_existsupdate'])) {
                $find_existsupdate = $this->request->get['find_existsupdate'];
            } else{
                $find_existsupdate = 0;
            }
            if(isset($this->request->get['find_cell_operations'])) {
                $find_cell_operations = $this->request->get['find_cell_operations'];
            } else{
                $find_cell_operations = array();
            }

            $find_data = array(
                'store'					=> $find_store,
                'language_id'			=> $find_language_id,
                'importon'				=> $find_importon,
                'existsupdate'			=> $find_existsupdate,
                'cell_operations'		=> $find_cell_operations,
                'mpseller_id'			=> $mpseller_id,
            );*/

            if(isset($this->request->get['find_language_id'])) {
                $find_language_id = $this->request->get['find_language_id'];
            } else {
                $find_language_id = 0;
            }

            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

            $i=0;
            $insert_area = 0;
            $edit_area = 0;
            $static_columns = 57;

            if(count($allDataInSheet) > 1) {
                foreach($allDataInSheet as $default_value) {
                    $value = $this->clean($default_value);

                    // Column Values
                    if($i != '0') {
                        $area_id = (isset($value['A']) ? (int)$value['A'] : '');
                        $area_name = (isset($value['B']) ? $value['B'] : '');

                        if(trim($area_name) == '') {
                            continue;
                        }

                        $country = (isset($value['C']) ? $value['C'] : '');
                        $zone = (isset($value['D']) ? $value['D'] : '');
                        $area_code = (isset($value['E']) ? $value['E'] : '');
                        $status = (isset($value['F']) ? $value['F'] : '');

                        // Insert Data
                        $insert_data = array();

                        $countryData = $this->model_localisation_country->getCountryByName($country);
                        $zoneData = $this->model_localisation_zone->getZoneByName($zone);

                        $insert_data['area_id'] 							= $area_id;
                        $insert_data['name']   						        = $area_name;
                        $insert_data['code'] 	    						= $area_code;
                        $insert_data['country_id']  						= $countryData['country_id'];
                        $insert_data['zone_id']  						    = $zoneData['zone_id'];
                        $insert_data['status']							    = $status;

                        $area_info = $this->model_localisation_area->getAreaByName($area_name, $find_language_id);

                        if($area_info) {
                            // Update Exists Product
                                $this->model_localisation_area->editArea($area_info['area_id'], $insert_data);

                                $edit_area++;
                        } else {
                            // Insert Exists Product
                            $this->model_localisation_area->addArea($insert_data);

                            $insert_area++;
                        }
                    }

                    $i++;
                }

                $text_success  = $this->language->get('text_success');

                if($edit_area) {
                    $text_success .= sprintf($this->language->get('text_success_update'), $edit_area);
                }

                if($insert_area) {
                    $text_success .= sprintf($this->language->get('text_success_insert'), $insert_area);
                }

                if(empty($insert_product) && empty($edit_product)) {
                    $text_success = $this->language->get('text_success_zero');
                }

                $json['success'] = $text_success;
            } else{
                $json['error']['warning'] = $this->language->get('text_no_result');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function clean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);

                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            if (ini_get('magic_quotes_gpc')) {
                $data = htmlspecialchars(stripslashes($data), ENT_COMPAT, 'UTF-8');
            } else {
                $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
            }
        }

        return $data;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'localisation/area')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('setting/store');
        $this->load->model('customer/customer');
        $this->load->model('localisation/area');

        foreach ($this->request->post['selected'] as $area_id) {
            if ($this->config->get('config_area_id') == $area_id) {
                $this->error['warning'] = $this->language->get('error_default');
            }

//            $store_total = $this->model_setting_store->getTotalStoresByAreaId($area_id);
//
//            if ($store_total) {
//                $this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
//            }

//            $address_total = $this->model_customer_customer->getTotalAddressesByAreaId($area_id);
//
//            if ($address_total) {
//                $this->error['warning'] = sprintf($this->language->get('error_address'), $address_total);
//            }

//            $area_to_area_total = $this->model_localisation_area->getTotalAreaToGeoZoneByZoneId($area_id);
//
//            if ($area_to_area_total) {
//                $this->error['warning'] = sprintf($this->language->get('error_area_to_area'), $area_to_area_total);
//            }
        }

        return !$this->error;
    }
}