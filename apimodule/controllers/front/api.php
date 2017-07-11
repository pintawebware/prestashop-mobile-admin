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
class ApimoduleApiModuleFrontController extends ModuleFrontController
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
		$return = ['status'=>false];

		if(isset($_GET['action'])){
			$action = $_GET['action'];
			switch ($action){
				case 'login':$this->login();
			}
		}

		header('Content-Type: application/json');
		die(Tools::jsonEncode($return));
	}

	public function login(){

		$return['status'] = false;

		$passwd = trim(Tools::getValue('passwd'));
		$email = trim(Tools::getValue('email'));
		if (empty($email)) {
			$this->errors[] = Tools::displayError('Email is empty.');
		} elseif (!Validate::isEmail($email)) {
			$this->errors[] = Tools::displayError('Invalid email address.');
		}

		if (empty($passwd)) {
			$this->errors[] = Tools::displayError('The password field is blank.');
		} elseif (!Validate::isPasswd($passwd)) {
			$this->errors[] = Tools::displayError('Invalid password.');
		}

		if (!count($this->errors)) {
			// Find employee
			$this->context->employee = new Employee();
			$is_employee_loaded = $this->context->employee->getByEmail($email, $passwd);
			$employee_associated_shop = $this->context->employee->getAssociatedShops();
			if (!$is_employee_loaded) {
				$this->errors[] = Tools::displayError('The Employee does not exist, or the password provided is incorrect.');
				$this->context->employee->logout();
			} elseif (empty($employee_associated_shop) && !$this->context->employee->isSuperAdmin()) {
				$this->errors[] = Tools::displayError('This employee does not manage the shop anymore (Either the shop has been deleted or permissions have been revoked).');
				$this->context->employee->logout();
			} else {
	//			PrestaShopLogger::addLog(sprintf($this->l('Back Office connection from %s', 'AdminTab', false, false), Tools::getRemoteAddr()), 1, null, '', 0, true, (int)$this->context->employee->id);

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
			$user_id = $this->context->employee->id;
			$device_token = trim(Tools::getValue('device_token'));
			if(!empty($device_token)){
				$os_type = empty(trim(Tools::getValue('os_type')))?'android':trim(Tools::getValue('os_type'));

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
		}

		$return['version'] = $this->API_VERSION;
		$return['response'] = ['token' => $token];
		$return['status'] = true;

		$return['errors'] = $this->errors;
		header('Content-Type: application/json');
		die(Tools::jsonEncode($return));
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
			$this->errors[] = "Error setUserDeviceToken";
		}
	}
	public function getUserToken($user_id){
		$sql = "SELECT * FROM " . _DB_PREFIX_ . "apimodule_user_token
		        WHERE user_id = '".$user_id."'";

		if ($row = Db::getInstance()->getRow($sql)){
			return $row;
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
			$this->errors[] = "Error setUserToken user_id=".$user_id." token = ".$token;
			$this->errors[] = $insert;
		}
	}
}
