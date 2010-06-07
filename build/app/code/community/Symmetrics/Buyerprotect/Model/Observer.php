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
 * Default Modul observer
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_Observer
{
    /**
     * Observer to add the Product to chart
     *
     * @param Varien_Event_Observer $observer current event observer
     * 
     * @todo check if product is alrady in cart
     * @todo check if other buyerprotection Product is alerady in cart
     *
     * @return null
     */
    public function addProductToCart($observer)
    {
        $frontController = Mage::app()->getFrontController();
        $request = $frontController->getRequest();
        Mage::log($frontController->getRequest()->getParams());
        if ($request->getParam('trusted_shops')) {
            /* @var $cart Mage_Checkout_Model_Cart */
            $cart = Mage::getSingleton('checkout/cart')
                ->setStore(Mage::app()->getStore());

            $cartProductIds = $cart->getProductIds();
            Mage::log($cart->getProductIds());
            $requestedProductId = $request->getParam('trusted_shops-product');

            // add Buyerprotection Product to cart
            if (!in_array($cartProductIds, $requestedProductId)) {
                $productIds = array();

                $cart->addProductsByIds($productIds);
                $cart->save();
            }

        }
        
    }
}