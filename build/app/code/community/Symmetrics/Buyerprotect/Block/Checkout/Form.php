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
 * @copyright 2010 Symmetrics Gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Block class to manage the functionality of the Buyerprotection form in payment section
 * of the checkout
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Block_Checkout_Form extends Mage_Core_Block_Template
{

    /**
     * Check if show Trusted Shops - Buyerprotection form in checkout
     * 
     * @todo implement functionality
     *
     * @return boolean
     */
    public function showForm()
    {
        return true;
    }

    /**
     * Get Product collection of all products with type buyerprotect
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function getAllTsProducts()
    {
        $buyerprotectionModel = Mage::getModel('buyerprotect/buyerprotection');
        /* @var $buyerprotectionModel Symmetrics_Buyerprotect_Model_Buyerprotection */

        $productCollection = $buyerprotectionModel->getAllTsProducts();
        return $productCollection;
    }

}
