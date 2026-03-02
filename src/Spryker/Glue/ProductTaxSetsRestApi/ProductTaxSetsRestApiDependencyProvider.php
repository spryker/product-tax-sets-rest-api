<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ProductTaxSetsRestApi;

use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Container;
use Spryker\Glue\ProductTaxSetsRestApi\Dependency\Client\ProductTaxSetsRestApiToTaxProductStorageClientBridge;
use Spryker\Glue\ProductTaxSetsRestApi\Dependency\Client\ProductTaxSetsRestApiToTaxStorageClientBridge;

/**
 * @method \Spryker\Glue\ProductTaxSetsRestApi\ProductTaxSetsRestApiConfig getConfig()
 */
class ProductTaxSetsRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_TAX_PRODUCT_STORAGE = 'CLIENT_TAX_PRODUCT_STORAGE';

    /**
     * @var string
     */
    public const CLIENT_TAX_STORAGE = 'CLIENT_TAX_STORAGE';

    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);
        $container = $this->addTaxProductStorageClient($container);
        $container = $this->addTaxStorageClient($container);

        return $container;
    }

    protected function addTaxProductStorageClient(Container $container): Container
    {
        $container->set(static::CLIENT_TAX_PRODUCT_STORAGE, function (Container $container) {
            return new ProductTaxSetsRestApiToTaxProductStorageClientBridge(
                $container->getLocator()->taxProductStorage()->client(),
            );
        });

        return $container;
    }

    protected function addTaxStorageClient(Container $container): Container
    {
        $container->set(static::CLIENT_TAX_STORAGE, function (Container $container) {
            return new ProductTaxSetsRestApiToTaxStorageClientBridge(
                $container->getLocator()->taxStorage()->client(),
            );
        });

        return $container;
    }
}
