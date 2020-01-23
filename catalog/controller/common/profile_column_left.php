<?php
class ControllerCommonProfileColumnLeft extends Controller {
    public function index() {

        $customer_info = [];
        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
        }

        $data = [];
        $data['account'] = $this->url->link('account/account', '', true);
        $data['edit'] = $this->url->link('account/edit', '', true);
        $data['password'] = $this->url->link('account/password', '', true);
        $data['address'] = $this->url->link('account/address', '', true);
        $data = array_merge($data, $customer_info);
        //print_r($customer_info);exit('asd');

        $file = !empty($customer_info['image']) ? $customer_info['image'] : 'no-avatar.png';
        $data['image_url'] = '/storage/upload/' . $file;
        //echo $data['image_url'];exit('aaaa');
        return $this->load->view('common/profile_column_left', $data);
    }
}
