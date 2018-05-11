<?php

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__.'/src')
;

return PhpCsFixer\Config::create()
    ->setUsingCache(true)
    //->setUsingLinter(false)
    ->setRules([
        '@Symfony' => true,
        'binary_operator_spaces' => true,
        'blank_line_before_return' => true,
        'cast_spaces' => true,
        'include' => true,
        'method_separation' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_consecutive_blank_lines' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unused_imports' => true,
        'no_whitespace_in_blank_line' => true,
        'object_operator_without_whitespace' => true,
        'phpdoc_align' => true,
        'phpdoc_indent' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_package' => true,
        'phpdoc_order' => true,
        'phpdoc_scalar' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'single_blank_line_before_namespace' => true,
        'short_scalar_cast' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_useless_else' => true,
        'ordered_class_elements' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline_array' => true,
    ])
    ->setFinder($finder)
;