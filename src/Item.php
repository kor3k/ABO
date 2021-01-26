<?php
declare(strict_types=1);

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

	/** @var string account prefix max 6 numbers */
	private string $senderAccountPrefix = '';

	/** @var string account number max 10 numbers */
	private string $senderAccountNumber = '';


	public function __construct(string $fullAccountNumber, float $amount, string $varSym)
	{
		$this->setAccount($fullAccountNumber)
			->setAmount($amount)
			->setVarSym($varSym);
	}


	public function setAmount(float $amount, bool $convert2cents = true): self
	{
		if ($convert2cents) {
			$amount *= 100;
		}
		if ($amount !== floor($amount)) {
			throw new InvalidArgumentException('Parameter $amount must be either whole number representing amount in cents or decimal number with 2 decimal places, given ' . $amount);
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
		if (!empty($number) && !is_numeric($number) || strlen($number) > $len) {
			throw new InvalidArgumentException("Parameter \$number must be numeric string of max length $len!");
		}
		$this->varSym = $number;
		return $this;
	}


	public function setConstSym(string $number): self
	{
		$len = 4;
		if (!empty($number) && !is_numeric($number) || strlen($number) > $len) {
			throw new InvalidArgumentException("Parameter \$number must be numeric string of max length $len!");
		}
		$this->constSym = $number;
		return $this;
	}


	public function setSpecSym(string $number): self
	{
		$len = 10;
		if (!empty($number) && !is_numeric($number) || strlen($number) > $len) {
			throw new InvalidArgumentException("Parameter \$number must be numeric string of max length $len!");
		}
		$this->specSym = $number;
		return $this;
	}


	public function setMessage(string $msg): self
	{
		$lines = 4;
		$maxLineLen = 35;
		$msg = substr(Utils::toAscii($msg), 0, $lines * $maxLineLen);
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
			$res .= Utils::formatAccountNumber($this->senderAccountNumber, $this->senderAccountPrefix) . ' ';
		}
		$res .= sprintf("%s %d %s %s%04d %s ", Utils::formatAccountNumber($this->accountNumber, $this->accountPrefix), $this->amount, $this->varSym, $this->bankCode, $this->constSym, $this->specSym);
		$res .= $this->message ? ('AV:' . $this->message) : '';
		$res .= "\r\n";

		return $res;
	}


}