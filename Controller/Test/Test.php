<?php
namespace Jadlog\Embarcador\Controller\Test;

//http://magento.dev.local/jadlog_embarcador/test/test
class Test extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	protected $_helperData;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Jadlog\Embarcador\Helper\Data $helperData,
		\Magento\Framework\View\Result\PageFactory $pageFactory
	) {
		$this->_pageFactory = $pageFactory;
		$this->_helperData = $helperData;
		return parent::__construct($context);
	}

	public function execute() {
		echo "<pre>";
		var_dump($this->_helperData->getConfig());
		echo "\n";
		echo "Modalidades selecionadas: ";
		var_dump($this->_helperData->getModalidades());
		echo "\n";
		echo "Pickup Habilitado: ";
		var_dump($this->_helperData->getPickupHabilitado());
		echo "\n";
		echo "Expresso Habilitado: ";
		var_dump($this->_helperData->getExpressoHabilitado());
		echo "</pre>";
		exit;
	}
}

