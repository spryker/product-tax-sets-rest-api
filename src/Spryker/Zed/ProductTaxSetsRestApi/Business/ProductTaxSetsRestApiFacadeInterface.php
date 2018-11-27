<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductTaxSetsRestApi\Business;

interface ProductTaxSetsRestApiFacadeInterface
{
    /**
     * Specification:
     *  - Updates tax sets without UUID.
     *
     * @api
     *
     * @deprecated Use Spryker\Zed\UtilUuidGenerator\Communication\Console\UuidGeneratorConsole instead.
     *
     * @return void
     */
    public function updateTaxSetsWithoutUuid(): void;
}
