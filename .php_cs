<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$header = <<<'EOF'
This file is part of PHP CS Fixer.
(c) kcloze <pei.greet@qq.com>
This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony'                   => true,
        '@Symfony:risky'             => true,
        'array_syntax'               => ['syntax' => 'short'],
        'combine_consecutive_unsets' => true,
        // one should use PHPUnit methods to set up expected exception instead of annotations
        'general_phpdoc_annotation_remove'      => ['expectedException', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp'],
        'header_comment'                        => ['header' => $header],
        'heredoc_to_nowdoc'                     => true,
        'no_extra_consecutive_blank_lines'      => ['break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block'],
        'no_unreachable_default_argument_value' => true,
        'no_useless_else'                       => true,
        'no_useless_return'                     => true,
        'ordered_class_elements'                => true,
        'ordered_imports'                       => true,
        'php_unit_strict'                       => true,
        'phpdoc_add_missing_param_annotation'   => true,
        'no_trailing_comma_in_singleline_array' => true, //单行数组最后一个元素不添加逗号
        'phpdoc_order'                          => true,
        'psr4'                                  => true,
        'strict_comparison'                     => false,
        'strict_param'                          => false, //这里设置为true，发现in_array方法会默认加上第3个参数为true，这使得in_array会对前两个参数值的类型也会做严格的校验，建议设置为false
        'binary_operator_spaces'                => ['align_double_arrow' => true, 'align_equals' => true],
        'concat_space'                          => ['spacing' => 'one'],
        'no_empty_statement'                    => true,
        'simplified_null_return'                => true,
        'no_extra_consecutive_blank_lines'      => true,
        'pre_increment'                         => false, //设置为false，$i++ 不会变成 ++$i
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->in(__DIR__)
    )
    ->setUsingCache(false)
;
