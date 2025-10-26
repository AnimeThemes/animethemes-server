<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodingStyle\Rector\FunctionLike\FunctionLikeToFirstClassCallableRector;
use Rector\Config\RectorConfig;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use RectorLaravel\Rector\Empty_\EmptyToBlankAndFilledFuncRector;
use RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector;
use RectorLaravel\Rector\MethodCall\ConvertEnumerableToArrayToAllRector;
use RectorLaravel\Rector\MethodCall\ResponseHelperCallToJsonResponseRector;
use RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector;
use RectorLaravel\Set\LaravelSetList;
use RectorLaravel\Set\LaravelSetProvider;

return RectorConfig::configure()
    ->withSetProviders(LaravelSetProvider::class)
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
    ])
    ->withSkip([
        __DIR__.'/database/migrations',
        AddOverrideAttributeToOverriddenMethodsRector::class,
        DisallowedEmptyRuleFixerRector::class,
        DispatchToHelperFunctionsRector::class,
        ConvertEnumerableToArrayToAllRector::class,
        FunctionLikeToFirstClassCallableRector::class,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
    )
    ->withImportNames(importDocBlockNames: false)
    ->withPhpSets();
