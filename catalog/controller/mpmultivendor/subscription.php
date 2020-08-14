<?php

class ControllerMpmultivendorSubscription extends Controller
{
    public function index()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        if (!$this->config->get('mpmultivendor_status')) {
            $this->response->redirect($this->url->link('common/home', '', true));
        }

        $this->load->language('mpmultivendor/subscription');
        $this->load->model('mpmultivendor/subscription');
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

            $customer = $this->model_account_customer->getStripeCustomerId($this->customer->getId());
            //print_r($sellerName);exit('okok');
            if(empty($customer['stripe_customer_id'])) {

                 $paymentMethod = \Stripe\PaymentMethod::create([
                    'type' => 'card',
                    'card' => [
                        'number' => '4242424242424242',
                        'exp_month' => 8,
                        'exp_year' => 2021,
                        'cvc' => '314',
                    ],
                ]);

                $customerData = \Stripe\Customer::create([
                    'name' => $sellerName,
                    'email' => $customer['email'],
                    'payment_method' => $paymentMethod['id'],
                    //'source' => $token,
//                    'email' => $this->customer->getEmail()
                ]);
                $customerId = $customerData['id'];
            } else {
                $customerId = $customer['stripe_customer_id'];
            }
            //print_r($sellerName);exit('okok');

            \Stripe\Subscription::create([
                "customer" => $customerId,
                "plan" => 'gold'
//                "items" => [
//                    'data' => [
//                        'price' => [
//                            'product' => $product
//                        ]
//                    ]
//                ],
            ]);

//            $charge = \Stripe\Charge::create([
//                'amount' => $planAmt,
//                'customer' => $customerId,
//                'currency' => 'usd',
//                'description' => '',
//                'source' => $token,
//            ]);

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