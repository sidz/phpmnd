<?php

declare(strict_types=1);

namespace PHPMND\PhpParser\Visitor;

use PHPMND\DetectionResult;
use PHPMND\FileReport;
use PHPMND\FileReportGenerator;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class DetectionVisitor extends NodeVisitorAbstract
{
    /**
     * @var FileReportGenerator
     */
    private $generator;

    /**
     * @var array<iterable<FileReport>
     */
    private $reports = [];

    public function __construct(FileReportGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function beforeTraverse(array $nodes): ?array
    {
        $this->reports = [];

        return null;
    }

    public function leaveNode(Node $node): ?Node
    {
        $this->reports[] = $this->generator->detect($node);

        return null;
    }

    /**
     * @return iterable<DetectionResult>
     */
    public function getDetections(): iterable
    {
        foreach ($this->reports as $report) {
            yield from $report;
        }
    }
}
