<?php
namespace Jadlog\Embarcador\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface {
  public function upgrade( ModuleDataSetupInterface $setup, ModuleContextInterface $context ) {
    /*
    if ( version_compare($context->getVersion(), '0.0.1', '<' )) {
      $data = [
        ['codigo' =>  0, 'descricao' => 'EXPRESSO',      'modal' => 'A', 'ativo' => 0],
        ['codigo' =>  3, 'descricao' => '.PACKAGE',      'modal' => 'R', 'ativo' => 0],
        ['codigo' =>  4, 'descricao' => 'RODOVIÁRIO',    'modal' => 'R', 'ativo' => 0],
        ['codigo' =>  5, 'descricao' => 'ECONÔMICO',     'modal' => 'R', 'ativo' => 0],
        ['codigo' =>  6, 'descricao' => 'DOC',           'modal' => 'R', 'ativo' => 0],
        ['codigo' =>  7, 'descricao' => 'CORPORATE',     'modal' => 'A', 'ativo' => 0],
        ['codigo' =>  9, 'descricao' => '.COM',          'modal' => 'A', 'ativo' => 1],
        ['codigo' => 10, 'descricao' => 'INTERNACIONAL', 'modal' => 'A', 'ativo' => 0],
        ['codigo' => 12, 'descricao' => 'CARGO',         'modal' => 'A', 'ativo' => 0],
        ['codigo' => 14, 'descricao' => 'EMERGENCIAL',   'modal' => 'R', 'ativo' => 0],
        ['codigo' => 40, 'descricao' => 'PICKUP',        'modal' => 'A', 'ativo' => 1]
      ];
      foreach ($data as $bind) {
        $setup->getConnection()->insertForce($setup->getTable('jadlog_modalidades'), $bind);
      }
    }
    */
  }
}
?>