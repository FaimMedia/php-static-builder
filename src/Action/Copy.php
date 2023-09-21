<?php

namespace FaimMedia\StaticBuilder\Action;

/**
 * Copy Action class
 */
class Copy extends AbstractAction implements ActionInterface
{
	protected $source;
	protected $path;

	/**
	 * Execute
	 */
	public function execute(?bool $start = null): mixed
	{
		$path = $this->path ?? '';
		if ($path && substr($path, -1) !== '/') {
			$path .= '/';
		}

		$to = $this->target . $path;

		if ($path && !file_exists($to)) {
			mkdir($to, 0775, true);
		}

		echo 'Copy' . PHP_EOL;
		echo ' - ' . $this->source . ' -> ' . $to . PHP_EOL;

		exec('cp -R ' . escapeshellarg($this->source) . ' ' . escapeshellarg($to));

		return true;
	}
}
