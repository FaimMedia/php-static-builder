<?php

namespace FaimMedia\StaticBuilder;

use FaimMedia\StaticBuilder\Action\ActionInterface;

use FaimMedia\StaticBuilder\Exception;

/**
 * Build init
 */
class Build
{
	protected $_actions = [];
	protected $_pid;

	protected $target;
	protected $port = 9000;
	protected $router;
	protected $log = '/dev/null';

	/**
	 * Constructor
	 */
	public function __construct(array $options)
	{
		$vars = get_object_vars($this);
		foreach ($options as $option => $value) {
			if (substr($option, 0, 1) === '_' || !array_key_exists($option, $vars)) {
				throw new Exception('Invalid option `' . $option . '`', Exception::INVALID_OPTION);
			}

			$this->{$option} = $value;
		}

		if (!$this->target || !file_exists($this->target) || !is_dir($this->target)) {
			throw new Exception('Invalid target', Exception::INVALID_TARGET);
		}
	}

	/**
	 * Set actions
	 */
	public function setActions(ActionInterface ...$actions): self
	{
		foreach ($actions as $action) {
			$this->addAction($action);
		}

		return $this;
	}

	/**
	 * Add action
	 */
	public function addAction(ActionInterface $action)
	{
		$action->setOptions([
			'target' => $this->target,
			'port'   => $this->port,
		]);

		$this->_actions[] = $action;
	}

	/**
	 * Build
	 */
	public function build(): void
	{
		$php = trim(`which php`);
		if (!$php) {
			throw new Exception('PHP executable not available', Exception::MISSING_PHP);
		}

		echo 'Starting webserver' . PHP_EOL;

		$this->_pid = (int) exec('php -S ' . escapeshellarg('127.0.0.1:' . $this->port) . ' ' . escapeshellarg($this->router) . ' > ' . escapeshellarg($this->log) . ' 2>&1 & echo $!;', $output);

		sleep(1);

		if (!$this->_pid) {
			throw new Exception('Could not start webserver', Exception::ERROR_WEBSERVER);
		}

		echo 'Webserver started with PID ' . $this->_pid . PHP_EOL;

		foreach ($this->_actions as $action) {
			$action->execute();
		}
	}

	/**
	 * Get log
	 */
	public function getLog(): ?string
	{
		if (!$this->log || $this->log === '/dev/null') {
			throw new Exception('No log available', Exception::NO_LOG);
		}

		$file = $this->log;

		$fopen = fopen($file, 'r');
		$content = fread($fopen, filesize($file));
		fclose($fopen);

		return $content;
	}

	/**
	 * Destruct
	 */
	public function __destruct()
	{
		if (!$this->_pid) {
			return;
		}

		echo 'Killing webserver' . PHP_EOL;
		exec('kill -9 ' . $this->_pid);
		$this->_pid = null;
	}
}
