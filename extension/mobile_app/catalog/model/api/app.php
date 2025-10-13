<?php
namespace Opencart\Catalog\Model\Extension\MobileApp\Api;
/**
 * Class App
 *
 * Can be called using $this->load->model('extension/mobile_app/api/app');
 *
 * @package Opencart\Catalog\Model\Extension\MobileApp\Api
 */
class App extends \Opencart\System\Engine\Model {

	public function getOrder(int $order_id,int $customer_id): array {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `order_id` = '" . (int)$order_id . "' AND `customer_id` = '" . (int)$customer_id . "' AND `customer_id` != '0' AND `order_status_id` > '0'");

		if ($order_query->num_rows) {
			// Country
			$this->load->model('localisation/country');

			$country_info = $this->model_localisation_country->getCountry($order_query->row['payment_country_id']);

			if ($country_info) {
				$payment_iso_code_2 = $country_info['iso_code_2'];
				$payment_iso_code_3 = $country_info['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			// Zone
			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($order_query->row['payment_zone_id']);

			if ($zone_info) {
				$payment_zone_code = $zone_info['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_info = $this->model_localisation_country->getCountry($order_query->row['shipping_country_id']);

			if ($country_info) {
				$shipping_iso_code_2 = $country_info['iso_code_2'];
				$shipping_iso_code_3 = $country_info['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			// Zone
			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($order_query->row['shipping_zone_id']);

			if ($zone_info) {
				$shipping_zone_code = $zone_info['code'];
			} else {
				$shipping_zone_code = '';
			}

			return [
				'payment_zone_code'   => $payment_zone_code,
				'payment_iso_code_2'  => $payment_iso_code_2,
				'payment_iso_code_3'  => $payment_iso_code_3,
				'payment_method'      => $order_query->row['payment_method'] ? json_decode($order_query->row['payment_method'], true)['name'] : '',
				'shipping_zone_code'  => $shipping_zone_code,
				'shipping_iso_code_2' => $shipping_iso_code_2,
				'shipping_iso_code_3' => $shipping_iso_code_3,
				'shipping_method'     => $order_query->row['shipping_method'] ? json_decode($order_query->row['shipping_method'], true)['name'] : ''
			] + $order_query->row;
		} else {
			return [];
		}
	}

	public function getOrders(int $customer_id, int $start = 0, int $limit = 20): array {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `customer_id` = '" . (int)$customer_id . "' AND `order_status_id` > '0' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' ORDER BY `order_id` DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

    public function getTotalProductsByOrderId(int $order_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int)$order_id . "'");

		if ($query->num_rows) {
			return (int)$query->row['total'];
		} else {
			return 0;
		}
	}

	public function getProducts(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOptions(int $order_id, int $order_product_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_option` WHERE `order_id` = '" . (int)$order_id . "' AND `order_product_id` = '" . (int)$order_product_id . "'");

		return $query->rows;
	}


	public function getTotals(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = '" . (int)$order_id . "' ORDER BY `sort_order`");

		return $query->rows;
	}

    public function getHistories(int $order_id): array {
		$query = $this->db->query("SELECT `date_added`, `os`.`name` AS `status`, `oh`.`comment`, `oh`.`notify` FROM `" . DB_PREFIX . "order_history` `oh` LEFT JOIN `" . DB_PREFIX . "order_status` `os` ON `oh`.`order_status_id` = `os`.`order_status_id` WHERE `oh`.`order_id` = '" . (int)$order_id . "' AND `os`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `oh`.`date_added`");

		return $query->rows;
	}
}
