<?php
class ControllerCommonProfileColumnLeft extends Controller {
    public function index() {

        $customer_info = [];
        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
            //print_r($customer_info['firstname']);exit('aaa');
        }


        $data = [];
        $data['edit'] = $this->url->link('account/edit', '', true);
        $data['password'] = $this->url->link('account/password', '', true);
        $data['address'] = $this->url->link('account/address', '', true);
        $data = array_merge($data, $customer_info);
        return $this->load->view('common/profile_column_left', $data);
    }
}
