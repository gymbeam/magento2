<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\Newsletter\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Newsletter\Model\Config;
use Magento\Framework\App\ObjectManager;

/**
 * Class PredispatchNewsletterObserver
 */
class PredispatchNewsletterObserver implements ObserverInterface
{
    /**
     * @deprecated
     * use Magento\Newsletter\Model\Config::XML_PATH_NEWSLETTER_ACTIVE instead
     */
    const XML_PATH_NEWSLETTER_ACTIVE = 'newsletter/general/active';

    /**
     * @var Newsletter Config
     */
    private $config;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * PredispatchNewsletterObserver constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $url
     * @param Config|null $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlInterface $url,
        Config $config = null
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
        $this->config = $config ?: ObjectManager::getInstance()->get(Config::class);
    }

    /**
     * Redirect newsletter routes to 404 when newsletter module is disabled.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer) : void
    {
        if (!$this->config->isActive(ScopeInterface::SCOPE_STORE)) {
            $defaultNoRouteUrl = $this->scopeConfig->getValue(
                'web/default/no_route',
                ScopeInterface::SCOPE_STORE
            );
            $redirectUrl = $this->url->getUrl($defaultNoRouteUrl);
            $observer->getControllerAction()
                ->getResponse()
                ->setRedirect($redirectUrl);
        }
    }
}
