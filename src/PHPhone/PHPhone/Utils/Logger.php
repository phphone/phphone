<?php
/**
 * User: Mike Knooihuisen
 * @version 11/13/18
 */

namespace PHPhone\PHPhone\Utils;


use PHPhone\Core\BaseLogger;
use \Zend\Log\Logger as ZLogger;
use Zend\Log\Writer\Stream;

class Logger extends BaseLogger
{
	private $logger;

	public function setup()
	{
		$logger = new ZLogger();
		$console = new Stream('php://output');
		$logger->addWriter(new Stream("phone.log"));

		$logger->addWriter($console);

		$this->logger = $logger;
	}

	public function log($level, $msg)
	{
		$level = ($level != null)? $level : ZLogger::INFO;

		if($msg != null) {
			$this->logger->log($level, $msg);
		}
		return $this;
	}
}