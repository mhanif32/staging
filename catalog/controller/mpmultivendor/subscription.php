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
            'text' => $this->language->get('Account'),
            'href' => $this->url->link('account/account', '', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('Membership Plans'),
            'href' => $this->url->link('mpmultivendor/subscription', '', true)
        );

        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        //plans
        $data['plans'] = $this->model_mpmultivendor_subscription->getSubscriptionPlans();
        $data['action_subscribe'] = $this->url->link('mpmultivendor/subscription/add', '', true);
        $checkPlan = $this->model_account_customer->getSellerPlan($this->customer->getId());
        $data['checkPlan'] = !empty($checkPlan) ? true : false;
        $data['subscribed_plan'] = $checkPlan;
        //echo '<pre>';print_r($checkPlan);exit('okokok');
        $this->response->setOutput($this->load->view('mpmultivendor/subscription', $data));
    }

    public function add()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('mpmultivendor/subscription', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->model('mpmultivendor/subscription');
        $this->load->model('account/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            //load secret key
            if($this->config->get('payment_stripe_environment') == 'live' || (isset($this->request->request['livemode']) && $this->request->request['livemode'] == "true")) {
                $stripe_secret_key = $this->config->get('payment_stripe_live_secret_key');
            } else {
                $stripe_secret_key = $this->config->get('payment_stripe_test_secret_key');
            }
            $this->load->library('stripe');
            \Stripe\Stripe::setApiKey($stripe_secret_key);

            // Token is created using Stripe Checkout or Elements!, Get the payment token ID submitted by the form:
            $token = $this->request->post['stripe_token'];
            $sellerName = $this->request->post['sellerName'];
            $product = $this->request->post['radioPlan'];
            $plan_id = $this->request->post['plan_id'];

            //create a stripe customer
            $customer = $this->model_account_customer->getStripeCustomerId($this->customer->getId());

            //echo '<pre>';print_r($customer['stripe_customer_id']);exit('ookk');

            if(empty($customer['stripe_customer_id'])) {

                $customerData = \Stripe\Customer::create([
                    'name' => $sellerName,
                    'email' => $customer['email'],
                    'source' => $token
                ]);
                $customerId = $customerData['id'];
                //save stripe card
                $card = $customerData['sources']['data'][0];
                $this->model_mpmultivendor_subscription->saveStripeCard($card, $this->customer->getId());
            } else {
                //cancel plan
                if($customer['subscription_plan'] != null) {
                    $subscription = \Stripe\Subscription::retrieve($customer['subscription_plan']);
                    $subscription->cancel();
                }
                //remove subscription entry from the table
                $this->model_mpmultivendor_subscription->removeUserSubscription($customer['subscription_plan_id']);

                $customerId = $customer['stripe_customer_id'];
            }

            //create a subscription
            $stripedata = \Stripe\Subscription::create([
                "customer" => $customerId,
                'items' => [['plan' => $product]],
            ]);

            //entry in user subscription table
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

            //update customer table
            $updateCustomer = [
                'stripe_subscription_id' => $stripedata['id'],
                'stripe_customer_id' => $stripedata['customer'],
                'subscription_plan_id' => $plan_id,
                'subscription_plan' => $stripedata['id']
            ];
            $this->model_mpmultivendor_subscription->updateCustomer($updateCustomer, $this->customer->getId());

            $planData = $this->model_mpmultivendor_subscription->getSubscriptionPlan($plan_id);
            $this->session->data['success'] = 'Success : You have successfully subscribed to the '.$planData['name'].' Plan.';
            $this->response->redirect($this->url->link('account/account', '', true));
        }
    }

    public function savedcards()
    {
        $data = [];
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('mpmultivendor/subscription/savedcards', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('Account'),
            'href' => $this->url->link('account/account', '', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('Membership Plans'),
            'href' => $this->url->link('mpmultivendor/subscription', '', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('Saved Cards'),
            'href' => $this->url->link('mpmultivendor/subscription/savedcards', '', true)
        );

        $this->load->language('mpmultivendor/subscription');
        $this->load->model('mpmultivendor/subscription');

        $savedCards = $this->model_mpmultivendor_subscription->getSavedCards($this->customer->getId());

        $data['savedCards'] = $savedCards;
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('mpmultivendor/saved_cards', $data));
    }

    public function removecard()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('mpmultivendor/subscription/savedcards', '', true);
            $this->response->redirect($this->url->link('account/login', '', true));
        }

        $this->load->model('mpmultivendor/subscription');
        $this->load->model('account/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            //load secret key
            if($this->config->get('payment_stripe_environment') == 'live' || (isset($this->request->request['livemode']) && $this->request->request['livemode'] == "true")) {
                $stripe_secret_key = $this->config->get('payment_stripe_live_secret_key');
            } else {
                $stripe_secret_key = $this->config->get('payment_stripe_test_secret_key');
            }
            $this->load->library('stripe');
            \Stripe\Stripe::setApiKey($stripe_secret_key);

            //delete stripe card
            $user_card_id = $this->request->post['user_card_id'];
            $userCard = $this->model_mpmultivendor_subscription->getUserCard($user_card_id, $this->customer->getId());

            //echo '<pre>';print_r($userCard);exit('okok');
            if (!$userCard) {
                $this->session->data['warning'] = 'Warning : No card found.';
                $this->response->redirect($this->url->link('mpmultivendor/subscription/savedcards', '', true));
            }
            $removed = \Stripe\Customer::deleteSource(
                $userCard['stripe_customer_id'],
                $userCard['stripe_card_id']
            );
            if($removed['deleted'] == true) {
                $this->model_mpmultivendor_subscription->removeCard($user_card_id, $this->customer->getId());
            }
            $this->session->data['success'] = 'Success : Your card has been successfully removed.';
            $this->response->redirect($this->url->link('mpmultivendor/subscription/savedcards', '', true));
        }
    }

    public function cancel()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        //cancel subscription plan
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('mpmultivendor/subscription/cancel', '', true);
            $this->response->redirect($this->url->link('account/login', '', true));
        }
        $json = array();
        $this->load->model('mpmultivendor/subscription');
        $this->load->model('account/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            //load secret key
            if($this->config->get('payment_stripe_environment') == 'live' || (isset($this->request->request['livemode']) && $this->request->request['livemode'] == "true")) {
                $stripe_secret_key = $this->config->get('payment_stripe_live_secret_key');
            } else {
                $stripe_secret_key = $this->config->get('payment_stripe_test_secret_key');
            }
            $this->load->library('stripe');
            \Stripe\Stripe::setApiKey($stripe_secret_key);

            $customer = $this->model_account_customer->getStripeCustomerId($this->customer->getId());

            //cancel plan
            if($customer['subscription_plan'] != null) {
                $subscription = \Stripe\Subscription::retrieve($customer['subscription_plan']);
                $subscription->cancel();
            }
            //remove subscription entry from the table
            $this->model_mpmultivendor_subscription->removeUserSubscription($customer['subscription_plan_id']);
            $json['success'] = 'Success : Membership Plan has been cancelled successfully.';
        } else {
            $json['error'] = 'Warning : Something wrong happened, please try again.';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
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
}