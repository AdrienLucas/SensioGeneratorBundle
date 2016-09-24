<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sensio\Bundle\GeneratorBundle\Tests\Command;

use Sensio\Bundle\GeneratorBundle\Command\Validators;

class ValidatorsTest extends GenerateCommandTest
{

    /**
     * @dataProvider getValidBundleNamespaces
     */
    public function testValidBundleNamespace($validNamespace, $requireVendorNamespace)
    {
        $namespace = Validators::validateBundleNamespace($validNamespace, $requireVendorNamespace);
        $this->assertSame($namespace, $validNamespace);
    }

    public function getValidBundleNamespaces()
    {
        return array(
            array('AppBundle', false),
            array('Acme\AppBundle', true),
        );
    }

    /**
     * @dataProvider getInvalidBundleNamespaces
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidBundleNamespace($invalidNamespace, $requireVendorNamespace)
    {
        Validators::validateBundleNamespace($invalidNamespace, $requireVendorNamespace);
    }

    public function getInvalidBundleNamespaces()
    {
        return array(
            array('AppBundle', true),
            array('App', false),
            array('Abstract\AppBundle', false),
            array('App%Bundle', false),
        );
    }

    /**
     * @dataProvider getValidBundleNames
     */
    public function testValidBundleName($validName)
    {
        $namespace = Validators::validateBundleName($validName);
        $this->assertSame($namespace, $validName);
    }

    public function getValidBundleNames()
    {
        return array(
            array('AppBundle'),
            array('_42Bundle'),
        );
    }

    /**
     * @dataProvider getInvalidBundleNames
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidBundleName($invalidName)
    {
        Validators::validateBundleName($invalidName);
    }

    public function getInvalidBundleNames()
    {
        return array(
            array('App'),
            array('App-Bundle'),
            array('42Bundle'),
        );
    }


    /**
     * @dataProvider getValidFormats
     */
    public function testValidFormat($validFormat, $expectedFormat = null)
    {
        if($expectedFormat === null) {
            $expectedFormat = $validFormat;
        }

        $format = Validators::validateFormat($validFormat);
        $this->assertSame($format, $expectedFormat);
    }

    public function getValidFormats()
    {
        return array(
            array('php'),
            array('xml'),
            array('yml'),
            array('annotation'),
            array('yaml', 'yml'),
        );
    }

    /**
     * @dataProvider getInvalidFormats
     * @expectedException \RuntimeException
     */
    public function testInvalidFormat($invalidFormat)
    {
        Validators::validateFormat($invalidFormat);
    }

    public function getInvalidFormats()
    {
        return array(
            array(''),
            array('ini'),
        );
    }


    /**
     * @dataProvider getValidEntityAndControllerNames
     */
    public function testValidEntityName($validEntityName)
    {
        $entityName = Validators::validateEntityName($validEntityName);
        $this->assertSame($entityName, $validEntityName);
    }
    /**
     * @dataProvider getValidEntityAndControllerNames
     */
    public function testValidControllerName($validControllerName)
    {
        $controllerName = Validators::validateControllerName($validControllerName);
        $this->assertSame($controllerName, $validControllerName);
    }

    public function getValidEntityAndControllerNames()
    {
        return array(
            array('AppBundle:Post'),
            array('AppBundle:Blog\Post'),
            array('App_Bundle:Post_Entity2'),
            array('App12:34'),
        );
    }

    /**
     * @dataProvider getInvalidEntityAndControllerNames
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidEntityName($invalidEntityName)
    {
        Validators::validateEntityName($invalidEntityName);
    }

    /**
     * @dataProvider getInvalidEntityAndControllerNames
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidControllerName($invalidControllerName)
    {
        Validators::validateControllerName($invalidControllerName);
    }

    public function getInvalidEntityAndControllerNames()
    {
        return array(
            array('AppBundlePost'),
            array('AppBundle\Blog\Post'),
            array('AppBundle:Blog-Post'),
            array('12Bundle:Post'),
        );
    }

}
