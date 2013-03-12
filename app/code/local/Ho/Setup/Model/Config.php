<?php

class Ho_Setup_Model_Config extends Varien_Simplexml_Config
{
    const CACHE_ID  = 'ho_setup_config';
    const CACHE_TAG = 'ho_setup_config';


    /**
     * Sets cache ID and cache tags and loads configuration
     *
     * @param  string|Varien_Simplexml_Element $sourceData
     *
     * @return \Ho_Setup_Model_Config
     */
    public function __construct($sourceData=null)
    {
        $this->setCacheId(self::CACHE_ID);
        $this->setCacheTags(array(self::CACHE_TAG));
        parent::__construct($sourceData);
        $this->_loadConfig();
    }


    /**
     * Merge default config with config from additional xml files
     *
     * @return Ho_Setup_Model_Config
     */
    protected function _loadConfig()
    {
        if (Mage::app()->useCache(self::CACHE_ID)) {
            if ($this->loadCache()) {
                return $this;
            }
        }

        $mergeConfig = Mage::getModel('core/config_base');
        $config = Mage::getConfig();

        // Load additional config files
        $this->_addConfigFile('cms.xml', $mergeConfig);
        $this->_addConfigFile('email.xml', $mergeConfig);
        $this->_addConfigFile('systemconfig.xml', $mergeConfig);
        $this->_addConfigFile('tax.xml', $mergeConfig);

        $this->setXml($config->getNode());

        if (Mage::app()->useCache(self::CACHE_ID)) {
            $this->saveCache();
        }

        return $this;
    }

    /**
     * @param $fileName
     * @param $mergeConfig
     */
    protected function _addConfigFile($fileName, $mergeConfig)
    {
        $country = Mage::app()->getRequest()->getParam('country');

        $config = Mage::getConfig();
        $configFile = $config->getModuleDir('etc', 'Ho_Setup') . DS . $country . DS . $fileName;
        if (!file_exists($configFile)) {
            $configFile = $config->getModuleDir('etc', 'Ho_Setup') . DS . 'default' . DS . $fileName;
        }
        if (file_exists($configFile)) {
            if ($mergeConfig->loadFile($configFile)) {
                $config->extend($mergeConfig, true);
            }
        }
    }
}
