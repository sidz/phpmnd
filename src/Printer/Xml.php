<?php

declare(strict_types=1);

namespace PHPMND\Printer;

use function array_slice;
use function count;
use DOMDocument;
use function explode;
use PHPMND\Console\Application;
use PHPMND\DetectionResult;
use function reset;
use function str_replace;
use function strlen;
use function strpos;
use Symfony\Component\Console\Output\OutputInterface;

class Xml implements Printer
{
    /** @var string */
    private $outputPath;

    public function __construct(string $outputPath)
    {
        $this->outputPath = $outputPath;
    }

    /**
     * @param array<int, DetectionResult> $list
     */
    public function printData(OutputInterface $output, array $list): void
    {
        $groupedList = $this->groupDetectionResultPerFile($list);

        $output->writeln('Generate XML output...');
        $dom = new DOMDocument();
        $rootNode = $dom->createElement('phpmnd');
        $rootNode->setAttribute('version', Application::VERSION);
        $rootNode->setAttribute('fileCount', (string) count($groupedList));

        $filesNode = $dom->createElement('files');

        $total = 0;

        foreach ($groupedList as $path => $detectionResults) {
            $count = count($detectionResults);
            $total += $count;

            $fileNode = $dom->createElement('file');
            $fileNode->setAttribute('path', $path);
            $fileNode->setAttribute('errors', (string) $count);

            foreach ($detectionResults as $detectionResult) {
                $snippet = $this->getSnippet($detectionResult->getFile()->getContents(), $detectionResult->getLine(), $detectionResult->getValue());
                $entryNode = $dom->createElement('entry');
                $entryNode->setAttribute('line', (string) $detectionResult->getLine());
                $entryNode->setAttribute('start', (string) $snippet['col']);
                $entryNode->setAttribute('end', (string) ($snippet['col'] + strlen((string) $detectionResult->getValue())));

                $snippetNode = $dom->createElement('snippet');
                $snippetNode->appendChild($dom->createCDATASection($snippet['snippet']));

                $entryNode->appendChild($snippetNode);

                $fileNode->appendChild($entryNode);
            }

            $filesNode->appendChild($fileNode);
        }

        $rootNode->appendChild($filesNode);
        $rootNode->setAttribute('errorCount', (string) $total);

        $dom->appendChild($rootNode);

        $dom->save($this->outputPath);

        $output->writeln('XML generated at ' . $this->outputPath);
    }

    /**
     * @param array<int, DetectionResult> $list
     *
     * @return array<int, DetectionResult[]>
     */
    public function groupDetectionResultPerFile(array $list): array
    {
        $result = [];

        foreach ($list as $detectionResult) {
            $result[$detectionResult->getFile()->getRelativePathname()][] = $detectionResult;
        }

        return $result;
    }

    /**
     * Get the snippet and information about it
     *
     * @param int|string $text
     */
    private function getSnippet(string $content, int $line, $text): array
    {
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $lines = explode("\n", $content);

        $lineContent = array_slice($lines, $line - 1, 1);
        $lineContent = reset($lineContent);
        $start = strpos($lineContent, $text . '');

        return [
            'snippet' => $lineContent,
            'line' => $line,
            'magic' => $text,
            'col' => $start,
        ];
    }
}
