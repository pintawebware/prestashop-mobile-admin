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
class ApimoduleAuthModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = false;
	public $header = false;
    public $errors ='';
	public $API_VERSION = 2.0;
	public $return = [];
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$this->return['status']=false;

		if(isset($_GET['action'])){
			$action = $_GET['action'];
			switch ($action){
				case 'login':$this->login();
				case 'delete':$this->deleteDeviceToken();
				case 'update':$this->updateDeviceToken();
			}
		}
		$this->errors = Tools::displayError('Email is empty.');
		$this->return['error'] = $this->errors;
		header('Content-Type: application/json');
		die(Tools::jsonEncode($this->return));
	}
	private function valid()
	{
		$token = trim(Tools::getValue('token'));
		if (!empty($token)) {
			$error = 'You need to be logged!';
		} else {
			$results = $this->getTokens($token);
			if ($results) {
				$error = 'Your token is no longer relevant!';
			}
		}
		return $error;
	}

	/**
	 *
	 * @api {post} /index.php?action=login&fc=module&module=apimodule&controller=auth  Login
	 * @apiName Login
	 * @apiGroup Auth
	 *
	 * @apiParam {String} email User unique email.
	 * @apiParam {Number} password User's  password.
	 * @apiParam {String} device_token User's device's token for firebase notifications.
	 * @apiParam {String} os_type android|ios
	 *
     * @apiSuccess {Array[]}   response                           Array with content response.
     * @apiSuccess {Number}    version                            Current API version.
     * @apiSuccess {Bool}      status                             Response status.
     * @apiSuccess {String}    error                              Description error.
     *
     * @apiSuccess {String}    response.token                     Token.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *   {
	 *      "error": "",
     *       "version": 1,
     *       "response": {
     *          "token": "eb3d0b776b33638a5a34b0ed63b882b5"
     *       },
     *       "status": true
	 *   }
	 *
	 * @apiErrorExample Error-Response:
	 *
     *   {
     *      "error": "The password field is blank."
     *   }
	 *
	 */

	public function login(){

		$this->return = false;
		$this->return['status'] = false;
		$this->return['error'] = '';
		$passwd = trim(Tools::getValue('password'));
		$email = trim(Tools::getValue('email'));
		if (empty($email)) {
			$this->errors = Tools::displayError('Email is empty.');
		} elseif (!Validate::isEmail($email)) {
			$this->errors = Tools::displayError('Invalid email address.');
		}

		if (empty($passwd)) {
			$this->errors = Tools::displayError('The password field is blank.');
		} elseif (!Validate::isPasswd($passwd)) {
			$this->errors = Tools::displayError('Invalid password.');
		}


		if (empty($this->errors)) {
			// var_dump($this->errors);
			// Find employee
			$this->context->employee = new Employee();
			$is_employee_loaded = $this->context->employee->getByEmail($email, $passwd);
			$employee_associated_shop = $this->context->employee->getAssociatedShops();
			if (!$is_employee_loaded) {
				$this->errors = Tools::displayError('The Employee does not exist, or the password provided is incorrect.');
				$this->context->employee->logout();
			} elseif (empty($employee_associated_shop) && !$this->context->employee->isSuperAdmin()) {
				$this->errors = Tools::displayError('This employee does not manage the shop anymore (Either the shop has been deleted or permissions have been revoked).');
				$this->context->employee->logout();
			} else {
	
				$this->context->employee->remote_addr = (int)ip2long(Tools::getRemoteAddr());
				// Update cookie
				$cookie = Context::getContext()->cookie;
				$cookie->id_employee = $this->context->employee->id;
				$cookie->email = $this->context->employee->email;
				$cookie->profile = $this->context->employee->id_profile;
				$cookie->passwd = $this->context->employee->passwd;
				$cookie->remote_addr = $this->context->employee->remote_addr;

				if (!Tools::getValue('stay_logged_in')) {
					$cookie->last_activity = time();
				}
				$cookie->write();
			}
			if($this->context->employee->id){
				$user_id = $this->context->employee->id;
				$device_token = trim(Tools::getValue('device_token'));
				if(!empty($device_token)){
					if ( Tools::getValue('os_type') == 'ios' ) {
                        $os_type = 'ios';
                    } else {
                        $os_type = 'android';
                    }

					$udt = $this->getUserDevices($user_id,$device_token);
					if(!$udt){
						$this->setUserDeviceToken($user_id, $device_token,$os_type);
					}
				}

				$token = $this->getUserToken($user_id);
				if (empty($token['token'])) {
					$token = md5(mt_rand());
					$this->setUserToken($user_id, $token);
				}
				$this->return['version'] = $this->API_VERSION;
				$this->return['response'] = ['token' => $token];
				$this->return['status'] = true;
			}else{
				$this->return['error'] = "Invalid email or password";
				$this->return['status'] = false;
			}
		}else{
			$this->return['error'] = $this->errors;
		}

		
		header('Content-Type: application/json');
		die(Tools::jsonEncode($this->return));
	}

	/**
	 * @api {post} /index.php?action=delete&fc=module&module=apimodule&controller=auth  deleteUserDeviceToken
	 * @apiName deleteUserDeviceToken
	 * @apiGroup Auth
	 *
	 * @apiParam {String} old_token User's device's token for firebase notifications.
	 *
	 * @apiSuccess {Number} version  Current API version.
	 * @apiSuccess {Boolean} status  true.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *   {
	 *       "response":
	 *       {
	 *          "status": true,
	 *          "version": 1.0
	 *       }
	 *   }
	 *
	 * @apiErrorExample Error-Response:
	 *
	 *     {
	 *       "error": "Missing some params",
	 *       "version": 1.0,
	 *       "Status" : false
	 *     }
	 *
	 */
	public function deleteDeviceToken(){

		$this->return['status'] = false;

		$old_token = trim(Tools::getValue('old_token'));
		if (!empty($old_token)) {
			$deleted = $this->deleteUserDeviceToken($old_token);
			if($deleted){
				$this->return['status'] = true;
			}else{
				$this->return['error'] ='Can not find your token';
			}
		}else{
			$this->return['error'] ='Missing some params';
		}
		$this->return['version'] =$this->API_VERSION;
		header('Content-Type: application/json');
		die(Tools::jsonEncode($this->return));
	}

	/**
	 * @api {post} /index.php?action=update&fc=module&module=apimodule&controller=auth  updateUserDeviceToken
	 * @apiName updateUserDeviceToken
	 * @apiGroup Auth
	 *
	 * @apiParam {String} new_token User's device's new token for firebase notifications.
	 * @apiParam {String} old_token User's device's old token for firebase notifications.
	 *
	 * @apiSuccess {Number} version  Current API version.
	 * @apiSuccess {Boolean} status  true.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *   {
	 *       "response":
	 *       {
	 *          "status": true,
	 *          "version": 1.0
	 *       }
	 *   }
	 *
	 * @apiErrorExample Error-Response:
	 *
	 *     {
	 *       "error": "Missing some params",
	 *       "version": 1.0,
	 *       "Status" : false
	 *     }
	 *
	 */
	public function updateDeviceToken(){

		$this->return['status'] = false;

		$old_token = trim(Tools::getValue('old_token'));
		$new_token = trim(Tools::getValue('new_token'));
		if (!empty($old_token)) {
			$deleted = $this->updateUserDeviceToken($old_token,$new_token);
			if($deleted){
				$this->return['status'] = true;
			}else{
				$this->return['error'] ='Can not find your token';
			}
		}else{
			$this->return['error'] ='Missing some params';
		}

		$this->return['version'] =$this->API_VERSION;
		header('Content-Type: application/json');
		die(Tools::jsonEncode($this->return));
	}

	public function deleteUserDeviceToken($old_token) {

		$sql = "DELETE FROM `" . _DB_PREFIX_ . "apimodule_user_device` WHERE device_token = '".$old_token."' ";

		if (Db::getInstance()->query($sql)){
			return true;
		}
		else{
			return false;
		}
	}

	public function updateUserDeviceToken($old_token,$new_token) {

		$sql = "UPDATE `" . _DB_PREFIX_ . "apimodule_user_device` SET `device_token`=".$new_token." WHERE device_token = '".$old_token."' ";

		if (Db::getInstance()->query($sql)){
			return true;
		}
		else{
			return false;
		}
	}

	public function getUserDevices($user_id,$device_token) {

		$sql = "SELECT device_token,os_type FROM " . _DB_PREFIX_ . "apimodule_user_device 
		        WHERE user_id = '".$user_id."' and device_token = '".$device_token."'";

		if ($row = Db::getInstance()->getRow($sql)){
			return $row;
		}
		else{
			return false;
		}
	}

	public function setUserDeviceToken($user_id,$token,$os_type){

		$insert = Db::getInstance()->insert('apimodule_user_device', array(
			'user_id' => (int)$user_id,
			'device_token'      => $token,
			'os_type'      => $os_type
		));
		if($insert){
			return true;
		}else{
			$this->errors = "Error setUserDeviceToken";
		}
	}
	public function getUserToken($user_id){
		$sql = "SELECT * FROM " . _DB_PREFIX_ . "apimodule_user_token
		        WHERE user_id = '".$user_id."'";

		if ($row = Db::getInstance()->getRow($sql)){
			return $row['token'];
		}
		else{
			return false;
		}
	}
	public function setUserToken($user_id,$token){
		$insert = Db::getInstance()->insert('apimodule_user_token', array(
			'user_id' => (int)$user_id,
			'token'      => $token
		));

		if($insert){
			return true;
		}else{
			$this->errors = "Error setUserToken user_id=".$user_id." token = ".$token;
		}
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
