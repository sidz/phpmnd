<?php

declare(strict_types=1);

namespace PHPMND\Printer;

use function count;
use JakubOnderka\PhpConsoleColor\ConsoleColor;
use JakubOnderka\PhpConsoleHighlighter\Highlighter;
use const PHP_EOL;
use PHPMND\FileReportList;
use function sprintf;
use function str_repeat;
use Symfony\Component\Console\Output\OutputInterface;

class Console implements Printer
{
    const LINE_LENGTH = 80;

    public function printData(OutputInterface $output, FileReportList $fileReportList): void
    {
        $separator = str_repeat('-', self::LINE_LENGTH);
        $output->writeln(PHP_EOL . $separator . PHP_EOL);

        $total = 0;

        foreach ($fileReportList->getFileReports() as $fileReport) {
            $entries = $fileReport->getEntries();
            $total += count($entries);

            foreach ($entries as $entry) {
                $output->writeln(sprintf(
                    '%s:%d. Magic number: %s',
                    $fileReport->getFile()->getRelativePathname(),
                    $entry['line'],
                    $entry['value']
                ));

                $highlighter = new Highlighter(new ConsoleColor());
                $output->writeln(
                    $highlighter->getCodeSnippet($fileReport->getFile()->getContents(), $entry['line'], 0, 0)
                );
            }
            $output->writeln($separator . PHP_EOL);
        }
        $output->writeln('<info>Total of Magic Numbers: ' . $total . '</info>');
    }
}
