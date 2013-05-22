<?php
class Ho_Setup_Model_Setup_Systemconfig extends Ho_Setup_Model_Setup_Abstract
{
    /**
     * Setup Tax setting
     *
     * @return void
     */
    public function setup()
    {
        // modify config data
        $this->_updateConfigData();
    }

    /**
     * Update configuration settings
     *
     * @return void
     */
    protected function _updateConfigData()
    {
        $setup = $this->_getSetup();
        foreach ($this->_getConfigSystemConfig() as $key => $value) {
            $setup->setConfigData(str_replace('__', '/', $key), $value);
        }
    }

    /**
     * Get tax calculations from config file
     *
     * @return array
     */
    protected function _getConfigSystemConfig()
    {
        return $this->_getConfigNode('system_config', 'default');
    }
}
