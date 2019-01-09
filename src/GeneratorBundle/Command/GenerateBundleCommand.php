<?php

/**
 * Active Publishing - All right reserved
 *   Full copyright and license information is available in
 *   LICENSE.md which is distributed with this source code.
 *
 * @copyright Copyright (c) Active Publishing (https://activepublishing.fr)
 * @license Creative Common CC BY NC-ND 4.0
 * @author Active Publishing <contact@active-publishing.fr>
 */

declare(strict_types=1);

namespace ActivePublishing\GeneratorBundle\Command;

use GenerateBundle\Command\Helper\QuestionHelper;
use GenerateBundle\Generator\BundleGenerator;
use GenerateBundle\Model\Bundle;
use Sensio\Bundle\GeneratorBundle\Command\GenerateBundleCommand as BaseGenerateBundleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Command\Command;


class GenerateBundleCommand extends BaseGenerateBundleCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('activepublishing:generate:bundle')
            ->setDescription('Generates an active publishing bundle')
            ;
    }

    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $dirs = parent::getSkeletonDirs($bundle);

        array_unshift($dirs, __DIR__ . '/../Resources/skeleton');

        return $dirs;
    }

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $input->setOption('format', 'annotation');

        parent::initialize($input, $output);
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When namespace doesn't end with Bundle
     * @throws \RuntimeException         When bundle can't be executed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();

        $bundle = $this->createBundleObject($input);
        $bundle->setTestsDirectory($bundle->getTargetDirectory() . '/Tests');

        $questionHelper->writeSection($output, '- - - - - - Bundle generation - - - - - -');


        /** @var BundleGenerator $generator */
        $generator = $this->getGenerator();

        $output->writeln(sprintf(
            '> Generating an active publishing bundle skeleton into <info>%s</info>',
            $this->makePathRelative($bundle->getTargetDirectory())
        ));

        $generator->generateBundle($bundle);

        $errors = [];
        $fs = $this->getContainer()->get('filesystem');

        try {
            $fs->remove($bundle->getTestsDirectory());
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        $questionHelper->writeGeneratorSummary($output, $errors);
    }

    protected function getQuestionHelper()
    {
        $question = $this->getHelperSet()->get('question');
        if (!$question || get_class($question) !== QuestionHelper::class) {
            $this->getHelperSet()->set($question = new QuestionHelper());
        }

        return $question;
    }

    protected function createBundleObject(InputInterface $input)
    {
        $bundle = parent::createBundleObject($input);

        return new Bundle($bundle);
    }

    protected function createGenerator()
    {
        return new BundleGenerator($this->getContainer()->get('filesystem'));
    }
}
