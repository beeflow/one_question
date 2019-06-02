<?php

namespace App\TransportObject;


use InvalidArgumentException;

/**
 * @see     https://tools.ietf.org/html/rfc7807#section-3
 *
 * @package api\v2\src\lib\ApiResponse
 */
class InvalidParam extends AbstractTransportObject implements TransportObjectInterface
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $reason;

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return InvalidParam
	 */
	public function setName(string $name): InvalidParam
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getReason(): string
	{
		return $this->reason;
	}

	/**
	 * @param string $reason
	 *
	 * @return InvalidParam
	 */
	public function setReason(string $reason): InvalidParam
	{
		$this->reason = $reason;

		return $this;
	}

	/**
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'name'   => $this->name,
			'reason' => $this->reason
		];
	}

	/**
	 * @param array $params
	 *
	 * @return TransportObjectInterface
	 * @throws InvalidArgumentException
	 */
	public function prepareFromArray(array $params): TransportObjectInterface
	{
		if (isset($params['name']) && !empty($params['name'])) {
			$this->setName($params['name']);
		}

		if (isset($params['reason']) && !empty($params['reason'])) {
			$this->setReason($params['reason']);
		}

		if (!$this->isValid()) {
			throw new InvalidArgumentException('Incorrect object parameters.');
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isValid(): bool
	{
		$isValid = true;

		if (empty($this->name)) {
			$isValid = false;
			$this->addInvalidParam(
				$this->invalidParamFactory
					->getInvalidParam()
					->setName('name')
					->setReason('Name cannot be empty')
			);
		}

		if (empty($this->reason)) {
			$isValid = false;
			$this->addInvalidParam(
				$this->invalidParamFactory
					->getInvalidParam()
					->setName('reason')
					->setReason('Reason cannot be empty')
			);
		}

		return $isValid;
	}
}