<?php
class StockRegistryInterface implements \Magento\CatalogInventory\Api\StockRegistryInterface
{
    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * Inventory constructor.
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        StockRegistryInterface $stockRegistry
    )
    {
        $this->stockRegistry = $stockRegistry;
        parent::__construct();
    }

    /**
     * @param string $productSku
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * 
     * API Call: http://mymagento.com/rest/V1/products/{product-sku}/stockItems/{stack value}
     * assuming merchant_qty attribute already created in magento admins
     */
    public function updateStockItemBySku($productSku, \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $entityManager  = \Magento\Framework\EntityManager::getInstance();

        $productFactory = $objectManager->create('\Magento\Catalog\Model\ProductFactory');
        $product = $productFactory->create();
        $product->load($product->getIdBySku($productSku));

        if(!empty($product->getData('sku')))
        {
            $productStockItem = $this->stockRegistry->getStockItemBySku($productSku);
            $product->setMerchantQty($productStockItem);
            $entityManager->save($product);
            
            $productStockItem->setQty($stockItem);
            $this->stockRegistry->updateStockItemBySku($productSku, $productStockItem);
        }
        
     }
}
