<?php


use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->exclude('vendor')
    ->name('*.php');

return (new Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'no_closing_tag' => true,
    ])
    ->setFinder($finder);