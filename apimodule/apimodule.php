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
		$this->version = '1.0';
		$this->author = 'Pinta';
		$this->need_instance = 0;
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Api Module');
		$this->description = $this->l('Api module for manager shop');
		$this->ps_versions_compliancy = array('min' => '1.0', 'max' => '1.6.99.99');
	}

	/**
	 * @see Module::install()
	 */
	public function install()
	{
		/* Adds Module */
		if (parent::install())
		{

			/* Creates tables */
			$res = $this->createTables();

			return (bool)$res;
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
