<?php

namespace PHPMND\Printer;

use PHPMND\FileReportList;
use PHPMND\HintList;
use Symfony\Component\Console\Output\OutputInterface;

interface Printer
{
    public function printData(OutputInterface $output, FileReportList $fileReportList, HintList $hintList): void;
}
