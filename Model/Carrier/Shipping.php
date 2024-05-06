<?php

declare(strict_types=1);

namespace Bluethinkinc\ShippingPerCustomerGroup\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;
use Bluethinkinc\ShippingPerCustomerGroup\Helper\Data;

class Shipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'btshippingpercustomergroup';
    /**
     * @var bool
     */
    protected $_isFixed = true;
    /**
     * @var $rateResultFactory
     */
    private ResultFactory $rateResultFactory;
    /**
     * @var $rateMethodFactory
     */
    private MethodFactory $rateMethodFactory;
    /**
     * @var $helperData
     */
    protected Data $helperData;
    /**
     * @var $cartsession
     */
    protected $cartsession;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var $serialize
     */
    protected $serialize;

    /**
     * This is shipping constructor
     *
     * @param ScopeConfigInterface                         $scopeConfig
     * @param ErrorFactory                                 $rateErrorFactory
     * @param LoggerInterface                              $logger
     * @param ResultFactory                                $rateResultFactory
     * @param MethodFactory                                $rateMethodFactory
     * @param Data                                         $helperData
     * @param \Magento\Checkout\Model\Cart                 $cartsession
     * @param \Magento\Customer\Model\Session              $customerSession
     * @param \Magento\Framework\Serialize\Serializer\Json $serialize
     * @param array                                        $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Data $helperData,
        \Magento\Checkout\Model\Cart $cartsession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->helperData = $helperData;
        $this->cartsession = $cartsession;
        $this->_customerSession = $customerSession;
        $this->serialize = $serialize;
    }

    /**
     * Custom Shipping Rates Collector
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        
        $isShippingRate=$this->helperData->isShippingRate();
        $defaultcost=$this->helperData->isDefaultShippingCost();
        $CostPerCustomer=$this->helperData->isShippingCostPerCustomer();
        $isShowMethod=$this->helperData->isShowMethod();
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $shippingCost = (float) $this->totalitems(
            $isShippingRate,
            $defaultcost,
            $CostPerCustomer,
            $isShowMethod
        );
        $method->setPrice($shippingCost);
        $method->setCost($shippingCost);

        /**
         * This is result variable
         *
         * @var Result $result
         */
        $result = $this->rateResultFactory->create();
        $result->append($method);

        return $result;
    }

    /**
     * This is getAllowedMethods function
     *
     * @return data
     */
    public function getAllowedMethods(): array
    {
        return [$this->_code => $this->getConfigData('name')];
    }
    /**
     * This is totalitems function
     *
     * @param int $isShippingRate
     * @param int $defaultcost
     * @param string $CostPerCustomer
     * @param int $isShowMethod
     * @return data
     */
    public function totalitems($isShippingRate, $defaultcost, $CostPerCustomer, $isShowMethod)
    {
        $customergropid=$this->getCustomerGroupId();
        if ($isShowMethod==1) {
            $cost=$defaultcost;
        } else {
            $costpercustomerdata=$this->getconfigcustomergroup($CostPerCustomer);
            if ($isShippingRate==1) {
                if ($costpercustomerdata) {
                    foreach ($costpercustomerdata as $key => $value) {
                        if ($key==$customergropid) {
                            $items = $this->cartsession->getItemsQty();
                            $cost=$value*$items;
                            break;
                        } else {
                            $items = $this->cartsession->getItemsQty();
                            $cost=$defaultcost*$items;
                        }
                    }
                } else {
                    $items = $this->cartsession->getItemsQty();
                    $cost=$defaultcost*$items;
                }
            } else {
                if ($costpercustomerdata) {
                    foreach ($costpercustomerdata as $key => $value) {
                        if ($key==$customergropid) {
                            $cost=$value;
                            break;
                        } else {
                            $cost=$defaultcost;
                        }
                    }
                } else {
                    $cost=$defaultcost;
                }
            }
        }
        return $cost;
    }
    /**
     * This is getCustomerGroupId function
     *
     * @param int $storeId
     * @return int
     */
    public function getCustomerGroupId()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
        } else {
            return 0;
        }
    }

    /**
     * This is getconfigcustomergroup function
     *
     * @param string $CostPerCustomer
     * @return array
     */
    public function getconfigcustomergroup($CostPerCustomer)
    {
        $costpercustomerconfig = $CostPerCustomer;

        if ($costpercustomerconfig == '' || $costpercustomerconfig == null) {
            return;
        }

        $unserializedata = $this->serialize->unserialize($costpercustomerconfig);

        $customergroupcostarray = [];
        foreach ($unserializedata as $key => $row) {
            $customergroupcostarray[$row['customer_group']] = $row['cost'];
        }

        return $customergroupcostarray;
    }
}
