<?php

declare(strict_types=1);

namespace PHPMND;

use function array_diff;
use function array_filter;
use function array_map;
use function array_merge;
use function dirname;
use function realpath;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class PHPFinder extends Finder
{
    public function __construct(
        array $directories,
        array $exclude,
        array $excludePaths,
        array $excludeFiles,
        array $suffixes
    ) {
        parent::__construct();
        $dirs = array_filter($directories, 'is_dir');
        $files = array_diff($directories, $dirs);

        $this
            ->files()
            ->in($dirs)
            ->exclude(array_merge(['vendor'], $exclude))
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->append(
                array_map(
                    static function (string $file) {
                        return new SplFileInfo(realpath($file), dirname($file), $file);
                    },
                    $files
                )
            );

        foreach ($suffixes as $suffix) {
            $this->name('*.' . $suffix);
        }

        foreach ($excludePaths as $notPath) {
            $this->notPath($notPath);
        }

        foreach ($excludeFiles as $notName) {
            $this->notName($notName);
        }
    }
}
