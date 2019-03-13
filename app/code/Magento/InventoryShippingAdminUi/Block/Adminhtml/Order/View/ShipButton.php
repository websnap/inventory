<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryShippingAdminUi\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\InventoryShippingAdminUi\Model\IsOrderStockManageable;
use Magento\InventoryShippingAdminUi\Model\IsWebsiteInMultiSourceMode;

/**
 * Update order_ship button to redirect to Source Selection page
 *
 * @api
 */
class ShipButton extends Container
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var IsWebsiteInMultiSourceMode
     */
    private $isWebsiteInMultiSourceMode;

    /**
     * @var IsOrderStockManageable
     */
    private $isOrderStockManageable;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param IsWebsiteInMultiSourceMode $isWebsiteInMultiSourceMode
     * @param array $data
     * @param IsOrderStockManageable $isOrderStockManageable
     */
    public function __construct(
        Context $context,
        Registry $registry,
        IsWebsiteInMultiSourceMode $isWebsiteInMultiSourceMode,
        array $data = [],
        IsOrderStockManageable $isOrderStockManageable = null
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->isWebsiteInMultiSourceMode = $isWebsiteInMultiSourceMode;
        $this->isOrderStockManageable = $isOrderStockManageable ??
            ObjectManager::getInstance()->get(IsOrderStockManageable::class);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $order = $this->registry->registry('current_order');
        $websiteId = (int)$order->getStore()->getWebsiteId();
        if ($this->isWebsiteInMultiSourceMode->execute($websiteId) && $this->isOrderStockManageable->execute($order)) {
            $this->buttonList->update(
                'order_ship',
                'onclick',
                'setLocation(\'' . $this->getSourceSelectionUrl() . '\')'
            );
        }
        return $this;
    }

    /**
     * Source Selection URL getter
     *
     * @return string
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     */
    public function getSourceSelectionUrl()
    {
        return $this->getUrl(
            'inventoryshipping/SourceSelection/index',
            [
                'order_id' => $this->getRequest()->getParam('order_id')
            ]
        );
    }
}
