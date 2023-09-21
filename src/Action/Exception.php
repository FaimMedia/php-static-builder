<?php

namespace FaimMedia\StaticBuilder\Action;

use FaimMedia\StaticBuilder\Exception as BaseException;

/**
 * Exception
 */
class Exception extends BaseException
{
	/**
	 * Invalid option specified
	 */
	public const INVALID_OPTION = -1;

	/**
	 * fopen error
	 */
	public const ERROR_FOPEN = -2;

	/**
	 * Invalid curl response
	 */
	public const INVALID_RESPONSE = -3;

	/**
	 * Unexpected curl http status code
	 */
	public const UNEXPECTED_STATUS_CODE = -4;
}
