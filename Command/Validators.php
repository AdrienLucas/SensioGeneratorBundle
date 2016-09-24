<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sensio\Bundle\GeneratorBundle\Command;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validation;

/**
 * Validator functions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Validators
{
    /**
     * Validates that the given namespace (e.g. Acme\FooBundle) is a valid format.
     *
     * If $requireVendorNamespace is true, then we require you to have a vendor
     * namespace (e.g. Acme).
     *
     * @param $namespace
     * @param bool $requireVendorNamespace
     *
     * @return string
     */
    public static function validateBundleNamespace($namespace, $requireVendorNamespace = true)
    {
        $namespace = strtr($namespace, '/', '\\');
        $constraints = array();

        $constraints[] = new Regex(array(
            'pattern' => '/Bundle$/',
            'message' => 'The namespace must end with Bundle.')
        );

        $constraints[] = new Regex(array(
            'pattern' => '/^(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\?)+$/',
            'message' => 'The namespace contains invalid characters.'
        ));

        $reserved = self::getReservedWords();
        $constraints[] = new Callback(array('callback' => function($namespace, ExecutionContextInterface $context) use ($reserved) {
            // validate reserved keywords
            $parts =  explode('\\', $namespace);
            while(!empty($parts) && count($context->getViolations()) === 0) {
                $word = array_shift($parts);
                if (in_array(strtolower($word), $reserved)) {
                    $context
                        ->buildViolation(sprintf('The namespace cannot contain PHP reserved words ("%s").', $word))
                        ->addViolation();
                }
            }
        }));

        $constraints[] = new Callback(array('callback' => function($namespace, ExecutionContextInterface $context) use ($requireVendorNamespace) {
            if($requireVendorNamespace && strpos($namespace, '\\') === false) {
                $context
                    ->buildViolation(
                        sprintf('The namespace must contain a vendor namespace (e.g. "VendorName\$1%s" instead of simply "$1%s").', $namespace)."\n\n".
                        'If you\'ve specified a vendor namespace, did you forget to surround it with quotes (init:bundle "Acme\BlogBundle")?'
                    )
                    ->addViolation();
            }
        }));
        $validator = Validation::createValidator();
        $violations = $validator->validate($namespace, $constraints);

        if ($violations->count()) {
            throw new \InvalidArgumentException($violations->get(0));
        }

        return $namespace;
    }

    public static function validateBundleName($bundle)
    {
        if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $bundle)) {
            throw new \InvalidArgumentException(sprintf('The bundle name %s contains invalid characters.', $bundle));
        }

        if (!preg_match('/Bundle$/', $bundle)) {
            throw new \InvalidArgumentException('The bundle name must end with Bundle.');
        }

        return $bundle;
    }

    public static function validateControllerName($controller)
    {
        try {
            self::validateEntityName($controller);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The controller name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Post)',
                    $controller
                )
            );
        }

        return $controller;
    }

    public static function validateFormat($format)
    {
        if (!$format) {
            throw new \RuntimeException('Please enter a configuration format.');
        }

        $format = strtolower($format);

        // in case they typed "yaml", but ok with that
        if ($format == 'yaml') {
            $format = 'yml';
        }

        if (!in_array($format, array('php', 'xml', 'yml', 'annotation'))) {
            throw new \RuntimeException(sprintf('Format "%s" is not supported.', $format));
        }

        return $format;
    }

    /**
     * Performs basic checks in entity name.
     *
     * @param string $entity
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public static function validateEntityName($entity)
    {
        if (!preg_match('{^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*:[a-zA-Z0-9_\x7f-\xff\\\/]+$}', $entity)) {
            throw new \InvalidArgumentException(sprintf('The entity name isn\'t valid ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)', $entity));
        }

        return $entity;
    }

    public static function getReservedWords()
    {
        return array(
            'abstract',
            'and',
            'array',
            'as',
            'break',
            'callable',
            'case',
            'catch',
            'class',
            'clone',
            'const',
            'continue',
            'declare',
            'default',
            'do',
            'else',
            'elseif',
            'enddeclare',
            'endfor',
            'endforeach',
            'endif',
            'endswitch',
            'endwhile',
            'extends',
            'final',
            'finally',
            'for',
            'foreach',
            'function',
            'global',
            'goto',
            'if',
            'implements',
            'interface',
            'instanceof',
            'insteadof',
            'namespace',
            'new',
            'or',
            'private',
            'protected',
            'public',
            'static',
            'switch',
            'throw',
            'trait',
            'try',
            'use',
            'var',
            'while',
            'xor',
            'yield',
            '__CLASS__',
            '__DIR__',
            '__FILE__',
            '__LINE__',
            '__FUNCTION__',
            '__METHOD__',
            '__NAMESPACE__',
            '__TRAIT__',
            '__halt_compiler',
            'die',
            'echo',
            'empty',
            'exit',
            'eval',
            'include',
            'include_once',
            'isset',
            'list',
            'require',
            'require_once',
            'return',
            'print',
            'unset',
        );
    }
}
