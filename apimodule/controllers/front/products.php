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
                case 'setquantity':
                    $this->setQuantity();
                    break;
                case 'updateproduct':
                    $this->updateProduct();
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
                $data['status'] = $product->condition;
                $data['subtract_stock'] = $product->available_now;
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

    /**
     * @api {post} index.php?action=setquantity&fc=module&module=apimodule&controller=products  setQuantity
     * @apiName setQuantity
     * @apiGroup All
     *
     * @apiParam {Token} token your unique token.
     * @apiParam {Number} product_id unique product ID.
     * @apiParam {Number} quantity Actual quantity of the product.
     *
     * @apiSuccess {Number} version  Current API version.
     * @apiSuccess {Number} product_id  ID of the product.
     * @apiSuccess {Number} quantity  Actual quantity of the product.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *   "Response":
     *   {
     *       "product_id" : "1",
     *       "quantity" : "100500",
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
    public function setQuantity()
    {
        $return['status'] = false;
        $productId  = trim(Tools::getValue('product_id'));
        $quantity  = trim(Tools::getValue('quantity'));
//        $id_lang = $this->context->language->id;

        $product = new Product($productId, false);

        if ($product->id !== null) {
//            StockAvailable::setQuantity($product->id, null, (int)$quantity);
            $result = Db::getInstance()->update('stock_available', [
                'quantity' => (int)$quantity
            ],
            'id_product = '.(int)$productId
            );

            $return['status'] = true;
            $return['response']['product_id'] = $productId;
            $return['response']['quantity'] = $quantity;
        } else {
            $return['error'] = 'Could not find product with id = ' . $productId;
        }
        $return['version'] = $this->API_VERSION;

        header('Content-Type: application/json');
        die(Tools::jsonEncode($return));
    }

    /**
     * @api {get} index.php?action=updateproduct&fc=module&module=apimodule&controller=products  updateProduct
     * @apiName updateProduct
     * @apiGroup All
     *
     * @apiParam {Token} token your unique token.
     * @apiParam {Number} product_id unique product ID.
     * @apiParam {Number} product_id  ID of the product.
     * @apiParam {String} model     Model of the product.
     * @apiParam {String} name  Name of the product.
     * @apiParam {Number} quantity  Actual quantity of the product.
     * @apiParam {String} description     Detail description of the product.
     * @apiParam {String} description_short     Short description of the product.
     * @apiParam {Array} images  Array of the images of the product.
     *
     * @apiSuccess {Number} version  Current API version.
     * @apiSuccess {Number} product_id  ID of the product.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *   "Response":
     *   {
     *       "product_id" : "1",
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

    public function updateProduct()
    {
        $return['status'] = false;
        $productId = trim(Tools::getValue('product_id'));
        $quantity = trim(Tools::getValue('quantity'));
        $name = trim(Tools::getValue('name'));
        $desc = trim(Tools::getValue('description'));
        $descShort = trim(Tools::getValue('description_short'));
        $reference = trim(Tools::getValue('model'));
        $images = Tools::getValue('images');

        $id_lang = $this->context->language->id;

        $product = new Product($productId, false, $id_lang);
        if ($product->id !== null) {
            if (count($images) >= 0) {
//                if (isset($images['new']))
//                    $newImages = $images['new'];
                if (isset($images['remove']))
                    $toRemove = $images['remove'];
                if (isset($images['main']))
                    $mainImage = $images['main'];

                $productImages = Image::getImages($id_lang, $productId);
                if ($toRemove) {
                    foreach ($productImages as $item) {
                        $id = $item['id_image'];
                        if (in_array($id, $toRemove)) {
                            $image = new Image($id);
                            $image->delete();
//                            $image->save();
                        }
                    }
                }

                if ($mainImage) {
                    $cover =  Image::getCover($productId);
                    if ($cover) {
                        $image = new Image($cover['id_image']);
                        $image->cover = null;
                        $image->save();
                    }
                    $product->setCoverWs($mainImage);
                    $res = $product->save();


                    $image = new Image($mainImage);
                    $image->cover = 1;
                    $res = $image->save();
                }
                if (isset($_FILES)) {
                    $files = $_FILES;
                    foreach ($files as $file) {
                        $path = 'upload/' . $file['name'];
                        $imageUrl = $file['tmp_name'];
                        $type = exif_imagetype($imageUrl);
                        $validTypes = [1, 2, 3];
                        if (!in_array($type, $validTypes)) {
//                            $return['error'] = "Image " . $file['name'] . " format not recognized, allowed formats are: .gif, .jpg, .png";
                            break;
                        }
                        $image = new Image();
                        $image->id_product = $productId;
                        $image->position = Image::getHighestPosition($product->id) + 1;
                        if (($image->validateFields(false, true)) === true && ($image->validateFieldsLang(false, true)) === true && $image->add())
                        {

                            $copy = self::copyImg($product->id, $image->id, $imageUrl, 'products', true);
                            if (!$copy)
                            {
                                $image->delete();
                            }
                        }

                    }
                }
            }

            $product->reference = $reference;
//            $product->quantity = (int)$quantity;
//            StockAvailable::setQuantity($product->id, null, (int)$quantity);
            Db::getInstance()->update('stock_available', [
                'quantity' => (int)$quantity
            ],
            'id_product = '.(int)$productId
            );
            $product->name = $name;
            $product->description = $desc;
            $product->description_short = $descShort;
            $product->save();

            $return['version'] = $this->API_VERSION;
            $return['status'] = true;
            $return['response']['product_id'] = $productId;
        }
        header('Content-Type: application/json');
        die(Tools::jsonEncode($return));
    }

    /**
     * copyImg copy an image located in $url and save it in a path
     * according to $entity->$id_entity .
     * $id_image is used if we need to add a watermark
     *
     * @param int $id_entity id of product or category (set in entity)
     * @param int $id_image (default null) id of the image if watermark enabled.
     * @param string $url path or url to use
     * @param string $entity 'products' or 'categories'
     * @param bool $regenerate
     * @return bool
     */
    protected static function copyImg($id_entity, $id_image = null, $url, $entity = 'products', $regenerate = true)
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

        switch ($entity) {
            default:
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_.(int)$id_entity;
                break;
            case 'manufacturers':
                $path = _PS_MANU_IMG_DIR_.(int)$id_entity;
                break;
            case 'suppliers':
                $path = _PS_SUPP_IMG_DIR_.(int)$id_entity;
                break;
        }

        $url = urldecode(trim($url));
        $parced_url = parse_url($url);

        if (isset($parced_url['path'])) {
            $uri = ltrim($parced_url['path'], '/');
            $parts = explode('/', $uri);
            foreach ($parts as &$part) {
                $part = rawurlencode($part);
            }
            unset($part);
            $parced_url['path'] = '/'.implode('/', $parts);
        }

        if (isset($parced_url['query'])) {
            $query_parts = array();
            parse_str($parced_url['query'], $query_parts);
            $parced_url['query'] = http_build_query($query_parts);
        }

        if (!function_exists('http_build_url')) {
            require_once(_PS_TOOL_DIR_.'http_build_url/http_build_url.php');
        }

        $url = http_build_url('', $parced_url);

        $orig_tmpfile = $tmpfile;

        if (Tools::copy($url, $tmpfile)) {
            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmpfile)) {
                @unlink($tmpfile);
                return false;
            }

            $tgt_width = $tgt_height = 0;
            $src_width = $src_height = 0;
            $error = 0;
            ImageManager::resize($tmpfile, $path.'.jpg', null, null, 'jpg', false, $error, $tgt_width, $tgt_height, 5,
                $src_width, $src_height);
            $images_types = ImageType::getImagesTypes($entity, true);

            if ($regenerate) {
                $previous_path = null;
                $path_infos = array();
                $path_infos[] = array($tgt_width, $tgt_height, $path.'.jpg');
                foreach ($images_types as $image_type) {
                    $tmpfile = self::get_best_path($image_type['width'], $image_type['height'], $path_infos);

                    if (ImageManager::resize($tmpfile, $path.'-'.stripslashes($image_type['name']).'.jpg', $image_type['width'],
                        $image_type['height'], 'jpg', false, $error, $tgt_width, $tgt_height, 5,
                        $src_width, $src_height)) {
                        // the last image should not be added in the candidate list if it's bigger than the original image
                        if ($tgt_width <= $src_width && $tgt_height <= $src_height) {
                            $path_infos[] = array($tgt_width, $tgt_height, $path.'-'.stripslashes($image_type['name']).'.jpg');
                        }
                        if ($entity == 'products') {
                            if (is_file(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'.jpg');
                            }
                            if (is_file(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'_'.(int)Context::getContext()->shop->id.'.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'_'.(int)Context::getContext()->shop->id.'.jpg');
                            }
                        }
                    }
                    if (in_array($image_type['id_image_type'], $watermark_types)) {
                        Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
                    }
                }
            }
        } else {
            @unlink($orig_tmpfile);
            return false;
        }
        unlink($orig_tmpfile);
        return true;
    }

    private static function get_best_path($tgt_width, $tgt_height, $path_infos)
    {
        $path_infos = array_reverse($path_infos);
        $path = '';
        foreach ($path_infos as $path_info) {
            list($width, $height, $path) = $path_info;
            if ($width >= $tgt_width && $height >= $tgt_height) {
                return $path;
            }
        }
        return $path;
    }

}
