<?php
namespace Opencart\Catalog\Controller\Extension\AdminApp\Api;

class App extends \Opencart\System\Engine\Controller {
    public function login() {
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

    public function orderStatuses() {
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

    public function dashboardData() {
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
        $json['orders'] = array_map(function($order) {
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

    public function getCustomers() {
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

        $data = $this->model_extension_admin_app_api_app->getCustomers($page, $limit);
        
        $json['customers'] = array_map(function($customer) {
            return [
                'id' => (string)$customer['customer_id'],
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

    public function getProducts() {
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

        $data = $this->model_extension_admin_app_api_app->getProducts($page, $limit);
        
        $this->load->model('tool/image');
        
        $json['products'] = array_map(function($product) {
            return [
                'id' => (string)$product['product_id'],
                'name' => $product['name'],
                'model' => $product['model'],
                'quantity' => (int)$product['quantity'],
                'price' => (float)$product['price'],
                'image' => $product['image'] ? $this->model_tool_image->resize($product['image'], 100, 100) : '',
                'status' => (int)$product['status'],
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

    public function getCategories() {
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
        
        $json['categories'] = array_map(function($category) {
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

    public function getCoupons() {
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
        
        $json['coupons'] = array_map(function($coupon) {
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

    public function getReviews() {
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
        
        $json['reviews'] = array_map(function($review) {
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

    public function getOrders() {
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

        // Get page number from request, default to 1 if not provided
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 20;

        // Ensure valid page and limit values
        $page = max(1, $page);
        $limit = max(1, min(100, $limit)); // Limit between 1 and 100

        $orders_data = $this->model_extension_admin_app_api_app->getOrders($page, $limit);
        
        $json['orders'] = array_map(function($order) {
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

    private function validateToken() {
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


}