<?php

namespace snoblucha\Abo;

use InvalidArgumentException;
use snoblucha\Abo\Group;

class File
{
	const TYPE_UHRADA = 1501;
	const TYPE_INKASO = 1502;


	/** @var int max 3 numbers */
	private int $number = 0;

	/** @var int one of self::TYPE_* consts */
	private int $type = self::TYPE_UHRADA;

	/** @var string sender bank code, 4 chars */
	private string $bankCode;

	/** @var string 3 chars */
	private string $bankDepartment = '000';

	/** @var Group[] */
	private array $items = [];


	public function __construct(int $type = self::TYPE_UHRADA)
	{
		$this->setType($type);
	}


	/**
	 * @internal
	 * Should be called only from Abo::addFile().
	 */
	public function setNumber(int $number): self
	{
		$this->number = $number;
		return $this;
	}


	public function setType(int $type): self
	{
		$allowed = [self::TYPE_UHRADA, self::TYPE_INKASO];
		if (!in_array($type, $allowed, true)) {
			throw new InvalidArgumentException('Parameter $type has invalid value, given: ' . $type);
		}
		$this->type = $type;
		return $this;
	}


	public function setBankCode(string $bankCode): self
	{
		$len = 4;
		if (strlen($bankCode) !== $len || !is_numeric($bankCode)) {
			throw new InvalidArgumentException("Parameter \$bankCode must be numeric and $len characters long");
		}
		$this->bankCode = $bankCode;
		return $this;
	}


	public function setBankDepartment(string $number): self
	{
		$len = 3;
		if (strlen($number) !== $len || !is_numeric($number)) {
			throw new InvalidArgumentException("Parameter \$number must be numeric and $len characters long");
		}
		$this->bankDepartment = $number;
		return $this;
	}


	public function addGroup(): Group
	{
		$item = new Group();
		$this->items[] = $item;
		return $item;
	}


	public function generate(): string
	{
		$res = sprintf("1 %04d %03d%03d %04d\r\n", $this->type, $this->number, $this->bankDepartment, $this->bankCode);
		foreach ($this->items as $group) {
			$res .= $group->generate();
		}
		$res .= "5 +\r\n";
		return $res;
	}


}