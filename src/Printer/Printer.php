<?php

declare(strict_types=1);

namespace PHPMND\Printer;

use Symfony\Component\Console\Output\OutputInterface;

interface Printer
{
    public function printData(OutputInterface $output, array $list): void;
}
