<?php
declare(strict_types = 1);
namespace TYPO3\PharStreamWrapper\Tests\Functional;

use PHPUnit\Framework\TestCase;
use TYPO3\PharStreamWrapper\Helper;

class HelperTest extends TestCase
{
    /**
     * @return array
     */
    public function baseFileIsResolvedDataProvider(): array
    {
        $dataSet = [
            [
                'phar://{DIR}/bundle.phar',
                '{DIR}/bundle.phar'
            ],
            [
                'phar://{DIR}/bundle.phar/path',
                '{DIR}/bundle.phar'
            ],
            [
                'phar://{DIR}/bundle.phar/path/content.txt',
                '{DIR}/bundle.phar'
            ],
            [
                'phar://{DIR}/other/../bundle.phar/path/../other/content.txt',
                '{DIR}/bundle.phar'
            ],
            [
                'phar://{DIR}/../Fixtures/bundle.phar',
                '{DIR}/bundle.phar'
            ],
            [
                'phar://{DIR}/not-existing.phar/path/../other/content.txt',
                null
            ],
            [
                'phar://../Functional/Fixtures/bundle.phar',
                null
            ],
            [
                'phar://./Fixtures/bundle.phar',
                null
            ],
        ];

        $directory = __DIR__ . '/Fixtures';
        return $this->substituteFileNames($directory, $dataSet);
    }

    /**
     * @param string $path
     * @param string $expectation
     *
     * @test
     * @dataProvider baseFileIsResolvedDataProvider
     */
    public function baseFileIsResolved(string $path, string $expectation = null)
    {
        static::assertSame(
            $expectation,
            Helper::determineBaseFile($path)
        );
    }

    /**
     * @param string $directory
     * @param string[] $items
     * @return string[]
     */
    private function substituteFileNames(string $directory, array $items): array
    {
        $directory = rtrim($directory);

        return array_map(
            function ($item) use ($directory) {
                if (is_null($item)) {
                    return $item;
                }
                if (is_array($item)) {
                    return $this->substituteFileNames($directory, $item);
                }
                return str_replace('{DIR}', $directory, $item);
            },
            $items
        );
    }
}