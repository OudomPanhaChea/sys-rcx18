<?php defined('BASEPATH') OR exit('No direct script access allowed');
// v5.9
class Migration_update extends CI_Migration {

	public function __construct() {
		parent::__construct();
		$this->load->dbforge();
	}

	public function up() {  

		$this->db->query("CREATE TABLE `broadcast` (`id` INT NOT NULL AUTO_INCREMENT , `from_id` INT NOT NULL , `to_whom` TEXT NOT NULL , `subject` TEXT NOT NULL , `msg` TEXT NOT NULL , `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`))");
		$this->db->query("ALTER TABLE `transactions` ADD `tax` TEXT NOT NULL AFTER `amount`");
		$this->db->query("ALTER TABLE `orders` ADD `amount` TEXT NOT NULL AFTER `plan_id`, ADD `amount_with_tax` TEXT NOT NULL AFTER `amount`, ADD `tax` TEXT NOT NULL AFTER `amount_with_tax`");
		$this->db->query("UPDATE orders o INNER JOIN transactions t ON o.transaction_id = t.id SET o.amount = t.amount, o.amount_with_tax = t.amount");
		$this->db->query("ALTER TABLE `taxes` ADD `tax_type` TEXT NOT NULL DEFAULT 'invoice' AFTER `tax`");
		$this->db->query("ALTER TABLE `plans` ADD `hidden` INT NOT NULL DEFAULT '0' AFTER `status`");
		$this->db->query("INSERT INTO `settings` (`id`, `type`, `value`, `created`) VALUES (NULL, 'maintenance_mode', '{\"maintenance_mode\":0}', current_timestamp());");
		
		$this->db->set('value', '5.9');
        $this->db->where('type', 'system_version');
        $this->db->update('settings');

	}

	public function down() {
	}
}
