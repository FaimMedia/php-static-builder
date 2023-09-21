<?php

namespace FaimMedia\StaticBuilder\Action;

use FaimMedia\StaticBuilder\Action\Exception;

/**
 * Abstract Action class
 */
abstract class AbstractAction implements ActionInterface
{
	protected $target;
	protected $port = 8000;
	protected $ignore = [];

	/**
	 * Construct
	 */
	public function __construct(array $options = [])
	{
		$this->setOptions($options);
	}

	/**
	 * Set options
	 */
	public function setOptions(array $options): self
	{
		$vars = get_object_vars($this);
		foreach ($options as $option => $value) {
			if (substr($option, 0, 1) === '_' || !array_key_exists($option, $vars)) {
				throw new Exception('Invalid option `' . $option . '`', Exception::INVALID_OPTION);
			}

			if ($value === null) {
				continue;
			}

			$this->{$option} = $value;
		}

		return $this;
	}
}
