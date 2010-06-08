<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Model Class to handle buyer protection actions
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_Buyerprotection extends Mage_Core_Model_Abstract
{
    /**
     * Email model
     *
     * @var Mage_Core_Model_Email_Template
     */
    protected $_emailModel = null;
    
    /**
     * Options for Mage_Core_Model_Email_Template used in _sendEmailTransactional()
     *
     * @var Varien_Object
     */
    protected $_emailOptions = null;

    /**
     * Options for Mage_Core_Model_Email_Template
     *
     * @param Varien_Object $options options $_emailModel works with
     *
     * @return void
     */
    protected function _prepareEmail($options)
    {
        if (!($mailTemplate = $this->_emailModel)) {
            /* @var $mailTemplate Mage_Core_Model_Email_Template */
            $mailTemplate = Mage::getModel('core/email_template');

            $this->_emailModel = $mailTemplate;
        }

        $mailTemplate->setDesignConfig(array('area' => 'frontend'));
        $this->_emailOptions = $options;

        return;
    }

    /**
     * Parses email template and send it with
     * Mage_Core_Model_Email_Template::sendTransactional().
     * Resets $this->_emailOptions after send.
     *
     * @return void
     * @throw Exception
     */
    protected function _sendTransactional()
    {
        /* @var $options Varien_Object */
        $options = $this->_emailOptions;
        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = $this->_emailModel;

        if (!$options || !$mailTemplate) {
            throw Mage::exception($this, 'Email options/model is not set!');
        }

        $mailTemplate->sendTransactional(
            $options->getTemplate(),
            $options->getSender(),
            $options->getRecipient(),
            null,
            $options->getPostObject()
        );

        if (!$mailTemplate->getSentSuccess()) {
            throw Mage::exception($this, 'Email couldn\'t get sent!');
        }

        $this->_emailOptions = null;

        return;
    }

    /**
     * Get Product collection of all products with type buyerprotect
     *
     * @todo move this method to a Model class
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function getAllTsProducts()
    {
        /* @var $productCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        $productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToFilter('type_id', array('eq' => 'buyerprotect'))
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('name')
            ->setOrder('price', 'asc');

        $productCollection->load();

        return $productCollection;
    }

    /**
     * Method to send the  TS SOAP data via email if SOAP itself failed. The index
     * of the param array should follow the Varien_Object format!
     *
     * Data keys of param:
     *
     * returnValue: return_value
     * tsId: ts_id
     * tsProductId: ts_product_id
     * amount: amount
     * currency: currency
     * paymentType: payment_type
     * buyerEmail: buyer_email
     * shopCustomerID: shop_customer_id
     * shopOrderID: shop_order_id
     * orderDate: order_date
     * wsUser: ws_user
     * wsPassword: ws_password
     *
     * @param array $tsSoapData data which should be transmitted with SOAP
     *
     * @return void
     */
    public function sendTsEmailOnSoapFail($tsSoapData)
    {
        $storeConfigPaths = array(
            'is_active' => Symmetrics_Buyerprotect_Helper_Data::XML_PATH_TS_BUYERPROTECT_IS_ACTIVE,
            'template' => Symmetrics_Buyerprotect_Helper_Data::XML_PATH_TS_BUYERPROTECT_ERROR_EMAIL_TEMPLATE,
            'sender' => Symmetrics_Buyerprotect_Helper_Data::XML_PATH_TS_BUYERPROTECT_ERROR_EMAIL_SENDER,
            'recipient' => Symmetrics_Buyerprotect_Helper_Data::XML_PATH_TS_BUYERPROTECT_ERROR_EMAIL_RECIPIENT
        );

        // not activated
        if (!Mage::getStoreConfig($storeConfigPaths['is_active'])) {
            return;
        }

        $emailOptions = new Varien_Object();
        $tsSoapDataObject = new Varien_Object();

        $tsSoapDataObject->setData($tsSoapData);

        $options = array(
            'template' => Mage::getStoreConfig($storeConfigPaths['template']),
            'sender' => Mage::getStoreConfig($storeConfigPaths['sender']),
            'recipient' => Mage::getStoreConfig($storeConfigPaths['recipient']),
            'post_object' => array('tsSoapData' => $tsSoapDataObject)
        );
        $emailOptions->setData($options);

        $this->_prepareEmail($emailOptions);
        $this->_sendTransactional();

        return;
    }
}
