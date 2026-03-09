<?php

$finder = (new PhpCsFixer\Finder())
	->in(__DIR__)
	->ignoreDotFiles(true)
	->ignoreVCS(true)
	->exclude(['docs', 'vendor'])
	->name('*.php')
;

return (new PhpCsFixer\Config())
	->setUsingCache(true)
	->setFinder($finder)
	->setRules([
		'@PSR12' => true,
		'array_syntax' => ['syntax' => 'short'],
		'no_unused_imports' => true,
		'ordered_imports' => ['sort_algorithm' => 'alpha'],
		'single_quote' => true,
		'trailing_comma_in_multiline' => ['elements' => ['arguments', 'arrays', 'parameters']],
		'no_whitespace_in_blank_line' => true,
		'no_trailing_whitespace' => true,
	])
	->setIndent("\t")
;