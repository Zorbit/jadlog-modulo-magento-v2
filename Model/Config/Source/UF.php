<?php
namespace Jadlog\Embarcador\Model\Config\Source;
use Magento\Framework\Option\ArrayInterface;

class UF implements ArrayInterface {
  public function toOptionArray() {
    return [
      ['value' => 'AC', 'label' => __('Acre')],
      ['value' => 'AL', 'label' => __('Alagoas')],
      ['value' => 'AP', 'label' => __('Amapá')],
      ['value' => 'AM', 'label' => __('Amazonas')],
      ['value' => 'BA', 'label' => __('Bahia')],
      ['value' => 'CE', 'label' => __('Ceará')],
      ['value' => 'DF', 'label' => __('Distrito Federal')],
      ['value' => 'ES', 'label' => __('Espírito Santo')],
      ['value' => 'GO', 'label' => __('Goiás')],
      ['value' => 'MA', 'label' => __('Maranhão')],
      ['value' => 'MT', 'label' => __('Mato Grosso')],
      ['value' => 'MS', 'label' => __('Mato Grosso do Sul')],
      ['value' => 'MG', 'label' => __('Minas Gerais')],
      ['value' => 'PA', 'label' => __('Pará')],
      ['value' => 'PB', 'label' => __('Paraíba')],
      ['value' => 'PR', 'label' => __('Paraná')],
      ['value' => 'PE', 'label' => __('Pernambuco')],
      ['value' => 'PI', 'label' => __('Piauí')],
      ['value' => 'RJ', 'label' => __('Rio de Janeiro')],
      ['value' => 'RN', 'label' => __('Rio Grande do Norte')],
      ['value' => 'RS', 'label' => __('Rio Grande do Sul')],
      ['value' => 'RO', 'label' => __('Rondônia')],
      ['value' => 'RR', 'label' => __('Roraima')],
      ['value' => 'SC', 'label' => __('Santa Catarina')],
      ['value' => 'SP', 'label' => __('São Paulo')],
      ['value' => 'SE', 'label' => __('Sergipe')],
      ['value' => 'TO', 'label' => __('Tocantins')]
    ];
  }
}
?>