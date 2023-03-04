<?php

namespace Elfennol\SitemapGenerator\Command;

use Elfennol\SitemapGenerator\Extractor\Extractor;
use Elfennol\SitemapGenerator\Filter\FilterInterface;
use Elfennol\SitemapGenerator\Filter\Filters;
use Elfennol\SitemapGenerator\Generator\SitemapGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'generate-sitemap',
    description: 'Generate sitemap from urls.',
)]
class SitemapGeneratorCommand extends Command
{
    public function __construct(
        private readonly Extractor $extractor,
        /** @var FilterInterface[] */
        private readonly array $filters,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Urls source: *.txt or *.json. or *.xml',
            )
            ->addOption(
                'filters',
                'f',
                InputOption::VALUE_REQUIRED,
                'Filters separated with comma.',
            )
            ->addOption(
                'cookie',
                'c',
                InputOption::VALUE_REQUIRED,
                'Cookie string.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filterParams = [];
        if (isset($this->filters[Filters::REDIRECT->value]) && $input->getOption('cookie')) {
            $filterParams[Filters::REDIRECT->value]['cookie'] = $input->getOption('cookie');
            $this->filters[Filters::REDIRECT->value]->setParams($filterParams[Filters::REDIRECT->value]);
        }

        $urls = $this->extractor->extract($input->getArgument('source'));
        $filterNames = $input->getOption('filters');
        if ($filterNames) {
            foreach (explode(',', $filterNames) as $filterName) {
                if ($filterName && isset($this->filters[$filterName])) {
                    $filterResult = $this->filters[$filterName]->filter($urls);
                    $urls = $filterResult->getOk();
                    $urlsKo = $filterResult->getKo();
                    if ($urlsKo) {
                        $this->logger->error(json_encode($urlsKo, JSON_THROW_ON_ERROR), [
                            'filter' => $filterName
                        ]);
                    }
                }
            }
        }

        $output->write((new SitemapGenerator())->generate($urls));

        return Command::SUCCESS;
    }
}
