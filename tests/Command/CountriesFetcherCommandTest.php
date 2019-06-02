<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\Tests\Command;

use App\Command\CountriesFetcherCommand;
use App\Entity\Country;
use App\Entity\Currency;
use App\Factories\EntityFactory;
use App\Factories\TransportObjectFactory;
use App\TransportObject\InvalidParam;
use App\TransportObject\InvalidParamFactory;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Migrations\Configuration\Exception\JsonNotValid;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Translation\Translator;

class CountriesFetcherCommandTest extends TestCase
{
    /**
     * @var ManagerRegistry|MockObject
     */
    private $managerRegistryMock;

    /**
     * @var EntityFactory|MockObject
     */
    private $entityFactoryMock;

    /**
     * @var TransportObjectFactory|MockObject
     */
    private $transportObjectFactoryMock;

    /**
     * @var InvalidParamFactory|MockObject
     */
    protected $invalidParamFactoryMock;

    /**
     * @var Translator|MockObject
     */
    private $translatorMock;

    public function setUp(): void
    {
        $invalidParam = $this->createMock(InvalidParam::class);
        $this->translatorMock = $this->createMock(Translator::class);
        $this->translatorMock->method('trans')
            ->willReturn('Translated value');

        $this->invalidParamFactoryMock = $this->createMock(InvalidParamFactory::class);
        $this->invalidParamFactoryMock->method('getInvalidParam')
            ->willReturnReference($invalidParam);

        $country = $this->createMock(Country::class);
        $currency = $this->createMock(Currency::class);

        $this->entityFactoryMock = $this->createMock(EntityFactory::class);
        $this->entityFactoryMock->method('createCountry')
            ->willReturnReference($country);

        $this->entityFactoryMock->method('createCurrency')
            ->willReturnReference($currency);

        $objectManager = $this->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $objectRepository = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->managerRegistryMock = $this->getMockBuilder(ManagerRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->managerRegistryMock->method('getManager')
            ->willReturnReference($objectManager);

        $this->managerRegistryMock->method('getRepository')
            ->willReturnReference($objectRepository);

        $this->transportObjectFactoryMock = $this->createMock(TransportObjectFactory::class);
    }

    protected function tearDown(): void
    {
        $this->entityFactoryMock = null;
        $this->managerRegistryMock = null;
        $this->transportObjectFactoryMock = null;
        $this->invalidParamFactoryMock = null;
        $this->translatorMock = null;
    }

    public function testThrowsExceptionWithIncorrectApiUrl(): void
    {
        $application = new Application();
        $application->add(new CountriesFetcherCommand(
            'incorrect url',
            $this->managerRegistryMock,
            $this->entityFactoryMock,
            $this->transportObjectFactoryMock
        ));

        $this->expectException(InvalidArgumentException::class);

        $command = $application->find('countries:fetch');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }

    public function testThrowsExceptionWithInvalidJsonFormat()
    {
        $application = new Application();
        $application->add(new CountriesFetcherCommand(
            'http://api.nbp.pl/api/exchangerates/tables/A?format=xml',
            $this->managerRegistryMock,
            $this->entityFactoryMock,
            $this->transportObjectFactoryMock
        ));

        $this->expectException(JsonNotValid::class);

        $command = $application->find('countries:fetch');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }

    public function testThrowsExceptionWhenCannotCreateCountryTransportObject(): void
    {
        /** @var TransportObjectFactory|MockObject $transportObjectFactoryMock */
        $transportObjectFactoryMock = $this->getMockBuilder(TransportObjectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $country = $this->getMockBuilder(\App\TransportObject\TransportObjects\Country::class)
            ->disableOriginalConstructor()
            ->getMock();

        $country->method('prepareFromArray')
            ->withAnyParameters()
            ->willThrowException(new InvalidArgumentException());

        $transportObjectFactoryMock->method('createCountry')
            ->willReturnReference($country);

        $application = new Application();
        $application->add(new CountriesFetcherCommand(
            'http://api.nbp.pl/api/exchangerates/tables/A?format=json',
            $this->managerRegistryMock,
            $this->entityFactoryMock,
            $transportObjectFactoryMock
        ));

        $this->expectException(InvalidArgumentException::class);

        $command = $application->find('countries:fetch');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}