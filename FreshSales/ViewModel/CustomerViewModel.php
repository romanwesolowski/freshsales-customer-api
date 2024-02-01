<?php
declare(strict_types=1);

namespace WebIt\FreshSales\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Model\CustomerFactory;

/**
 * Class CustomerViewModel
 */
class CustomerViewModel implements ArgumentInterface
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    public function __construct(
        CustomerFactory $customerFactory
    ) {
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param int|null $id
     * @return string
     */
    public function getCustomerFreshSales(int $id = null): string
    {
        $customer = $this->customerFactory->create()->getCollection()
            ->addFieldToFilter('entity_id', $id)
            ->addAttributeToSelect('fresh_sales')
            ->getFirstItem();

        return $customer->getFreshSales() ?: '';
    }
}
