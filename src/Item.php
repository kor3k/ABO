<?php

namespace snoblucha\Abo;

use InvalidArgumentException;

class Item
{
	/** @var string recipient account prefix max 6 numbers */
	private string $accountPrefix = '';

	/** @var string recipient account number max 10 numbers */
	private string $accountNumber = '';

	/** @var string recipient bank code 4 numbers */
	private string $bankCode = '';

	/** @var int in cents/halere */
	private int $amount = 0;

	/** @var string max 10 numbers */
	private string $varSym = '';

	/** @var string max 10 numbers */
	private string $specSym = '';

	/** @var string max 4 numbers */
	private string $constSym = '';

	/** @var string max 4(lines)*35 chars */
	private string $message = '';

	/** @var string recipient account prefix max 6 numbers */
	private string $senderAccountPrefix = '';

	/** @var string recipient account number max 10 numbers */
	private string $senderAccountNumber = '';


	public function __construct(string $fullAccountNumber, float $amount, string $varSym)
	{
		$this->setAccount($fullAccountNumber)
			->setAmount($amount)
			->setVarSym($varSym);
	}


	/**
	 * Set the amount to transfer.
	 * @param float $amount
	 * @param bool $halere is $amount in halere?
	 */
	public function setAmount(float $amount, bool $halere = false): self
	{
		if (!$halere) {
			$amount *= 100;
		}
		$this->amount = intval($amount);
		return $this;
	}


	public function getAmount(): int
	{
		return $this->amount;
	}


	/**
	 * @param string $fullAccountNumber in format (xxxxxx-)xxxxxxxxxx/xxxx
	 */
	public function setAccount(string $fullAccountNumber): self
	{
		$account = explode('/', $fullAccountNumber);
		$this->bankCode = $account[1];
		if (strpos($account[0], '-') !== false) {
			$number = explode('-', $account[0]);
			$this->accountPrefix = $number[0];
			$this->accountNumber = $number[1];
		} else {
			$this->accountNumber = $account[0];
		}

		return $this;
	}


	/**
	 * @param string $fullAccountNumber in format (xxxxxx-)xxxxxxxx/xxxx
	 */
	public function setSenderAccount(string $fullAccountNumber): self
	{
		$account = explode('/', $fullAccountNumber);
//		$this->senderBankCode = $account[1];
		if (strpos($account[0], '-') !== false) {
			$number = explode('-', $account[0]);
			$this->senderAccountPrefix = $number[0];
			$this->senderAccountNumber = $number[1];
		} else {
			$this->senderAccountNumber = $account[0];
		}

		return $this;
	}


	public function setVarSym(string $number): self
	{
		$len = 10;
		if (!is_numeric($number) || strlen($number) > $len) {
			throw new InvalidArgumentException("Parameter \$number must be numeric string of max length $len!");
		}
		$this->varSym = $number;
		return $this;
	}


	public function setConstSym(string $number): self
	{
		$len = 4;
		if (!is_numeric($number) || strlen($number) > $len) {
			throw new InvalidArgumentException("Parameter \$number must be numeric string of max length $len!");
		}
		$this->constSym = $number;
		return $this;
	}


	public function setSpecSym(string $number): self
	{
		$len = 10;
		if (!is_numeric($number) || strlen($number) > $len) {
			throw new InvalidArgumentException("Parameter \$number must be numeric string of max length $len!");
		}
		$this->specSym = $number;
		return $this;
	}


	public function setMessage(string $msg): self
	{
		$lines = 4;
		$maxLineLen = 35;
		$msg = substr($msg, 0, $lines * $maxLineLen);
		$this->message = rtrim(chunk_split($msg, $maxLineLen, '|'), '| ');
		return $this;
	}


	/**
	 * @param bool $omitSenderAccount true if the sender number is already in the group header
	 * @return string
	 */
	public function generate(bool $omitSenderAccount): string
	{
		$res = '';
		if (!$omitSenderAccount) {
			$res .= Abo::formatAccountNumber($this->senderAccountNumber, $this->senderAccountPrefix) . ' ';
		}
		$res .= sprintf("%s %d %s %s%04d %s ", Abo::formatAccountNumber($this->accountNumber, $this->accountPrefix), $this->amount, $this->varSym, $this->bankCode, $this->constSym, $this->specSym);
		$res .= $this->message ? ('AV:' . $this->message) : '';
		$res .= "\r\n";

		return $res;
	}


}