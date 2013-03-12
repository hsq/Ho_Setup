<?php
class Ho_Setup_Model_Setup_Agreements extends Ho_Setup_Model_Setup_Abstract
{
    /**
     * Setup Checkout Agreements
     *
     * @return void
     */
    public function setup()
    {
        // Build and create agreements
        $agreements = array(
            array(
                'name' => 'AGB',
                'content' => '{{block type="cms/block" block_id="gs_business_terms"}}',
                'checkbox_text'
                    => 'Ich habe die Allgemeinen Geschäftsbedingungen gelesen und stimme diesen ausdrücklich zu.',
                'is_active' => '1',
                'is_html' => '1',
                'stores' => array('0')
            ),
            array(
                'name' => 'Widerrufsbelehrung',
                'content' => '{{block type="cms/block" block_id="gs_revocation"}}',
                'checkbox_text' => 'Ich habe die Widerrufsbelehrung gelesen.',
                'is_active' => '1',
                'is_html' => '1',
                'stores' => array('0')
            )
        );
        foreach ($agreements as $agreement) {
            $model = Mage::getModel('checkout/agreement');
            $model = $this->_loadExistingModel($model, 'name', $agreement['name']);
            $model->addData($agreement);
            $model->save();
        }

        // Set config value to true
        $setup = Mage::getModel('eav/entity_setup', 'core_setup');
        $setup->setConfigData('checkout/options/enable_agreements', '1');
    }
}
