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

namespace ActivePublishing\GeneratorBundle\Command\Helper;

use Symfony\Component\Console\Output\OutputInterface;

class QuestionHelper extends \Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper
{
    public function writeSection(OutputInterface $output, $text, $style = 'bg=magenta;fg=white')
    {
        $text = str_replace('Symfony bundle generator', 'Active Publishing bundle generator', $text);

        parent::writeSection($output, $text, $style);
    }

    public function writeGeneratorSummary(OutputInterface $output, $errors)
    {
        if (!$errors) {
            $this->writeSection($output, 'Everything is ok! Now get to work ;).');
        } else {
            $this->writeSection($output, array(
                'The command was not able to configure everything automatically.',
                'You\'ll need to make the following changes manually.',
            ), 'error');

            $output->writeln($errors);
        }
    }

}
