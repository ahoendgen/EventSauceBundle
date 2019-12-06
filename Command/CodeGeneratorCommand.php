<?php
declare(strict_types=1);

namespace Jphooiveld\Bundle\EventSauceBundle\Command;

use EventSauce\EventSourcing\CodeGeneration\CodeDumper;
use EventSauce\EventSourcing\CodeGeneration\YamlDefinitionLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CodeGeneratorCommand extends Command
{
    protected static $defaultName = 'eventsauce:code-generator';

    protected function configure() : void
    {
        $this
            ->setDescription('Generate code based on given yaml file')
            ->addArgument('definitionFile', InputArgument::REQUIRED, 'Path to yaml file which should get parsed')
            ->addArgument('outputDirectory', InputArgument::REQUIRED, 'Where shall we write the generated files?');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io        = new SymfonyStyle($input, $output);
        $yamlPath  = $input->getArgument('definitionFile');
        $outputDir = $input->getArgument('outputDirectory');

        $loader  = new YamlDefinitionLoader();
        $dumper  = new CodeDumper();
        $phpCode = $dumper->dump($loader->load($yamlPath));

        foreach ($phpCode as $index => $item) {
            if (!is_dir($outputDir . DIRECTORY_SEPARATOR . $item['type'])) {
                mkdir($outputDir . DIRECTORY_SEPARATOR . $item['type']);
            }

            file_put_contents($outputDir . DIRECTORY_SEPARATOR . $item['type'] . DIRECTORY_SEPARATOR . $item['name'] . '.php', $item['code']);
        }

        return 0;
    }
}
