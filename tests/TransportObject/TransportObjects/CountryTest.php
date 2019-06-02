<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\Tests\TransportObject\TransportObjects;

use App\FieldNames\CountryFields;
use App\FieldNames\CurrencyFields;
use App\TransportObject\TransportObjects\Country;
use App\TransportObject\TransportObjects\Currency;
use Faker\Factory;
use InvalidArgumentException;
use TypeError;

class CountryTest extends AbstractTransportObjectTestCase
{
    private const MAX_COUNTRY_NAME_LENGTH         = 255;
    private const MAX_CURRENCY_NAME_LENGTH        = 150;
    private const MAX_CURRENCY_SYMBOL_LENGTH      = 10;
    private const WORDS_IN_TOO_LONG_ELEMENT_VALUE = 55;

    /**
     * @dataProvider getIncorrectData
     *
     * @param array $incorrectData
     */
    public function testThrowsExceptionWithIncorrectParams(array $incorrectData)
    {
        $this->expectException(InvalidArgumentException::class);
        (new Country($this->invalidParamFactoryMock, $this->translatorMock))
            ->prepareFromArray($incorrectData);
    }

    public function testThrowsTypeErrorWithIncorrectParamInSetter()
    {
        $currency = $this->createMock(Currency::class);
        $this->expectException(TypeError::class);
        (new Country($this->invalidParamFactoryMock, $this->translatorMock))
            ->setCurrencies([$currency, $currency, $currency]);

        $this->assertTrue(true);
    }

    public function testReturnsCountryObjectWhetCorrectCurrencyIsSet()
    {
        $currency = $this->createMock(Currency::class);
        $country = (new Country($this->invalidParamFactoryMock, $this->translatorMock))
            ->setCurrencies($currency);
        $this->assertInstanceOf(Country::class, $country);
    }

    public function testReturnsCorrectArrayWithCorrectParams(): void
    {
        $faker = Factory::create();
        $given = [
            CountryFields::NAME        => $faker->text(self::MAX_COUNTRY_NAME_LENGTH),
            CountryFields::NATIVE_NAME => $faker->text(self::MAX_COUNTRY_NAME_LENGTH),
        ];

        $expected = array_merge($given, [
            CountryFields::CURRENCIES => []
        ]);

        $actual = (new Country($this->invalidParamFactoryMock))
            ->prepareFromArray($given)
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testReturnsCorrectName(): void
    {
        $faker = Factory::create();
        $name = $faker->text(self::MAX_COUNTRY_NAME_LENGTH);
        $given = [
            CountryFields::NAME        => $name,
            CountryFields::NATIVE_NAME => $faker->text(self::MAX_COUNTRY_NAME_LENGTH),
        ];

        $actual = (new Country($this->invalidParamFactoryMock))
            ->prepareFromArray($given);

        $this->assertEquals($name, $actual->getCountryName());
    }

    public function testReturnsCorrectNativeName(): void
    {
        $faker = Factory::create();
        $name = $faker->text(self::MAX_COUNTRY_NAME_LENGTH);
        $given = [
            CountryFields::NAME        => $faker->text(self::MAX_COUNTRY_NAME_LENGTH),
            CountryFields::NATIVE_NAME => $name,
        ];

        $actual = (new Country($this->invalidParamFactoryMock))
            ->prepareFromArray($given);

        $this->assertEquals($name, $actual->getCountryNativeName());
    }


    public function testReturnFullDataWithCurrencies(): void
    {
        $faker = Factory::create();

        $currency = $this->createMock(Currency::class);
        $currency->method('toArray')
            ->willReturn([
                CurrencyFields::NAME   => $faker->text(self::MAX_CURRENCY_NAME_LENGTH),
                CurrencyFields::SYMBOL => $faker->text(self::MAX_CURRENCY_SYMBOL_LENGTH),
                CurrencyFields::CODE   => $faker->currencyCode,
            ]);

        $given = [
            CountryFields::NAME        => $faker->text(self::MAX_COUNTRY_NAME_LENGTH),
            CountryFields::NATIVE_NAME => $faker->text(self::MAX_COUNTRY_NAME_LENGTH)
        ];

        $expected = array_merge($given, [
            CountryFields::CURRENCIES => [
                $currency->toArray(),
                $currency->toArray(),
                $currency->toArray()
            ]
        ]);

        $currencies = [$currency, $currency, $currency];

        $actual = (new Country($this->invalidParamFactoryMock))
            ->prepareFromArray($given)
            ->setCurrencies(...$currencies)
            ->toArray();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function getIncorrectData(): array
    {
        $faker = Factory::create();

        return [
            'empty data'              => [[]],
            'missing name'            => [[
                                              CountryFields::NATIVE_NAME => $faker->country
                                          ]],
            'missing native name'     => [[
                                              CountryFields::NAME => $faker->country
                                          ]],
            'name is too long'        => [[
                                              CountryFields::NAME        => $faker->words(self::WORDS_IN_TOO_LONG_ELEMENT_VALUE, true),
                                              CountryFields::NATIVE_NAME => $faker->country

                                          ]],
            'native name is too long' => [[
                                              CountryFields::NAME        => $faker->country,
                                              CountryFields::NATIVE_NAME => $faker->words(self::WORDS_IN_TOO_LONG_ELEMENT_VALUE, true),
                                          ]]
        ];
    }
}