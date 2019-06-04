<?php

namespace App\TransportObject;

use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractTransportObject implements TransportObjectInterface
{
    protected const REQUIRED_FIELD = 'api.error.required_field';

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var InvalidParam[]
     */
    protected $invalidParams = [];

    /**
     * @var InvalidParamFactory
     */
    protected $invalidParamFactory;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param InvalidParamFactory $invalidParamFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(InvalidParamFactory $invalidParamFactory, TranslatorInterface $translator = null)
    {
        $this->invalidParamFactory = $invalidParamFactory;
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    public function getLastError(): string
    {
        return end($this->errors) ?: '';
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return InvalidParam[]
     */
    public function getInvalidParams(): array
    {
        return $this->invalidParams ?? [];
    }

    /**
     * @param InvalidParam $invalidParam
     *
     * @return TransportObjectInterface
     */
    protected function addInvalidParam(InvalidParam $invalidParam): TransportObjectInterface
    {
        $this->invalidParams[] = $invalidParam;

        return $this;
    }

    /**
     * @param string $message
     */
    protected function setError(string $message): void
    {
        $this->errors[] = $message;
    }

    /**
     * @param string $paramName
     */
    protected function addInvalidParamByName(string $paramName): void
    {
        $this->addInvalidParam(
            $this->invalidParamFactory
                ->getInvalidParam()
                ->setName($paramName)
                ->setReason($this->translator->trans(self::REQUIRED_FIELD))
        );
    }

    /**
     * @param string $elementName
     * @param string $elementValue
     * @param int    $elementMaxLength
     *
     * @return bool
     */
    protected function isStringElementValid(string $elementName, string $elementValue, int $elementMaxLength): bool
    {
        if (empty($elementValue)) {
            $this->addInvalidParam(
                $this->invalidParamFactory->getInvalidParam()
                    ->setName($elementName)
                    ->setReason(
                        sprintf(
                            $this->translator->trans('transport_object.errors.element_cannot_be_empty'),
                            $elementName
                        )
                    )
            );

            return false;
        }

        if (mb_strlen($elementValue) <= $elementMaxLength) {
            return true;
        }

        $this->addInvalidParam(
            $this->invalidParamFactory->getInvalidParam()
                ->setName($elementName)
                ->setReason(
                    sprintf(
                        $this->translator->trans('transport_object.errors.string_element_is_too_long'),
                        $elementName,
                        $elementMaxLength
                    )
                )
        );

        return false;
    }
}