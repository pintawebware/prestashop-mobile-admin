<?php

/**
 * @since   1.0
 */

if (!defined('_PS_VERSION_'))
	exit;

//include_once(_PS_MODULE_DIR_.'apimodule/api.php');

class Apimodule extends Module
{
	protected $_html = '';

	public function __construct()
	{
		$this->name = 'apimodule';
		$this->tab = 'administration';
		$this->version = '2.0';
		$this->author = 'PintaWebWare';
		$this->need_instance = 1;
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Api Module');
		$this->description = $this->l('Api module for manager shop');
		$this->ps_versions_compliancy = array('min' => '1.0', 'max' => '1.8.99.99');
	}


	/**
	  * @see Module::install()
	  */
	 public function install()
	 {
	  /* Adds Module */
	  if (parent::install())
	  {	 
		if($this->createTables() && $this->registerHook('actionValidateOrder')){
			  return true;
		}
	 
	  }

	  return false;
	 }



	/**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
		/* Deletes Module */
		if (parent::uninstall())
		{
			/* Deletes tables */
			$res = $this->deleteTables();

			return (bool)$res;
			return true;
		}

		return false;
	}

	/**
	 * Creates tables
	 */
	protected function createTables()
	{
		/* Slides */
		$sql = '
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'apimodule_user_device` (
				`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`user_id` INT NOT NULL,
				`device_token` VARCHAR(500), 
				`os_type` VARCHAR(20));';
		$res = (bool)Db::getInstance()->execute($sql);

		/* Slides configuration */
		$sql = '
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'apimodule_user_token` (
			    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`user_id` INT NOT NULL,
				`token` VARCHAR(32));';
		$res = Db::getInstance()->execute($sql);

		return true;
	}
	
	public function hookActionValidateOrder($output)
    {
            $this->sendNotifications($output);  
    }
	
	 public function sendNotifications($output)
    {

        $registrationIds = array();
        $sql = "SELECT * FROM `" . _DB_PREFIX_ . "apimodule_user_device`";
        $devices = Db::getInstance()->ExecuteS( $sql );
        $ids = [];

        foreach($devices as $device){
			if(strtolower($device['os_type']) == 'ios'){
				$ids['ios'][] = $device['device_token'];
			}else{
				$ids['android'][] = $device['device_token'];
			}
        }

	    $order = $output['order'];

	    $msg = array(
		    'body'  => number_format($order->total_paid, 2, '.', ''),
		    'title'         => "http://".$_SERVER['HTTP_HOST'],
		    'vibrate'       => 1,
		    'sound'         => 1,
		    'priority'=>'high',
	        'new_order' => [
			    'order_id'=>$order->id,
			    'total'=>number_format($order->total_paid, 2, '.', ''),
			    'currency_code'=>$output['currency']->iso_code,
			    'site_url' => "http://".$_SERVER['HTTP_HOST'],
		    ],
		    'event_type' => 'new_order'
	    );

	    $msg_android = array(

		    'new_order' => [
			    'order_id'=>$order->id,
			    'total'=>number_format($order->total_paid, 2, '.', ''),
			    'currency_code'=>$output['currency']->iso_code,
			    'site_url' => "http://".$_SERVER['HTTP_HOST'],
		    ],
		    'event_type' => 'new_order'
	    );

	    foreach ($ids as $k=>$mas):
		    if($k=='ios'){
			    $fields = array
			    (
				    'registration_ids' => $ids[$k],
				    'notification' => $msg,
			    );
		    }else{
			    $fields = array
			    (
				    'registration_ids' => $ids[$k],
				    'data' => $msg_android
			    );
		    }
	        $this->sendCurl($fields);

		endforeach;
    }

    private function sendCurl($fields){
	    $API_ACCESS_KEY = 'AAAAlhKCZ7w:APA91bFe6-ynbVuP4ll3XBkdjar_qlW5uSwkT5olDc02HlcsEzCyGCIfqxS9JMPj7QeKPxHXAtgjTY89Pv1vlu7sgtNSWzAFdStA22Ph5uRKIjSLs5z98Y-Z2TCBN3gl2RLPDURtcepk';
	    $headers = array
	    (
		    'Authorization: key=' . $API_ACCESS_KEY,
		    'Content-Type: application/json'
	    );

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	    curl_exec($ch);
	    curl_close($ch);
    }
	
	

	/**
	 * deletes tables
	 */
	protected function deleteTables()
	{
		return Db::getInstance()->execute('
			DROP TABLE IF EXISTS `'._DB_PREFIX_.'apimodule_user_device`, `'._DB_PREFIX_.'apimodule_user_token`;
		');
	}
}
