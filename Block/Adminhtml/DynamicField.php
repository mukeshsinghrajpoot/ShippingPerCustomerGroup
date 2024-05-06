<?php

namespace Bluethinkinc\ShippingPerCustomerGroup\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Bluethinkinc\ShippingPerCustomerGroup\Block\Adminhtml\Form\Field\CustomColumn;

class DynamicField extends AbstractFieldArray
{
    /**
     * This is dropdownRenderer variable
     *
     * @var $dropdownRenderer
     */
    private $dropdownRenderer;
     /**
      * This is _prepareToRender function.
      */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'customer_group',
            [
                'label' => __('Customer Group'),
                'renderer' => $this->getDropdownRenderer(),
            ]
        );
        $this->addColumn(
            'cost',
            [
                'label' => __('Cost'),
                'class' => 'required-entry',
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * This is _prepareArrayRow function.
     *
     * @param array $row
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];
        $dropdownField = $row->getDropdownField();
        if ($dropdownField !== null) {
            $options['option_' . $this->getDropdownRenderer()->calcOptionHash($dropdownField)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }

     /**
      * This is getDropdownRenderer function.
      *
      * @return data
      */
    private function getDropdownRenderer()
    {
        if (!$this->dropdownRenderer) {
            $this->dropdownRenderer = $this->getLayout()->createBlock(
                CustomColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->dropdownRenderer;
    }
}
