<?php

namespace App\Tests\TransportObject\TransportObjects;

use App\FieldNames\CurrencyFields;
use App\TransportObject\TransportObjects\Currency;
use Faker\Factory;
use InvalidArgumentException;

class CurrencyTest extends AbstractTransportObjectTestCase
{
    private const MAX_NAME_LENGTH                 = 150;
    private const MAX_SYMBOL_LENGTH               = 10;
    private const WORDS_IN_TOO_LONG_ELEMENT_VALUE = 55;

    /**
     * @dataProvider getIncorrectData
     *
     * @param array $incorrectData
     */
    public function testThrowsExceptionWithIncorrectParams(array $incorrectData): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Currency($this->invalidParamFactoryMock, $this->translatorMock))
            ->prepareFromArray($incorrectData);
    }

    public function testReturnsCorrectArrayWithCorrectParams(): void
    {
        $faker = Factory::create();

        $given = [
            CurrencyFields::NAME   => $faker->text(self::MAX_NAME_LENGTH),
            CurrencyFields::CODE   => $faker->currencyCode,
            CurrencyFields::SYMBOL => $faker->randomLetter
        ];

        $actual = (new Currency($this->invalidParamFactoryMock))
            ->prepareFromArray($given)
            ->toArray();

        $this->assertEquals($given, $actual);
    }

    public function testReturnsCorrectName(): void
    {
        $faker = Factory::create();
        $name = $faker->text(self::MAX_NAME_LENGTH);

        $given = [
            CurrencyFields::NAME   => $name,
            CurrencyFields::CODE   => $faker->currencyCode,
            CurrencyFields::SYMBOL => $faker->randomLetter
        ];

        $actual = (new Currency($this->invalidParamFactoryMock))
            ->prepareFromArray($given);

        $this->assertEquals($name, $actual->getName());
    }

    public function testReturnsCorrectCode(): void
    {
        $faker = Factory::create();
        $code = $faker->currencyCode;

        $given = [
            CurrencyFields::NAME   => $faker->text(self::MAX_NAME_LENGTH),
            CurrencyFields::CODE   => $code,
            CurrencyFields::SYMBOL => $faker->randomLetter
        ];

        $actual = (new Currency($this->invalidParamFactoryMock))
            ->prepareFromArray($given);

        $this->assertEquals($code, $actual->getCode());
    }

    public function testReturnsCorrectSymbol(): void
    {
        $faker = Factory::create();
        $symbol = $faker->randomLetter;

        $given = [
            CurrencyFields::NAME   => $faker->text(self::MAX_NAME_LENGTH),
            CurrencyFields::CODE   => $faker->currencyCode,
            CurrencyFields::SYMBOL => $symbol
        ];

        $actual = (new Currency($this->invalidParamFactoryMock))
            ->prepareFromArray($given);

        $this->assertEquals($symbol, $actual->getSymbol());
    }

    /**
     * @return array
     */
    public function getIncorrectData(): array
    {
        $faker = Factory::create();

        return [
            'empty data'               => [[]],
            'missing currency code'    => [[
                                               CurrencyFields::NAME   => $faker->text(self::MAX_NAME_LENGTH),
                                               CurrencyFields::SYMBOL => $faker->text(self::MAX_SYMBOL_LENGTH),
                                           ]],
            'missing currency name'    => [[
                                               CurrencyFields::CODE   => $faker->currencyCode,
                                               CurrencyFields::SYMBOL => $faker->text(self::MAX_SYMBOL_LENGTH),
                                           ]],
            'too long currency code'   => [[
                                               CurrencyFields::NAME   => $faker->text(self::MAX_NAME_LENGTH),
                                               CurrencyFields::CODE   => $faker->words(self::WORDS_IN_TOO_LONG_ELEMENT_VALUE, true),
                                               CurrencyFields::SYMBOL => $faker->text(self::MAX_SYMBOL_LENGTH),
                                           ]],
            'too long currency symbol' => [[
                                               CurrencyFields::NAME   => $faker->text(self::MAX_NAME_LENGTH),
                                               CurrencyFields::CODE   => $faker->currencyCode,
                                               CurrencyFields::SYMBOL => $faker->words(self::WORDS_IN_TOO_LONG_ELEMENT_VALUE, true)
                                           ]],
            'too long currency name'   => [[
                                               CurrencyFields::NAME   => $faker->words(self::WORDS_IN_TOO_LONG_ELEMENT_VALUE, true),
                                               CurrencyFields::CODE   => $faker->currencyCode,
                                               CurrencyFields::SYMBOL => $faker->text(self::MAX_SYMBOL_LENGTH),
                                           ]],

        ];
    }
}
