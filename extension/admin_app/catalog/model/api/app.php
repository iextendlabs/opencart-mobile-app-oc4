<?php
namespace Opencart\Catalog\Model\Extension\AdminApp\Api;

class App extends \Opencart\System\Engine\Model {
    public function getUserByUsername($username) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE username = '" . $this->db->escape($username) . "' AND status = '1'");
        
        return $query->row;
    }
    
    public function saveApiToken($user_id, $token) {
        // First, invalidate any existing tokens for this user
        $this->db->query("UPDATE " . DB_PREFIX . "api_session SET status = '0' WHERE user_id = '" . (int)$user_id . "'");
        
        // Insert new token with 24 hour expiry
        $this->db->query("INSERT INTO " . DB_PREFIX . "api_session SET user_id = '" . (int)$user_id . "', token = '" . $this->db->escape($token) . "', status = '1', date_added = NOW(), date_modified = NOW(), expire_date = DATE_ADD(NOW(), INTERVAL 24 HOUR)");
    }
    
    public function validateApiToken($token) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "api_session WHERE token = '" . $this->db->escape($token) . "' AND status = '1' AND expire_date > NOW()");
        
        return !empty($query->row);
    }
    
    public function getTotalOrders() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE `order_status_id` > '0'");
        return (int)$query->row['total'];
    }
    
    public function getTotalCustomers() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer");
        return (int)$query->row['total'];
    }
    
    public function getTotalProducts() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product");
        return (int)$query->row['total'];
    }

    public function getTotalRevenue() {
        $query = $this->db->query("SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > 0");
        return (float)$query->row['total'];
    }
    
    public function getLatestOrders($limit = 5) {
        $query = $this->db->query("SELECT 
            o.order_id,
            o.firstname,
            o.lastname,
            CONCAT(o.firstname, ' ', o.lastname) AS customer_name,
            o.total,
            o.date_added,
            o.currency_code,
            o.currency_value,
            os.name AS status,
            CONCAT(UPPER(LEFT(o.firstname, 1)), UPPER(LEFT(o.lastname, 1))) AS initials
            FROM `" . DB_PREFIX . "order` o
            LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id)
            WHERE os.language_id = '" . (int)$this->config->get('config_language_id') . "'
            ORDER BY o.order_id DESC 
            LIMIT " . (int)$limit);
        
        return $query->rows;
    }

    public function getOrders($page = 1, $limit = 20) {
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT 
            o.order_id,
            o.firstname,
            o.lastname,
            CONCAT(o.firstname, ' ', o.lastname) AS customer_name,
            o.total,
            o.date_added,
            o.currency_code,
            o.currency_value,
            o.order_status_id,
            os.name AS status,
            CONCAT(UPPER(LEFT(o.firstname, 1)), UPPER(LEFT(o.lastname, 1))) AS initials
            FROM `" . DB_PREFIX . "order` o
            LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id)
            WHERE os.language_id = '" . (int)$this->config->get('config_language_id') . "'
            ORDER BY o.order_id DESC
            LIMIT " . (int)$start . ", " . (int)$limit);

        $total_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order`");
        
        return [
            'orders' => $query->rows,
            'total' => (int)$total_query->row['total']
        ];
    }

    public function getCustomers($page = 1, $limit = 20) {
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT 
            customer_id,
            CONCAT(firstname, ' ', lastname) AS name,
            email,
            CONCAT(UPPER(LEFT(firstname, 1)), UPPER(LEFT(lastname, 1))) AS initials
            FROM " . DB_PREFIX . "customer
            ORDER BY customer_id DESC
            LIMIT " . (int)$start . ", " . (int)$limit);

        $total_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer");
        
        return [
            'customers' => $query->rows,
            'total' => (int)$total_query->row['total']
        ];
    }

    public function getProducts($page = 1, $limit = 20) {
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT 
            p.product_id,
            pd.name,
            p.model,
            p.quantity,
            p.price,
            p.image,
            p.status,
            p.date_added
            FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            ORDER BY p.product_id DESC
            LIMIT " . (int)$start . ", " . (int)$limit);

        $total_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product");
        
        return [
            'products' => $query->rows,
            'total' => (int)$total_query->row['total']
        ];
    }

    public function getCategories($page = 1, $limit = 20) {
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT 
            c.category_id,
            cd.name,
            c.image
            FROM " . DB_PREFIX . "category c
            LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
            WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            ORDER BY c.sort_order, cd.name
            LIMIT " . (int)$start . ", " . (int)$limit);

        $total_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category");
        
        return [
            'categories' => $query->rows,
            'total' => (int)$total_query->row['total']
        ];
    }

    public function getCoupons($page = 1, $limit = 20) {
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT 
            coupon_id,
            code,
            CONCAT(type, ' ', discount) as discount
            FROM " . DB_PREFIX . "coupon
            ORDER BY coupon_id DESC
            LIMIT " . (int)$start . ", " . (int)$limit);

        $total_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "coupon");
        
        return [
            'coupons' => $query->rows,
            'total' => (int)$total_query->row['total']
        ];
    }

    public function getReviews($page = 1, $limit = 20) {
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT 
            r.review_id,
            CONCAT(c.firstname, ' ', c.lastname) as customer,
            r.rating,
            r.text as review,
            r.status
            FROM " . DB_PREFIX . "review r
            LEFT JOIN " . DB_PREFIX . "customer c ON (r.customer_id = c.customer_id)
            ORDER BY r.date_added DESC
            LIMIT " . (int)$start . ", " . (int)$limit);

        $total_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review");
        
        return [
            'reviews' => $query->rows,
            'total' => (int)$total_query->row['total']
        ];
    }

    public function getWeeklyStats() {
        $stats = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $day = substr(date('D', strtotime($date)), 0, 1); // Get first letter of day name
            
            // Daily orders
            $orders_query = $this->db->query("SELECT COUNT(*) AS total 
                FROM `" . DB_PREFIX . "order` 
                WHERE DATE(date_added) = '" . $this->db->escape($date) . "'");
            
            // Daily customers
            $customers_query = $this->db->query("SELECT COUNT(*) AS total 
                FROM `" . DB_PREFIX . "customer` 
                WHERE DATE(date_added) = '" . $this->db->escape($date) . "'");
            
            $stats[] = [
                'day' => $day,
                'orders' => (int)$orders_query->row['total'],
                'customers' => (int)$customers_query->row['total']
            ];
        }
        
        return $stats;
    }
}