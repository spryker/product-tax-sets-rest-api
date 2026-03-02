<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ProductTaxSetsRestApi;

use Spryker\Glue\Kernel\AbstractFactory;
use Spryker\Glue\ProductTaxSetsRestApi\Dependency\Client\ProductTaxSetsRestApiToTaxProductStorageClientInterface;
use Spryker\Glue\ProductTaxSetsRestApi\Dependency\Client\ProductTaxSetsRestApiToTaxStorageClientInterface;
use Spryker\Glue\ProductTaxSetsRestApi\Processor\Expander\ProductTaxSetRelationshipExpander;
use Spryker\Glue\ProductTaxSetsRestApi\Processor\Expander\ProductTaxSetRelationshipExpanderInterface;
use Spryker\Glue\ProductTaxSetsRestApi\Processor\Mapper\ProductTaxSetResourceMapper;
use Spryker\Glue\ProductTaxSetsRestApi\Processor\Mapper\ProductTaxSetResourceMapperInterface;
use Spryker\Glue\ProductTaxSetsRestApi\Processor\ProductTaxSet\ProductTaxSetReader;
use Spryker\Glue\ProductTaxSetsRestApi\Processor\ProductTaxSet\ProductTaxSetReaderInterface;

class ProductTaxSetsRestApiFactory extends AbstractFactory
{
    public function createProductTaxSetReader(): ProductTaxSetReaderInterface
    {
        return new ProductTaxSetReader(
            $this->getTaxProductStorageClient(),
            $this->getTaxStorageClient(),
            $this->getResourceBuilder(),
            $this->createProductTaxSetResourceMapper(),
        );
    }

    public function createProductTaxSetRelationshipExpander(): ProductTaxSetRelationshipExpanderInterface
    {
        return new ProductTaxSetRelationshipExpander($this->createProductTaxSetReader());
    }

    public function createProductTaxSetResourceMapper(): ProductTaxSetResourceMapperInterface
    {
        return new ProductTaxSetResourceMapper();
    }

    public function getTaxProductStorageClient(): ProductTaxSetsRestApiToTaxProductStorageClientInterface
    {
        return $this->getProvidedDependency(ProductTaxSetsRestApiDependencyProvider::CLIENT_TAX_PRODUCT_STORAGE);
    }

    public function getTaxStorageClient(): ProductTaxSetsRestApiToTaxStorageClientInterface
    {
        return $this->getProvidedDependency(ProductTaxSetsRestApiDependencyProvider::CLIENT_TAX_STORAGE);
    }
}
