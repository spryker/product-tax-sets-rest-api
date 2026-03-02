<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ProductTaxSetsRestApi\Dependency\Client;

use Generated\Shared\Transfer\TaxSetStorageTransfer;

interface ProductTaxSetsRestApiToTaxStorageClientInterface
{
    public function findTaxSetStorageByIdTaxSet(int $idTaxSet): ?TaxSetStorageTransfer;
}
