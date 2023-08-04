<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Service\OmdbApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoviePosterCommand extends Command
{
    protected static $defaultName = 'app:movies:poster';
    protected static $defaultDescription = 'Get movies posters from omdbapi.com';

    private $movieRepository;
    private $omdbApi;
    private $em;

    public function __construct(
        MovieRepository $movieRepository,
        OmdbApi $omdbApi,
        EntityManagerInterface $em
    )
    {
        $this->movieRepository = $movieRepository;
        $this->omdbApi = $omdbApi;
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Permet un affichage trop stylé dans le terminal
        $io = new SymfonyStyle($input, $output);

        $io->info('Mise à jour des posters');

        // Récupérer tous les films (via MovieRepository)
        $movies = $this->movieRepository->findAll();

        foreach($movies as $movie) {

            $io->info($movie->getTitle());

            $moviePoster = $this->omdbApi->fetch($movie->getTitle());

            if (!$moviePoster) {
                $io->warning('Poster de film non trouvé');
            } else {
                $movie->setPoster($moviePoster['Poster']);
            }
        }

        $this->em->flush();

        $io->success('Posters mis à jour');

        return Command::SUCCESS;
    }
}