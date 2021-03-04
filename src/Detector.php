<?php

declare(strict_types=1);

namespace PHPMND;

use const PHP_VERSION;
use PHPMND\Console\Option;
use PHPMND\Visitor\DetectorVisitor;
use PHPMND\Visitor\ParentConnectorVisitor;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\SplFileInfo;
use function version_compare;

class Detector
{
    /**
     * @var Option
     */
    private $option;

    public function __construct(Option $option)
    {
        $this->option = $option;
    }

    public function detect(SplFileInfo $file): FileReport
    {
        // For PHP < 8.0 we want to specify a lexer object.
        // Otherwise the code creates a `Lexer\Emulative()` instance, which by default uses PHP 8 compatibility
        // with e.g. longer list of reserved keywords
        $lexer = version_compare('8.0', PHP_VERSION) > 0 ? new Lexer() : null;

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, $lexer);
        $traverser = new NodeTraverser();

        $fileReport = new FileReport($file);

        $traverser->addVisitor(new ParentConnectorVisitor());
        $traverser->addVisitor(new DetectorVisitor($fileReport, $this->option));

        $stmts = $parser->parse($file->getContents());
        $traverser->traverse($stmts);

        return $fileReport;
    }
}
