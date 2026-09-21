<?php

declare(strict_types=1);

use Pest\Rector\Rules\ChainExpectCallsRector;
use Pest\Rector\Set\PestSetList;
use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodingStyle\Rector\ArrowFunction\ArrowFunctionDelegatingCallToFirstClassCallableRector;
use Rector\CodingStyle\Rector\Closure\ClosureDelegatingCallToFirstClassCallableRector;
use Rector\Config\RectorConfig;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Php85\Rector\Property\AddOverrideAttributeToOverriddenPropertiesRector;
use RectorLaravel\Rector\Empty_\EmptyToBlankAndFilledFuncRector;
use RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector;
use RectorLaravel\Rector\MethodCall\ConvertEnumerableToArrayToAllRector;
use RectorLaravel\Rector\MethodCall\ResponseHelperCallToJsonResponseRector;
use RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withComposerBased(laravel: true)
    ->withSets([
        LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
        LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME,
        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        LaravelSetList::LARAVEL_IF_HELPERS,
        LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
        PestSetList::CODING_STYLE,
    ])
    ->withRules([
        ResponseHelperCallToJsonResponseRector::class,
        EmptyToBlankAndFilledFuncRector::class,
    ])
    ->withConfiguredRule(RemoveDumpDataDeadCodeRector::class, [
        'dd', 'dump', 'var_dump',
    ])
    ->withCache(
        cacheDirectory: '/tmp/rector',
        cacheClass: FileCacheStorage::class,
    )
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bootstrap/app.php',
        __DIR__.'/database',
        __DIR__.'/public',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withSkip([
        __DIR__.'/database/migrations',
        AddOverrideAttributeToOverriddenMethodsRector::class,
        AddOverrideAttributeToOverriddenPropertiesRector::class,
        ChainExpectCallsRector::class,
        DispatchToHelperFunctionsRector::class,
        ConvertEnumerableToArrayToAllRector::class,
        ArrowFunctionDelegatingCallToFirstClassCallableRector::class,
        ClosureDelegatingCallToFirstClassCallableRector::class,
    ])
    ->withCodeQualityLevel(1)
    ->withCodingStyleLevel(1)
    ->withImportNames(importDocBlockNames: false)
    ->withPhpSets();
