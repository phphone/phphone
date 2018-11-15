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

class NewStateCommand extends Command
{
	protected function configure()
	{
		$this->setName('make:state')
			 ->setDescription("Generates a new state from a template.")
			 ->setHelp("Easily create new states for use in a PHPhone application");

		$this->addArgument("name", InputArgument::REQUIRED, "The name of the new state.");
		$this->addOption("stasis", "s", InputOption::VALUE_OPTIONAL, "Attach new state to the given stasis program, if multiple.");
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
	}
}