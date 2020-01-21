<?php
class ControllerCommonProfileColumnLeft extends Controller {
    public function index() {

        $data = [];
        $data['edit'] = $this->url->link('account/edit', '', true);
        $data['password'] = $this->url->link('account/password', '', true);
        $data['address'] = $this->url->link('account/address', '', true);
        return $this->load->view('common/profile_column_left', $data);
    }
}
