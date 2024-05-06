<?php

namespace Bluethinkinc\ShippingPerCustomerGroup\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Return ShippingCostPerCustomer
 */
class ShippingCostPerCustomer implements OptionSourceInterface
{
    /**
     * Function toOptionArray
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Per Item(s)')],
            ['value' => '2', 'label' => __('Per Order')]
        ];
    }
}
