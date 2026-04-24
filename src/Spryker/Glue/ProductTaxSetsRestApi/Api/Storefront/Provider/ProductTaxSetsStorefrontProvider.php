<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\ProductTaxSetsRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\ProductTaxSetsStorefrontResource;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\TaxProductStorage\TaxProductStorageClientInterface;
use Spryker\Client\TaxStorage\TaxStorageClientInterface;
use Spryker\Glue\ProductTaxSetsRestApi\ProductTaxSetsRestApiConfig;
use Symfony\Component\HttpFoundation\Response;

class ProductTaxSetsStorefrontProvider extends AbstractStorefrontProvider
{
    protected const string URI_VAR_SKU = 'abstractProductSku';

    public function __construct(
        protected TaxProductStorageClientInterface $taxProductStorageClient,
        protected TaxStorageClientInterface $taxStorageClient,
    ) {
    }

    /**
     * @return array<\Generated\Api\Storefront\ProductTaxSetsStorefrontResource>
     */
    protected function provideCollection(): array
    {
        $sku = $this->resolveAbstractProductSku();

        $taxProductStorage = $this->taxProductStorageClient->findTaxProductStorageByProductAbstractSku($sku);

        if ($taxProductStorage === null) {
            $this->throwTaxSetsNotFound();
        }

        $taxSetStorage = $this->taxStorageClient->findTaxSetStorageByIdTaxSet($taxProductStorage->getIdTaxSet());

        if ($taxSetStorage === null) {
            $this->throwTaxSetsNotFound();
        }

        $taxRates = [];
        foreach ($taxSetStorage->getTaxRates() as $taxRate) {
            $taxRates[] = [
                'name' => $taxRate->getName(),
                'rate' => (string)$taxRate->getRate(),
                'country' => $taxRate->getCountry(),
            ];
        }

        $resource = new ProductTaxSetsStorefrontResource();
        $resource->uuid = $taxSetStorage->getUuid();
        $resource->name = $taxSetStorage->getName();
        $resource->restTaxRates = $taxRates;

        return [$resource];
    }

    protected function resolveAbstractProductSku(): string
    {
        if (!$this->hasUriVariable(static::URI_VAR_SKU)) {
            $this->throwAbstractProductNotFound();
        }

        $sku = (string)$this->getUriVariable(static::URI_VAR_SKU);

        if ($sku === '') {
            $this->throwAbstractProductNotFound();
        }

        return $sku;
    }

    protected function throwAbstractProductNotFound(): never
    {
        throw new GlueApiException(
            Response::HTTP_NOT_FOUND,
            ProductTaxSetsRestApiConfig::RESPONSE_CODE_CANT_FIND_ABSTRACT_PRODUCT,
            ProductTaxSetsRestApiConfig::RESPONSE_DETAIL_CANT_FIND_ABSTRACT_PRODUCT,
        );
    }

    protected function throwTaxSetsNotFound(): never
    {
        throw new GlueApiException(
            Response::HTTP_NOT_FOUND,
            ProductTaxSetsRestApiConfig::RESPONSE_CODE_CANT_FIND_PRODUCT_TAX_SETS,
            ProductTaxSetsRestApiConfig::RESPONSE_DETAIL_CANT_FIND_PRODUCT_TAX_SETS,
        );
    }
}
