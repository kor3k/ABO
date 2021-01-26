<?php

namespace snoblucha\Abo;

use DateTimeImmutable;
use DateTimeInterface;

class Group
{
	/** @var ?string max 6 numbers */
	private ?string $accountPrefix = null;

	/** @var ?string max 10 numbers */
	private ?string $accountNumber = null;

	/** @var ?string format ddmmyy */
	private ?string $dueDate = null;

	/** @var Item[] */
	private array $items = [];


	/**
	 * Set the account for the full group. The account will not be rendered in items.
	 */
	public function setAccount(string $number, string $prefix = null): void
	{
		$this->accountNumber = $number;
		$this->accountPrefix = $prefix;
	}


	public function setDate(?DateTimeInterface $date = null): self
	{
		if ($date === null) {
			$date = new DateTimeImmutable();
		}
		$this->dueDate = $date->format('dmy');
		return $this;
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


	public function generate(): string
	{
		$res = "2 ";
		if ($this->accountNumber !== null) {
			$res .= Abo::formatAccountNumber($this->accountNumber, $this->accountPrefix) . " ";
		}
		if ($this->dueDate === null) {
			$this->setDate();
		}
		$res .= sprintf("%014d %s", $this->getAmount(), $this->dueDate);
		$res .= "\r\n";
		foreach ($this->items as $item) {
			$res .= $item->generate($this->accountNumber != null);
		}
		$res .= "3 +\r\n";
		return $res;
	}


}