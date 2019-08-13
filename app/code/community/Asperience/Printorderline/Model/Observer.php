<?php
/**
 * @category   ASPerience
 * @package    Asperience_DeleteAllOrders
 * @author     ASPerience - www.asperience.fr
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Asperience_Printorderline_Model_Observer
{
    public function addOptionToSelect($observer)
    {

	    if ($observer->getEvent()->getBlock()->getId() == 'sales_order_grid') {
    		$massBlock = $observer->getEvent()->getBlock()->getMassactionBlock();
	        if ($massBlock) {
	            $massBlock->addItem('pdforders_order', array(
		            'label'=> Mage::helper('printorderline')->__('Print order lines'),
				    'url'  =>  Mage::helper('adminhtml')->getUrl('printorderline/order/pdforders', array('_secure'=>true)),
	            ));
			}
		}
    }
}
