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
	private int $bank = 0;
	private int $bankDepartment = 0;

	/** @var Item[] */
	private array $items = [];


	public function __construct(int $type = self::TYPE_UHRADA)
	{
		$this->type = $type;
	}


	public function generate(string $senderBank = ''): string
	{
		$res = sprintf("1 %04d %03d%03d %04d\r\n", $this->type, $this->number, $this->bankDepartment, $this->bank);
		foreach ($this->items as $item) {
			$res .= $item->generate(true, $senderBank);
		}
		$res .= "5 +\r\n";
		return $res;
	}


	public function setBankDepartment(int $number): self
	{
		$this->bankDepartment = $number;
		return $this;
	}


	/**
	 * Set number of file. Should be called only from abo.
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
	 * nastavit kod banky, ktere se dany soubor tyka(ktere to posilame?).
	 * @param int/string $bankCode kod banky
	 */
	public function setBank($bankCode): self
	{
		$this->bank = $bankCode;
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