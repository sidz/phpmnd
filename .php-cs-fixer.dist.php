<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude([
        '.github',
        'tests/Fixtures',
    ])
    ->ignoreDotFiles(false)
    ->name('*php')
    ->name('phpmnd')
;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP71Migration' => true,
        '@PHP71Migration:risky' => true,
        '@PHPUnit60Migration:risky' => true,
        '@PHPUnit75Migration:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'do',
                'for',
                'foreach',
                'if',
                'include',
                'include_once',
                'require',
                'require_once',
                'return',
                'switch',
                'throw',
                'try',
                'while',
                'yield',
            ],
        ],
        'compact_nullable_typehint' => true,
        'concat_space' => ['spacing' => 'one'],
        'final_static_access' => true,
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'logical_operators' => true,
        'native_constant_invocation' => true,
        'native_function_invocation' => [
            'include' => ['@internal'],
        ],
        'no_alternative_syntax' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_trailing_whitespace_in_string' => false,
        'no_unset_cast' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'ordered_interfaces' => true,
        'phpdoc_align' => [
            'align' => 'left',
        ],
        'php_unit_dedicate_assert' => true,
        'php_unit_method_casing' => [
            'case' => 'snake_case',
        ],
        'php_unit_set_up_tear_down_visibility' => true,
        'php_unit_strict' => true,
        'php_unit_ordered_covers' => true,
        'phpdoc_order_by_value' => [
            'annotations' => ['covers'],
        ],
        'php_unit_test_annotation' => [
            'style' => 'prefix',
        ],
        'php_unit_test_case_static_method_calls' => [
            'call_type' => 'this',
        ],
        'phpdoc_no_empty_return' => true,
        'phpdoc_order' => true,
        'phpdoc_summary' => false,
        'self_static_accessor' => true,
        'single_line_throw' => false,
        'static_lambda' => true,
        'strict_comparison' => true,
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    ])
    ->setFinder($finder)
;
