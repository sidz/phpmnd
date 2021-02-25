<?php

declare(strict_types=1);

namespace PHPMND;

use PHPMND\Console\Option;
use PHPMND\PhpParser\FileParser;
use PHPMND\PhpParser\Visitor\DetectionVisitor;
use PHPMND\PhpParser\Visitor\ParentConnectorVisitor;
use PhpParser\NodeTraverser;
use Symfony\Component\Finder\SplFileInfo;

class Detector
{
    /**
     * @var Option
     */
    private $option;

    /**
     * @var FileParser
     */
    private $parser;

    public function __construct(FileParser $parser, Option $option)
    {
        $this->parser = $parser;
        $this->option = $option;
    }

    public function detect(SplFileInfo $file): iterable
    {
        $statements = $this->parser->parse($file);

        $traverser = new NodeTraverser();

        $detectorVisitor = new DetectionVisitor(
            new FileReportGenerator(
                $file,
                $this->option
            )
        );

        $traverser->addVisitor(new ParentConnectorVisitor());
        $traverser->addVisitor($detectorVisitor);

        $traverser->traverse($statements);

        yield from $detectorVisitor->getDetections();
    }
}
