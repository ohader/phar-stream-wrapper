<?php
declare(strict_types = 1);
namespace TYPO3\PharStreamWrapper\Tests\Functional\Interceptor;

/*
 * This file is part of the TYPO3 project.
 *
 * It is free software; you can redistribute it and/or modify it under the terms
 * of the MIT License (MIT). For the full copyright and license information,
 * please read the LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\PharStreamWrapper\Helper;
use TYPO3\PharStreamWrapper\Phar\Reader;

/**
 * @requires PHP 8.0
 */
class NativePhpTest extends AbstractTestCase
{
    /**
     * @var string[]
     */
    protected $allowedPaths = [
        __DIR__ . '/../Fixtures/bundle.phar',
        __DIR__ . '/../Fixtures/bundle.phar.gz',
        __DIR__ . '/../Fixtures/bundle.phar.bz2',
        __DIR__ . '/../Fixtures/bundle.phar.png',
        __DIR__ . '/../Fixtures/Source/../bundle.phar',
    ];

    /**
     * @var string[]
     */
    protected $allowedAliasedPaths = [
        __DIR__ . '/../Fixtures/geoip2.phar',
        __DIR__ . '/../Fixtures/alias-no-path.phar',
        __DIR__ . '/../Fixtures/alias-with-path.phar',
    ];

    /**
     * @var string[]
     */
    protected $deniedPaths = [
        __DIR__ . '/../Fixtures/compromised.phar',
        __DIR__ . '/../Fixtures/compromised.phar.gz',
        __DIR__ . '/../Fixtures/compromised.phar.bz2',
        __DIR__ . '/../Fixtures/compromised.phar.png',
        __DIR__ . '/../Fixtures/compromised.phar.gz.png',
        __DIR__ . '/../Fixtures/compromised.phar.bz2.png',
    ];

    protected $invalidPaths = [
        __DIR__ . '/../Fixtures/compromised.phar/../bundle.phar',
    ];

    /**
     * @var int|null
     */
    const EXPECTED_EXCEPTION_CODE = null;

    protected function setUp()
    {
        parent::setUp();

        if (!in_array('phar', stream_get_wrappers())) {
            $this->markTestSkipped('Phar stream wrapper is not registered');
        }
    }

    /**
     * @return array
     */
    public function isFileSystemInvocationAcceptableDataProvider(): array
    {
        $fixturePath = __DIR__ . '/../Fixtures';

        return [
            'include phar' => [
                $fixturePath . '/geoip2.phar',
                // Reader invocations: one for alias, one for meta-data
                [Helper::class . '::determineBaseFile' => 1, Reader::class . '->resolveContainer' => 2]
            ],
            'include autoloader' => [
                'phar://' . $fixturePath . '/geoip2.phar/vendor/autoload.php',
                // Reader invocations: one for alias, one for meta-data
                [Helper::class . '::determineBaseFile' => 1, Reader::class . '->resolveContainer' => 2]
            ],
        ];
    }
}
