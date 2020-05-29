<?php
class ModelExtensionTmdcurrencyprice extends Model {
	public function install() {
$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."product_currency_optionvalue` (
  `product_id` int(11) NOT NULL,
  `product_option_id` int(11) NOT NULL,
  `product_option_value_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `option_value_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `price` decimal(15,4) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."product_currency_price` (
  `product_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `price` decimal(15,4) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."special_currency_price` (
  `product_id` int(11) NOT NULL,
  `product_special_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `price` decimal(10,4) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."discount_currency_price` (
  `product_id` int(11) NOT NULL,
  `product_discount_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `price` decimal(10,4) NOT NULL DEFAULT '0.00'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
	}
	public function uninstall() {
	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."product_currency_optionvalue`");
	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."product_currency_price`");
	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."special_currency_price`");
	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."discount_currency_price`");
	}
}
