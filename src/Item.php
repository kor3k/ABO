<?php

namespace snoblucha\Abo;

class Item
{
	// in cents/halere
	private int $amount = 0;
	private string $varSym = '';
	private string $specSym = '';
	private string $constSym = '';
	private string $bankCode = '';
	private string $accountNumber = '';
	private string $accountPrefix = '';
	private string $destAccount = '';
	private string $destAccountPrefix = '';
	private string $message = '';


	public function __construct(string $fullAccountNumber, float $amount, string $varSym)
	{
		$this->setAmount($amount)->setAccount($fullAccountNumber)->setVarSym($varSym);
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
	 * @param string $fullAccountNumber in format (xxxx-)xxxxxxxx/xxxx
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
	 * @param string $fullAccountNumber in format (xxxx-)xxxxxx/xxxx
	 */
	public function setDestAccount(string $fullAccountNumber): self
	{
		$account = explode('/', $fullAccountNumber);
		//$this->destBankCode = $account[1];
		if (strpos($account[0], '-') !== false) {
			$number = explode('-', $account[0]);
			$this->destAccountPrefix = $number[0];
			$this->destAccount = $number[1];
		} else {
			$this->destAccount = $account[0];
		}

		return $this;
	}


	public function setVarSym(string $varSym): self
	{
		$this->varSym = $varSym;
		return $this;
	}


	public function setConstSym(string $constSym): self
	{
		$this->constSym = $constSym;
		return $this;
	}


	public function setSpecSym(string $specSym): self
	{
		$this->specSym = $specSym;
		return $this;
	}


	public function setMessage(string $message): self
	{
		$lines = 4;
		$maxLineLen = 35;
		$message = substr($message, 0, $lines * $maxLineLen);
		$this->message = rtrim(chunk_split($message, $maxLineLen, '|'), '| ');
		return $this;
	}


	/**
	 * @param boolean $supressNumber if the destination number is in the group header
	 * @return string
	 */
	public function generate(bool $supressNumber = true): string
	{
		$res = '';
		if (!$supressNumber) {
			$res .= Abo::formatAccountNumber($this->destAccount, $this->destAccountPrefix) . ' ';
		}
		$res .= sprintf("%s %d %s %s%04d ", Abo::formatAccountNumber($this->accountNumber, $this->accountPrefix), $this->amount, $this->varSym, $this->bankCode, $this->constSym);

		$res .= $this->specSym . ' ';
		$res .= $this->message ? ('AV:' . $this->message) : '';
		$res .= "\r\n";

		return $res;
	}


}