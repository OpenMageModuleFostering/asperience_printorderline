<?php
/**
 * @category   ASPerience
 * @package    Asperience_Printorderline
 * @author     ASPerience - www.asperience.fr
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Asperience_Printorderline_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

	protected function _prepareMassaction()
    {
    	parent::_prepareMassaction();
        $this->getMassactionBlock()->addItem('pdforders_order', array(
             'label'=> Mage::helper('printorderline')->__('Print order lines'),
             'url'  => $this->getUrl('*/*/pdforders'),
        ));
        return $this;
    }
}
?>
