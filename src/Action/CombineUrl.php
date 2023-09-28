<?php

namespace FaimMedia\StaticBuilder\Action;

use FaimMedia\StaticBuilder\Action\AbstractAction;

use Curl\{
	MultiCurl,
	Curl,
};

/**
 * Combine multiple urls and execute
 */
class CombineUrl extends AbstractAction implements ActionInterface
{
	protected $encoding;
	protected $hostname;
	protected $path;
	protected $rateLimit;

	protected MultiCurl $_multi;
	protected array $_urls = [];

	/**
	 * Constructor
	 */
	public function __construct(array $options = [], Url ...$urls)
	{
		parent::__construct($options);

		$this->_multi = new MultiCurl();

		if ($this->rateLimit && $this->rateLimit !== 1) {
			$this->_multi->setRateLimit($this->rateLimit);
		}

		foreach ($urls as $url) {
			$this->addUrl($url);
		}
	}

	/**
	 * Add url
	 */
	public function addUrl(Url ...$url)
	{
		foreach ($url as $u) {
			$this->_urls[] = $u;
		}
	}

	/**
	 * Execute
	 */
	public function execute(?bool $start = null): mixed
	{
		foreach ($this->_urls as $url) {
			$encoding = $this->encoding ?? [];
			if (!is_array($encoding)) {
				$encoding = [$encoding ?? null];
			}

			if (!in_array('', $encoding, true)) {
				array_unshift($encoding, '');
			}

			foreach ($encoding as $enc) {
				$url = clone $url;

				$url->setOptions([
					'hostname' => $this->hostname,
					'port'     => $this->port,
					'encoding' => $enc,
					'target'   => rtrim($this->target, '/') . '/' .
						($this->path ? rtrim($this->path, '/') . '/' : ''),
				]);

				$curl = $url->execute(false);
				if (!($curl instanceof Curl)) {
					throw new Exception('Url action should return `' . Curl::class . '` for combining');
				}

				if ($this->rateLimit === 1) {
					$curl->exec();
				} else {
					$this->_multi->addCurl($curl);
				}
			}
		}

		if ($this->rateLimit !== 1) {
			$this->_multi->start();
		}

		return true;
	}

	/**
	 * Destruct
	 */
	public function __destruct()
	{
		$this->_multi?->stop();
	}
}
