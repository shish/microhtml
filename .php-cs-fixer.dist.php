<?php
return (new PhpCsFixer\Config())->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder(PhpCsFixer\Finder::create()->in(__DIR__))
;