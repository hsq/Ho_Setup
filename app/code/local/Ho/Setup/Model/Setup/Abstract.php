<?php

class Ho_Setup_Model_Setup_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * @var Mage_Eav_Model_Entity_Setup
     */
    protected $_setup;

    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    /**
     * Setup setup class and connection
     */
    public function __construct()
    {
        $this->_setup = Mage::getModel('eav/entity_setup', 'core_setup');
        $this->_connection = $this->_setup->getConnection();
    }

    /**
     * Get config.xml data
     *
     * @return array
     */
    public function getConfigData()
    {
        $configData = Mage::getSingleton('ho_setup/config')
            ->getNode('default/ho_setup')
            ->asArray();

        return $configData;
    }

    /**
     * @param string $configPath
     * @param string $value
     */
    public function setConfigData($configPath, $value)
    {
        $setup = $this->_getSetup();
        $setup->setConfigData($configPath, $value);
    }

    /**
     * Get config.xml data
     *
     * @param string      $node      xml node
     * @param string|null $childNode if set, child node of the first node
     *
     * @return array
     */
    protected function _getConfigNode($node, $childNode = null)
    {
        $configData = $this->getConfigData();
        if ($childNode) {
            return $configData[$node][$childNode];
        } else {
            return $configData[$node];
        }
    }

    /**
     * Get template content
     *
     * @param string $filename template file name
     *
     * @return string
     */
    public function getTemplateContent($filename)
    {
        return @file_get_contents(Mage::getBaseDir() . DS . $filename);
    }

    /**
     * Load a model by attribute code
     *
     * @param  Mage_Core_Model_Abstract $model
     * @param  string                   $attributeCode
     * @param  string                   $value
     * @return Mage_Core_Model_Abstract
     */
    protected function _loadExistingModel($model, $attributeCode, $value)
    {
        foreach ($model->getCollection() as $singleModel) {
            if ($singleModel->getData($attributeCode) == $value) {
                $model->load($singleModel->getId());

                return $model;
            }
        }

        return $model;
    }

    /**
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getConnection()
    {
        return $this->_connection;
    }

    /**
     * @return Mage_Eav_Model_Entity_Setup
     */
    protected function _getSetup()
    {
        return $this->_setup;
    }
}
