<?php

namespace FaimMedia\StaticBuilder\Action;

use FaimMedia\StaticBuilder\Action\Exception;

use Curl\Curl;

/**
 * Url Action class
 */
class Url extends AbstractAction implements ActionInterface
{
	protected $url;
	protected $hostname;
	protected $encoding;
	protected $filename;
	protected $save = true;
	protected $expect = 200;

	/**
	 * Get encoding
	 */
	public function getEncoding(): ?string
	{
		return $this->encoding;
	}

	/**
	 * Set encoding
	 */
	public function setEncoding(?string $encoding = null): self
	{
		$this->encoding = $encoding;

		return $this;
	}

	/**
	 * Get hostname
	 */
	public function getHostname(): ?string
	{
		return $this->hostname;
	}

	/**
	 * Set encoding
	 */
	public function setHostname(?string $hostname = null): self
	{
		$this->hostname = $hostname;

		return $this;
	}

	/**
	 * Execute
	 */
	public function execute(?bool $start = true): Curl
	{
		$curl = new Curl();

		if ($this->hostname) {
			$curl->setHeader('Host', $this->hostname);
		}
		if ($this->encoding) {
			$curl->setHeader('Accept-Encoding', $this->encoding);
		}

		$curl->setUrl('http://127.0.0.1:' . $this->port . '/' . $this->url);
		$curl->complete(function (Curl $curl): void {
			$statusCode = $curl->getHttpStatusCode();

			echo ' - Completed ' . $this->url . ' [response code: ' . $statusCode . ']' . PHP_EOL;

			if ($statusCode !== $this->expect) {
				throw new Exception('Invalid status code returned, expected ' . $this->expect . ', got ' . $statusCode, Exception::UNEXPECTED_STATUS_CODE);
			}

			$response = $curl->getResponse();

			if (!$response) {
				throw new Exception('Invalid response for `' . $this->url . '`', Exception::INVALID_RESPONSE);
			}

			if (!$this->save) {
				return;
			}

			$filename = basename($this->url);
			$path = $this->target . substr($this->url, 0, -strlen($filename));

			if ($path && !file_exists($path)) {
				@mkdir($path, 0775, true);
			}

			if (!$filename) {
				$filename = 'index';
			}

			if ($this->filename) {
				$filename = $this->filename;
			}

			if ($this->encoding) {
				$filename .= '.' . $this->encoding;
			}

			$extension = '.html';

			$contentType = explode(';', $curl->getResponseHeaders()['Content-Type'] ?? '')[0];
			switch ($contentType) {
				case 'text/xml':
					$extension = '.xml';
					break;
			}

			echo '   - Content-Type: ' . $contentType . PHP_EOL;
			echo '   - Saving file ' . $filename . $extension . PHP_EOL;

			$file = $path . $filename . $extension;

			$fopen = fopen($file, 'w');
			if (!$fopen) {
				throw new Exception('Could not save file: `' . $file . '`', Exception::ERROR_FOPEN);
			}
			fwrite($fopen, $response);
			fclose($fopen);
		});

		if ($start) {
			$curl->exec();
		}

		return $curl;
	}
}
