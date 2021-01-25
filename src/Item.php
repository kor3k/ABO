<?php

namespace snoblucha\Abo;

class Item
{
	private int $amount;
	private string $variable_sym;
	private string $spec_sym;
	private string $const_sym;
	private string $bank = '';
	private string $account_number = '';
	private string $account_pre = '';
	private string $dest_account = '';
	private string $dest_account_pre = '';
	private string $message = '';


	public function __construct(string $full_account_number, float $amount, string $variable_sym)
	{
		$this->setAmount($amount)->setAccount($full_account_number)->setVarSym($variable_sym);
	}


	/**
	 * Set the amount to transfer
	 * @param float $float
	 * @param boolean $halere amount is in halere
	 */
	public function setAmount(float $amount, bool $halere = false): self
	{
		if (!$halere) {
			$amount *= 100;
		}
		$this->amount = intval($amount);
		return $this;
	}


	public function getAmount()
	{
		return $this->amount;
	}


	/**
	 * @param string $account - account in format (xxxx-)xxxxxxxx/xxxx
	 */
	public function setAccount(string $account): self
	{
		$account = explode('/', $account);
		$this->bank = $account[1];
		if (strpos($account[0], '-') !== false) {
			$number = explode('-', $account[0]);
			$this->account_pre = $number[0];
			$this->account_number = $number[1];
		} else {
			$this->account_number = $account[0];
		}

		return $this;
	}


	/**
	 * @param string $account in format (xxxx-)xxxxxx/xxxx
	 */
	public function setDestAccount(string $account): self
	{
		$account = explode('/', $account);
		//$this->bank = $account[1]; //ba
		if (strpos($account[0], '-') !== false) {
			$number = explode('-', $account[0]);
			$this->dest_account_pre = $number[0];
			$this->dest_account = $number[1];
		} else {
			$this->account_number = $account[0];
		}

		return $this;
	}


	public function setVarSym(string $varSym): self
	{
		$this->variable_sym = $varSym;
		return $this;
	}


	public function setConstSym(string $constSym): self
	{
		$this->const_sym = $constSym;
		return $this;
	}


	public function setSpecSym(string $specSym): self
	{
		$this->spec_sym = $specSym;
		return $this;
	}


	public function setMessage(/* string */$message): self
	{
		if (is_array($message)) {
			$message = implode(' AV|', $message);
		}
		$this->message = $message;
		return $this;
	}


	/**
	 * @param boolean $supress_number if the destination number is in the group header
	 * @param string $senderBank
	 * @return string
	 */
	public function generate(bool $supress_number = true, string $senderBank = ''): string
	{
		$res = '';
		if (!$supress_number) {
			$res .= Abo::account($this->dest_account, $this->dest_account_pre) . ' ';
		}
		$res .= sprintf("%s %d %s %s%04d ", Abo::account($this->account_number, $this->account_pre), $this->amount, $this->variable_sym, $this->bank, $this->const_sym);

		$res .= (strlen($this->spec_sym) ? $this->spec_sym : ($senderBank == '6800' ? '' : ' ')) . ' ';
		$res .= ($this->message ? substr('AV:' . $this->message, 0, 38) : ' ');
		$res .= "\r\n";

		return $res;
	}


}