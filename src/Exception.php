<?php

namespace FaimMedia\StaticBuilder;

use Exception as BaseException;
use Throwable;

/**
 * Exception
 */
class Exception extends BaseException implements Throwable
{
	/**
	 * Could not start webserver
	 */
	public const ERROR_WEBSERVER = -1;

	/**
	 * PHP executable is not available
	 */
	public const MISSING_PHP = -2;

	/**
	 * Invalid option
	 */
	public const INVALID_OPTION = -3;

	/**
	 * Invalid target
	 */
	public const INVALID_TARGET = -4;
}
