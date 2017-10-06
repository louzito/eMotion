<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateReservationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:reservation:update')
            ->setDescription('Modifie le statut des réservations et envoie un mail pour tout retard.')
            ->setHelp('Modifie le statut des réservations et envoie un mail pour tout retard.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $offreS = $this->getContainer()->get('offre_service');
        $output->writeln([
            'Update des réservations en cours:',
        ]);
        $offreS->setEtatEnCoursReservation();
        $output->writeln([
            'Ok',
        ]);
        $output->writeln([
            'Update des réservations en retard:',
        ]);
        $offreS->setEtatEnRetardReservation();
        $output->writeln([
            'Ok',
        ]);

    }
}