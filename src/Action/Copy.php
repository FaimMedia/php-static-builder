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
		$path = rtrim($this->path ?? '', '/') . '/';
		$to = $this->target . $path;

		if ($path && !file_exists($to)) {
			mkdir($to, 0775, true);
		}

		echo 'Copy' . PHP_EOL;
		echo ' - ' . $this->source . ' -> ' . $to . PHP_EOL;

		if (!empty($this->ignore)) {
			exec('which rsync', result_code: $code);
			if ($code) {
				throw new Exception('Using the `ignore` argument requires `rsync` to be installed');
			}

			$base = basename($this->source);
			$exclude = array_map(function (string $dir) use ($base): string {
				return '--exclude=' . escapeshellarg('/' . $base . '/' . $dir . '/');
			}, $this->ignore);

			$cmd = 'rsync -av ' . join(' ', $exclude)
				. ' ' . escapeshellarg($this->source) . ' ' . escapeshellarg($to);

			$this->exec($cmd);

			return true;
		}

		$this->exec('cp -R ' . escapeshellarg($this->source) . ' ' . escapeshellarg($to));

		return true;
	}

	/**
	 * Run command and check exit code
	 */
	protected function exec(string $cmd): void
	{
		exec($cmd, $output, $code);

		if ($code !== 0) {
			throw new Exception("Error occured on copy:\r\n" . join("\r\n", $output));
		}
	}
}
