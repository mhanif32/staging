<?php

class ControllerAccountNewsletter extends Controller
{
    public function index()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/newsletter', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->language('account/newsletter');

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            $this->load->model('account/customer');

            $this->model_account_customer->editNewsletter($this->request->post['newsletter']);

            //for mailchimp
            if (isset($this->request->post['newsletter']) && $this->request->post['newsletter'] == 1) {

                require_once(DIR_SYSTEM.'library/Mailchimp.php');

                $api_key = "8eb36d68fdd192272667a1d18e4b5e89-us2";
                $list_id = "c8a7f538d4";

                $Mailchimp = new Mailchimp($api_key);
                $Mailchimp_Lists = new Mailchimp_Lists($Mailchimp);

                try {
                    $subscriber = $Mailchimp_Lists->subscribe(
                        $list_id,
                        array(
                            'email' => $this->customer->getEmail()
                        ),      // Specify the e-mail address you want to add to the list.
                        array(
                            'FNAME' => $this->customer->getFirstName(),
                            'LNAME' => $this->customer->getLastName(),
                        ),   // Set the first name and last name for the new subscriber.
                        'text',    // Specify the e-mail message type: 'html' or 'text'
                        FALSE,     // Set double opt-in: If this is set to TRUE, the user receives a message to confirm they want to be added to the list.
                        TRUE       // Set update_existing: If this is set to TRUE, existing subscribers are updated in the list. If this is set to FALSE, trying to add an existing subscriber causes an error.
                    );
                } catch (Exception $e) {
                    echo "Caught exception: " . $e;
                }

                if (!empty($subscriber['leid'])) {
                    echo "Subscriber added successfully.";
                } else {
                    echo "Subscriber add attempt failed.";
                }
            }


            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('account/account', '', true));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_newsletter'),
            'href' => $this->url->link('account/newsletter', '', true)
        );

        $data['action'] = $this->url->link('account/newsletter', '', true);

        $data['newsletter'] = $this->customer->getNewsletter();

        $data['back'] = $this->url->link('account/account', '', true);

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('account/newsletter', $data));
    }
}