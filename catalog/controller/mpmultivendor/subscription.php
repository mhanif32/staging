<?php

class ControllerMpmultivendorSubscription extends Controller
{
    public function index()
    {
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
            $plan_id = $this->request->post['plan_id'];

            $customer = $this->model_account_customer->getStripeCustomerId($this->customer->getId());
            if(empty($customer['stripe_customer_id'])) {

                $customerData = \Stripe\Customer::create([
                    'name' => $sellerName,
                    'email' => $customer['email'],
                    'source' => $token
                ]);
                $customerId = $customerData['id'];
            } else {
                $customerId = $customer['stripe_customer_id'];
            }

            $stripedata = \Stripe\Subscription::create([
                "customer" => $customerId,
                'items' => [['plan' => $product]],
            ]);

            $subscriptionData = [
                'customer_id' => $this->customer->getId(),
                'subscription_plan_id' => $plan_id,
                'stripe_subscription_id' => $stripedata['id'],
                'stripe_customer_id' => $stripedata['customer'],
                'stripe_status' => $stripedata['status'],
                'amount' => $stripedata['plan']['amount'] / 100,
                'start_date' => $stripedata['current_period_start'],
                'end_date' => $stripedata['current_period_end']
            ];
            $this->model_mpmultivendor_subscription->createUserSubscription($subscriptionData);

            $this->session->data['success'] = 'Success : Your plan has been successfully subscribed.';
            $this->response->redirect($this->url->link('account/account', '', true));
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