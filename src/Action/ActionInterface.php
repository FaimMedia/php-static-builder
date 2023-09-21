<?php

namespace FaimMedia\StaticBuilder\Action;

/**
 * Action interface
 */
interface ActionInterface
{
	/**
	 * Construct
	 */
	public function __construct(array $options = []);

	/**
	 * Set options
	 */
	public function setOptions(array $options): self;

	/**
	 * Execute
	 */
	public function execute(?bool $start = null): mixed;
}
