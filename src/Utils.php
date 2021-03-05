<?php
declare(strict_types=1);

namespace snoblucha\Abo;

use InvalidArgumentException;
use function Symfony\Component\String\u;

class Utils
{

	public static function formatAccountNumber(string $number, string $prefix = null): string
	{
		$res = '';
		if ($prefix) {
			if (!is_numeric($prefix) || strlen($prefix) > 6) {
				throw new InvalidArgumentException('Parameter $prefix must be numeric string of max length 6!');
			}
			$res .= $prefix . '-';
		}
		if (!is_numeric($number) || strlen($number) > 10) {
			throw new InvalidArgumentException('Parameter $number must be numeric string of max length 10!');
		}

//		$res .= sprintf('%010d', $number);
		$res .= $number;
		return $res;
	}


	public static function toAscii(string $s): string
	{
		return u($s)->ascii()->toString();
	}


}