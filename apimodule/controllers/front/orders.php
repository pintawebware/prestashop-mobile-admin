<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class ApimoduleOrdersModuleFrontController extends ModuleFrontController {
	public $ssl = true;
	public $display_column_left = false;
	public $header = false;
	public $errors = [];
	public $API_VERSION = 1.8;
	public $return = [];

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent() {
		$this->return['status'] = false;
		if(isset($_GET['action'])/*&& $this->valid()*/){

			$action = $_GET['action'];
			switch ($action){
				case 'list':$this->getOrdersList();break;
				case 'products':$this->getOrderProducts();break;
				case 'history':$this->getOrdersHistory();break;
				case 'info':$this->getOrdersInfo();break;
				case 'pad':$this->getPaymentAndDelivery();break;
			}
		}
		$this->errors[] = "No action";
		header( 'Content-Type: application/json' );
		die( Tools::jsonEncode( $this->return ) );
	}

	private function valid() {
		$token = trim( Tools::getValue( 'token' ) );
		if ( ! empty( $token ) ) {
			$this->errors[] = 'You need to be logged!';
			return false;
		} else {
			$results = $this->getTokens( $token );
			if ( $results ) {
				$this->errors = 'Your token is no longer relevant!';
				return false;
			}
		}

		return true;
	}
	public function getTokens($token){
		$sql = "SELECT * FROM " . _DB_PREFIX_ . "apimodule_user_token
		        WHERE token = '".$token."'";

		if ($row = Db::getInstance()->getRow($sql)){
			return $row;
		}
		else{
			return false;
		}
	}
	public function getOrdersList(){
		$page = trim( Tools::getValue( 'page' ) );
		$limit = trim( Tools::getValue( 'limit' ) );
		$filter = trim( Tools::getValue( 'filter' ) );
		$platform = trim( Tools::getValue( 'platform' ) );
		$order_status_id = trim( Tools::getValue( 'order_status_id' ) );
		$fio = trim( Tools::getValue( 'fio' ) );
		$min_price = trim( Tools::getValue( 'min_price' ) );
		$max_price = trim( Tools::getValue( 'max_price' ) );
		$date_min = trim( Tools::getValue( 'date_min' ) );
		$date_max = trim( Tools::getValue( 'date_max' ) );

		if (isset($page) && (int)$page!= 0 && isset($limit) && (int)$limit != 0) {
			$page = ($page - 1) * $limit;
			$limit = $limit;
		} else {
			$page = 0;
			$limit = 9999;
		}

		if (isset($filter)) {
			$orders = $this->getOrders(array('filter' => $filter, 'page' => $page, 'limit' => $limit));
		} elseif (!empty($platform) && $platform == 'android') {
			$filter = [];
			$filter['order_status_id'] = !empty($order_status_id)?$order_status_id:'';
			$filter['fio'] = !empty($fio)?$fio:'';
			$filter['min_price'] = !empty($min_price)?$min_price:1;
			$filter['max_price'] = !empty($max_price)?$max_price:$this->getMaxOrderPrice();
			$filter['date_min'] = !empty($date_min)?$date_min:1;
			$filter['date_max'] = !empty($date_max)?$date_max:1;

			$orders = $this->getOrders(array('filter' => $filter, 'page' => $page, 'limit' => $limit));

		} else {
			$orders = $this->getOrders(array('page' => $page, 'limit' => $limit));
		}
		$response = [];
		$orders_to_response = [];

		$sum = 0;
		$quantity = 0;

		foreach ($orders as $order) {
			$sum = $sum + $order['total_paid'];
			$quantity++;
			$data['order_number'] = $order['id_order'];
			$data['order_id'] = $order['id_order'];
			$data['fio'] = $order['firstname'] . ' ' . $order['lastname'];

			$data['status'] = $order['id_order_state'];

			$data['total'] = number_format($order['total_paid'], 2, '.', '');
			$data['date_added'] = $order['date_add'];
			$data['currency_code'] = Context::getContext()->currency->iso_code;
			$orders_to_response[] = $data;

		}

		$this->return['total_quantity'] = $quantity;
		$this->return['currency_code'] = Context::getContext()->currency->iso_code;
		$this->return['total_sum'] = number_format($sum, 2, '.', '');
		$this->return['orders'] = $orders_to_response;
		$this->return['max_price'] = $this->getMaxOrderPrice();
		$statuses = $this->OrderStatusList();
		$this->return['statuses'] = $statuses;
		$this->return['api_version'] = $this->API_VERSION;

		$this->return['version'] = $this->API_VERSION;
		$this->return['response'] = $response;
		$this->return['status'] = true;

		$this->return['errors'] = $this->errors;
		header('Content-Type: application/json');
		die(Tools::jsonEncode($this->return));

	}

	public function getOrderProducts()
	{
		$id = trim( Tools::getValue( 'order_id' ) );

		if (!empty($id)) {
			$order = new Order($id);
			$products = $order->getProducts();

			if (count($products) > 0) {
				$data               = array();
				$total_discount_sum = 0;
				$shipping_price        = 0;
				$total_price        = 0;
				foreach ( $products as $product ):
					$array = [];
					if (!empty($product['image'])) {
						$image = Image::getCover($product['product_id']);
						$imagePath = Link::getImageLink($product->link_rewrite, $image['id_image'], 'home_default');
						$array['image'] = $imagePath;
					}

					if (!empty($product['product_name'])) {
						$array['name'] = strip_tags( htmlspecialchars_decode( $product['product_name'] ) );
					}
					if (!empty($product['model'])){
						$array['model'] = $product['model'];
					}
					if (!empty($product['quantity'])){
						$quantity = number_format( $product['quantity'], 2, '.', '' );
						$array['quantity'] = $quantity;
					}else{
						$quantity = 1;
					}
					if (!empty($product['price'])){
						$array['price'] = number_format( $product['price'], 2, '.', '' );
					}
					$array['product_id'] = $product['product_id'];

					$array['discount_price'] = $product['product_quantity_discount'];
					$array['discount']       = $product['quantity_discount'];

					$total_discount_sum += $product['product_quantity_discount'];

					$total_price += $product['price'] * $quantity;

					$shipping_price += $product['additional_shipping_cost'];

					$data['products'][] = $array;
				endforeach;

				$data['total_order_price'] = array(
					'total_discount' => $total_discount_sum,
					'total_price' => $total_price,
					'shipping_price' => +number_format($shipping_price, 2, '.', ''),
					'total' => $total_price + $shipping_price
				);

				$this->return['version'] = $this->API_VERSION;
				$this->return['response'] = $data;
				$this->return['status'] = true;

				$this->return['errors'] = $this->errors;
				header('Content-Type: application/json');
				die(Tools::jsonEncode($this->return));
			} else {
				$this->return['errors'] = 'Can not found any products in order with id = ' . $id;
				header('Content-Type: application/json');
				die(Tools::jsonEncode($this->return));
			}
		} else {

			$this->return['version'] = $this->API_VERSION;
			$this->return['status'] = false;

			$this->return['errors'] = 'You have not specified ID';
			header('Content-Type: application/json');
			die(Tools::jsonEncode($this->return));
		}

	}


	public function getOrdersHistory(){
		$id = trim( Tools::getValue( 'order_id' ) );
		if (!empty($id)) {
			$order = new Order($id);
			$history = $order->getHistory();
			$data = array();
			$response = [];
			$statuses = $this->OrderStatusList();
			$statusArray = [];
			foreach ($statuses as $one):
				$statusArray[$one['id_order_state']] = $one['name'];
			endforeach;

			if (!empty($history)) {
				foreach ( $history as $item ):
					$statusId = $item['id_order_state'];
					$data['name'] = $statusArray[$statusId];
					$data['order_status_id'] =$statusId;
					$data['date_added'] = $item['date_add'];
					$data['comment'] ='';
					$response['orders'][] = $data;
				endforeach;

				$response['statuses'] = $statuses;

				$this->return['status'] = true;
				$this->return['response'] = $response;

			} else {

				$this->return['status']  = false;
				$this->return['errors']  = 'Can not found any statuses for order with id = ' . $id;

			}
		} else {
			$this->return['status']  = false;
			$this->return['errors']  = 'You have not specified ID';
		}
		$this->return['version'] = $this->API_VERSION;
		header( 'Content-Type: application/json' );
		die( Tools::jsonEncode( $this->return ) );
	}

	public function getOrdersInfo(){
		$id = trim( Tools::getValue( 'order_id' ) );
		$id = 5;
		if (!empty($id)) {
			$order = new Order($id);
			/*echo "<pre>";
			print_r($order);
			echo "</pre>";
			die();*/
			$data = array();
			$statuses = $this->OrderStatusList();
			$statusArray = [];
			foreach ($statuses as $one):
				$statusArray[$one['id_order_state']] = $one['name'];
			endforeach;

			$customer = new Customer($order->id_customer);

			if ($order) {
				$data['order_number'] = $order->id_order;

				if (isset($customer->firstname) && isset($customer->lastname)) {
					$data['fio'] = $customer->firstname . ' ' . $customer->lastname;
				}
				if (isset($customer->email)) {
					$data['email'] = $customer->email;
				} else {
					$data['email'] = '';
				}
				/*if (isset($customer->telephone)) {
					$data['telephone'] = $customer->telephone;
				} else {
					$data['telephone'] = '';
				}*/
				$data['telephone'] = '';

				$data['date_add'] = $order->date_add;

				if (isset($order->total_paid)) {
					$data['total'] = number_format($order->total_paid, 2, '.', '');;
				}
				if (isset($order->current_state)) {
					$data['status'] = $statusArray[$order->current_state];
				} else {
					$data['status'] = '';
				}

				$data['statuses'] = $statuses;
				$data['currency_code'] = Context::getContext()->currency->iso_code;;

				$this->return['status']  = false;
				$this->return['response']  = $data;

			} else {
				$this->return['status']  = false;
				$this->return['errors']  = 'Can not found order with id = ' . $id;
			}
		} else {
			$this->return['status']  = false;
			$this->return['errors']  = 'You have not specified ID';
		}

		$this->return['version'] = $this->API_VERSION;
		header( 'Content-Type: application/json' );
		die( Tools::jsonEncode( $this->return ) );
	}

	public function getPaymentAndDelivery()
	{
		$id = trim( Tools::getValue( 'order_id' ) );
		$id = 5;
		if (!empty($id)) {
			$order = new Order($id);
			/*echo "<pre>";
			print_r($order);
			echo "</pre>";
			die();*/
			$data = array();
			$statuses = $this->OrderStatusList();
			$statusArray = [];
			foreach ($statuses as $one):
				$statusArray[$one['id_order_state']] = $one['name'];
			endforeach;

			$mode_de_paiement = array();

			$modules_list = Module::getPaymentModules();

			foreach($modules_list as $module)
			{
				$module_obj = Module::getInstanceById($module['id_module']);
				array_push($mode_de_paiement, $module_obj->displayName);
			}
			echo "10";
			p($modules_list);
			echo "12";
			die();

			if ($order) {

				$data['shipping_address'] = '';

				if (isset($order[0]['payment_method']) && $order[0]['payment_method'] != '') {
					$data['payment_method'] = $order[0]['payment_method'];
				}
				if (isset($order[0]['shipping_method']) && $order[0]['shipping_method'] != '') {
					$data['shipping_method'] = $order[0]['shipping_method'];
				}
				if (isset($order[0]['shipping_address_1']) && $order[0]['shipping_address_1'] != '') {
					$data['shipping_address'] .= $order[0]['shipping_address_1'];
				}
				if (isset($order[0]['shipping_address_2']) && $order[0]['shipping_address_2'] != '') {
					$data['shipping_address'] .= ', ' . $order[0]['shipping_address_2'];
				}
				if (isset($order[0]['shipping_city']) && $order[0]['shipping_city'] != '') {
					$data['shipping_address'] .= ', ' . $order[0]['shipping_city'];
				}
				if (isset($order[0]['shipping_country']) && $order[0]['shipping_country'] != '') {
					$data['shipping_address'] .= ', ' . $order[0]['shipping_country'];
				}
				if (isset($order[0]['shipping_zone']) && $order[0]['shipping_zone'] != '') {
					$data['shipping_address'] .= ', ' . $order[0]['shipping_zone'];
				}

				$this->response->setOutput(json_encode(['version' => $this->API_VERSION, 'response' => $data, 'status' => true]));


			} else {

				$this->response->setOutput(json_encode(['version' => $this->API_VERSION, 'error' => 'Can not found order with id = ' . $id, 'status' => false]));
			}
		} else {

			$this->response->setOutput(json_encode(['version' => $this->API_VERSION, 'error' => 'You have not specified ID', 'status' => false]));
		}
	}

	public function OrderStatusList()
	{
		$sql = "SELECT * FROM " . _DB_PREFIX_ . "order_state_lang WHERE id_lang = 1 ";
				$results = Db::getInstance()->ExecuteS( $sql );
		return $results;
	}
	public function getMaxOrderPrice()
	{
		$sql = "SELECT MAX(total_paid) AS total FROM `" . _DB_PREFIX_ . "orders` as o
		            INNER JOIN " . _DB_PREFIX_ . "order_history as oh ON o.id_order=oh.id_order 
		            WHERE oh.id_order_state != '0'";
		$total = 0;
		if ($row = Db::getInstance()->getRow($sql)){
			$total = number_format($row['total'], 2, '.','');
		}
		return $total;
	}
	public function getOrders( $data = array() ) {

		$sql = "SELECT o.id_order,o.date_add,o.total_paid, oh.id_order_state, c.firstname, c.lastname FROM " . _DB_PREFIX_ . "orders AS o 
					INNER JOIN " . _DB_PREFIX_ . "order_history as oh ON o.id_order=oh.id_order 
					INNER JOIN " . _DB_PREFIX_ . "customer as c ON c.id_customer=o.id_customer  ";
		if (isset($data['filter'])) {
			if (isset($data['filter']['order_status_id']) &&
			            (int)($data['filter']['order_status_id']) != 0 &&
			            $data['filter']['order_status_id'] != '') {
				$sql .= " WHERE oh.id_order_state = " . (int)$data['filter']['order_status_id'];
			} else {
				$sql .= " WHERE oh.id_order_state != 0 ";
			}
			if (isset($data['filter']['fio']) && $data['filter']['fio'] != '') {
				$params = [];
				$newparam = explode(' ', $data['filter']['fio']);

				foreach ($newparam as $key => $value) {
					if ($value == '') {
						unset($newparam[$key]);
					} else {
						$params[] = $value;
					}
				}

				$sql .= " AND ( c.firstname LIKE '%" . $params[0] . "%' OR o.lastname LIKE '%" . $params[0] . "%'";

				foreach ($params as $param) {
					if ($param != $params[0]) {
						$sql .= " OR o.firstname LIKE '%" . $params[0] . "%' 
									OR o.lastname LIKE '%" . $param . "%'";
					};
				}
				$sql .= " ) ";
			}
			if (isset($data['filter']['min_price']) && isset($data['filter']['max_price']) && $data['filter']['max_price'] != ''  && $data['filter']['min_price'] != 0) {
				$sql .= " AND o.total > " . $data['filter']['min_price'] . " AND o.total <= " . $data['filter']['max_price'];
			}
			if (isset($data['filter']['date_min']) && $data['filter']['date_min'] != '') {
				$date_min = date('y-m-d', strtotime($data['filter']['date_min']));
				$sql .= " AND DATE_FORMAT(o.date_add,'%y-%m-%d') > '" . $date_min . "'";
			}
			if (isset($data['filter']['date_max']) && $data['filter']['date_max'] != '') {
				$date_max = date('y-m-d', strtotime($data['filter']['date_max']));
				$sql .= " AND DATE_FORMAT(o.date_add,'%y-%m-%d') < '" . $date_max . "'";
			}


		} else {
			$sql .= " WHERE oh.id_order_state != 0 ";
		}
		$sql .= " GROUP BY o.id_order ORDER BY o.id_order DESC";

		$sql .= " LIMIT " . (int)$data['limit'] . " OFFSET " . (int)$data['page'];

		$results = Db::getInstance()->ExecuteS( $sql );

		return $results;
	}

}
