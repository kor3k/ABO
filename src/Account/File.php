<?php

namespace snoblucha\Abo\Account;

use InvalidArgumentException;
use snoblucha\Abo\Group;
use snoblucha\Abo\Item;

class File
{
	const TYPE_UHRADA = 1501;
	const TYPE_INKASO = 1502;
	const UHRADA = self::TYPE_UHRADA; // BC
	const INKASO = self::TYPE_INKASO; // BC


	private int $number = 0;
	private int $type = self::TYPE_UHRADA;
	private string $bankCode;
	private string $bankDepartment = '000';

	/** @var Group[] */
	private array $items = [];


	public function __construct(int $type = self::TYPE_UHRADA)
	{
		$this->type = $type;
	}


	public function generate(string $senderBank = ''): string
	{
		$res = sprintf("1 %04d %03d%03d %04d\r\n", $this->type, $this->number, $this->bankDepartment, $this->bankCode);
		foreach ($this->items as $item) {
			$res .= $item->generate(true, $senderBank);
		}
		$res .= "5 +\r\n";
		return $res;
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


	/**
	 * Set number of file.
	 * @internal Should be called only from Abo::addAccountFile().
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


	/**
	 * Set recipient bank code.
	 */
	public function setBankCode(string $bankCode): self
	{
		$len = 4;
		if (strlen($bankCode) !== $len || !is_numeric($bankCode)) {
			throw new InvalidArgumentException("Parameter \$bankCode must be numeric and $len characters long");
		}
		$this->bankCode = $bankCode;
		return $this;
	}


	/**
	 * Add a group to item and return it to set up.
	 */
	public function addGroup(): Group
	{
		$item = new Group();
		$this->items[] = $item;
		return $item;
	}


}