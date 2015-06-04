<?php namespace Kalephan\BodyClass;

class BodyClass {
	private $data = [];

	public function get() {
		return implode(' ', $this->data);
	}

	public function add($class) {
		if (!is_string($class)) {
			throw new \Exception("Body class must is a string.");
		}

		$this->data[] = $class;
	}

	public function set($classes) {
		if (is_string($classes)) {
			$classes = (array) $classes;
		}

		if (!is_array($classes)) {
			throw new \Exception("Body class must is an array.");
		}

		$this->data = $classes;
	}
}
