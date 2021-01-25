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
	private ?string $account_number;
	private ?string $account_pre_number;

	/** @var Item[] */
	private array $items = [];
	private ?string $dueDate = null;


	public function generate(string $senderBank = ''): string
	{
		$res = "2 ";
		if ($this->account_number != null) {
			$res .= Abo::account($this->account_number, $this->account_pre_number) . " ";
		}
		if ($this->dueDate == null) {
			$this->setDate(); //date is not set, so today is the day
		}
		$res .= sprintf("%014d %s", $this->getAmount(), $this->dueDate);
		$res .= "\r\n";
		foreach ($this->items as $item) {
			$res .= $item->generate($this->account_number != null, $senderBank);
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
	 * Optional item! Account that is used on one side (Our)
	 */
	public function setAccount(string $number, string $pre = null): void
	{
		$this->account_number = $number;
		$this->account_pre_number = $pre;
	}


	/**
	 * adds abo_item to group. and returns it for set up/
	 * @return Item
	 */
	public function addItem(string $account_number, float $amount, string $variable_sym): Item
	{
		$item = new Item($account_number, $amount, $variable_sym);
		$this->items[] = $item;
		return $item;
	}


	/**
	 * Get the amount in halere
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