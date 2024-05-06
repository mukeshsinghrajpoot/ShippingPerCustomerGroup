<?php

namespace Bluethinkinc\ShippingPerCustomerGroup\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ObjectManager;

class CustomColumn extends Select
{
    /**
     * This is customerGp variable
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    private $customerGp;
    /**
     * This is constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGp
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGp,
        array $data = []
    ) {
        $this->customerGp = $customerGp;
        parent::__construct($context, $data);
    }
     /**
      * This is setInputName function.
      *
      * @param string $value
      * @return data
      */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
    /**
     * This is setInputId function.
     *
     * @param string $value
     * @return data
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }
    /**
     * This is _toHtml function.
     *
     * @return data
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }
    /**
     * This is getSourceOptions function.
     *
     * @return data
     */
    private function getSourceOptions()
    {
        $customerGroups =  $this->customerGp->toOptionArray();
        return $customerGroups;
    }
}
