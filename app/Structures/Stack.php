<?php

namespace App\Structures;

class Stack
{
	public $Items = [];
	private $Maximum = null;

	public function __construct($Maximum = null)
	{
		$this->Maximum = $Maximum;
	}

	public function Push($obj)
	{
		if ($this->IsFull()) {
			throw new \Exception('Tried to push to a full stack!');
		}
		array_push($this->Items, $obj);
	}

	public function Pop()
	{
		if ($this->IsEmpty()) {
			return null;
		}

		$val = $this->Peek();

		array_pop($this->Items);

		return $val;
	}

	public function Peek()
	{
		return $this->Items[count($this->Items) - 1];
	}

	public function IsEmpty()
	{
		return empty($this->Items);
	}

	public function IsFull()
	{
		return $this->Maximum !== null && count($this->Items) >= $this->Maximum;
	}
}
