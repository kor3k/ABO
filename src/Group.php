<?php
declare(strict_types=1);

namespace snoblucha\Abo;

use DateTimeImmutable;
use DateTimeInterface;

class Group
{
	/** @var ?string max 6 numbers */
	private ?string $semderAccountPrefix = null;

	/** @var ?string max 10 numbers */
	private ?string $senderAccountNumber = null;

	/** @var ?string format ddmmyy */
	private ?string $dueDate = null;

	/** @var Item[] */
	private array $items = [];


	/**
	 * Set the account for the full group. The account will not be rendered in items.
	 */
	public function setSenderAccount(string $number, string $prefix = null): void
	{
		$this->senderAccountNumber = $number;
		$this->semderAccountPrefix = $prefix;
	}


	public function setDueDate(?DateTimeInterface $date = null): self
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
		if ($this->senderAccountNumber !== null) {
			$res .= Utils::formatAccountNumber($this->senderAccountNumber, $this->semderAccountPrefix) . " ";
		}
		if ($this->dueDate === null) {
			$this->setDueDate();
		}
		$res .= sprintf("%014d %s", $this->getAmount(), $this->dueDate);
		$res .= "\r\n";
		foreach ($this->items as $item) {
			$res .= $item->generate($this->senderAccountNumber !== null);
		}
		$res .= "3 +\r\n";
		return $res;
	}


}