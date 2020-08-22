<?php
class ControllerInformationBecomeExecutiveMemberInfo extends Controller {
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


        $data['my_account_href'] = $this->url->link('account/edit', '', true);
        $data['executive_member_register_link'] = $this->url->link('account/register', '&role=executive-member', true);

        $becomeExecutiveMember = $this->model_catalog_information->getInfoMessage(17);
        $data['becomeExecutiveMember'] = $becomeExecutiveMember['title'];
        $data['becomeExecutiveMemberDesc'] = html_entity_decode($becomeExecutiveMember['description'], ENT_QUOTES, 'UTF-8');

        // Load the template file and show output
        $this->response->setOutput($this->load->view('information/become_executive_member_info', $data));
    }
}