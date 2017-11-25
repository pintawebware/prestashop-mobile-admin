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
class ApimoduleClientsModuleFrontController extends ModuleFrontController {
	public $ssl = true;
	public $display_column_left = false;
	public $header = false;
	public $errors = [];
	public $API_VERSION = 2.0;
	public $return = [];

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent() {
		$this->return['status'] = false;
		if(isset($_GET['action']) && $this->valid()){

			$action = $_GET['action'];
			switch ($action){
				case 'list':$this->getClientsList();break;
				case 'info':$this->getClientsInfo();break;
				case 'orders':$this->getClientsOrders();break;
			}
		}
		$this->errors[] = "No action";
		header( 'Content-Type: application/json' );
		die( Tools::jsonEncode( $this->return ) );
	}

	private function valid() {
		$token = trim( Tools::getValue( 'token' ) );
		if ( empty( $token ) ) {
			$this->errors[] = 'You need to be logged!';
			return false;
		} else {
			$results = $this->getTokens( $token );
			if (! $results ) {
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


	/**
	 * @api {get} index.php?action=list&fc=module&module=apimodule&controller=clients  getClients
	 * @apiName GetClients
	 * @apiGroup Clients
	 *
	 * @apiParam {Token} token your unique token.
	 * @apiParam {Number} page number of the page.
	 * @apiParam {Number} limit limit of the orders for the page.
	 * @apiParam {String} fio full name of the client.
	 * @apiParam {String} sort param for sorting clients(sum/quantity/date_add).
	 *
	 * @apiSuccess {Number} version  Current API version.
	 * @apiSuccess {Number} client_id  ID of the client.
	 * @apiSuccess {String} fio     Client's FIO.
	 * @apiSuccess {Number} total  Total sum of client's orders.
	 * @apiSuccess {String} currency_code  Default currency of the shop.
	 * @apiSuccess {Number} quantity  Total quantity of client's orders.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 * {
	 *   "Response"
	 *   {
	 *     "clients"
	 *      {
	 *          {
	 *              "client_id" : "88",
	 *              "fio" : "Anton Kiselev",
	 *              "total" : "1006.00",
	 *              "currency_code": "UAH",
	 *              "quantity" : "5"
	 *          },
	 *          {
	 *              "client_id" : "10",
	 *              "fio" : "Vlad Kochergin",
	 *              "currency_code": "UAH",
	 *              "total" : "555.00",
	 *              "quantity" : "1"
	 *          }
	 *      }
	 *    },
	 *    "Status" : true,
	 *    "version": 1.0
	 * }
	 * @apiErrorExample Error-Response:
	 * {
	 *      "Error" : "Not one client found",
	 *      "version": 1.0,
	 *      "Status" : false
	 * }
	 *
	 *
	 */
	public function getClientsList()
	{
		$page = trim( Tools::getValue( 'page' ) );
		$limit = trim( Tools::getValue( 'limit' ) );
		$fio = trim( Tools::getValue( 'fio' ) );
		$sort = trim( Tools::getValue( 'sort' ) );

		if (isset($page) && (int)$page!= 0 && isset($limit) && (int)$limit != 0) {
			$page = ($page - 1) * $limit;
			$limit = $limit;
		} else {
			$page = 0;
			$limit = 20;
		}
		if (empty($sort)) {
			$sort = 'date_add';
		}
		if (empty($fio)) {
			$fio = '';
		}
		if ($sort == 'total') {
		    $sort = 'total_paid';
        }
		$this->return['status'] = false;
		$clients = $this->getClients(array('page' => $page, 'limit' => $limit, 'sort' => $sort, 'fio' => $fio));
		$response = [];
		if (count($clients) > 0) {
				$data = [];
			foreach ($clients as $client) {
				$data['client_id'] = $client['id_customer'];
				$data['fio'] = $client['firstname'] . ' ' . $client['lastname'];
				$data['total'] = number_format($client['sum'], 2, '.', '');
				$data['quantity'] = $client['quantity'];
				$data['currency_code'] = Context::getContext()->currency->iso_code;
				$clients_to_response[] = $data;
			}
			$response['clients'] = $clients_to_response;

			$this->return['status'] = true;
			$this->return['response'] = $response;
		} else {
			$this->return['status'] = true;
			$this->return['response']['clients'] = [];
		}
		$this->return['version'] = $this->API_VERSION;
		header('Content-Type: application/json');
		die(Tools::jsonEncode($this->return));
	}

	/**
	 * @api {get} index.php?action=info&fc=module&module=apimodule&controller=clients getClientInfo
	 * @apiName getClientInfo
	 * @apiGroup Clients
	 *
	 * @apiParam {Token} token your unique token.
	 * @apiParam {Number} client_id unique client ID.
	 *
	 * @apiSuccess {Number} version  Current API version.
	 * @apiSuccess {Number} client_id  ID of the client.
	 * @apiSuccess {String} fio     Client's FIO.
	 * @apiSuccess {Number} total  Total sum of client's orders.
	 * @apiSuccess {Number} quantity  Total quantity of client's orders.
	 * @apiSuccess {String} email  Client's email.
	 * @apiSuccess {String} telephone  Client's telephone.
	 * @apiSuccess {Number} cancelled  Total quantity of cancelled orders.
	 * @apiSuccess {Number} completed  Total quantity of completed orders.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 * {
	 *   "Response"
	 *   {
	 *         "client_id" : "88",
	 *         "fio" : "Anton Kiselev",
	 *         "total" : "1006.00",
	 *         "quantity" : "5",
	 *         "cancelled" : "1",
	 *         "completed" : "2",
	 *         "email" : "client@mail.ru",
	 *         "telephone" : "13456789"
	 *   },
	 *   "Status" : true,
	 *   "version": 1.0
	 * }
	 * @apiErrorExample Error-Response:
	 * {
	 *      "Error" : "Not one client found",
	 *      "version": 1.0,
	 *      "Status" : false
	 * }
	 *
	 *
	 */
	public function getClientsInfo()
	{

		$id = trim( Tools::getValue( 'client_id' ) );
		$this->return['status'] = false;
		if (!empty($id)) {
			$client = $this->getClientInfo($id);
			if ($client) {
				$data['client_id'] = $client['id_customer'];
				$data['fio'] = $client['firstname'] . ' ' . $client['lastname'];
				$data['email'] = $client['email'];

				$data['telephone'] = $this->getPhones($id);

				$data['total'] = number_format($client['sum'], 2, '.', '');
				$data['quantity'] = $client['quantity'];

				$data['completed'] = $client['completed'];
				$data['cancelled'] = $client['cancelled'];
				$data['currency_code'] = Context::getContext()->currency->iso_code;
				$this->return['status'] = true;
				$this->return['response'] = $data;

			} else {

				$this->return['error'] = 'Can not found client with id = ' . $id;
			}
		} else {
			$this->return['error'] = 'You have not specified ID';
		}
		$this->return['version'] = $this->API_VERSION;
		header('Content-Type: application/json');
		die(Tools::jsonEncode($this->return));
	}

	private function getPhones($id){
		$id_lang = $this->context->language->id;
		$customer = new Customer($id);
		$array = $customer->getAddresses($id_lang);
		$phones = [];
		//var_dump($array);
		foreach ($array  as $item ) {
			if(!in_array($item['phone'],$phones)) {
				$trim = trim($item['phone']);
				if(!empty($trim)){
					// $phones[] = str_replace(' ','-',$trim);
					$phones = str_replace(' ','-',$trim);
				}
			}
		}
		return $phones;
	}
	/**
	 * @api {get} index.php?action=orders&fc=module&module=apimodule&controller=clients  getClientOrders
	 * @apiName getClientOrders
	 * @apiGroup Clients
	 *
	 * @apiParam {Token} token your unique token.
	 * @apiParam {Number} client_id unique client ID.
	 * @apiParam {String} sort param for sorting orders(total/date_add/completed/cancelled).
	 *
	 * @apiSuccess {Number} version  Current API version.
	 * @apiSuccess {Number} id_order  ID of the order.
	 * @apiSuccess {Number} order_number  Number of the order.
	 * @apiSuccess {String} status  Status of the order.
	 * @apiSuccess {String} currency_code  Default currency of the shop.
	 * @apiSuccess {Number} total  Total sum of the order.
	 * @apiSuccess {Date} date_add  Date added of the order.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 * {
	 *   "Response"
	 *   {
	 *       "orders":
	 *          {
	 *             "order_id" : "1",
	 *             "order_number" : "1",
	 *             "status" : 1,
	 *             "currency_code": "UAH",
	 *             "total" : "106.00",
	 *             "date_add" : "2016-12-09 16:17:02"
	 *          },
	 *          {
	 *             "order_id" : "2",
	 *             "currency_code": "UAH",
	 *             "order_number" : "2",
	 *             "status" : 2,
	 *             "total" : "506.00",
	 *             "date_add" : "2016-10-19 16:00:00"
	 *          }
	 *          "statuses" :
	 *                  {
	 *                         {
	 *                             "name": "Отменено",
	 *                             "id_order_state": "7",
	 *                             "id_lang": "1"
	 *                         },
	 *                         {
	 *                             "name": "Сделка завершена",
	 *                             "id_order_state": "5",
	 *                             "id_lang": "1"
	 *                          },
	 *                          {
	 *                              "name": "Ожидание",
	 *                              "id_order_state": "1",
	 *                              "id_lang": "1"
	 *                           }
	 *                    }
	 *    },
	 *    "Status" : true,
	 *    "version": 1.0
	 * }
	 * @apiErrorExample Error-Response:
	 * {
	 *      "Error" : "You have not specified ID",
	 *      "version": 1.0,
	 *      "Status" : false
	 * }
	 *
	 *
	 */

	public function getClientsOrders()
	{
		$id = trim( Tools::getValue( 'client_id' ) );
		$sort = trim( Tools::getValue( 'sort' ) );
		$this->return['status'] = false;
		if (!empty($id)) {
			if ($sort) {
				switch ($sort) {
					case 'date_add':
						$sort = 'date_add';
						break;
					case 'total':
						$sort = 'total_paid';
						break;
					case 'completed':
						$sort = 'completed';
						break;
					case 'cancelled':
						$sort = 'cancelled';
						break;
					default:
						$sort = 'date_add';
				}
			} else {
				$sort = 'date_add';
			}

			$orders = $this->getClientOrders($id, $sort);

			if ($orders) {
				foreach ($orders as $order) {
					$data['order_id'] = $order['id_order'];
					$data['order_number'] = $order['id_order'];
					$data['total'] = number_format($order['total_paid'], 2, '.', '');
					$data['date_add'] = $order['date_add'];
					$data['currency_code'] = Context::getContext()->currency->iso_code;
					if (isset($order['name'])) {
						$data['status'] = $order['name'];
					} else {
						$data['status'] = '';
					}

					$to_response[] = $data;
				}
				$response['orders'] = $to_response;
				$response['statuses'] = $this->OrderStatusList();
				$this->return['status'] = true;
				$this->return['response'] = $response;

			} else {
				$this->return['status'] = true;
				$this->return['response'] = ['orders' => []];
							}
		} else {
			$this->return['error'] = 'You have not specified ID';
		}

		$this->return['version'] = $this->API_VERSION;
		header('Content-Type: application/json');
		die(Tools::jsonEncode($this->return));
	}

	public function getClients($data = array())
	{
		$sql = "SELECT  SUM(o.total_paid) sum, COUNT(o.id_order) quantity, c.firstname, c.lastname, c.date_add, c.id_customer 
				FROM " . _DB_PREFIX_ . "orders AS o 
				LEFT JOIN " . _DB_PREFIX_ . "customer AS c ON c.id_customer = o.id_customer 
				WHERE c.id_customer != 0 ";

		if (!empty($data['fio'])) {
			$params = [];
			$newparam = explode(' ', $data['fio']);

			foreach ($newparam as $key => $value) {
				if ($value == '') {
					unset($newparam[$key]);
				} else {
					$params[] = $value;
				}
			}

			$sql .= " AND ( c.firstname LIKE \"%" . $params[0] . "%\" OR c.lastname LIKE \"%" . $params[0] . "%\" ";

			foreach ($params as $param) {
				if ($param != $params[0]) {
					$sql .= " OR c.firstname LIKE \"%" . $params[0] . "%\" OR  c.lastname LIKE \"%" . $param . "%\" ";
				};
			}
			$sql .= " ) ";
		}
		$sql .= " group by c.id_customer";

		if(isset($data['sort']) && $data['sort'] != ''){
			$sql .= " ORDER BY ". $data['sort'] ." DESC";
		}

		$sql .= " LIMIT " . (int)$data['limit'] . " OFFSET " . (int)$data['page'];

		$results = Db::getInstance()->ExecuteS( $sql );
		return $results;
	}

	public function getClientInfo ($id)
	{

		$sql = "SELECT  SUM(o.total_paid) sum, 
				COUNT(o.id_order) quantity, c.firstname, c.lastname, c.date_add, c.id_customer, c.email 
				FROM " . _DB_PREFIX_ . "orders AS o 
				LEFT JOIN " . _DB_PREFIX_ . "customer AS c ON c.id_customer = o.id_customer ";

		$sql .= "  WHERE c.id_customer = ".$id ;
		$sql .= " group by c.id_customer";
		$results = Db::getInstance()->getRow( $sql );

		$completed = "SELECT  COUNT(o.id_order) completed
					FROM " . _DB_PREFIX_ . "orders AS o 
					LEFT JOIN " . _DB_PREFIX_ . "customer AS c ON c.id_customer = o.id_customer 
					INNER JOIN " . _DB_PREFIX_ . "order_history as oh ON o.id_order=oh.id_order 
					WHERE c.id_customer = ". $id ." AND oh.id_order_state = 5 group by c.id_customer";

		$cancelled = "SELECT  COUNT(o.id_order) cancelled FROM " . _DB_PREFIX_ . "orders AS o 
					LEFT JOIN " . _DB_PREFIX_ . "customer AS c ON c.id_customer = o.id_customer 
					INNER JOIN " . _DB_PREFIX_ . "order_history as oh ON o.id_order=oh.id_order 
					WHERE c.id_customer = ". $id ." AND oh.id_order_state = 6 group by c.id_customer";

		if(Db::getInstance()->getRow( $completed )){
			$c = Db::getInstance()->getRow( $completed );
			$results['completed'] =$c['completed'];
		}else{
			$results['completed'] =0;
		}
		if(Db::getInstance()->getRow( $completed )){
			$c = Db::getInstance()->getRow( $cancelled );
			$results['cancelled'] =$c['cancelled'];
		}else{
			$results['cancelled'] =0;
		}

		return $results;
	}

	public function getClientOrders($id, $sort)
	{

		if ($sort != 'cancelled' && $sort != 'completed'){
			$sql = "SELECT o.id_order, o.total_paid, o.date_add, 
 						(SELECT id_order_state FROM ps_order_history as oh 
 						 WHERE o.id_order=oh.id_order ORDER BY id_order DESC LIMIT 0,1) as name
 						FROM " . _DB_PREFIX_ . "orders AS o 
						
						WHERE o.id_customer = " . $id;
//			INNER JOIN " . _DB_PREFIX_ . "order_history AS oh ON o.id_order=oh.id_order
			$sql .= "  ORDER BY " . $sort . " DESC";

			$results = Db::getInstance()->ExecuteS( $sql );

		}elseif($sort == 'cancelled'){
			$sql = "SELECT o.id_order, o.total_paid, o.date_add, 
					(SELECT id_order_state FROM ps_order_history as oh 
 						 WHERE o.id_order=oh.id_order ORDER BY id_order DESC LIMIT 0,1) as name FROM " . _DB_PREFIX_ . "orders AS o 
					
					WHERE o.id_customer = " . $id . " AND  oh.id_order_state != 6
					GROUP BY o.id_order ORDER BY o.date_add DESC";

			$sql2 = "SELECT o.id_order, o.total_paid, o.date_add, oh.id_order_state
 						 FROM " . _DB_PREFIX_ . "orders AS o 
                         INNER JOIN ps_order_history AS oh ON oh.id_order = o.id_order 
								 WHERE o.id_customer = " . $id . " AND   oh.id_order_state = 6 
								 GROUP BY o.id_order ORDER BY o.date_add DESC";

			$results = Db::getInstance()->ExecuteS( $sql2 );
		}elseif($sort == 'completed'){
			$sql = "SELECT o.id_order, o.total_paid, o.date_add, 
 (SELECT id_order_state FROM ps_order_history as oh 
 						 WHERE o.id_order=oh.id_order ORDER BY id_order DESC LIMIT 0,1) as name
 						 FROM " . _DB_PREFIX_ . "orders AS o 
					
					WHERE o.id_customer = " . $id . " AND   oh.id_order_state != 5
					GROUP BY o.id_order ORDER BY o.date_add DESC";

			$sql2 = "SELECT o.id_order, o.total_paid, o.date_add, oh.id_order_state
 						 FROM " . _DB_PREFIX_ . "orders AS o 
					     INNER JOIN ps_order_history AS oh ON oh.id_order = o.id_order 
					WHERE o.id_customer = " . $id . " AND  oh.id_order_state = 5 
					GROUP BY o.id_order ORDER BY o.date_add DESC";

			$results = Db::getInstance()->ExecuteS( $sql2 );
		}

		return $results;
	}

	public function OrderStatusList()
	{
		$sql = "SELECT id_order_state,id_lang, name FROM " . _DB_PREFIX_ . "order_state_lang WHERE id_lang = 1 ";
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
