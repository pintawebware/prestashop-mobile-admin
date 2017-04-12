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
class ApimoduleProductsModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;
    public $header = false;
    public $errors =[];
    public $API_VERSION = 1.0;
    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->return['status'] = false;
        if(isset($_GET['action']) && $this->valid()){

            $action = $_GET['action'];
            switch ($action) {
                case 'products':
                    $this->products();
                    break;
                case 'getproductbyid':
                    $this->getProductById();
                    break;
            }
        }
        $this->return['error'] = "No action";
        header( 'Content-Type: application/json' );
        die( Tools::jsonEncode( $this->return ) );
    }

    /**
     * @api {get} index.php?action=products&fc=module&module=apimodule&controller=products  getProductsList
     * @apiName getProductsList
     * @apiGroup All
     *
     * @apiParam {Token} token your unique token.
     * @apiParam {Number} page number of the page.
     * @apiParam {Number} limit limit of the orders for the page.
     * @apiParam {String} name name of the product for search.
     *
     * @apiSuccess {Number} version  Current API version.
     * @apiSuccess {Number} product_id  ID of the product.
     * @apiSuccess {String} model     Model of the product.
     * @apiSuccess {String} name  Name of the product.
     * @apiSuccess {String} currency_code  Default currency of the shop.
     * @apiSuccess {Number} price  Price of the product.
     * @apiSuccess {Number} quantity  Actual quantity of the product.
     * @apiSuccess {Url} image  Url to the product image.
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *   "Response":
     *   {
     *      "products":
     *      {
     *           {
     *             "product_id" : "1",
     *             "model" : "Black",
     *             "name" : "HTC Touch HD",
     *             "price" : "100.00",
     *             "currency_code": "UAH",
     *             "quantity" : "83",
     *             "image" : "http://site-url/image/catalog/demo/htc_touch_hd_1.jpg"
     *           },
     *           {
     *             "product_id" : "2",
     *             "model" : "White",
     *             "name" : "iPhone",
     *             "price" : "300.00",
     *             "currency_code": "UAH",
     *             "quantity" : "30",
     *             "image" : "http://site-url/image/catalog/demo/iphone_1.jpg"
     *           }
     *      }
     *   },
     *   "Status" : true,
     *   "version": 1.0
     * }
     * @apiErrorExample Error-Response:
     * {
     *      "Error" : "Not one product not found",
     *      "version": 1.0,
     *      "Status" : false
     * }
     *
     *
     */

    public function products(){

        $return['status'] = false;
        $page = trim(Tools::getValue('page'));
        $limit = trim(Tools::getValue('limit'));
        $name = trim(Tools::getValue('name'));
        if(!empty($page) && !empty($limit)){
            $page = ($page - 1) * $limit;
            $limit = $_REQUEST['limit'];
        } else {
            $page = 0;
            $limit = 10;
        }
 $to_response = [];
        $id_lang = $this->context->language->id;
        if(empty($name)) {
	        $productObj  = new Product();
	        $products    = $productObj->getProducts( $id_lang, $page, $limit, 'id_product', 'DESC' );
	       
	        if ( count( $products ) > 0 ) {
		        foreach ( $products as $product ) {
			        $data['product_id'] = $product['id_product'];
			        $data['model']      = $product['reference'];
			        $data['quantity']   = Db::getInstance()->getRow( " SELECT p.id_product, sa.quantity FROM ps_product p
	INNER JOIN ps_stock_available sa ON p.id_product = sa.id_product AND id_product_attribute = 0	 
	WHERE p.id_product = " . $product['id_product'] )['quantity'];

			        $idImage = Db::getInstance()->getRow( "SELECT id_image FROM ps_image WHERE cover = 1 AND id_product =  " . $product['id_product'] )['id_image'];
			        $imgPath = '';
			        for ( $i = 0; $i < strlen( $idImage ); $i ++ ) {
				        $imgPath .= $idImage[ $i ] . '/';
			        }
			        $imgPath .= $idImage . '.jpg';
			        $data['image'] = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $imgPath;

			        $data['price'] = number_format( $product['price'], 2, '.', '' );
			        $data['name']  = $product['name'];
			        global $currency;
			        $data['currency_code'] = $currency->iso_code;
			        $to_response[]         = $data;
		        }
	        }
        }else{
	        $products = $this->getProductsList($page, $limit, $name);
	        foreach ( $products as $product ) {
		        $data['product_id'] = $product['id_product'];
		        $data['model']      = $product['reference'];
		        $data['quantity']   = $product['quantity'];
		     
		        $p = new Product($product['id_product']);
		        $image = Image::getCover( $p->id );

		        $imagePath = Link::getImageLink($p->link_rewrite, $image['id_image'], 'home_default');

                $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://'; 
		        $data['image'] = $protocol.$imagePath;

		        $data['price'] = number_format( $product['price'], 2, '.', '' );
		        $data['name']  = $product['name'];
		        global $currency;
		        $data['currency_code'] = $currency->iso_code;
		        $to_response[]         = $data;
	        }
        }
        if(!count($return['errors'])){
            $return['status'] = true;
            $return['response']['products'] = $to_response;
        }
        $return['version'] = $this->API_VERSION;


        header('Content-Type: application/json');
        die(Tools::jsonEncode($return));
    }


    /**
     * @api {get} index.php?action=getproductbyid&fc=module&module=apimodule&controller=products  getProductInfo
     * @apiName getProductInfo
     * @apiGroup All
     *
     * @apiParam {Token} token your unique token.
     * @apiParam {Number} product_id unique product ID.
     *
     * @apiSuccess {Number} version  Current API version.
     * @apiSuccess {Number} product_id  ID of the product.
     * @apiSuccess {String} model     Model of the product.
     * @apiSuccess {String} name  Name of the product.
     * @apiSuccess {Number} price  Price of the product.
     * @apiSuccess {String} currency_code  Default currency of the shop.
     * @apiSuccess {Number} quantity  Actual quantity of the product.
     * @apiSuccess {String} description     Detail description of the product.
     * @apiSuccess {Array} images  Array of the images of the product.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *   "Response":
     *   {
     *       "product_id" : "1",
     *       "model" : "Black",
     *       "name" : "HTC Touch HD",
     *       "price" : "100.00",
     *       "currency_code": "UAH"
     *       "quantity" : "83",
     *       "main_image" : "http://site-url/image/catalog/demo/htc_iPhone_1.jpg",
     *       "description" : "Revolutionary multi-touch interface.↵	iPod touch features the same multi-touch screen technology as iPhone.",
     *       "images" :
     *       [
     *           "http://site-url/image/catalog/demo/htc_iPhone_1.jpg",
     *           "http://site-url/image/catalog/demo/htc_iPhone_2.jpg",
     *           "http://site-url/image/catalog/demo/htc_iPhone_3.jpg"
     *       ]
     *   },
     *   "Status" : true,
     *   "version": 1.0
     * }
     * @apiErrorExample Error-Response:
     * {
     *      "Error" : "Can not found product with id = 10",
     *      "version": 1.0,
     *      "Status" : false
     * }
     *
     *
     */


    public function getProductById() {

        $return['status'] = false;
        $product_id = trim(Tools::getValue('product_id'));

        $id_lang = $this->context->language->id;
        $product = new Product($product_id,false, $id_lang);

        if ($product->id !== null) {
                $data['images'] = [];
                $data['product_id'] = (int)$product->id;
                $data['model'] = $product->reference;
                 $data['description'] = strip_tags($product->description);
                $data['quantity'] =  Db::getInstance()->getRow(" SELECT p.id_product, sa.quantity FROM ps_product p
 
INNER JOIN ps_stock_available sa ON p.id_product = sa.id_product AND id_product_attribute = 0
 
WHERE p.id_product = ".$product->id)['quantity'];
            $images = $product->getImages();
            if(count($images) > 0){
                foreach ($images as $image) {
                    $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://'; 
                        $image = Link::getImageLink($product->link_rewrite, $image['id_image'], 'home_default');
                    $data['images'][] = $protocol.$image;
                }
            }

                $data['price'] = number_format($product->price, 2, '.', '');
                $data['name'] = $product->name;
                global $currency;
                $data['currency_code'] = $currency->iso_code;


        }else{
            $return['error'] = 'Can non fid product with id = '.$product_id;
        }

        if(!count($return['error'])){
            $return['status'] = true;
            $return['response'] = $data;
        }
        $return['version'] = $this->API_VERSION;


        header('Content-Type: application/json');
        die(Tools::jsonEncode($return));
    }

	public function getProductsList ($page, $limit, $name = '')
	{
		$sql = "SELECT p.id_product, p.reference, p.quantity,  p.price, pl.name
					FROM " . _DB_PREFIX_ . "product AS p 
					LEFT JOIN " . _DB_PREFIX_ . "product_lang pl ON p.id_product = pl.id_product 
					WHERE pl.id_lang = 1 " ;
		if($name != ''){
			$sql .= " AND (pl.name LIKE '%" .$name. "%' OR p.reference LIKE '%" .$name. "%')";
		}
		$sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$page;

		$results = Db::getInstance()->ExecuteS( $sql );

		return $results;
	}

    private function valid() {
        $token = trim( Tools::getValue( 'token' ) );
        if ( empty( $token ) ) {
            $this->errors[] = 'You need to be logged!';
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
}
