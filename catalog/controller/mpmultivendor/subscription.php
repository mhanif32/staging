<?php

class ControllerMpmultivendorSubscription extends Controller
{
    public function index()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('mpmultivendor/subscription', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->language('mpmultivendor/subscription');
        $this->load->model('mpmultivendor/subscription');
        $this->load->model('account/customer');

        $customer = $this->model_account_customer->getStripeCustomerId($this->customer->getId());

        if (empty($customer['role']) || $customer['role'] != 'seller') {

            $this->response->redirect($this->url->link('account/login', '', true));
        }


        if (!$this->config->get('mpmultivendor_status')) {
            $this->response->redirect($this->url->link('common/home', '', true));
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

        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        //plans
        $data['plans'] = $this->model_mpmultivendor_subscription->getSubscriptionPlans();
        $data['action_subscribe'] = $this->url->link('mpmultivendor/subscription/add', '', true);
        $this->response->setOutput($this->load->view('mpmultivendor/subscription', $data));
    }

    public function add()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        $this->load->model('mpmultivendor/subscription');
        $this->load->model('account/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->load->library('stripe');
            \Stripe\Stripe::setApiKey('sk_test_51H8inyJvSOEFkXrXcFESwhvDkx08F0DI8KfkUnwO14cGKdxv36U0hWj9GSusI2ZMrd3NJaLBI3u13Q26Uj9osSTH00b6wMWu3v');

            // Token is created using Stripe Checkout or Elements!, Get the payment token ID submitted by the form:
            $token = $this->request->post['stripe_token'];
            $sellerName = $this->request->post['sellerName'];
            $product = $this->request->post['radioPlan'];
            $stripe_card = $this->request->post['stripe_card'];
            $stripe_cvc = $this->request->post['stripe_cvc'];
            $stripe_expdate = $this->request->post['stripe_expdate'];
            $expDate = explode('/', $stripe_expdate);
            //echo '<pre>';print_r($this->request->post);exit('okoko');

            $customer = $this->model_account_customer->getStripeCustomerId($this->customer->getId());

            if(empty($customer['stripe_customer_id'])) {

                 $paymentMethod = \Stripe\PaymentMethod::create([
                    'type' => 'card',
                    'card' => [
                        'number' => '4242 4242 4242 4242',
                        'exp_month' => '05',
                        'exp_year' => '2024',
                        'cvc' => '262',
                    ],
                ]);

                $customerData = \Stripe\Customer::create([
                    'name' => $sellerName,
                    'email' => $customer['email'],
                    'payment_method' => $paymentMethod['id'],
                ]);
                //print_r($customerData['id']);exit('bbb');
                $customerId = $customerData['id'];
            } else {
                $customerId = $customer['stripe_customer_id'];
            }
            //print_r($sellerName);exit('okok');

            \Stripe\Subscription::create([
                "customer" => $customerId,
                'items' => [['plan' => $plan->plan_id]],
//                "items" => [
//                    'data' => [
//                        'price' => [
//                            'product' => $product
//                        ]
//                    ]
//                ],
            ]);
        }
    }

    protected function validateForm()
    {
        return true;
//        if (!$this->user->hasPermission('modify', 'mpmultivendor/subscription/add')) {
//            $this->error['warning'] = $this->language->get('error_permission');
//        }
//
//        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
//            $this->error['name'] = $this->language->get('error_name');
//        }
//        return !$this->error;
    }

    protected function getCurrentPlan()
    {

    }
}