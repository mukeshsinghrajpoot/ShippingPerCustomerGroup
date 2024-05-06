<?php
namespace Bluethinkinc\ShippingPerCustomerGroup\Helper;

use Magento\Customer\Model\Customer;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\AuthorizationInterface;
use Psr\Log\LoggerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var logger
     */
    protected $logger;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param AuthorizationInterface $authorization
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        AuthorizationInterface $authorization,
        LoggerInterface $logger
    ) {
        $this->storeManager   = $storeManager;
        $this->_authorization = $authorization;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * This is a getConfig function
     *
     * @param  string $key
     * @param  null|int $store
     * @return null|string
     */
    public function getConfig($key, $store = null)
    {
        $store  = $this->storeManager->getStore($store);
        $result = $this->scopeConfig->getValue(
            'carriers/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }
    /**
     * This is isAllowed function
     *
     * @return boolean
     */
    public function isAllowed()
    {
        return $this->isActive() && $this->_authorization->isAllowed('Bluethinkinc_ShippingPerCustomerGroup::allow');
    }

    /**
     * This is isActive function
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->getConfig('btshippingpercustomergroup/active');
    }
    /**
     * This is isTitle function
     *
     * @return boolean
     */
    public function isTitle()
    {
        return $this->getConfig('btshippingpercustomergroup/title');
    }
    /**
     * This is isMethodName function
     *
     * @return boolean
     */
    public function isMethodName()
    {
        return $this->getConfig('btshippingpercustomergroup/name');
    }
    /**
     * This is isDefaultShippingCost function
     *
     * @return boolean
     */
    public function isDefaultShippingCost()
    {
        return $this->getConfig('btshippingpercustomergroup/shipping_cost');
    }
    /**
     * This is isShippingRate function
     *
     * @return boolean
     */
    public function isShippingRate()
    {
        return $this->getConfig('btshippingpercustomergroup/shipping_rate');
    }
    /**
     * This is isShippingCostPerCustomer function
     *
     * @return boolean
     */
    public function isShippingCostPerCustomer()
    {
        return $this->getConfig('btshippingpercustomergroup/shipping_cost_per_customer');
    }
    /**
     * This is isAllowspecific function
     *
     * @return boolean
     */
    public function isAllowspecific()
    {
        return $this->getConfig('btshippingpercustomergroup/sallowspecific');
    }
    /**
     * This is isSpecificcountry function
     *
     * @return boolean
     */
    public function isSpecificcountry()
    {
        return $this->getConfig('btshippingpercustomergroup/specificcountry');
    }
    /**
     * This is isShowMethod function
     *
     * @return boolean
     */
    public function isShowMethod()
    {
        return $this->getConfig('btshippingpercustomergroup/showmethod');
    }
    /**
     * This is isSortOrder function
     *
     * @return boolean
     */
    public function isSortOrder()
    {
        return $this->getConfig('btshippingpercustomergroup/sort_order');
    }
}
