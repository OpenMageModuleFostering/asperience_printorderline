<?php
/**
 * @category   ASPerience
 * @package    Asperience_Printorderline
 * @author     ASPerience - www.asperience.fr
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Asperience_Printorderline_Model_Order_Pdf_Order extends Mage_Sales_Model_Order_Pdf_Abstract
{

	public $y = 570;
	
    public function getPdf($orders = array())
    {
    	$this->_beforeGetPdf();
        $this->_initRenderer('order');

        $pdf = new Zend_Pdf();
        $style = new Zend_Pdf_Style();
		
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4_LANDSCAPE);
        
        $pdf->pages[] = $page;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $this->_setFontBold($page);
            
        /* Add table */
        $page->setFillColor(new Zend_Pdf_Color_Html('#D9E5EE'));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);

        /* Add table head */
        $page->drawRectangle(40, $this->y, 800, $this->y -15);
        $this->y -=10;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $page->drawText(Mage::helper('printorderline')->__('Order'), 50, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('printorderline')->__('Bill to informations'), 160, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('printorderline')->__('Ship to informations'), 330, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('printorderline')->__('Shipping and billing methods'), 500, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('printorderline')->__('Totals'), 680, $this->y, 'UTF-8');
        
        
        foreach ($orders as $order) {
            //Data
	        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
	        
	        $lgMaxAdd = 50;
	        $lgMaxMet = 50;
	        
	        //Payment method
	        try {
		        $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
		            ->setIsSecureMode(true)
		            ->toPdf();
	        } catch (Exception $e) {
	        	$paymentInfo = $order->getPayment()->getMethod();
	        }
	        $payment = str_replace('{{pdf_row_separator}}', ' ', $paymentInfo);
	        $payment =  wordwrap($payment, $lgMaxMet, "|\n", 1);
			$payment = explode('|', $payment);
			
			//Shipping description
	        $ShippingDesc = $order->getShippingDescription();
			$ShippingDesc =  wordwrap($ShippingDesc, $lgMaxMet, "|\n", 1);
			$ShippingDesc = explode('|', $ShippingDesc);
			
			//Billing informations
			$billingAddress = $order->getBillingAddress();
			if($billingAddress) {
		        $BillingCpn = $billingAddress->getCompany();
				$BillingCpn =  wordwrap($BillingCpn, $lgMaxAdd, "|\n", 1);
				$BillingCpn = explode('|', $BillingCpn);				
			} else {
		        $BillingCpn = array();
			}
			
			//Shipping informations
			$shippingAddress = $order->getShippingAddress();
			if($shippingAddress) {
				$ShippingCpn = $shippingAddress->getCompany();
				$ShippingCpn =  wordwrap($ShippingCpn, $lgMaxAdd, "|\n", 1);
				$ShippingCpn = explode('|', $ShippingCpn);
			} else {
		        $ShippingCpn = array();
			}
			
			$val = max(count($BillingCpn), count($ShippingCpn), (count($payment) + count($ShippingDesc)));
						
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
	        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
	        $page->setLineWidth(0.5);
	        $page->drawRectangle(40, $this->y-5, 800, $this->y-15-$val*10);
	        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        
			$this->y-=15;
			$this->_setFontBold($page);
	        $page->drawText(Mage::helper('printorderline')->__('N° ').$order->getRealOrderId(), 50, $this->y, 'UTF-8');
	        $this->_setFontRegular($page);
	        if($billingAddress)
	        	$billingAddressName = $billingAddress->getName();
	        else
	        	$billingAddressName = '';
	        $page->drawText($billingAddressName, 160, $this->y, 'UTF-8');
	        if($shippingAddress)
	        	$shippingAddressName = $shippingAddress->getName();
	        else
	        	$shippingAddressName = '';
	        $page->drawText($shippingAddressName, 330, $this->y, 'UTF-8');
	        $page->drawText(Mage::helper('printorderline')->__('Tax: ').$order->formatPriceTxt($order->getTaxAmount()), 680, $this->y, 'UTF-8');
	        $page->drawText(Mage::helper('printorderline')->__('Ship: ').$order->formatPriceTxt($order->getShippingAmount()), 740, $this->y, 'UTF-8');
	        
	        $saveY = $this->y;
         	foreach ($payment as $value){
	            if (trim($value)!=='') {
	                $page->drawText(strip_tags(trim($value)), 500, $saveY, 'UTF-8');
	                $saveY-=10;
	            }
	        }
	        
	        $this->y-=15;
	        $this->_setFontBold($page);
	        $page->drawText(Mage::helper('printorderline')->__('Date: ').Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(), 'medium', false), 50, $this->y, 'UTF-8');
			$this->_setFontRegular($page);
			$page->drawText(Mage::helper('sales')->__('SubT: ').$order->formatPriceTxt($order->getSubtotal()), 680, $this->y, 'UTF-8');
	        $page->drawText(Mage::helper('printorderline')->__('GrandT: ').$order->formatPriceTxt($order->getGrandTotal()), 740, $this->y, 'UTF-8');
	        
	        $saveY = $this->y;
       		foreach ($BillingCpn as $value){
		        if ($value!=='') {
		            $page->drawText(strip_tags($value), 160, $saveY, 'UTF-8');
		            $saveY -=10;
		        }
		    }
		    
		    $saveY = $this->y;
        	foreach ($ShippingCpn as $value){
		        if ($value!=='') {
		            $page->drawText(strip_tags($value), 330, $saveY, 'UTF-8');
		            $saveY -=10;
		        }
		    }
		    $saveY = $this->y;
        	foreach ($ShippingDesc as $value){
		        if ($value!=='') {
		            $page->drawText(strip_tags($value), 500, $saveY, 'UTF-8');
		            $saveY -=10;
		        }
		    }
		    $this->y -= $val*10-20;
			
        	if ($this->y<60) {
                    /* Add new table head */
                    $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4_LANDSCAPE);
                    $pdf->pages[] = $page;
                    $this->y = 570;

                    $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
			        $this->_setFontBold($page);
			            
			        /* Add table */
			        $page->setFillColor(new Zend_Pdf_Color_Html('#D9E5EE'));
			        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
			        $page->setLineWidth(0.5);
			
			        /* Add table head */
			        $page->drawRectangle(40, $this->y, 800, $this->y -15);
			        $this->y -=10;
			        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
			        $page->drawText(Mage::helper('printorderline')->__('Order'), 50, $this->y, 'UTF-8');
			        $page->drawText(Mage::helper('printorderline')->__('Bill to informations'), 160, $this->y, 'UTF-8');
			        $page->drawText(Mage::helper('printorderline')->__('Ship to informations'), 330, $this->y, 'UTF-8');
			        $page->drawText(Mage::helper('printorderline')->__('Shipping and billing methods'), 500, $this->y, 'UTF-8');
			        $page->drawText(Mage::helper('printorderline')->__('Totals'), 680, $this->y, 'UTF-8');
                }
        }

        $this->_afterGetPdf();
		
        return $pdf;
    }
    
}
