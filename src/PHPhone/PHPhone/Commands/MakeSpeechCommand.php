<?php
/**
 * User: Mike Knooihuisen
 * @version 11/14/18
 */

namespace PHPhone\PHPhone\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeSpeechCommand extends Command
{

	protected function configure(){
	    $this->setName("make:speech")
			->setDescription("Generate a new speech resource")
			->setHelp("Create a new audio file and add it to asterisk");

	    $this->addArgument('name', InputArgument::REQUIRED, "The name of the new audio file.");
	    $this->addArgument('text', InputArgument::REQUIRED, "The text to include in the speech file.");

	    $this->addOption("service", 's', InputOption::VALUE_OPTIONAL, "The speech-to-text service to use", "poly");
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

	}

}