<?php
class ControllerInformationStepsUploadProductInfo extends Controller {
    public function index() {
        // Optional. This calls for your language file
        $this->load->language('information/delivery_info');

        // Optional. Set the title of your web page
        $this->document->setTitle($this->language->get('heading_title'));

        // Get "heading_title" from language file
        $data['heading_title'] = $this->language->get('heading_title');

        // All the necessary page elements
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');




        $stepsUploadProduct = $this->model_catalog_information->getInfoMessage(16);
        $data['stepsUploadProduct'] = $stepsUploadProduct['title'];
        $data['stepsUploadProductDesc'] = html_entity_decode($stepsUploadProduct['description'], ENT_QUOTES, 'UTF-8');

        // Load the template file and show output
        $this->response->setOutput($this->load->view('information/steps_upload_product_info', $data));
    }
}