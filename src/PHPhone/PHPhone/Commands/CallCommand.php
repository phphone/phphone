<?php
/**
 * User: Mike Knooihuisen
 * @version 11/13/18
 */

namespace PHPhone\PHPhone\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CallCommand extends Command
{
	protected function configure()
	{
		$this->setName('make:call')
			->setDescription("Make a new outgoing call")
			->setHelp("Create a new outgoing call and dump it in the defined state")
			->addUsage("make:call <number> <stasis>@<state>");

		$this->addArgument("number", InputArgument::REQUIRED, "The number to call");
		$this->addArgument("state", InputOption::VALUE_REQUIRED, "The stasis and state the call is placed into.", "default@start");
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// ...
	}
}