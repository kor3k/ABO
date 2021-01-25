<?php

namespace snoblucha\Abo;

use snoblucha\Abo\Account\File;

class Abo
{
	const HEADER = 'UHL1';


	/** @var File[] */
	private array $items = [];
	private ?string $organization;
	private ?string $date;
	private int $comittent_number = 0;
	private ?string $fixedKeyPart = null;
	private ?string $securityCode = null;
	private string $senderBank = '';


	public function __construct(string $organization = "")
	{
		$this->setOrganization($organization);
		$this->setDate();
	}


	/**
	 * Set the organization name. Less then 20 chars.
	 */
	public function setOrganization(string $organization)
	{
		$this->organization = $organization;
		return $this;
	}


	public function setSenderBank(string $bankCode): self
	{
		$this->senderBank = $bankCode;
		return $this;
	}


	/**
	 * Optional part of the header. Set the Fixed key part and security code.
	 * @param string $fixed 6 numbers
	 * @param string $securityCode 6 numbers
	 */
	public function setSecurityKey(string $fixed, string $securityCode): self
	{
		if (!is_numeric($fixed) || !is_numeric($securityCode) || strlen($fixed) !== 6 || strlen($securityCode) !== 6) {
			throw new InvalidArgumentException('Parameters $fixed and $securityCode must be numeric strings of length 6!');
		}
		$this->fixedKeyPart = $fixed;
		$this->securityCode = $securityCode;
		return $this;
	}


	/**
	 * Set date of file
	 * @param string $date format DDMMYY
	 */
	public function setDate(?string $date = null): self
	{
		if ($date == null) {
			$date = date('dmy');
		}
		$this->date = $date;
		return $this;
	}


	public function setComittentNumer(int $number): self
	{
		$this->comittent_number = $number;
		return $this;
	}


	public function addAccountFile($type = File::TYPE_UHRADA): File
	{
		$item = new File($type);
		$this->items[] = $item;
		$item->setNumber(count($this->items));
		return $item;
	}


	/**
	 * Get the account files
	 * @return File[]
	 */
	public function getFiles(): array
	{
		return $this->items;
	}


	public function generate(): string
	{
		$res = sprintf("%s%s% -20s%010d%03d%03d", self::HEADER, $this->date, $this->organization, $this->comittent_number, 1, 1 + count($this->items));
		if ($this->securityCode) {
			$res .= sprintf("%06d%06d", $this->fixedKeyPart, $this->securityCode);
		}
		$res .= "\r\n";

		foreach ($this->items as $item) {
			$res .= $item->generate($this->senderBank);
		}

		return $res;
	}


	public static function account(string $number, string $pre = null): string
	{
		$res = '';
		if ($pre) {
			$res .= sprintf("%s-", $pre);
		}
		$res .= sprintf("%s", $number);
		return $res;
	}


}