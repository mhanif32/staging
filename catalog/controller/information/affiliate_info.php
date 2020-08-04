<?php
class ControllerInformationAffiliateInfo extends Controller {
    public function index() {
              // All the necessary page elements
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $data['franchisee_link'] = $this->url->link('affiliate/register', '', true);


        $becomeFranchisee = $this->model_catalog_information->getInfoMessage(12);
        $data['becomeFranchisee'] = $becomeFranchisee['title'];
        $data['becomeFranchiseeDesc'] = html_entity_decode($becomeFranchisee['description'], ENT_QUOTES, 'UTF-8');

        // Load the template file and show output
        $this->response->setOutput($this->load->view('information/affiliate_info', $data));
    }
}