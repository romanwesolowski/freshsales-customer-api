<?php
declare(strict_types=1);

namespace WebIt\FreshSales\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use WebIt\FreshSales\Model\FreshSalesApiMethods;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class CreateFreshSalesAccount
 */
class CreateFreshSalesAccount implements ObserverInterface
{
    /**
     * @var FreshSalesApiMethods
     */
    protected $freshSalesApiMethods;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param FreshSalesApiMethods $freshSalesApiMethods
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        FreshSalesApiMethods $freshSalesApiMethods,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->freshSalesApiMethods = $freshSalesApiMethods;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $customer = $observer->getEvent()->getCustomer();

        $success = $this->addCustomerToFreshSales(
            [
                'identifier' => $customer->getEmail(),
                'First name' => $customer->getFirstname(),
                'Last name' => $customer->getLastname(),
                'Email' => $customer->getEmail()
            ]
        );

        if ($success) {
            $customer = $this->customerRepository->getById($customer->getId());
            $customer->setData('fresh_sales', 'Customer view in FreshSales');
            $this->customerRepository->save($customer);
        }
    }

    /**
     * @param array $properties
     * @return void
     */
    public function addCustomerToFreshSales(array $properties): void
    {
        $message['identifier'] = $properties['identifier'];
        unset($properties['identifier']);
        $message['visitor'] = $this->freshSalesApiMethods->convertArrayToObject($properties);

        $this->freshSalesApiMethods->post('visitors', $message);
    }
}
