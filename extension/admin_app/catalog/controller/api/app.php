<?php

namespace Opencart\Catalog\Controller\Extension\AdminApp\Api;

class App extends \Opencart\System\Engine\Controller
{
    public function login()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        $json['success'] = false;

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        // Check both POST and JSON input
        $username = isset($this->request->post['username']) ? $this->request->post['username'] : (isset($input['username']) ? $input['username'] : '');
        $password = isset($this->request->post['password']) ? $this->request->post['password'] : (isset($input['password']) ? $input['password'] : '');

        if ($username && $password) {
            $this->load->model('extension/admin_app/api/app');

            $user_info = $this->model_extension_admin_app_api_app->getUserByUsername($username);

            if ($user_info && password_verify($password, $user_info['password'])) {
                // Generate API token
                $api_token = bin2hex(random_bytes(16));

                // Save token to database with expiry
                $this->model_extension_admin_app_api_app->saveApiToken($user_info['user_id'], $api_token);

                $json['success'] = true;
                $json['token'] = $api_token;
                $json['user'] = [
                    'user_id' => $user_info['user_id'],
                    'username' => $user_info['username'],
                    'firstname' => $user_info['firstname'],
                    'lastname' => $user_info['lastname']
                ];
            } else {
                $json['error'] = $this->language->get('error_login');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function dashboardData()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401; // Unauthorized status code
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        // Get total statistics
        $json['totalProducts'] = $this->model_extension_admin_app_api_app->getTotalProducts();
        $json['totalOrders'] = $this->model_extension_admin_app_api_app->getTotalOrders();
        $json['totalClients'] = $this->model_extension_admin_app_api_app->getTotalCustomers();
        $json['totalRevenue'] = $this->model_extension_admin_app_api_app->getTotalRevenue();

        // Get latest 5 orders
        $latest_orders = $this->model_extension_admin_app_api_app->getLatestOrders(5);
        $json['orders'] = array_map(function ($order) {
            return [
                'id' => (string)$order['order_id'],
                'customer' => $order['customer_name'],
                'total' => $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']),
                'status' => $order['status'],
                'date' => date('Y-m-d H:i:s', strtotime($order['date_added'])),
                'initials' => $order['initials']
            ];
        }, $latest_orders);

        // Get weekly statistics
        $json['weeklyStats'] = $this->model_extension_admin_app_api_app->getWeeklyStats();

        $json['success'] = true;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCategories()
    {
        $json = [];

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 20;

        $page = max(1, $page);
        $limit = max(1, min(100, $limit));

        $data = $this->model_extension_admin_app_api_app->getCategories($page, $limit);

        $this->load->model('tool/image');

        $json['categories'] = array_map(function ($category) {
            return [
                'id' => (string)$category['category_id'],
                'name' => $category['name'],
                'image' => $category['image'] ? $this->model_tool_image->resize($category['image'], 100, 100) : ''
            ];
        }, $data['categories']);

        $json['pagination'] = [
            'total' => $data['total'],
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($data['total'] / $limit),
            'hasMore' => ($page * $limit) < $data['total']
        ];

        $json['success'] = true;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCoupons()
    {
        $json = [];

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 20;

        $page = max(1, $page);
        $limit = max(1, min(100, $limit));

        $data = $this->model_extension_admin_app_api_app->getCoupons($page, $limit);

        $json['coupons'] = array_map(function ($coupon) {
            return [
                'id' => (string)$coupon['coupon_id'],
                'code' => $coupon['code'],
                'discount' => $coupon['discount']
            ];
        }, $data['coupons']);

        $json['pagination'] = [
            'total' => $data['total'],
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($data['total'] / $limit),
            'hasMore' => ($page * $limit) < $data['total']
        ];

        $json['success'] = true;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getReviews()
    {
        $json = [];

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 20;

        $page = max(1, $page);
        $limit = max(1, min(100, $limit));

        $data = $this->model_extension_admin_app_api_app->getReviews($page, $limit);

        $json['reviews'] = array_map(function ($review) {
            return [
                'id' => (string)$review['review_id'],
                'customer' => $review['customer'],
                'rating' => (int)$review['rating'],
                'review' => $review['review'],
                'status' => (int)$review['status']
            ];
        }, $data['reviews']);

        $json['pagination'] = [
            'total' => $data['total'],
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($data['total'] / $limit),
            'hasMore' => ($page * $limit) < $data['total']
        ];

        $json['success'] = true;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function validateToken()
    {
        if (isset($this->request->get['token']) || isset($this->request->server['HTTP_AUTHORIZATION'])) {
            $token = isset($this->request->get['token']) ? $this->request->get['token'] : '';

            if (empty($token) && isset($this->request->server['HTTP_AUTHORIZATION'])) {
                $parts = explode(' ', $this->request->server['HTTP_AUTHORIZATION']);
                if (count($parts) == 2 && strcasecmp($parts[0], 'Bearer') == 0) {
                    $token = $parts[1];
                }
            }

            if ($token) {
                $this->load->model('extension/admin_app/api/app');
                return $this->model_extension_admin_app_api_app->validateApiToken($token);
            }
        }

        return false;
    }

    public function getOrders()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        // Get JSON input for filters
        $input = json_decode(file_get_contents('php://input'), true);

        // Get filter parameters
        $filter_data = [
            'customer_name' => isset($input['customerName']) ? $input['customerName'] : '',
            'customer_id' => isset($input['customerId']) ? $input['customerId'] : '',
            'order_id' => isset($input['orderId']) ? $input['orderId'] : '',
            'date' => isset($input['date']) ? $input['date'] : '',
            'status' => isset($input['status']) ? $input['status'] : ''
        ];

        // Get page number from request, default to 1 if not provided
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 20;

        // Ensure valid page and limit values
        $page = max(1, $page);
        $limit = max(1, min(100, $limit)); // Limit between 1 and 100

        $orders_data = $this->model_extension_admin_app_api_app->getOrders($page, $limit, $filter_data);

        $json['orders'] = array_map(function ($order) {
            return [
                'id' => (string)$order['order_id'],
                'customer' => $order['customer_name'],
                'total' => $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']),
                'status' => $order['status'],
                'date' => date('Y-m-d H:i:s', strtotime($order['date_added'])),
                'initials' => $order['initials']
            ];
        }, $orders_data['orders']);

        $json['pagination'] = [
            'total' => $orders_data['total'],
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($orders_data['total'] / $limit),
            'hasMore' => ($page * $limit) < $orders_data['total']
        ];

        $json['success'] = true;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getOrder()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        // Validate API token
        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        // Get order_id from POST or JSON input
        $order_id = isset($this->request->post['order_id']) ? $this->request->post['order_id'] : (isset($input['order_id']) ? $input['order_id'] : 0);

        if (!$order_id) {
            $json['error'] = 'Order ID is required';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        $order_info = $this->model_extension_admin_app_api_app->getOrder($order_id);

        if ($order_info) {
            $json['success'] = true;
            $json['order'] = $order_info;
        } else {
            $json['success'] = false;
            $json['error'] = 'Order not found';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function deleteOrder()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        $input = json_decode(file_get_contents('php://input'), true);

        $order_id = isset($this->request->post['order_id']) ? $this->request->post['order_id'] : (isset($input['order_id']) ? $input['order_id'] : '');

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
        } elseif (!$order_id) {
            $json['error'] = 'Order ID is required';
            $json['status'] = 400;
            $json['code'] = 'ORDER_ID_REQUIRED';
            $json['success'] = false;
        } else {
            $this->load->model('extension/admin_app/api/app');

            if ($this->model_extension_admin_app_api_app->deleteOrder($order_id)) {
                $json['success'] = true;
                $json['message'] = 'Order deleted successfully';
            } else {
                $json['error'] = 'Order not found';
                $json['status'] = 404;
                $json['code'] = 'ORDER_NOT_FOUND';
                $json['success'] = false;
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function updateOrderStatus()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        $input = json_decode(file_get_contents('php://input'), true);

        $order_id = isset($this->request->post['order_id']) ? $this->request->post['order_id'] : (isset($input['order_id']) ? $input['order_id'] : '');
        $order_status_id = isset($this->request->post['order_status_id']) ? $this->request->post['order_status_id'] : (isset($input['order_status_id']) ? $input['order_status_id'] : '');

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
        } elseif (!$order_id) {
            $json['error'] = 'Order ID is required';
            $json['status'] = 400;
            $json['code'] = 'ORDER_ID_REQUIRED';
            $json['success'] = false;
        } elseif (!$order_status_id) {
            $json['error'] = 'Order Status ID is required';
            $json['status'] = 400;
            $json['code'] = 'STATUS_ID_REQUIRED';
            $json['success'] = false;
        } else {
            $this->load->model('extension/admin_app/api/app');

            if ($this->model_extension_admin_app_api_app->updateOrderStatus($order_id, $order_status_id)) {
                $json['success'] = true;
                $json['message'] = 'Order status updated successfully';
            } else {
                $json['error'] = 'Order not found';
                $json['status'] = 404;
                $json['code'] = 'ORDER_NOT_FOUND';
                $json['success'] = false;
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function orderStatuses()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
        } else {
            $this->load->model('extension/admin_app/api/app');

            $order_statuses = $this->model_extension_admin_app_api_app->getOrderStatuses();

            $json['success'] = true;
            $json['data'] = $order_statuses;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function generateInvoice()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        // Validate API token
        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        // Get order_id from POST or JSON input
        $order_id = isset($this->request->post['order_id']) ? $this->request->post['order_id'] : (isset($input['order_id']) ? $input['order_id'] : 0);

        if (!$order_id) {
            $json['error'] = 'Order ID is required';
            $json['status'] = 400;
            $json['code'] = 'ORDER_ID_REQUIRED';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        list($success, $invoice_no, $invoice_prefix) = $this->model_extension_admin_app_api_app->generateInvoiceNo($order_id);

        if ($success) {
            $json['success'] = true;
            $json['full_invoice_no'] = $invoice_prefix . $invoice_no;
        } else {
            $json['success'] = false;
            $json['error'] = 'Failed to generate invoice number';
            $json['status'] = 500;
            $json['code'] = 'INVOICE_GENERATION_FAILED';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCustomers()
    {
        $json = [];

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        // Get JSON input for filters
        $input = json_decode(file_get_contents('php://input'), true);

        // Get filter parameters
        $filter_data = [
            'search' => isset($input['search']) ? $input['search'] : '', // Will search in both name and email
            'status' => isset($input['status']) ? $input['status'] : '' // 0, 1, or undefined
        ];

        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 20;

        $page = max(1, $page);
        $limit = max(1, min(100, $limit));

        $data = $this->model_extension_admin_app_api_app->getCustomers($page, $limit, $filter_data);

        $json['customers'] = array_map(function ($customer) {
            return [
                'customer_id' => (string)$customer['customer_id'],
                'status' => (string)$customer['status'],
                'name' => $customer['name'],
                'email' => $customer['email'],
                'initials' => $customer['initials']
            ];
        }, $data['customers']);

        $json['pagination'] = [
            'total' => $data['total'],
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($data['total'] / $limit),
            'hasMore' => ($page * $limit) < $data['total']
        ];

        $json['success'] = true;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCustomer()
    {
        $json = [];

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $customer_id = isset($this->request->get['customer_id']) ? (int)$this->request->get['customer_id'] : (isset($input['customer_id']) ? (int)$input['customer_id'] : 0);

        if (!$customer_id) {
            $json['error'] = 'Customer ID is required';
            $json['status'] = 400;
            $json['code'] = 'CUSTOMER_ID_REQUIRED';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        $customer_info = $this->model_extension_admin_app_api_app->getCustomerDetails($customer_id);

        if ($customer_info) {
            $json['success'] = true;
            $json['customer'] = $customer_info;
        } else {
            $json['error'] = 'Customer not found';
            $json['status'] = 404;
            $json['code'] = 'CUSTOMER_NOT_FOUND';
            $json['success'] = false;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function toggleCustomerStatus()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        // Validate API token
        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        // Get customer_id from POST or JSON input
        $customer_id = isset($this->request->post['customer_id']) ? $this->request->post['customer_id'] : (isset($input['customer_id']) ? $input['customer_id'] : 0);

        if (!$customer_id) {
            $json['error'] = 'Customer ID is required';
            $json['status'] = 400;
            $json['code'] = 'CUSTOMER_ID_REQUIRED';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        $result = $this->model_extension_admin_app_api_app->toggleCustomerStatus($customer_id);

        if ($result !== false) {
            $json['success'] = true;
            $json['status'] = $result;
            $json['message'] = 'Customer status updated successfully';
        } else {
            $json['success'] = false;
            $json['error'] = 'Customer not found';
            $json['status'] = 404;
            $json['code'] = 'CUSTOMER_NOT_FOUND';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function deleteCustomer()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $customer_id = isset($this->request->post['customer_id']) ? (int)$this->request->post['customer_id'] : (isset($input['customer_id']) ? (int)$input['customer_id'] : 0);

        if (!$customer_id) {
            $json['error'] = 'Customer ID is required';
            $json['status'] = 400;
            $json['code'] = 'CUSTOMER_ID_REQUIRED';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        if ($this->model_extension_admin_app_api_app->deleteCustomer($customer_id)) {
            $json['success'] = true;
            $json['message'] = 'Customer deleted successfully';
        } else {
            $json['error'] = 'Customer not found';
            $json['status'] = 404;
            $json['code'] = 'CUSTOMER_NOT_FOUND';
            $json['success'] = false;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function updateCustomer()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['customer_id'])) {
            $json['error'] = 'Customer ID is required';
            $json['status'] = 400;
            $json['code'] = 'CUSTOMER_ID_REQUIRED';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Required fields validation
        $required_fields = ['firstname', 'lastname', 'email', 'telephone'];
        $missing_fields = [];

        foreach ($required_fields as $field) {
            if (!isset($input[$field]) || trim($input[$field]) === '') {
                $missing_fields[] = $field;
            }
        }

        if (!empty($missing_fields)) {
            $json['error'] = 'Required fields missing: ' . implode(', ', $missing_fields);
            $json['status'] = 400;
            $json['code'] = 'REQUIRED_FIELDS_MISSING';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        $customer_data = [
            'customer_id' => (int)$input['customer_id'],
            'firstname' => $input['firstname'],
            'lastname' => $input['lastname'],
            'email' => $input['email'],
            'telephone' => $input['telephone'],
            'newsletter' => isset($input['newsletter']) ? ((int)$input['newsletter'] ? 1 : 0) : 0,
            'status' => isset($input['status']) ? ((int)$input['status'] ? 1 : 0) : 1,
            'customer_group_id' => isset($input['customer_group_id']) ? (int)$input['customer_group_id'] : 1
        ];

        if (isset($input['password']) && !empty($input['password'])) {
            $customer_data['password'] = $input['password'];
        }

        $result = $this->model_extension_admin_app_api_app->updateCustomer($customer_data);

        if ($result === true) {
            $json['success'] = true;
            $json['message'] = 'Customer updated successfully';
        } else {
            $json['success'] = false;
            $json['error'] = $result;
            $json['status'] = 400;
            $json['code'] = 'UPDATE_FAILED';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getProducts()
    {
        $json = [];

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        $input = json_decode(file_get_contents('php://input'), true);

        $filter_data = [
            'name' => isset($input['name']) ? $input['name'] : '',
            'stock_status_id' => isset($input['stock_status_id']) ? $input['stock_status_id'] : '',
            'status' => isset($input['status']) ? $input['status'] : '',
            'minPrice' => isset($input['minPrice']) ? (float)$input['minPrice'] : null,
            'maxPrice' => isset($input['maxPrice']) ? (float)$input['maxPrice'] : null
        ];

        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 20;

        $page = max(1, $page);
        $limit = max(1, min(100, $limit));

        $data = $this->model_extension_admin_app_api_app->getProducts($page, $limit, $filter_data);

        $this->load->model('tool/image');

        $json['products'] = array_map(function ($product) {
            return [
                'id' => (string)$product['product_id'],
                'name' => $product['name'],
                'model' => $product['model'],
                'quantity' => (int)$product['quantity'],
                'price' => (float)$product['price'],
                'image' => $product['image'] ? $this->model_tool_image->resize($product['image'], 100, 100) : '',
                'status' => (int)$product['status'],
                'stock_status_name' => $product['stock_status_name'],
                'dateAdded' => $product['date_added']
            ];
        }, $data['products']);

        $json['pagination'] = [
            'total' => $data['total'],
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($data['total'] / $limit),
            'hasMore' => ($page * $limit) < $data['total']
        ];

        $json['success'] = true;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function deleteProduct()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $product_id = isset($input['product_id']) ? (int)$input['product_id'] : 0;

        if (!$product_id) {
            $json['error'] = 'Product ID is required';
            $json['status'] = 400;
            $json['code'] = 'PRODUCT_ID_REQUIRED';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        if ($this->model_extension_admin_app_api_app->deleteProduct($product_id)) {
            $json['success'] = true;
            $json['message'] = 'Product deleted successfully';
        } else {
            $json['error'] = 'Product not found';
            $json['status'] = 404;
            $json['code'] = 'PRODUCT_NOT_FOUND';
            $json['success'] = false;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function toggleProductStatus()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        $input = json_decode(file_get_contents('php://input'), true);

        $product_id = isset($input['product_id']) ? $input['product_id'] : '';

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
        } elseif (!$product_id) {
            $json['error'] = 'Product ID is required';
            $json['status'] = 400;
            $json['code'] = 'PRODUCT_ID_REQUIRED';
            $json['success'] = false;
        } else {
            $this->load->model('extension/admin_app/api/app');

            $result = $this->model_extension_admin_app_api_app->toggleProductStatus($product_id);

            if ($result !== false) {
                $json['success'] = true;
                $json['status'] = $result;
                $json['message'] = 'Product status updated successfully';
            } else {
                $json['error'] = 'Product not found';
                $json['status'] = 404;
                $json['code'] = 'PRODUCT_NOT_FOUND';
                $json['success'] = false;
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getStockStatuses() {
        $json = [];

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        $stock_statuses = $this->model_extension_admin_app_api_app->getStockStatuses();

        $json['success'] = true;
        $json['stock_statuses'] = $stock_statuses;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getProduct()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        $product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;

        if (!$product_id) {
            $json['error'] = 'Product ID is required';
            $json['status'] = 400;
            $json['code'] = 'PRODUCT_ID_REQUIRED';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $product_info = $this->model_extension_admin_app_api_app->getProduct($product_id);

        if ($product_info) {
            $json['success'] = true;
            $json['product'] = $product_info;
        } else {
            $json['error'] = 'Product not found';
            $json['status'] = 404;
            $json['code'] = 'PRODUCT_NOT_FOUND';
            $json['success'] = false;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function updateProduct()
    {
        $json = [];

        $this->load->language('extension/admin_app/api/app');

        if (!$this->validateToken()) {
            $json['error'] = $this->language->get('error_token');
            $json['status'] = 401;
            $json['code'] = 'TOKEN_INVALID';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['product_id'])) {
            $json['error'] = 'Product ID is required';
            $json['status'] = 400;
            $json['code'] = 'PRODUCT_ID_REQUIRED';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Basic validation
        $required_fields = ['name', 'model', 'price', 'quantity', 'status', 'stock_status_id'];
        $missing_fields = [];

        foreach ($required_fields as $field) {
            if (!isset($input[$field])) {
                $missing_fields[] = $field;
            }
        }

        if (!empty($missing_fields)) {
            $json['error'] = 'Required fields missing: ' . implode(', ', $missing_fields);
            $json['status'] = 400;
            $json['code'] = 'REQUIRED_FIELDS_MISSING';
            $json['success'] = false;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/admin_app/api/app');

        $product_data = [
            'product_id' => (int)$input['product_id'],
            'name' => $input['name'],
            'model' => $input['model'],
            'price' => (float)$input['price'],
            'quantity' => (int)$input['quantity'],
            'status' => (int)$input['status'],
            'stock_status_id' => (int)$input['stock_status_id']
        ];

        $result = $this->model_extension_admin_app_api_app->updateProduct($product_data);

        if ($result === true) {
            $json['success'] = true;
            $json['message'] = 'Product updated successfully';
        } else {
            $json['success'] = false;
            $json['error'] = $result;
            $json['status'] = 400;
            $json['code'] = 'UPDATE_FAILED';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
