<?php
/**
 * Padoosoft Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0).
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you are unable to obtain it through the world-wide-web, please send
 * an email to support@mage-addons.com so we can send you a copy immediately.
 *
 * @category   Padoo
 * @package    Padoo_Testimonial
 * @author     PadooSoft Team
 * @copyright  Copyright (c) 2010-2012 Padoo Co. (http://mage-addons.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Padoo_Testimonial_Adminhtml_TestimonialController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('padoo_testimonial/testimonial');
	}
	
    /**
     * Init here
     */
	protected function _initAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('padoo_testimonial/testimonial');
		$this->_addBreadcrumb(Mage::helper('padoo_testimonial')->__('Testimonials'), Mage::helper('padoo_testimonial')->__('Testimonials'));
	}

    /**
     * View grid action
     */
	public function indexAction()
	{
		$this->_initAction();
		$this->renderLayout();
	}

    /**
     * View edit form action
     */
	public function editAction()
	{
		$this->_initAction();
		$this->_addContent($this->getLayout()->createBlock('padoo_testimonial/adminhtml_testimonial_edit'));
		$this->renderLayout();
	}

    /**
     * View new form action
     */
	public function newAction()
	{
		$this->editAction();
	}

    /**
     * Save form action
     */
	public function saveAction()
	{
		if ($this->getRequest()->getPost()) {
			try {
				$data = $this->getRequest()->getPost();
				if (isset($_FILES['testimonial_img']['name']) and (file_exists($_FILES['testimonial_img']['tmp_name']))) {
					$uploader = new Varien_File_Uploader('testimonial_img');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','mp4','flv'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media') . DS . 'testimonials';
					$uploader->save($path, $_FILES['testimonial_img']['name']);
					$data['testimonial_img'] = 'testimonials/' . $_FILES['testimonial_img']['name'];
				} else {
					if(isset($data['testimonial_img']['delete']) && $data['testimonial_img']['delete'] == 1) {
						$data['testimonial_img'] = '';
					} else {
						unset($data['testimonial_img']);
					}
				}
				//save store view
				$storeView = $data['stores'];
				$dataStore = "";
				foreach($storeView as $store){
					if($dataStore != "") $dataStore .=",";
					$dataStore .= $store;
				} 
				$data['store_id'] = $dataStore;
				$model = Mage::getModel('padoo_testimonial/testimonial');
				$model->setData($data)->setTestimonialId($this->getRequest()->getParam('id'));
				if ($model->getTestimonialDate == NULL) {
					$model->setTestimonialDate(now());
				}
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('padoo_testimonial')->__('Testimonial was successfully saved'));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}

		$this->_redirect('*/*/');
	}

    /**
     * Delete action
     */
	public function deleteAction()
	{
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$model = Mage::getModel('padoo_testimonial/testimonial');
				$model->setTestimonialId($this->getRequest()->getParam('id'))
				      ->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('padoo_testimonial')->__('Testimonial was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}

		$this->_redirect('*/*/');
	}
	
	public function massDeleteAction() {
		$bannerIds = $this->getRequest()->getParam('padoo_testimonial');
		if (!is_array($bannerIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		} else {
			try {
				foreach ($bannerIds as $bannerId) {
					$model = Mage::getModel('padoo_testimonial/testimonial')->load($bannerId);
					$_helper = Mage::helper('padoo_testimonial');
					$model->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($bannerIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function massStatusAction() {
		$bannerIds =  $this->getRequest()->getParam('padoo_testimonial');
		if (!is_array($bannerIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
		} else {
			try {
				foreach ($bannerIds as $bannerId) {
					$banner = Mage::getSingleton('padoo_testimonial/testimonial')->load($bannerId)->setTestimonialStatus($this->getRequest()->getParam('status'))->setIsMassupdate(true)->save();
				}
				$this->_getSession()->addSuccess(
						$this->__('Total of %d record(s) were successfully updated', count($bannerIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function exportCsvAction() {
		$fileName = 'padoo_testimonial.csv';
		$content = $this->getLayout()->createBlock('padoo_testimonial/adminhtml_padoo_testimonial_grid')->getCsv();
		$this->_sendUploadResponse($fileName, $content);
	}
	public function exportXmlAction() {
		$fileName = 'padoo_testimonial.xml';
		$content = $this->getLayout()->createBlock('padoo_testimonial/adminhtml_padoo_testimonial_grid')->getXml();
		$this->_sendUploadResponse($fileName, $content);
	}
}