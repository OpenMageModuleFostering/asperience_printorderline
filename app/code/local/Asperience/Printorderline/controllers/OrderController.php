<?php
/**
 * @category   ASPerience
 * @package    Asperience_Printorderline
 * @author     ASPerience - www.asperience.fr
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';
class Asperience_Printorderline_OrderController extends Mage_Adminhtml_Sales_OrderController
{
    
	public function pdfordersAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
        	
        	$orders = array();
            foreach ($orderIds as $orderId) {
                $orders[] = Mage::getModel('sales/order')->load($orderId);
            }
            
        	if ($orders) {
                $flag = true;
                $pdf = Mage::getModel('Asperience_Printorderline/order_pdf_order')->getPdf($orders);
            }
            if ($flag) {
                return $this->_prepareDownloadResponse('order'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders'));
                $this->_redirect('*/*/');
            }

        }
        $this->_redirect('*/*/');

    }
    
}