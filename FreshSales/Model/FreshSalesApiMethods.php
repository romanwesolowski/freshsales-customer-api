<?php
declare(strict_types=1);

namespace WebIt\FreshSales\Model;

use Magento\Store\Model\ScopeInterface;
use stdClass;
use WebIt\FreshSales\Logger\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class CreateFreshSalesAccount
 */
class FreshSalesApiMethods
{
    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $appToken;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Logger $logger
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Logger $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->domain = $this->scopeConfig->getValue('fresh_sales/config/domain', ScopeInterface::SCOPE_STORE);
        $this->appToken = $this->scopeConfig->getValue('fresh_sales/config/app_token', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param string $action
     * @param array $message
     * @return bool
     */
    public function post(string $action, array $message): bool
    {
        $url = $this->constructUrl($action);
        $message['application_token'] = $this->appToken;
        $message['sdk'] = 'php';
        $body = json_encode($message);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Content-Length: ' . strlen($body)
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpStatus != 200) {
            $this->logger->info("Freshsales encountered an error. CODE: " . $httpStatus . " Response: " . $response);
        } else {
            return true;
        }

        return false;
    }

    /**
     * @param array $prop
     * @return stdClass
     */
    public function convertArrayToObject(array $prop): stdClass
    {
        $object = new stdClass();
        foreach ($prop as $key => $value) {
            $object->$key = $value;
        }

        return $object;
    }

    /**
     * @param string $action
     * @return string
     */
    private function constructUrl(string $action): string
    {
        return $this->domain . '/track/' . $action;
    }

}
