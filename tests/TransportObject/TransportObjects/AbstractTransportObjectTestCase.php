<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\Tests\TransportObject\TransportObjects;

use App\TransportObject\InvalidParam;
use App\TransportObject\InvalidParamFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

class AbstractTransportObjectTestCase extends TestCase
{
    /**
     * @var InvalidParamFactory|MockObject
     */
    protected $invalidParamFactoryMock;

    /**
     * @var Translator|MockObject
     */
    protected $translatorMock;

    public function setUp(): void
    {
        $invalidParam = $this->createMock(InvalidParam::class);
        $this->translatorMock = $this->createMock(Translator::class);
        $this->translatorMock->method('trans')
            ->willReturn('Translated value');
        
        $this->invalidParamFactoryMock = $this->createMock(InvalidParamFactory::class);
        $this->invalidParamFactoryMock->method('getInvalidParam')
            ->willReturnReference($invalidParam);
    }

    protected function tearDown(): void
    {
        $this->invalidParamFactoryMock = null;
        $this->translatorMock = null;
    }
}