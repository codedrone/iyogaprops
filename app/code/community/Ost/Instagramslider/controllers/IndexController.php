<?php
class Ost_Instagramslider_IndexController extends Mage_Core_Controller_Front_Action
{
	protected function _getHelper()
    {
        return Mage::helper('instagramslider')->getConfigData();
    }
    
    public function indexAction()
    {
		$dataInsta = $this->_getHelper();
		Mage::register('feedbackOst', $dataInsta);
		$this->loadLayout();
		$this->renderLayout();
    }
    
}

?>
