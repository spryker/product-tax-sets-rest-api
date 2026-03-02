<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ProductTaxSetsRestApi\Processor\ProductTaxSet;

use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

interface ProductTaxSetReaderInterface
{
    public function getTaxSets(RestRequestInterface $restRequest): RestResponseInterface;

    public function findProductAbstractTaxSetsByProductAbstractSku(
        string $productAbstractSku,
        RestRequestInterface $restRequest
    ): ?RestResourceInterface;
}
