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
    public $API_VERSION = 2.0;
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
                case 'mainimage':
                    $this->mainImage();
                    break;
                case 'deleteimage':
                    $this->deleteImage();
                    break;
                case 'getcategories':
                    $this->getCategoriesList();
                    break;
                case 'createproduct':
                    $this->createProduct();
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
     * @apiSuccess {String} vendor_code     Vendor code of the product.
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
     *             "vendor_code" : "12423",
     *             "name" : "HTC Touch HD",
     *             "price" : "100.00",
     *             "currency_code": "UAH",
     *             "quantity" : "83",
     *             "image" : "http://site-url/image/catalog/demo/htc_touch_hd_1.jpg"
     *           },
     *           {
     *             "product_id" : "2",
     *             "vendor_code" : "45632",
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
        $return['errors'] = [];
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
                    $data['vendor_code']      = $product['reference'];
                    $data['quantity']   = Db::getInstance()->getRow( "SELECT p.id_product, sa.quantity FROM 
                        "._DB_PREFIX_."product p INNER JOIN "._DB_PREFIX_."stock_available sa ON p.id_product = sa.id_product AND id_product_attribute = 0 WHERE p.id_product = " . $product['id_product'] )['quantity'];

                    $idImage = Db::getInstance()->getRow( "SELECT id_image FROM ps_image WHERE cover = 1 AND id_product =  " . $product['id_product'] )['id_image'];
                    $imgPath = '';
                    for ( $i = 0; $i < strlen( $idImage ); $i ++ ) {
                        $imgPath .= $idImage[ $i ] . '/';
                    }
                    if ($idImage) {
                        $imgPath .= $idImage . '.jpg';
                        $data['image'] = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $imgPath;
                    } else {
                        $data['image'] = '';
                    }

                    $data['price'] = number_format( $product['price'], 2, '.', '' );
                    $data['name']  = $product['name'];
                    $category = new Category((int)$product['id_category_default'], (int)$this->context->language->id);
                    if ($category->name) {
                        $data['category'] = $category->name;
                    } else {
                        $categories = $this->getCategoriesByProduct($product['id_product']);
                        if (!empty($categories)) {
                            $data['category'] = $categories[0]['name'];
                        } else {
                            $data['category'] = '';
                        }
                    }

                    global $currency;
                    $data['currency_code'] = $currency->iso_code;
                    $to_response[]         = $data;
                }
            }
        }else{
            $products = $this->getProductsList($page, $limit, $name);
            foreach ( $products as $product ) {
                $data['product_id'] = $product['id_product'];
                $data['vendor_code']      = $product['reference'];
                $data['quantity']   = $product['quantity'];

                $p = new Product($product['id_product']);
                $image = Image::getCover( $p->id );

                $linkObj = new Link();
                $imagePath = $linkObj->getImageLink($p->link_rewrite, $image['id_image'], 'home_default');

                $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
                $data['image'] = $protocol.$imagePath;

                $data['price'] = number_format( $product['price'], 2, '.', '' );
                $data['name']  = $product['name'];
                $category = new Category((int)$product['id_category_default'], (int)$this->context->language->id);
                $data['category'] = $category->name;

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
     * @apiSuccess {String} vendor_code     Vendor code of the product.
     * @apiSuccess {Boolean} status_name      Status of the product.
     * @apiSuccess {String} categories   Product categories
     * @apiSuccess {String} name  Name of the product.
     * @apiSuccess {Number} price  Price of the product.
     * @apiSuccess {String} currency_code  Default currency of the shop.
     * @apiSuccess {Number} quantity  Actual quantity of the product.
     * @apiSuccess {String} description     Detail description of the product.
     * @apiSuccess {Array} images  Array of the images of the product.
     *
     * @apiSuccess {Array[]}   options                   Array of the product options.
     * @apiSuccess {String}    options.option_id         Option id.
     * @apiSuccess {String}    options.option_name       Option name.
     * @apiSuccess {String}    options.option_value_id   Option value id.
     * @apiSuccess {String}    options.option_value_name Option value name.
     * @apiSuccess {String}    options.language_id       Language id of options and option values.
     *

     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *   "Response":
     *   {
     *       "product_id" : "1",
     *       "vendor_code" : "Black",
     *       "name" : "HTC Touch HD",
     *       "price" : "100.00",
     *       "status_name" : "Enabled",
     *       "categories" : [
     *              {
     *                  "id_category":"7",
     *                   "name":"Blouses"
     *              },
     *              {
     *                   "id_category":"5",
     *                   "name":"T-shirts"
     *              }
     *         ]
     *       "currency_code": "UAH"
     *       "quantity" : "83",
     *       "main_image" : "http://site-url/image/catalog/demo/htc_iPhone_1.jpg",
     *       "description" : "Revolutionary multi-touch interface.↵ iPod touch features the same multi-touch screen technology as iPhone.",
     *       "images" :
     *       [
     *           "http://site-url/image/catalog/demo/htc_iPhone_1.jpg",
     *           "http://site-url/image/catalog/demo/htc_iPhone_2.jpg",
     *           "http://site-url/image/catalog/demo/htc_iPhone_3.jpg"
     *       ]
     *       "options": [ 
     *                      {
     *                          "option_id": "11",
     *                          "option_value_id": "46",
     *                          "option_value_name": "Small",
     *                          "option_name": "Size"
     *                      },
     *                      {
     *                          "option_id": "11",
     *                          "option_value_id": "47",
     *                          "option_value_name": "Medium",
     *                          "option_name": "Size"
     *                      },
     *                      {
     *                          "option_id": "11",
     *                          "option_value_id": "48",
     *                          "option_value_name": "Large",
     *                          "option_name": "Size"
     *                      }
     *                  ]
     *                  
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
        $return['error'] = [];
        $product_id = trim(Tools::getValue('product_id'));

        $id_lang = $this->context->language->id;
        $product = new Product($product_id,false, $id_lang);

        if ($product->id !== null) {
            $data['images'] = [];
            $data['product_id'] = strval($product->id);
            $data['vendor_code'] = $product->reference;
//                $data['status'] = $product->condition;
            $data['status_name'] = ($product->active) ? "Enabled" : "Disabled";
//                $category = new Category((int)$product->id_category_default, (int)$this->context->language->id);
            $categories = $this->getCategoriesByProduct($product_id);
//                $data['categories'] = $category->name;
            $data['categories'] = $categories;
            $options = $this->getOptionsByProduct($product_id);
            $data['options'] = $options;
//                $data['subtract_stock'] = $product->available_now;
            $data['description'] = $product->description ? $product->description : "";
            $data['quantity'] =  Db::getInstance()->getRow(" SELECT p.id_product, sa.quantity FROM "._DB_PREFIX_."product p
 
INNER JOIN "._DB_PREFIX_."stock_available sa ON p.id_product = sa.id_product AND id_product_attribute = 0
 
WHERE p.id_product = ".$product->id)['quantity'];
            $images = $product->getImages($id_lang);
            if(count($images) > 0){
                foreach ($images as $image) {
                    $tmp = [];
                    $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
                    $linkObj = new Link();
                    $link = $linkObj->getImageLink($product->link_rewrite, $image['id_image'], 'home_default');
                    $tmp['image'] = $protocol.$link;
                    if ($image['cover']) {
                        $tmp['image_id'] = -1;
                    } else {
                        $tmp['image_id'] = $image['id_image'];
                    }
                    if ($image['cover']) {
                        array_unshift($data['images'], $tmp);
                    } else {
                        $data['images'][] = $tmp;
                    }
                }
                $cover =  Image::getCover($product_id);
                if (!$cover) {
                    $tmp = [];
                    $tmp['image_id'] = -1;
                    $tmp['image'] = '';
                    array_unshift($data['images'], $tmp);
                }
            } else {
                $tmp = [];
                $tmp['image_id'] = -1;
                $tmp['image'] = "";
                $data['images'][] = $tmp;
            }

            $data['price'] = number_format($product->price, 2, '.', '');
            $data['name'] = $product->name;
            global $currency;
            $data['currency_code'] = $currency->iso_code;


        }else{
            $return['error'] = 'Can non fid product with id = '.$product_id;
        }

        if(!count($return['error'])){
            unset($return['error']);
            $return['status'] = true;
            $return['response'] = $data;
        }
        $return['version'] = $this->API_VERSION;


        header('Content-Type: application/json');
        die(Tools::jsonEncode($return));
    }

    private function getCategoriesByProduct($id)
    {
        $product = (int)$id;
        $id_lang = $this->context->language->id;

        $sql = "SELECT c.id_category AS category_id, cl.name 
                    FROM " . _DB_PREFIX_ . "category_product AS cp 
                    INNER JOIN " . _DB_PREFIX_ . "category c ON cp.id_category = c.id_category 
                    INNER JOIN " . _DB_PREFIX_ . "category_lang cl ON c.id_category = cl.id_category 
                    WHERE cp.id_product =  $product 
                    AND id_lang = " . $id_lang .
                    " AND cp.id_category <> 1 
                    AND cp.id_category <> 2" ;
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }

    private function getOptionsByProduct($id)
    {
        $product_id = (int)$id;
        $id_lang = $this->context->language->id;

        $id_product_attribute_query = "SELECT pa.id_product_attribute 
                                       FROM " . _DB_PREFIX_ . "product_attribute pa 
                                       WHERE pa.id_product = $product_id";

        $id_product_attribute_result = Db::getInstance()->ExecuteS($id_product_attribute_query);

        $results = array();

        foreach ($id_product_attribute_result as $row => $field) {

            $id_product_attribute = $field['id_product_attribute'];

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

            $results[] = $id_attribute_result; 
        }

        return $results;
    }

    public function getProductsList ($page, $limit, $name = '')
    {
        $id_lang = $this->context->language->id;

        $sql = "SELECT p.id_product, p.reference, p.quantity,  p.price, pl.name, p.id_category_default 
                    FROM " . _DB_PREFIX_ . "product AS p 
                    LEFT JOIN " . _DB_PREFIX_ . "product_lang pl ON p.id_product = pl.id_product 
                    WHERE pl.id_lang = " . $id_lang ;
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
     * @api {post} index.php?action=updateproduct&fc=module&module=apimodule&controller=products  updateProduct
     * @apiName updateProduct
     * @apiGroup All
     *
     * @apiParam {Token} token your unique token.
     * @apiParam {Number} product_id  ID of the product.
     * @apiParam {String} vendor_code     Vendor code of the product.
     * @apiParam {String} name  Name of the product.
     * @apiParam {Number} quantity  Actual quantity of the product.
     * @apiParam {Number} price  Price of the product.
     * @apiParam {String} description     Detail description of the product.
     * @apiParam {String} description_short     Short description of the product.
     * @apiParam {Number} categories  Array of categories of the product.
     * @apiParam {Number} status  Status of the product.
     * @apiParam {Array} images  Array of the images of the product.
     * @apiParam {Array} options                   Array of the product options.
     * @apiParam {Array} options.option_value_ids  Array of combination of option_value_ids.
     *
     * @apiSuccess {Number} version  Current API version.
     * @apiSuccess {Number} product_id  ID of the product.
     * @apiSuccess {Array} images Product images.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *   "Response":
     *   {
     *       "product_id" : "1",
     *       "images": [
     *               {
     *                   "image": "http://site-url/image/catalog/demo/htc_iPhone_1.jpg",
     *                   "image_id": -1
     *               },
     *               {
     *                   "image": "http://site-url/image/catalog/demo/htc_iPhone_1.jp",
     *                   "image_id": "5"
     *               },
     *               {
     *                   "image": "http://site-url/image/catalog/demo/htc_iPhone_1.jp",
     *                   "image_id": "6"
     *               }
     *          ]
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
        $quantity = $quantity ? $quantity : 0;
        $name = trim(Tools::getValue('name'));
        $price = trim(Tools::getValue('price'));
        $price = $price ? $price : 0;
        $price = floatval($price);
        $desc = trim(Tools::getValue('description'));
        $descShort = trim(Tools::getValue('description_short'));
        $reference = trim(Tools::getValue('vendor_code'));
        $categories = isset($_REQUEST['categories']) ? $_REQUEST['categories'] : null;
        $options = isset($_REQUEST['options']) ? $_REQUEST['options'] : null;
        $status = filter_var(trim(Tools::getValue('status')), FILTER_VALIDATE_BOOLEAN);
        $images = Tools::getValue('images');

        $id_lang = $this->context->language->id;
        if ($productId == 0) {
            $product = new Product(null, false, $id_lang);
        } else {
            $product = new Product($productId, false, $id_lang);
        }

        if ($product->id !== null || $productId == 0) {

//            }
            if ($productId == 0) {
                $languages=Language::getLanguages();
                foreach($languages as $lang){
                    $product->name[$lang['id_lang']] = $name;
                    $product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($name);
                    $product->description[$lang['id_lang']] = $desc;
                    $product->description_short[$lang['id_lang']] = $descShort;
                }
            }
            $product->reference = $reference;
//            $product->quantity = (int)$quantity;
//            StockAvailable::setQuantity($product->id, null, (int)$quantity);
            if ($name) {
                $product->name = $name;
            }
            $product->price = $price;
            if ($desc) {
                $product->description = $desc;
            }
            if ($descShort) {
                $product->description_short = $descShort;
            }


//            $product->id_category_default = $categoryId;
            $product->active = $status;
            try{
                $product->save();
            } catch (PrestaShopException $e){
                $return['error'] = 'Could not save product';
                $return['version'] = $this->API_VERSION;
                $return['status'] = false;
                header('Content-Type: application/json');
                die(Tools::jsonEncode($return));
            }
            $this->updateProductCategories($product->id, $categories);
            $this->updateProductOptions($product->id, $options);
            Db::getInstance()->update('stock_available', [
                'quantity' => (int)$quantity
            ],
                'id_product = '.(int)$product->id
            );

            if ( isset($_FILES['image']) ) {
                $imagesCounter = 0;
                $files = $_FILES['image'];

                foreach ($_FILES['image']['name'] as $key => $name) {
                    $path = 'upload/' . $name;
                    $imageUrl = $_FILES['image']["tmp_name"][$key];
                    $type = exif_imagetype($imageUrl);
                    $validTypes = [1, 2, 3];

                    if (!in_array($type, $validTypes)) {
//                            $return['error'] = "Image " . $file['name'] . " format not recognized, allowed formats are: .gif, .jpg, .png";
                        break;
                    }

                    $image = new Image();
                    $image->id_product = $product->id;
                    $image->position = Image::getHighestPosition($product->id) + 1;

                    if (($image->validateFields(false, true)) === true && ($image->validateFieldsLang(false, true)) === true && $image->add())
                    {
                        $version = _PS_VERSION_;
                        $arr = explode('.', $version);
                        if (count($arr) > 1) {
                            $subversion = $arr[1];
                            if ($subversion < 6) {
                                $copy = self::copyImgOld($product->id, $image->id, $imageUrl, 'products');
                            } else {
                                $copy = self::copyImg($product->id, $image->id, $imageUrl, 'products', true);
                            }
                        } else {
                            $copy = self::copyImg($product->id, $image->id, $imageUrl, 'products', true);
                        }

                        if (!$copy)
                        {
                            $image->delete();
                        }
                    }
                    if (!$productId && !$imagesCounter) {
                        $image->cover = 1;
                        $image->save();
                        $product->setCoverWs($image->id);
                        $product->save();
                        $imagesCounter = 1;
                    }

                }
            }
            $data = [];
            $images = $product->getImages($id_lang);
            $data['images'] = [];
            if(count($images) > 0){
                foreach ($images as $image) {
                    $tmp = [];
                    $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
                    $linkObj = new Link();
                    $link = $linkObj->getImageLink($product->link_rewrite, $image['id_image'], 'home_default');
                    $tmp['image'] = $protocol.$link;
                    if ($image['cover']) {
                        $tmp['image_id'] = -1;
                    } else {
                        $tmp['image_id'] = $image['id_image'];
                    }
                    if ($image['cover']) {
                        array_unshift($data['images'], $tmp);
                    } else {
                        $data['images'][] = $tmp;
                    }
                }
                $cover =  Image::getCover($product->id);
                if (!$cover) {
                    $tmp = [];
                    $tmp['image_id'] = -1;
                    $tmp['image'] = '';
                    array_unshift($data['images'], $tmp);
                }
            } else {
                $tmp = [];
                $tmp['image_id'] = -1;
                $tmp['image'] = "";
                $data['images'][] = $tmp;
            }

            $return['version'] = $this->API_VERSION;
            $return['status'] = true;
            $return['response']['product_id'] = $product->id;
            $return['response']['images'] = $data['images'];
        }
        header('Content-Type: application/json');
        die(Tools::jsonEncode($return));
    }

    public function updateProductCategories($productId, $categories)
    {
        if ($productId) {
            $id = (int) $productId;
            if (is_array($categories)) {

                $delete = "DELETE FROM `ps_category_product` "
                    . "WHERE id_product = $id "
                    . "AND id_category <> 2 "
                    . "AND id_category <> 1";
                $results = Db::getInstance()->execute($delete);
                $pos = Db::getInstance()->ExecuteS("SELECT MAX(position) AS pos from ps_category_product WHERE id_product = $id");
                $pos = $pos[0]['pos'];
                foreach ($categories as $category) {
                    $pos++;
                    $category = (int)$category;
                    $ins = Db::getInstance()->insert('category_product', [
                        'id_category' => $category,
                        'id_product' => $id,
                        'position' => $pos
                    ]);
                }

            }
        }
    }

    public function updateProductOptions($productId, $options)
    {
        if ($productId) {
            $product_id = (int) $productId;

            $delete_query = "DELETE pac, pa, pas
                             FROM   " . _DB_PREFIX_ . "product_attribute pa
                             LEFT JOIN   " . _DB_PREFIX_ . "product_attribute_combination pac 
                               ON   pa.id_product_attribute = pac.id_product_attribute
                             LEFT JOIN   " . _DB_PREFIX_ . "product_attribute_shop pas 
                               ON   pas.id_product_attribute = pac.id_product_attribute
                             WHERE  pa.id_product = $product_id
                               AND  pas.id_product = $product_id";
            $results = Db::getInstance()->execute($delete_query);

            foreach ($options as $attribute_ids) {
                
                $id_product_attribute_query = "INSERT INTO " . _DB_PREFIX_ . "product_attribute (id_product) VALUES ($product_id)";
                $id_product_attribute_result = Db::getInstance()->execute($id_product_attribute_query);
                $product_attribute_id = Db::getInstance()->Insert_ID();

                /*
                 * Record the association of the attribute with the product in the shop table.
                 */ 
                $product_attribute_shop_query = "INSERT INTO ". _DB_PREFIX_ . "product_attribute_shop (
                      id_product,
                      id_product_attribute,
                      id_shop 
                    ) VALUES ( " .
                      "'" . (int)$product_id . "'," .
                      "'" . (int)$product_attribute_id . "'," .
                      "'1')";
                $result = Db::getInstance()->execute($product_attribute_shop_query);


                foreach ($attribute_ids as $attribute_id) {
                  
                    /*
                     * Record the association of the attribute with the product in the database.
                     */ 

                    $product_attribute_combination_query = "INSERT INTO ". _DB_PREFIX_ . "product_attribute_combination (
                          id_attribute,
                          id_product_attribute
                        ) VALUES ( " .
                          "'" . (int)$attribute_id . "'," .
                          "'" . (int)$product_attribute_id . "')";

                    $result = Db::getInstance()->execute($product_attribute_combination_query);
                }
            }
        }
    }

    /**
     * @api {post} index.php?action=mainimage&fc=module&module=apimodule&controller=products  mainImage
     * @apiName mainImage
     * @apiGroup All
     *
     * @apiParam {Token} token your unique token.
     * @apiParam {Number} image_id main image ID.
     *
     * @apiSuccess {Number} version  Current API version.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *   "Status" : true,
     *   "version": 1.0
     * }
     * @apiErrorExample Error-Response:
     * {
     *      "Error" : "Could not found image with id = 10",
     *      "version": 1.0,
     *      "Status" : false
     * }
     *
     *
     */

    public function mainImage()
    {
        $return['status'] = false;
//        $productId = trim(Tools::getValue('product_id'));

        $imageId = trim(Tools::getValue('image_id'));
        if ($imageId) {

            $image = new Image($imageId);
            $productId = $image->id_product;
            if ($productId) {
                $id_lang = $this->context->language->id;
                $product = new Product($productId, false, $id_lang);
            }
            if ($image->id !== null) {
                $cover =  Image::getCover($productId);
                if ($cover) {
                    Image::deleteCover($productId);
//                    $oldImage = new Image($cover['id_image']);
//                    $oldImage->cover = null;
//                    $oldImage->save();
                }
                $image->cover = 1;
                $image->update();
//                $product->setCoverWs($imageId);
//                $product->save();


                $return['status'] = true;
                $return['version'] = $this->API_VERSION;
            } else {
                $return['error'] = 'Could not find image with id = '.$imageId;
            }

        }
        header('Content-Type: application/json');
        die(Tools::jsonEncode($return));
    }

    /**
     * @api {post} index.php?action=deleteimage&fc=module&module=apimodule&controller=products  deleteImage
     * @apiName deleteImage
     * @apiGroup All
     *
     * @apiParam {Token} token your unique token.
     * @apiParam {Number} product_id product ID.
     * @apiParam {Number} image_id image ID.
     *
     * @apiSuccess {Number} version  Current API version.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *   "Status" : true,
     *   "version": 1.0
     * }
     * @apiErrorExample Error-Response:
     * {
     *      "Error" : "Can not found image with id = 10",
     *      "version": 1.0,
     *      "Status" : false
     * }
     *
     *
     */

    public function deleteImage()
    {
        $return['status'] = false;
        $imageId = trim(Tools::getValue('image_id'));
        $image = new Image($imageId);
        if ($image->id !== null) {
            $image->delete();
            $return['status'] = true;
            $return['version'] = $this->API_VERSION;
        } else if ($imageId == -1) {
            $product_id = trim(Tools::getValue('product_id'));
            $cover =  Image::getCover($product_id);
            if ($cover) {
                $oldImage = new Image($cover['id_image']);
                $oldImage->cover = null;
                $oldImage->delete();
                $return['status'] = true;
                $return['version'] = $this->API_VERSION;
            } else {
                $return['error'] = 'Could not delete empty image';
            }
        } else {
            $return['error'] = 'Could not find with image id = ' . $imageId;
        }

        header('Content-Type: application/json');
        die(Tools::jsonEncode($return));
    }

    /**
     * @api {get} index.php?action=getcategories&fc=module&module=apimodule&controller=products  getCategoriesList
     * @apiName getCategoriesList
     * @apiGroup All
     *
     * @apiParam {Token} token your unique token.
     * @apiParam {Number} category_id unique category ID.
     *
     * @apiSuccess {Number} version  Current API version.
     * @apiSuccess {Number} category_id  ID of the category.
     * @apiSuccess {String} name  Name of the category.
     * @apiSuccess {Boolean} parent  Specifies whether this category has child categories.
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *   "Response":
     *   {
     *      "categories":
     *      {
     *          "3" : {
     *             "category_id" : "1",
     *             "name" : "Computers",
     *             "parent" : "true",
     *           },
     *          "4" : {
     *             "category_id" : "1",
     *             "name" : "Notebooks",
     *             "parent" : "false"
     *           }
     *      }
     *   },
     *   "Status" : true,
     *   "version": 1.0
     * }
     * @apiErrorExample Error-Response:
     * {
     *      "Error" : "Not one category not found",
     *      "version": 1.0,
     *      "Status" : false
     * }
     *
     *
     */

    public function getCategoriesList()
    {
        $return['status'] = false;
        $id = trim(Tools::getValue('category_id'));
        if ($id == -1) {
            $results = $this->getAllCategories();
        } else {
            $results = $this->getChildCategories($id);
        }

        if (count($results)) {
            $output = [];
            foreach ($results as $result) {
                if ($result['id_category'] == 1 || $result['id_category'] == 2) continue;
                $tmp = [];
                $tmp['category_id'] = $result['id_category'];
                $tmp['name'] = $result['name'];
//                if ($id == -1)
                $tmp['parent'] = false;
                $output[$tmp['category_id']] = $tmp;
                if (isset($result['child_count']) && $result['child_count']) {
                    $output[$tmp['category_id']]['parent'] = true;
                }
//                if ($id == -1) {
//                    if ($result['id_parent']) {
//                        if ($result['id_parent'] != 1 && $result['id_parent'] != 2) {
//                            $output[$result['id_parent']]['parent'] = true;
//                        }
//                    }
//                }
            }
            $return['status'] = true;
            $return['version'] = $this->API_VERSION;
            $return['response']['categories'] = array_values($output);
        } else {
            $return['error'] = 'No items found';
        }
        header('Content-Type: application/json');
        die(Tools::jsonEncode($return));
    }

    private function getAllCategories()
    {
        $id_lang = $this->context->language->id;
        $sql = "SELECT a.`id_category`, `name`, `description`, 
                sa.`position` AS `position`, `id_parent`, 
                `active` , sa.position position, 
                 (SELECT COUNT(*) as parent FROM `". _DB_PREFIX_ ."category` categ WHERE categ.id_parent = a.id_category) AS child_count
                FROM `". _DB_PREFIX_ ."category` a 
                LEFT JOIN `". _DB_PREFIX_ ."category_lang` b 
                ON (b.`id_category` = a.`id_category` AND b.`id_lang` = ".$id_lang." AND b.`id_shop` = 1) 
                LEFT JOIN `". _DB_PREFIX_ ."category_shop` sa 
                ON (a.`id_category` = sa.`id_category` AND sa.id_shop = 1) 
                WHERE (a.id_parent = 1 OR a.id_parent = 2)
                ORDER BY sa.`position`";

        $results = Db::getInstance()->ExecuteS( $sql );
        return $results;
    }

    private function getChildCategories($category)
    {
        $id_lang = $this->context->language->id;

        $sql = "SELECT a.`id_category`, `name`, `description`, 
                sa.`position` AS `position`, `id_parent`, 
                `active` , sa.position position, 
                (SELECT COUNT(*) as parent FROM `". _DB_PREFIX_ ."category` categ WHERE categ.id_parent = a.id_category) AS child_count
                FROM `". _DB_PREFIX_ ."category` a 
                LEFT JOIN `". _DB_PREFIX_ ."category_lang` b 
                ON (b.`id_category` = a.`id_category` AND b.`id_lang` = ".$id_lang." AND b.`id_shop` = 1) 
                LEFT JOIN `". _DB_PREFIX_ ."category_shop` sa 
                ON (a.`id_category` = sa.`id_category` AND sa.id_shop = 1) 
                WHERE a.`id_parent` = $category 
                ORDER BY sa.`position`";
        $results = Db::getInstance()->ExecuteS( $sql );
        return $results;
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

    /**
     * copyImg copy an image located in $url and save it in a path
     * according to $entity->$id_entity .
     * $id_image is used if we need to add a watermark
     *
     * @param int $id_entity id of product or category (set in entity)
     * @param int $id_image (default null) id of the image if watermark enabled.
     * @param string $url path or url to use
     * @param string entity 'products' or 'categories'
     * @return void
     */
    protected static function copyImgOld($id_entity, $id_image = null, $url, $entity = 'products')
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

        switch ($entity)
        {
            default:
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_.(int)$id_entity;
                break;
        }
        $url = str_replace(' ', '%20', trim($url));

        // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
        if (!ImageManager::checkImageMemoryLimit($url))
            return false;

        // 'file_exists' doesn't work on distant file, and getimagesize make the import slower.
        // Just hide the warning, the traitment will be the same.
        if (@copy($url, $tmpfile))
        {
            ImageManager::resize($tmpfile, $path.'.jpg');
            $images_types = ImageType::getImagesTypes($entity);
            foreach ($images_types as $image_type)
                ImageManager::resize($tmpfile, $path.'-'.stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height']);

            if (in_array($image_type['id_image_type'], $watermark_types))
                Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
        }
        else
        {
            unlink($tmpfile);
            return false;
        }
        unlink($tmpfile);
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
