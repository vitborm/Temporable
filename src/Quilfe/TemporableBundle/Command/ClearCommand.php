<?php
/**
 *  Copyright 2014 Vitaly Bormotov <bormvit@mail.ru>
 *
 *  This file is part of Quilfe Temporable.
 *
 *  Quilfe Temporable is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Quilfe Temporable is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Quilfe Temporable. If not, see <http://www.gnu.org/licenses/>.
**/
namespace Quilfe\TemporableBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Quilfe\TemporableBundle\Entity\Task;
use Quilfe\TemporableBundle\Entity\TimeUnit;

class ClearCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('clear');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

        $timeUnitRepository = $em->getRepository(TimeUnit::class);
        $taskRepository = $em->getRepository(Task::class);

        $output->writeln("Cleaning old time units...");
        $timeUnitRepository->clearOld();

        $output->writeln("Cleaning old completed tasks...");
        $taskRepository->clearOld();

        $output->writeln("Merging time units...");
        $timeUnitRepository->merge();

        $output->writeln("<info>Done!</info>");
    }
}
