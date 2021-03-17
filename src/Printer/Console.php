<?php

declare(strict_types=1);

namespace PHPMND\Printer;

use function count;
use JakubOnderka\PhpConsoleHighlighter\Highlighter;
use PHPMND\DetectionResult;
use function sprintf;
use Symfony\Component\Console\Output\OutputInterface;

class Console implements Printer
{
    /**
     * @var Highlighter
     */
    private $highlighter;

    public function __construct(Highlighter $highlighter)
    {
        $this->highlighter = $highlighter;
    }

    public function printData(OutputInterface $output, array $list): void
    {
        /** @var DetectionResult $detection */
        foreach ($list as $detection) {
            $output->writeln(sprintf(
                '%s:%d. Magic number: %s',
                $detection->getFile()->getRelativePathname(),
                $detection->getLine(),
                $detection->getValue()
            ));

            $output->writeln(
                $this->highlighter->getCodeSnippet($detection->getFile()->getContents(), $detection->getLine(), 0, 0)
            );
        }

        $output->writeln('<info>Total of Magic Numbers: ' . count($list) . '</info>');
    }
}
