<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Headers;

use FFI\Contracts\Headers\HeaderInterface;
use FFI\Contracts\Preprocessor\Exception\DirectiveDefinitionExceptionInterface;
use FFI\Contracts\Preprocessor\Exception\PreprocessorExceptionInterface;
use FFI\Contracts\Preprocessor\PreprocessorInterface;
use FFI\Headers\BassMix\HeadersDownloader;
use FFI\Headers\BassMix\Platform;
use FFI\Headers\BassMix\Version;
use FFI\Contracts\Headers\VersionInterface;
use FFI\Preprocessor\Preprocessor;

class BassMix implements HeaderInterface
{
    /**
     * @var non-empty-string
     */
    private const HEADERS_DIRECTORY = __DIR__ . '/../resources/headers';

    /**
     * @param PreprocessorInterface $pre
     * @param VersionInterface $version
     */
    public function __construct(
        public readonly PreprocessorInterface $pre,
        public readonly VersionInterface $version = Version::LATEST,
    ) {
        if (!$this->exists()) {
            HeadersDownloader::download($this->version, self::HEADERS_DIRECTORY);

            if (!$this->exists()) {
                throw new \RuntimeException('Could not initialize (download) header files');
            }
        }
    }

    /**
     * @return bool
     */
    private function exists(): bool
    {
        return \is_file($this->getHeaderPathname());
    }

    /**
     * @return non-empty-string
     */
    public function getHeaderPathname(): string
    {
        return self::HEADERS_DIRECTORY . '/' . $this->version->toString() . '/bassmix.h';
    }

    /**
     * @param Platform|null $platform
     * @param VersionInterface|non-empty-string $version
     * @param PreprocessorInterface $pre
     * @return self
     * @throws DirectiveDefinitionExceptionInterface
     */
    public static function create(
        Platform $platform = null,
        VersionInterface|string $version = Version::LATEST,
        PreprocessorInterface $pre = new Preprocessor(),
    ): self {
        $pre = clone $pre;

        $pre->add('stdint.h', '');
        $pre->add('bass.h', <<<'CPP'
            #define WINAPI
            #define CALLBACK
            typedef uint32_t DWORD;
            typedef uint64_t QWORD;
            typedef int BOOL;

            typedef DWORD HSTREAM;
            typedef DWORD HSYNC;

            typedef void (CALLBACK SYNCPROC)(HSYNC, DWORD, DWORD, void*);
        CPP);

        if ($platform === Platform::WINDOWS) {
            $pre->define('_WIN32', '1');
        }

        if (!$version instanceof VersionInterface) {
            $version = Version::create($version);
        }

        if ($platform?->supportedBy($version) === false) {
            /** @psalm-suppress NullPropertyFetch */
            $message = \vsprintf('Platform [%s] not supported by version [%s]', [
                $platform->name,
                $version->toString(),
            ]);

            throw new \InvalidArgumentException($message);
        }

        return new self($pre, $version);
    }

    /**
     * @return non-empty-string
     * @throws PreprocessorExceptionInterface
     */
    public function __toString(): string
    {
        return $this->pre->process(new \SplFileInfo($this->getHeaderPathname())) . \PHP_EOL;
    }
}
