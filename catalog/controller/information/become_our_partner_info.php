<?php
class ControllerInformationBecomeOurPartnerInfo extends Controller {
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

        $becomeOurPartner = $this->model_catalog_information->getInfoMessage(14);
        $data['becomeOurPartner'] = $becomeOurPartner['title'];
        $data['becomeOurPartnerDesc'] = html_entity_decode($becomeOurPartner['description'], ENT_QUOTES, 'UTF-8');

        $data['customer_register_link'] = $this->url->link('account/register', '', true);


        // Load the template file and show output
        $this->response->setOutput($this->load->view('information/become_our_partner_info', $data));

    }
}