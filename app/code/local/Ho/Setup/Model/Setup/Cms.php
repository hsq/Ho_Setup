<?php
class Ho_Setup_Model_Setup_Cms extends Ho_Setup_Model_Setup_Abstract
{
    /**
     * Setup Pages, Blocks and especially Footer Block
     *
     * @return void
     */
    public function setup()
    {
        // execute pages
        foreach ($this->_getConfigPages() as $name => $data) {
            if ($data['execute'] == 1) {
                $this->_createCmsPage($data, false);
            }
        }

        // execute blocks
        foreach ($this->_getConfigBlocks() as $name => $data) {
            if ($data['execute'] == 1) {
                if ($name == 'gs_footerlinks') {
                    $this->_updateFooterLinksBlock($data);
                } else {
                    $this->_createCmsBlock($data, false);
                }
            }
        }
    }

    /**
     * Get pages/default from config file
     *
     * @return array
     */
    protected function _getConfigPages()
    {
        return $this->_getConfigNode('pages', 'default');
    }

    /**
     * Get blocks/default from config file
     *
     * @return array
     */
    protected function _getConfigBlocks()
    {
        return $this->_getConfigNode('blocks', 'default');
    }

    /**
     * Get footer_links/default from config file
     *
     * @return array
     */
    protected function _getFooterLinks()
    {
        return $this->_getConfigNode('footer_links', 'default');
    }

    /**
     * Collect data and create CMS page
     *
     * @param array   $pageData cms page data
     * @param boolean $override override cms page if it exists
     *
     * @return void
     */
    protected function _createCmsPage($pageData, $override=true)
    {
        if (!is_array($pageData)) {
            return null;
        }

        $page = Mage::getModel('cms/page')->load($pageData['identifier']);

        $pageData = array(
            'page_id' => $page->getId(),
            'title' => $pageData['title'],
            'identifier' => $pageData['identifier'],
            'content' => $this->getTemplateContent($pageData['text']),
            'root_template' => $pageData['root_template'],
            'stores' => $page->getStoreId() ? $page->getStoreId() : array('0'),
            'is_active' => 1,
        );

        if (!(int) $page->getId() || $override) {
            $page->setData($pageData)->save();
        }
    }

    /**
     * Collect data and create CMS block
     *
     * @param array   $blockData cms block data
     * @param boolean $override  override cms block if it exists
     *
     * @return void
     */
    protected function _createCmsBlock($blockData, $override=true)
    {
        $block = Mage::getModel('cms/block')->load($blockData['identifier']);
        $blockData['content'] = $this->getTemplateContent($blockData['text']);
        if (!$block->getId() || $override) {
            $blockData['stores'] = array('0');
            $blockData['is_active'] = '1';
            $blockData['block_id'] = $block->getId();

            $block->setData($blockData)->save();
        }
    }

    /**
     * Generate footer_links block from config data
     *
     * @return string
     */
    protected function _createFooterLinksContent()
    {
        $footerLinksHtml = '<ul>';
        $footerLinksCounter = 0;

        foreach ($this->_getFooterLinks() as $data) {
            $footerLinksCounter++;
            $title = $data['title'];
            $target = $data['target'];
            $class = '';
            if ($footerLinksCounter == count($this->_getFooterLinks())) {
                $class = 'last';
            }
            $footerLinksHtml .= '<li class="'.$class.'">';
            $footerLinksHtml .= '<a href="{{store url="' . $target . '"}}">' . $title . '</a></li>';
        }

        $footerLinksHtml .= '</ul>';

        return $footerLinksHtml;
    }

    /**
     * Update footer_links cms block
     *
     * @param  array $blockData cms block data
     * @return void
     */
    protected function _updateFooterLinksBlock($blockData)
    {
        /** @var $block Mage_Cms_Model_Block */
        $block = Mage::getModel('cms/block')->load($blockData['identifier']);

        if ($block->getId()) {

            /** @var $backupBlock Mage_Cms_Model_Block */
            $backupBlock = Mage::getModel('cms/block')->load($blockData['identifier'] . '_backup');
            if (!$backupBlock->getId()) {

                // create copy of original block
                $data = array();
                $data['block_id'] = $block->getId();
                $data['identifier'] = $blockData['identifier'] . '_backup';

                $block->setData($data)->save();

                /** @var $block Mage_Cms_Model_Block */
                $block = Mage::getModel('cms/block');
            }
        }

        $data = array(
            'title' => $blockData['title'],
            'identifier' => $blockData['identifier'],
            'content' => $this->_createFooterLinksContent(),
            'stores' => array('0'),
            'is_active' => '1',
        );

        $block->addData($data)->save();
    }
}
