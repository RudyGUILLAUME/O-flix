<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OflixUserListCommand extends Command
{
    // Ces deux variables sont utilisées pour donner un nom a la commande
    // Ainsi qu'un message d'aide (php bin/console help commande)
    protected static $defaultName = 'oflix:user:list';
    protected static $defaultDescription = 'Display registered users of oflix app';

    private $ur;

    /**
     * Le constructeur doit etre utilisé pour les injections de dépendances
     * Il faut bien penser à appeler le constructeur parent
     *
     * @param UserRepository $ur
     */
    public function __construct(UserRepository $ur)
    {
        $this->ur = $ur;
        // Appel du constructeur de la Class parent
        parent::__construct();
    }

    /**
     * Cette méthode est appelée par symfony pour initialiser et configurer
     * notre commande
     * Les arguments nécessaires 
     * Ainsi que les options (--option)
     * 
     * php bin/console commande argument --option
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    /**
     * C'est dans cette méthode que les traitements effectifs de la commande
     * sont réalisés. 
     * Symfony mets à disposition beaucoup de méthodes pour peaufiner l'affichage
     * comme "Table" utilisé ici, voir la doc pour un détail sur ces fonctions
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Récup d'un argument (précédemment défini dans la méthode 'configure')
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        // Récup d'une option (précédemment défini dans la méthode 'configure')
        if ($input->getOption('force')) {
            $io->note(sprintf('You passed an option: --force'));
        }

        // Affichage sous forme structurée des mes users

        $table = new Table($output);

        $lines = [];

        foreach($this->ur->findAll() as $user) {
            $lines[] = [
                $user->getId(),
                $user->getEmail(),
                join(",", $user->getRoles()),
                $user->getUserIdentifier(),
                $user->getPassword()
            ];
        }

        $table
            ->setHeaders(['Id', 'Email', 'Roles', 'Identifieur', 'Password'])
            ->setRows($lines);

        // Affichage de la table
        $table->render();

        // $io->success permet d'afficher un message en vert
        // $io->error permet d'afficher un message en rouge

        //$io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        // Toujours retourner le status de la commande (SUCCESS ou ERROR)
        
        return Command::SUCCESS;
    }
}
