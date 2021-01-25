<?php

namespace snoblucha\Abo;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Class Group
 * @package snoblucha\Abo
 */
class Group
{
	private ?string $accountNumber;
	private ?string $accountPrefix;

	/** @var Item[] */
	private array $items = [];
	private ?string $dueDate = null;


	public function generate(): string
	{
		$res = "2 ";
		if ($this->accountNumber != null) {
			$res .= Abo::formatAccountNumber($this->accountNumber, $this->accountPrefix) . " ";
		}
		if ($this->dueDate == null) {
			$this->setDate(); //date is not set, so today is the day
		}
		$res .= sprintf("%014d %s", $this->getAmount(), $this->dueDate);
		$res .= "\r\n";
		foreach ($this->items as $item) {
			$res .= $item->generate($this->accountNumber != null);
		}
		$res .= "3 +\r\n";
		return $res;
	}


	/**
	 * Set date of the execution.
	 */
	public function setDate(?DateTimeInterface $date = null): self
	{
		if ($date == null) {
			$date = new DateTimeImmutable();
		}
		$this->dueDate = $date->format('dmy');
		return $this;
	}


	/**
	 * Set the account for the full group. The account will not be rendered in items
	 */
	public function setAccount(string $number, string $prefix = null): void
	{
		$this->accountNumber = $number;
		$this->accountPrefix = $prefix;
	}


	public function addItem(string $fullAccountNumber, float $amount, string $varSym): Item
	{
		$item = new Item($fullAccountNumber, $amount, $varSym);
		$this->items[] = $item;
		return $item;
	}


	/**
	 * Get the amount in halere.
	 */
	public function getAmount(): int
	{
		$res = 0;
		foreach ($this->items as $item) {
			$res += $item->getAmount();
		}
		return $res;
	}


}