<?php
class ControllerCommonProfileColumnLeft extends Controller {
    public function index() {

        $customer_info = [];
        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $this->load->model('account/customer');
            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
        }

        $data = [];
        //my account list
        $data['account'] = $this->url->link('account/account', '', true);
        $data['edit'] = $this->url->link('account/edit', '', true);
        $data['change_password'] = $this->url->link('account/password', '', true);
        $data['address'] = $this->url->link('account/address', '', true);

        //order list
        $data['order'] = $this->url->link('account/order', '', true);
        $data['download'] = $this->url->link('account/download', '', true);
        $data['reward'] = $this->url->link('account/reward', '', true);
        $data['return'] = $this->url->link('account/return', '', true);
        $data['transaction'] = $this->url->link('account/transaction', '', true);
        $data['recurring'] = $this->url->link('account/recurring', '', true);

        $data['wishlist'] = $this->url->link('account/wishlist', '', true);
        $data['logout'] = $this->url->link('account/logout', '', true);

        $data = array_merge($data, $customer_info);
        $file = !empty($customer_info['image']) ? $customer_info['image'] : 'no-avatar.png';
        $data['image_url'] = '/storage/upload/' . $file;
        return $this->load->view('common/profile_column_left', $data);
    }
}
