<?php
namespace Jadlog\Embarcador\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface {
  public function uninstall( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
    $setup->startSetup();
    $connection = $setup->getConnection();
    $connection->dropTable($connection->getTableName('jadlog_modalidades'));
    $connection->dropTable($connection->getTableName('jadlog_quote'));
    $connection->dropTable($connection->getTableName('jadlog_sales_order'));
    $setup->endSetup();
  }
}
?>
