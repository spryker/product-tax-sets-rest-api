<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\ProductTaxSetsRestApi\Api\Storefront\Provider;

use ApiPlatform\Metadata\Operation;
use Generated\Api\Storefront\ProductTaxSetsStorefrontResource;
use Generated\Shared\Transfer\TaxProductStorageTransfer;
use Generated\Shared\Transfer\TaxSetStorageTransfer;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\ApiPlatform\Provider\BatchLoadableProviderInterface;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\TaxProductStorage\TaxProductStorageClientInterface;
use Spryker\Client\TaxStorage\TaxStorageClientInterface;
use Spryker\Glue\ProductTaxSetsRestApi\ProductTaxSetsRestApiConfig;
use Spryker\Service\Serializer\SerializerServiceInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @implements \Spryker\ApiPlatform\Provider\BatchLoadableProviderInterface<\Generated\Api\Storefront\ProductTaxSetsStorefrontResource>
 */
class ProductTaxSetsStorefrontProvider extends AbstractStorefrontProvider implements BatchLoadableProviderInterface
{
    protected const string URI_VAR_SKU = 'abstractProductSku';

    public function __construct(
        protected TaxProductStorageClientInterface $taxProductStorageClient,
        protected TaxStorageClientInterface $taxStorageClient,
        protected SerializerServiceInterface $serializer,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (isset($uriVariables[static::BATCH_DATA_KEY]) && is_array($uriVariables[static::BATCH_DATA_KEY])) {
            return $this->provideBatch($uriVariables[static::BATCH_DATA_KEY]);
        }

        return parent::provide($operation, $uriVariables, $context);
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

        return [$this->buildResource($taxSetStorage)];
    }

    /**
     * @param array<array<string, mixed>> $batchUriVariables
     *
     * @return array<\Generated\Api\Storefront\ProductTaxSetsStorefrontResource>
     */
    protected function provideBatch(array $batchUriVariables): array
    {
        $productAbstractSkus = [];

        foreach ($batchUriVariables as $itemUriVariables) {
            $productAbstractSku = $itemUriVariables[static::URI_VAR_SKU] ?? '';

            if ($productAbstractSku !== '') {
                $productAbstractSkus[] = $productAbstractSku;
            }
        }

        if ($productAbstractSkus === []) {
            return [];
        }

        $taxProductStorages = $this->taxProductStorageClient->getTaxProductStoragesByProductAbstractSkus($productAbstractSkus);

        $idTaxSets = array_unique(array_map(
            fn (TaxProductStorageTransfer $taxProductStorage) => $taxProductStorage->getIdTaxSet(),
            $taxProductStorages,
        ));

        if ($idTaxSets === []) {
            return [];
        }

        $taxSetStorages = $this->taxStorageClient->getTaxSetStoragesByIdTaxSets($idTaxSets);

        $resources = [];

        foreach ($taxSetStorages as $taxSetStorage) {
            $resources[] = $this->buildResource($taxSetStorage);
        }

        return $resources;
    }

    protected function buildResource(TaxSetStorageTransfer $taxSetStorage): ProductTaxSetsStorefrontResource
    {
        $taxRates = [];

        foreach ($taxSetStorage->getTaxRates() as $taxRate) {
            $taxRates[] = [
                'name' => $taxRate->getName(),
                'rate' => (string)$taxRate->getRate(),
                'country' => $taxRate->getCountry(),
            ];
        }

        return $this->serializer->denormalize(
            [
                'uuid' => $taxSetStorage->getUuid(),
                'name' => $taxSetStorage->getName(),
                'restTaxRates' => $taxRates,
            ],
            ProductTaxSetsStorefrontResource::class,
        );
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
