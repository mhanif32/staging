<?php
class ControllerAccountSeller extends Controller {

    private $error = array();

    public function index() {
        $data = array();

        $this->response->setOutput($this->load->view('account/seller', $data));
    }

    public function profile() {
        $data = array();

        $owner_id = $this->request->get['owner_id'];
        $sellerData = $this->model_account_customer->isSeller($owner_id);
        if (!$sellerData) {
            $this->response->redirect($this->url->link('common/home', '', true));
        }

        $seller_info = $this->model_account_mpmultivendor_seller->getSellerInfo($owner_id);
        $seller['filter_mpseller_id'] = $seller_info['mpseller_id'];

        //for products listing
        $products = $this->model_account_mpmultivendor_product->getProducts($seller);
        foreach ($products as $product) {

            if ($product['image']) {
                $image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
            }

            $data['products'][] = array(
                'name' => $product['name'],
                'image' => $image,
                //'rating'      => $service['rating'],
                'href' => $this->url->link('product/product', '&product_id='.$product['product_id'], true)
            );
        }

        $data['owner_name'] = $sellerData['firstname'] . ' ' . $sellerData['lastname'];
        $data['owner_description'] = $seller_info['description'];
        $data['owner_address'] = $seller_info['address'] . ' ' . $seller_info['city'];
        $data['owner_email'] = $seller_info['email'];

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('account/seller_profile', $data));
    }
}