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
	public $errors = '';
	public $API_VERSION = 2.0;
	public $return = [];

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent() {
		$this->return['status'] = false;
		if(isset($_GET['action'])/* && $this->valid()*/){

			$action = $_GET['action'];
			switch ($action){
				case 'list':$this->getOrdersList();break;
				case 'products':$this->getOrderProducts();break;
				case 'history':$this->getOrdersHistory();break;
				case 'info':$this->getOrdersInfo();break;
				case 'pad':$this->getPaymentAndDelivery();break;
				case 'status_update':$this->statusUpdate();break;
				case 'delivery_update':$this->changeOrderDelivery();break;
			}
		}
		$this->errors = "No action";
		header( 'Content-Type: application/json' );
		die( Tools::jsonEncode( $this->return ) );
	}

	private function valid() {
		$token = trim( Tools::getValue( 'token' ) );
		if ( empty( $token ) ) {
			$this->errors = 'You need to be logged!';
			return false;
		} else {
			$results = $this->getTokens( $token );
			if (!$results ) {
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
	 * @api {get} /index.php?action=list&fc=module&module=apimodule&controller=orders  getOrders
	 * @apiName GetOrders
	 * @apiGroup Orders
     *
     * @apiParam {Token}     token your unique token.
     * @apiParam {Number}    page number of the page.
     * @apiParam {Number}    limit limit of the orders for the page.
     * @apiParam {Array[]}   filter Array of the filters.
     * @apiParam {String}    filter.fio full name of the client.
     * @apiParam {Number}    filter.order_status_id unique id of the order.
     * @apiParam {Number}    filter.min_price min price of order.
     * @apiParam {Number}    filter.max_price max price of order.
     * @apiParam {Date}      filter.date_min min date adding of the order.
     * @apiParam {Date}      filter.date_max max date adding of the order.
	 *
     * @apiSuccess {Number}  version                          Current API version.
     * @apiSuccess {Bool}    status                           Response status.
     * @apiSuccess {Array[]} response                         Array with content response.
     * @apiSuccess {String}  response.total_quantity          Total quantity of the orders.
     * @apiSuccess {String}  response.currency_code           Default currency of the shop.
     * @apiSuccess {Number}  response.total_sum               Total amount of orders.
     * @apiSuccess {String}  response.max_price               Maximum order amount.
     * @apiSuccess {Number}  response.api_version             Current API version.
     *
     * @apiSuccess {Array[]} response.orders                  Array of the orders.
     * @apiSuccess {Array[]} response.statuses                Array of the order statuses.

     * @apiSuccess {String} response.orders.order_id          ID of the order.
     * @apiSuccess {String} response.orders.order_number      Number of the order.
     * @apiSuccess {String} response.orders.fio               Client's FIO.
     * @apiSuccess {String} response.orders.status            Status of the order.
     * @apiSuccess {String} response.orders.total             Total sum of the order.
     * @apiSuccess {String} response.orders.date_added        Date added of the order.
     * @apiSuccess {String} response.orders.currency_code     Currency of the order.
     *
     * @apiSuccess {String} response.statuses.name            Status Name.
     * @apiSuccess {String} response.statuses.order_status_id Status id.
     * @apiSuccess {String} response.statuses.language_id     Language id.
	 *
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK {
     *  "status": true,
     *   "response": {
     *       "total_quantity": 14,
     *       "currency_code": "UAH",
     *       "total_sum": "1257.76",
     *       "orders": [
     *           {
     *               "order_number": "14",
     *               "order_id": "14",
     *               "fio": "тестовый  заказ",
     *               "status": "Ожидается оплата чеком",
     *               "total": "61.19",
     *               "date_add": "2019-01-07 23:24:56",
     *               "currency_code": "UAH"
     *           },
     *           {
     *               "order_number": "4",
     *               "order_id": "4",
     *               "fio": "John DOE",
     *               "status": "Ожидается оплата чеком",
     *               "total": "89.89",
     *               "date_add": "2018-01-04 09:10:54",
     *               "currency_code": "UAH"
     *           }
     *       ],
     *       "max_price": "367.19",
     *       "statuses": [
     *           {
     *               "id_order_state": "1",
     *               "id_lang": "1",
     *               "name": "Ожидается оплата чеком"
     *           },
     *           {
     *               "id_order_state": "2",
     *               "id_lang": "1",
     *               "name": "Платеж принят"
     *           }
     *       ],
     *   },
     *   "version": 1,
     *  "error": ""
	 * }
	 * @apiErrorExample Error-Response:
	 * {
	 *      "version": 1.0,
	 *      "Status" : false
	 * }
	 *
	 */
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

		if (!empty($order_status_id)||!empty($fio)||
		    !empty($min_price)||!empty($max_price)||
		    !empty($date_min)||!empty($date_max)) {
			$filter = [];
			$filter['order_status_id'] = !empty($order_status_id)?$order_status_id:'';
			$filter['fio'] = !empty($fio)?$fio:'';
			$filter['min_price'] = !empty($min_price)?$min_price:1;
			$filter['max_price'] = !empty($max_price)?$max_price:$this->getMaxOrderPrice();
			$filter['date_min'] = !empty($date_min)?$date_min:'';
			$filter['date_max'] = !empty($date_max)?$date_max:'';

			$orders = $this->getOrders(array('filter' => $filter, 'page' => $page, 'limit' => $limit));
			$total = $this->getOrders([
			    'filter' => $filter,
                'total' => true
            ]);

		} else {
			$orders = $this->getOrders(array('page' => $page, 'limit' => $limit));
            $total = $this->getOrders([
                'total' => true
            ]);
		}
		$response = [];
		$orders_to_response = [];

		$sum = 0;
		$quantity = 0;
		$statuses = $this->OrderStatusList();

		$statusArray = [];
		foreach ($statuses as $one):
			$statusArray[$one['id_order_state']] = $one['name'];
		endforeach;
		foreach ($orders as $order) {
			$sum = $sum + $order['total_paid'];
			$quantity++;
			$data['order_number'] = $order['id_order'];
			$data['order_id'] = $order['id_order'];
			$data['fio'] = $order['firstname'] . ' ' . $order['lastname'];

			$data['status'] = $statusArray[$order['id_order_state']];

			$data['total'] = number_format($order['total_paid'], 2, '.', '');
			$data['date_add'] = $order['date_add'];
			$data['currency_code'] = Context::getContext()->currency->iso_code;
			$orders_to_response[] = $data;

		}
		$totalQty = 0;
		$totalSum = 0;
		foreach ($total as $order) {
            $totalQty++;
            $totalSum += $order['total_paid'];
        }

		$this->return['response']['total_quantity'] = $totalQty;
		$this->return['response']['currency_code'] = Context::getContext()->currency->iso_code;
		$this->return['response']['total_sum'] = number_format($totalSum, 2, '.', '');
		$this->return['response']['orders'] = $orders_to_response;
		$this->return['response']['max_price'] = $this->getMaxOrderPrice();

		$this->return['response']['statuses'] = $statuses;
		$this->return['response']['api_version'] = $this->API_VERSION;

		$this->return['version'] = $this->API_VERSION;

		$this->return['status'] = true;

		$this->return['error'] = $this->errors;
		header('Content-Type: application/json');
		die(Tools::jsonEncode($this->return));

	}


	/**
	 * @api {get} /index.php?action=products&fc=module&module=apimodule&controller=orders  getOrderProducts
	 * @apiName getOrderProducts
	 * @apiGroup Orders
	 *
	 * @apiParam {Token} token your unique token.
	 * @apiParam {ID} order_id unique order id.
	 *
     *
     * @apiSuccess {Array[]}   response                           Array with content response.
     * @apiSuccess {Number}    version                            Current API version.
     * @apiSuccess {Bool}      status                             Response status.
     *
     * @apiSuccess {Array[]}   response.products                  Array of products.
     * @apiSuccess {String}    response.products.name             Name of the product.
     * @apiSuccess {String}    response.products.image            Picture of the product.
     * @apiSuccess {String}    response.products.model            Model of the product.
     * @apiSuccess {String}    response.products.quantity         Quantity of the product.
     * @apiSuccess {String}    response.products.price            Price of the product.
     * @apiSuccess {String}    response.products.product_id       Unique product id.
     * @apiSuccess {String}    response.products.discount         Percentage discount.
     * @apiSuccess {String}    response.products.discount_price   Cost with discount.
     *
     * @apiSuccess {Array[]}   response.products.options                   Array of of the product options.
     * @apiSuccess {String}    response.products.options.option_id         Option id.
     * @apiSuccess {String}    response.products.options.option_name       Option name.
     * @apiSuccess {String}    response.products.options.option_value_id   Option value id.
     * @apiSuccess {String}    response.products.options.option_value_name Option value name.
     * @apiSuccess {String}    response.products.options.language_id       Language id of options and option values.
     *
     * @apiSuccess {Array[]}   response.total_order_price                  The array with a list of prices.
     * @apiSuccess {Number}    response.total_order_price.total_discount     The amount of the discount for the order.
     * @apiSuccess {Number}    response.total_order_price.total_price        Sum of product's prices.
     * @apiSuccess {Number}    response.total_order_price.shipping_price     Cost of the shipping.
     * @apiSuccess {Number}    response.total_order_price.total              Total order sum.
     * @apiSuccess {String}    response.total_order_price.currency_code      Currency of the order.
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK  {
     *   "status": true,
     *   "response": {
     *       "products": [
     *           {
     *               "image": "http://prestashop1721.pixy.pro/1-home_default/.jpg",
     *               "name": "Faded Short Sleeve T-shirts - Color : Orange, Size : S",
     *               "model": "",
     *               "quantity": 1,
     *               "price": "16.51",
     *               "product_id": "1",
     *               "discount_price": "0.000000",
     *               "discount": "0"
     *           }
     *       ],
     *       "total_order_price": {
     *           "total_discount": 47.38,
     *           "total_price": "89.89",
     *           "shipping_price": 2,
     *           "total": "91.89",
     *           "currency_code": "UAH"
     *       }
     *   },
     *   "version": 1
	 * }
	 * @apiErrorExample Error-Response:
	 *
	 *     {
	 *          "error": "Can not found any products in order with id = 10",
	 *          "version": 1.0,
	 *          "Status" : false
	 *     }
	 *
	 */
	public function getOrderProducts()
	{
		$id = trim( Tools::getValue( 'order_id' ) );
		$this->return['status'] = false;
		if (!empty($id)) {
			$order = new Order($id);
			$products = $order->getProducts();

			if (count($products) > 0) {
                $id_lang = $this->context->language->id;
				$data               = array();
				$total_discount_sum = 0;
				$shipping_price        = 0;
				$total_price        = 0;
				foreach ( $products as $product ):
					$productId = $product['id_product'];
                    $productObj = new Product($productId, false, $id_lang);
					$array = [];
					if (!empty($product['image'])) {
						$image = Image::getCover($product['product_id']);
                        $linkObj = new Link();
						$imagePath = $linkObj->getImageLink($productObj->link_rewrite, $image['id_image'], 'home_default');
						 $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://'; 
						$array['image'] = $protocol.$imagePath;
					}else{
						$array['image'] = '';
					}

					if (!empty($product['product_name'])) {
						$array['name'] = strip_tags( htmlspecialchars_decode( $product['product_name'] ) );
					}else{
						$array['name'] = '';
					}
					if (!empty($product['model'])){
						$array['model'] = $product['model'];
					}else{
						$array['model'] = '';
					}
					if (!empty($product['product_quantity'])&& $product['product_quantity']!=0){

						$array['quantity'] = (int)$product['product_quantity'];
					}else{
						$array['quantity'] = (int)$product['minimal_quantity'];
					}
					if (!empty($product['total_price_tax_incl'])){
						$array['price'] = number_format( $product['total_price_tax_incl'], 2, '.', '' );
					}else{
						$array['price'] = 0;
					}
					$array['product_id'] = $product['product_id'];

					$array['discount_price'] = $product['product_quantity_discount'];
					$array['discount']       = $product['quantity_discount'];
					$array['options']        = $this->getOptionsByProductAttributeId($product['product_attribute_id']);
					$array['features'] =  $this->getFeatureByProductAttributeId($product['product_id']);
                    //var_dump($product);die();
					$total_discount_sum += $product['product_quantity_discount'];

					$shipping_price += $product['additional_shipping_cost'];

					$data['products'][] = $array;
				endforeach;

				$total_price = $order->total_paid;
				$total_shipping = $order->total_shipping;
				$total = $total_price + $total_shipping;

				$data['total_order_price'] = array(
					'total_discount' => $total_discount_sum,
					'total_price' => number_format($total_price, 2, '.', ''),
					'shipping_price' => +number_format($order->total_shipping, 2, '.', ''),
					'total' => number_format($total, 2, '.', ''),
					'currency_code' => Context::getContext()->currency->iso_code
				);

				$this->return['response'] = $data;
				$this->return['status'] = true;

			} else {
				$this->return['error'] = 'Can not found any products in order with id = ' . $id;

			}
		} else {

			$this->return['error'] = 'You have not specified ID';
		}

		$this->return['version'] = $this->API_VERSION;
		header('Content-Type: application/json');
		die(Tools::jsonEncode($this->return));
	}


  private function getOptionsByProductAttributeId($id)
  {
      $id_product_attribute = (int)$id;
      $id_lang = $this->context->language->id;
    
      $id_attribute_query = "SELECT
                              a.id_attribute       option_value_id,
                              a.id_attribute_group option_id,
                              al.id_lang           language_id,
                              al.name              option_value_name,
                              agl.name             option_name
                             FROM       " . _DB_PREFIX_ . "product_attribute_combination pac
                             INNER JOIN " . _DB_PREFIX_ . "attribute                     a
                             INNER JOIN " . _DB_PREFIX_ . "attribute_group_lang          agl
                             INNER JOIN " . _DB_PREFIX_ . "attribute_lang                al
                             WHERE  pac.id_product_attribute = " . (int)$id_product_attribute . " 
                             AND    a.id_attribute = pac.id_attribute
                             AND    al.id_attribute = a.id_attribute
                             AND    al.id_lang = $id_lang 
                             AND    agl.id_attribute_group = a.id_attribute_group
                             AND    agl.id_lang = $id_lang";
      $id_attribute_result = Db::getInstance()->ExecuteS($id_attribute_query);

      return $id_attribute_result;
  }

    private function getFeatureByProductAttributeId($id){
        $id_product_feature = (int)$id;
        $id_lang = $this->context->language->id;

        $id_feature_query = "SELECT " . _DB_PREFIX_ . "feature.id_feature feature_id,
                                " . _DB_PREFIX_ . "feature_lang.id_lang language_id ,
                                " . _DB_PREFIX_ . "feature_lang.name feature_name,
                                " . _DB_PREFIX_ . "feature_value_lang.id_feature_value feature_value_id,
                                " . _DB_PREFIX_ . "feature_value_lang.value feature_value_name

                                FROM " . _DB_PREFIX_ . "feature, " . _DB_PREFIX_ . "feature_lang, " . _DB_PREFIX_ . "feature_product," . _DB_PREFIX_ . "feature_value, " . _DB_PREFIX_ . "feature_value_lang

                                WHERE 
                                " . _DB_PREFIX_ . "feature.id_feature = " . _DB_PREFIX_ . "feature_lang.id_feature
                                AND " . _DB_PREFIX_ . "feature.id_feature = " . _DB_PREFIX_ . "feature_product.id_feature
                                AND " . _DB_PREFIX_ . "feature.id_feature = " . _DB_PREFIX_ . "feature_value.id_feature
                                AND " . _DB_PREFIX_ . "feature_value.id_feature_value = " . _DB_PREFIX_ . "feature_value_lang.id_feature_value
                                AND " . _DB_PREFIX_ . "feature_product.id_feature_value = " . _DB_PREFIX_ . "feature_value.id_feature_value
                                AND " . _DB_PREFIX_ . "feature_lang.id_lang = ".$id_lang."
                                AND " . _DB_PREFIX_ . "feature_value_lang.id_lang = ".$id_lang."
                                AND " . _DB_PREFIX_ . "feature_product.id_product =".(int)$id_product_feature;
        $id_feature_result = Db::getInstance()->ExecuteS($id_feature_query);

        return $id_feature_result;
    }



	/**
	 * @api {get} /index.php?action=history&fc=module&module=apimodule&controller=orders  getOrderHistory
	 * @apiName getOrderHistory
	 * @apiGroup Orders
	 *
	 * @apiParam {Number} order_id unique order ID.
	 * @apiParam {Token} token your unique token.
	 *
     * @apiSuccess {Number}    version                            Current API version.
     * @apiSuccess {Bool}      status                             Response status.
     *
     * @apiSuccess {Array[]}   response                           Array with content response.
     * @apiSuccess {Array[]}   response.orders                    An array with information about the order.
     * @apiSuccess {String}    response.orders.name               Status of the order.
     * @apiSuccess {String}    response.orders.order_status_id    ID of the status of the order.
     * @apiSuccess {String}    response.orders.date_add           Date of adding status of the order.
     * @apiSuccess {String}    response.orders.comment            Some comment added from manager.
     * @apiSuccess {Array[]}   response.statuses                  Statuses list for order.
     *
     * @apiSuccess {String}    response.statuses.name             Status name.
     * @apiSuccess {String}    response.statuses.id_lang          Language id.
     * @apiSuccess {String}    response.statuses.id_order_state  Status id.
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK {
        "status": true,
        "response": {
            "orders": [
                {
                    "name": "Ожидается оплата чеком",
                    "order_status_id": "1",
                    "date_add": "2018-01-04 09:10:55",
                    "comment": ""
                }
            ],
            "statuses": [
                {
                    "id_order_state": "1",
                    "id_lang": "1",
                    "name": "Ожидается оплата чеком"
                },
                {
                    "id_order_state": "2",
                    "id_lang": "1",
                    "name": "Платеж принят"
                },
                {
                    "id_order_state": "3",
                    "id_lang": "1",
                    "name": "В обработке"
                }
            ]
        },
        "version": 1
	 * }
	 * @apiErrorExample Error-Response:
	 * {
	 *          "error": "Can not found any statuses for order with id = 5",
	 *          "version": 1.0,
	 *          "Status" : false
	 * }
	 */

	public function getOrdersHistory(){
		$id = trim( Tools::getValue( 'order_id' ) );
		if (!empty($id)) {
			$order = new Order($id);
            $id_lang = $this->context->language->id;
			$history = $order->getHistory($id_lang);
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
					$data['date_add'] = $item['date_add'];
					$data['comment'] ='';
					$response['orders'][] = $data;
				endforeach;

				$response['statuses'] = $statuses;

				$this->return['status'] = true;
				$this->return['response'] = $response;

			} else {

				$this->return['status']  = false;
				$this->return['error']  = 'Can not found any statuses for order with id = ' . $id;

			}
		} else {
			$this->return['status']  = false;
			$this->return['error']  = 'You have not specified ID';
		}
		$this->return['version'] = $this->API_VERSION;
		header( 'Content-Type: application/json' );
		die( Tools::jsonEncode( $this->return ) );
	}

	/**
	 * @api {get} /index.php?action=info&fc=module&module=apimodule&controller=orders  getOrderInfo
	 * @apiName getOrderInfo
	 * @apiGroup Orders
	 *
     * @apiSuccess {Number}    version                            Current API version.
     * @apiSuccess {Bool}      status                             Response status.
	 *
     * @apiSuccess {Number}    version                            Current API version.
     * @apiSuccess {Bool}      status                             Response status.
     * @apiSuccess {Array[]}   response                           Array with content response.
     *
     * @apiSuccess {String}    response.order_number              Number of the order.
     * @apiSuccess {String}    response.fio                       Client's FIO.
     * @apiSuccess {String}    response.status                    Status of the order.
     * @apiSuccess {String}    response.email                     Client's email.
     * @apiSuccess {String}    response.telephone                 Client's phone.
     * @apiSuccess {String}    response.total                     Total sum of the order.
     * @apiSuccess {String}    response.currency_code             Default currency of the shop.
     * @apiSuccess {String}    response.date_add                  Date added of the order.
     * @apiSuccess {Array[]}   response.statuses                  Statuses list for order.
     *
     * @apiSuccess {String}    response.statuses.id_lang          Language id
     * @apiSuccess {String}    response.statuses.name             Status name
     * @apiSuccess {String}    response.statuses.id_order_state   Status id
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK {
     *   "status": true,
     *   "response": {
     *       "order_number": 4,
     *       "fio": "John DOE",
     *       "email": "pub@prestashop.com",
     *       "telephone": "0102030405",
     *       "date_add": "2018-01-04 09:10:54",
     *       "total": "89.89",
     *       "status": "Ожидается оплата чеком",
     *       "statuses": [
     *           {
     *               "id_order_state": "1",
     *               "id_lang": "1",
     *               "name": "Ожидается оплата чеком"
     *           },
     *           {
     *               "id_order_state": "2",
     *               "id_lang": "1",
     *               "name": "Платеж принят"
     *           }
     *       ],
     *       "currency_code": "UAH"
     *   },
     *   "version": 1
	 * }
	 *
	 * @apiErrorExample Error-Response:
	 *
	 *     {
	 *       "error" : "Can not found order with id = 5",
	 *       "version": 1.0,
	 *       "Status" : false
	 *     }
	 */
	public function getOrdersInfo(){
		$id = trim( Tools::getValue( 'order_id' ) );

		$this->return['status']  = false;
		if (!empty($id)) {
			$order = new Order($id);

			$data = array();
			$statuses = $this->OrderStatusList();
			$statusArray = [];
			foreach ($statuses as $one):
				$statusArray[$one['id_order_state']] = $one['name'];
			endforeach;

			$idc = $order->id_customer;
			$customer = new Customer($idc);
			$id_address = $order->id_address_delivery;
			$oad = new Address($id_address);

			if ($order) {
				$data['order_number'] = $order->id;

				if (isset($customer->firstname) && isset($customer->lastname)) {
					$data['fio'] = $customer->firstname . ' ' . $customer->lastname;
				}
				if (isset($customer->email)) {
					$data['email'] = $customer->email;
				} else {
					$data['email'] = '';
				}
				if (!empty($oad->phone)) {
					$trim = trim($oad->phone);
					$phone = str_replace(' ','-',$trim);
					$data['telephone'] = $phone;
					/*$data['address1'] = trim($oad->address1);
					$data['city'] = trim($oad->city);*/
				} else {
					$data['telephone'] = '';
				}


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

				$this->return['status']  = true;
				$this->return['response']  = $data;

			} else {

				$this->return['error']  = 'Can not found order with id = ' . $id;
			}
		} else {

			$this->return['error']  = 'You have not specified ID';
		}

		$this->return['version'] = $this->API_VERSION;
		header( 'Content-Type: application/json' );
		die( Tools::jsonEncode( $this->return ) );
	}


	/**
	 * @api {get} /index.php?action=pad&fc=module&module=apimodule&controller=orders  getOrderPaymentAndDelivery
	 * @apiName getOrderPaymentAndDelivery
	 * @apiGroup Orders
	 *
	 * @apiParam {Number} order_id unique order ID.
	 * @apiParam {Token} token your unique token.
	 *
     * @apiSuccess {Number}    version                            Current API version.
     * @apiSuccess {Bool}      status                             Response status.

     * @apiSuccess {Array[]}   response                           Array with content response.
     * @apiSuccess {String}    response.payment_method            Payment method.
     * @apiSuccess {String}    response.shipping_method           Shipping method.
     * @apiSuccess {String}    response.shipping_address          Shipping address.
     * @apiSuccess {String}    response.shipping_phone            Shipping phone.
     * @apiSuccess {String}    response.payment_phone             Payment phone.
     * @apiSuccess {String}    response.payment_address           Payment address.
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK {
     *   "status": true,
     *   "response": {
     *       "shipping_address": "TEst address Dnepr ",
     *       "payment_method": "Payment by check",
     *       "shipping_method": "Доставка завтра!"
     *       "shipping_phone": "0102030405",
     *       "payment_phone": "0102030405",
     *       "payment_address": "TEst address Dnepr ",
     *   },
     *   "version": 1
	 * }
	 * @apiErrorExample Error-Response:
	 *
	 *    {
	 *      "error": "Can not found order with id = 90",
	 *      "version": 1.0,
	 *      "Status" : false
	 *   }
	 *
	 */
	public function getPaymentAndDelivery()
	{
		$id = trim( Tools::getValue( 'order_id' ) );

		$this->return['status']  = false;
		if (!empty($id)) {
			$order = new Order($id);
			$address_delivery = new Address(intval($order->id_address_delivery));
			$address_payment = new Address(intval($order->id_address_invoice));
			$id_carrier = $order->id_carrier;
			$carriers = new Carrier($id_carrier);
			foreach ($carriers->delay as $one):
				$shipping_method = $one;
			endforeach;
			$data = array();
			$statuses = $this->OrderStatusList();
			$statusArray = [];
			foreach ($statuses as $one):
				$statusArray[$one['id_order_state']] = $one['name'];
			endforeach;


			if ($order) {

				$data['shipping_address'] = '';
				$data['payment_address'] = '';
                $data['shipping_phone'] = '';
                $data['payment_phone'] = '';
                $data['payment_method'] = '';
                $data['shipping_phone_mobile'] = '';

				if (!empty($order->payment)) {
					$data['payment_method'] = $order->payment;
				}
				if (!empty($shipping_method)) {
					$data['shipping_method'] = $shipping_method;
				}
				if (!empty($address_delivery->address1)) {
					$data['shipping_address'] .= $address_delivery->address1." ";
				}
				if (!empty($address_delivery->city)) {
					$data['shipping_address'] .= $address_delivery->city." ";
				}
				if (!empty($address_delivery->phone)) {
					$data['shipping_phone'] .= $address_delivery->phone;
				}
				if (!empty($address_delivery->phone_mobile)) {
					$data['shipping_phone_mobile'] .= $address_delivery->phone_mobile;
				}

				if (!empty($address_payment->address1)) {
					$data['payment_address'] .= $address_payment->address1." ";
				}
				if (!empty($address_payment->city)) {
					$data['payment_address'] .= $address_payment->city." ";
				}
				if (!empty($address_payment->phone)) {
					$data['payment_phone'] .= $address_payment->phone;
				}
				if (!empty($address_payment->phone_mobile)) {
					$data['payment_phone_mobile'] .= $address_payment->phone_mobile;
				}

				$this->return['status']  = true;
				$this->return['response']  = $data;

			} else {
				$this->return['error']  = 'Can not found order with id = ' . $id;
					}
		} else {
			$this->return['error']  = 'You have not specified ID';
		}

		$this->return['version'] = $this->API_VERSION;
		header( 'Content-Type: application/json' );
		die( Tools::jsonEncode( $this->return ) );
	}

	/**
	 * @api {get} index.php?action=status_update&fc=module&module=apimodule&controller=orders  statusUpdate
	 * @apiName update Order Status
	 * @apiGroup Orders
	 *
	 * @apiParam {Number} order_id  unique order ID.
	 * @apiParam {Number} status_id unique status ID.
	 * @apiParam {Token} token your unique token.
     *
     * @apiSuccess {Number}    version                            Current API version.
     * @apiSuccess {Bool}      status                             Response status.

     * @apiSuccess {Array[]}   response                           Array with content response.
     * @apiSuccess {String}    response.name                      Status name.
     * @apiSuccess {String}    response.date_add                  Date of change.
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK {
     *  "status": true,
     *   "response": {
     *       "name": "Платеж принят",
     *       "date_add": "2019-01-14 13:21:02"
     *   },
     *   "version": 1
	 * }
	 * @apiErrorExample Error-Response:
	 *
	 *    {
	 *      "error": "Can not found order with id = 90",
	 *      "version": 1.0,
	 *      "Status" : false
	 *   }
	 *
	 */
	public function statusUpdate()
	{
		$statusId = trim( Tools::getValue( 'status_id' ) );
		$orderId = trim( Tools::getValue( 'order_id' ) );
		$inform = trim( Tools::getValue( 'inform' ) );

		$this->return['status']  = false;
		if (!empty($statusId) && !empty($orderId)) {

			$sql = "SELECT id_order_history FROM " . _DB_PREFIX_ . "order_history as oh WHERE oh.id_order = '" . $orderId."'";

			if ($row = Db::getInstance()->getRow($sql)) {

				$insert = Db::getInstance()->insert('order_history', array(
					'id_employee' => 1,
					'id_order'      => $orderId,
					'id_order_state'      => $statusId,
					'date_add'      => date('Y-m:d H:i:s')
				));
				$insert_id = Db::getInstance()->Insert_ID();
				$sql = "UPDATE " . _DB_PREFIX_ . "orders SET current_state = '" . $statusId . "' 
						WHERE id_order = '" . $orderId . "'";

				Db::getInstance()->query( $sql );
				if ( $inform == true ) {
					$sql = "SELECT c.email, c.firstname  FROM " . _DB_PREFIX_ . "customer AS c
				        INNER JOIN " . _DB_PREFIX_ . "orders as o ON c.id_customer = o.id_customer                    
				        WHERE o.id_order = " . $orderId;

					if($d = Db::getInstance()->getRow($sql)) {
						$order = new Order($orderId);
						$history = new OrderHistory($insert_id);
						$templateVars = array();
                        $version = _PS_VERSION_;
                        $arr = explode('.', $version);
                        if (count($arr) > 1) {
                            $subversion = $arr[1];
                            if ($subversion < 6) {
                                $history->id_order = $orderId;
                                $history->addWithemail();
                            } else {
                                $history->sendEmail($order, $templateVars);
                            }
                        } else {
                            $history->sendEmail($order, $templateVars);
                        }
					}

				}
				$sql = "SELECT os.name,oh.date_add FROM " . _DB_PREFIX_ . "order_history as oh
							INNER JOIN " . _DB_PREFIX_ . "order_state_lang as os ON oh.id_order_state = os.id_order_state 
				
				            WHERE os.id_lang = 1 and oh.id_order = '" . $orderId."' ORDER BY oh.date_add DESC";
				$data = [];
				if ($row = Db::getInstance()->getRow($sql)) {
					$data = [
						'name' =>$row['name'],
							'date_add'=>$row['date_add']
				];
				}
				$this->return['response'] = $data;
				$this->return['status']  = true;
			}else{
				$this->return['error']  = "Can not found order with id = ' . $orderId";
			}
			
		}else{
			$this->return['error']  = "You have not specified order Id or status Id";
		}
		
		$this->return['version'] = $this->API_VERSION;
		header( 'Content-Type: application/json' );
		die( Tools::jsonEncode( $this->return ) );

	}

	/**
	 * @api {get} index.php?action=delivery_update&fc=module&module=apimodule&controller=orders  changeOrderDelivery
	 * @apiName update Order Delivery
	 * @apiGroup Orders
	 *
	 * @apiParam {String} address New shipping address.
	 * @apiParam {String} city New shipping city.
	 * @apiParam {Number} order_id unique order ID.
	 * @apiParam {Token} token your unique token.
	 *
	 * @apiSuccess {Number} version  Current API version.
	 * @apiSuccess {Boolean} response Status of change address.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *   {
	 *         "status": true,
	 *         "version": 1.0
	 *    }
	 * @apiErrorExample Error-Response:
	 *
	 *     {
	 *       "error": "Can not change address",
	 *       "version": 1.0,
	 *       "Status" : false
	 *     }
	 *
	 */
	public function changeOrderDelivery()
	{
		$order_id = trim( Tools::getValue( 'order_id' ) );
		$address = trim( Tools::getValue( 'address' ) );
		$city = trim( Tools::getValue( 'city' ) );
		$order = new Order($order_id);

		$sql = "UPDATE " . _DB_PREFIX_ . "address SET address1 = '" . $address . "'";
		if (!empty($city)) {
			$sql .= " , city = '" . $city . "'";
		}
		$sql .= " WHERE id_address = '" . $order->id_address_delivery . "'";

		Db::getInstance()->query( $sql );

		$this->return['status'] = true;
		$this->return['version'] = $this->API_VERSION;
		header( 'Content-Type: application/json' );
		die( Tools::jsonEncode( $this->return ) );
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

		// $sql = "SELECT o.id_order,o.date_add,o.total_paid, oh.id_order_state, c.firstname, c.lastname FROM " . _DB_PREFIX_ . "orders AS o 
		// 			INNER JOIN " . _DB_PREFIX_ . "order_history as oh ON o.id_order=oh.id_order 
		// 			INNER JOIN " . _DB_PREFIX_ . "customer as c ON c.id_customer=o.id_customer  ";
		$sql = "SELECT o.id_order,o.date_add,o.total_paid, oh.id_order_state, c.firstname, c.lastname FROM " . _DB_PREFIX_ . "orders AS o  INNER JOIN " . _DB_PREFIX_ . "order_history as oh ON o.current_state=oh.id_order_state
			INNER JOIN " . _DB_PREFIX_ . "customer as c ON c.id_customer=o.id_customer";
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

				$sql .= " AND ( c.firstname LIKE '%" . $params[0] . "%' OR c.lastname LIKE '%" . $params[0] . "%'";

				foreach ($params as $param) {
					if ($param != $params[0]) {
						$sql .= " OR c.firstname LIKE '%" . $params[0] . "%' 
									OR c.lastname LIKE '%" . $param . "%'";
					};
				}
				$sql .= " ) ";
			}
			if (!empty($data['filter']['min_price']) && !empty($data['filter']['max_price'])
			    && $data['filter']['max_price'] != 0  && $data['filter']['min_price'] != 0) {
				$sql .= " AND o.total_paid > " . $data['filter']['min_price'] . " AND o.total_paid <= " . $data['filter']['max_price'];
			}
			if (!empty($data['filter']['date_min'])) {
				$date_min = date('y-m-d', strtotime($data['filter']['date_min']));
				$sql .= " AND DATE_FORMAT(o.date_add,'%y-%m-%d') > '" . $date_min . "'";
			}
			if (!empty($data['filter']['date_max'])) {
				$date_max = date('y-m-d', strtotime($data['filter']['date_max']));
				$sql .= " AND DATE_FORMAT(o.date_add,'%y-%m-%d') < '" . $date_max . "'";
			}


		} else {
			$sql .= " WHERE oh.id_order_state != 0 ";
		}
		$sql .= " GROUP BY o.id_order ORDER BY o.id_order DESC";
        if (!isset($data['total'])) {
            $sql .= " LIMIT " . (int)$data['limit'] . " OFFSET " . (int)$data['page'];
        }

//echo $sql;
		$results = Db::getInstance()->ExecuteS( $sql );

		return $results;
	}


}
