<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers\BassMix\Tests;

use FFI\Headers\BassMix;
use FFI\Headers\BassMix\Platform;
use FFI\Headers\BassMix\Version;
use FFI\Headers\Testing\Downloader;

class BinaryCompatibilityTestCase extends TestCase
{
    private function skipIfPlatformNotSupported(Version $version, Platform $platform): void
    {
        if (!$version->supportedOn($platform)) {
            $this->markTestSkipped($platform->name . ' not supported by version ' . $version->toString());
        }
    }

    /**
     * @requires OSFAMILY Linux
     * @dataProvider versionsDataProvider
     */
    public function testLinuxBinaryCompatibility(Version $version): void
    {
        $this->skipIfPlatformNotSupported($version, Platform::LINUX);

        if (!\is_file($bass = __DIR__ . '/storage/' . $version->toString() . '/libbass.so')) {
            Downloader::zip('https://www.un4seen.com/files/bass%s-linux.zip', [
                \str_replace('.', '', $version->toString()),
            ])
                ->extract('x64/libbass.so', $bass);
        }

        if (!\is_file($mix = __DIR__ . '/storage/' . $version->toString() . '/libbassmix.so')) {
            Downloader::zip('https://www.un4seen.com/files/bassmix%s-linux.zip', [
                \str_replace('.', '', $version->toString()),
            ])
                ->extract('x64/libbassmix.so', $mix);
        }

        $this->assertHeadersCompatibleWith(BassMix::create(Platform::LINUX, $version), $mix);
    }

    /**
     * @requires OSFAMILY Windows
     * @dataProvider versionsDataProvider
     */
    public function testWindowsBinaryCompatibility(Version $version): void
    {
        if (!\is_file($bass = __DIR__ . '/storage/' . $version->toString() . '/bass.dll')) {
            $result = Downloader::zip('https://www.un4seen.com/files/bass%s.zip', [
                \str_replace('.', '', $version->toString()),
            ]);

            if (\PHP_INT_SIZE === 8 && $result->exists('x64/bass.dll')) {
                $result->extract('x64/bass.dll', $bass);
            } elseif (\PHP_INT_SIZE === 4 && $result->exists('bass.dll')) {
                $result->extract('bass.dll', $bass);
            } else {
                $this->markTestSkipped('Incompatible OS bits');
            }
        }

        if (!\is_file($mix = __DIR__ . '/storage/' . $version->toString() . '/bassmix.dll')) {
            $result = Downloader::zip('https://www.un4seen.com/files/bassmix%s.zip', [
                \str_replace('.', '', $version->toString()),
            ]);

            if (\PHP_INT_SIZE === 8 && $result->exists('x64/bassmix.dll')) {
                $result->extract('x64/bassmix.dll', $mix);
            } elseif (\PHP_INT_SIZE === 4 && $result->exists('bassmix.dll')) {
                $result->extract('bassmix.dll', $mix);
            } else {
                $this->markTestSkipped('Incompatible OS bits');
            }
        }

        $this->assertHeadersCompatibleWith(BassMix::create(Platform::WINDOWS, $version), $mix);
    }

    /**
     * @requires OSFAMILY Darwin
     * @dataProvider versionsDataProvider
     */
    public function testDarwinBinaryCompatibility(Version $version): void
    {
        $this->skipIfPlatformNotSupported($version, Platform::DARWIN);

        if (!\is_file($bass = __DIR__ . '/storage/' . $version->toString() . '/libbass.dylib')) {
            $result = Downloader::zip('https://www.un4seen.com/files/bass%s-osx.zip', [
                \str_replace('.', '', $version->toString()),
            ]);

            if (
                (\PHP_INT_SIZE === 8 && $version->gte('2.4')) ||
                (\PHP_INT_SIZE === 4 && $version->lt('2.4'))
            ) {
                $result->extract('libbass.dylib', $bass);
            } else {
                $this->markTestSkipped('Incompatible OS bits');
            }
        }

        if (!\is_file($mix = __DIR__ . '/storage/' . $version->toString() . '/libbassmix.dylib')) {
            $result = Downloader::zip('https://www.un4seen.com/files/bassmix%s-osx.zip', [
                \str_replace('.', '', $version->toString()),
            ]);

            if (
                (\PHP_INT_SIZE === 8 && $version->gte('2.4')) ||
                (\PHP_INT_SIZE === 4 && $version->lt('2.4'))
            ) {
                $result->extract('libbassmix.dylib', $mix);
            } else {
                $this->markTestSkipped('Incompatible OS bits');
            }
        }

        $this->assertHeadersCompatibleWith(BassMix::create(Platform::DARWIN, $version), $mix);
    }
}
