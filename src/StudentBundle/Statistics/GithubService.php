<?php

namespace StudentBundle\Statistics;

use StudentBundle\Entity\Team;
use Doctrine\ORM\EntityManager;

class GithubService
{
    private $em;
    private $clientId;
    private $clientSecret;
    
    public function __construct(EntityManager $em, string $clientId, string $clientSecret)
    {
        $this->em = $em;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }
    
    public function getStats(int $teamId): array
    {
        $teamRepository = $this->em->getRepository(Team::class);
        $githubUrl = $teamRepository->find($teamId)
            ->getGithubRepository()
        ;
        
        if (!preg_match('/([^\/]+)\/([^\/]+)$/', $githubUrl, $matches)) {
            throw new \Exception('Url github invalide.');
        }
        
        $user = $matches[1];
        $repositoryName = $matches[2];
        $clientId = $this->clientId;
        $clientSecret = $this->clientSecret;
        
        $apiUrl = sprintf(
            'https://api.github.com/repos/%s/%s/stats/contributors?client_id=%s&client_secret=%s',
            $user,
            $repositoryName,
            $clientId,
            $clientSecret
        );
        
        $client = new \GuzzleHttp\Client();
        $json = $client->request('GET', $apiUrl)
            ->getBody()
            ->getContents()
        ;
        
        $data = json_decode($json, true);
        
        return $data;
    }
    
    public function getRandomCommits(int $teamId): array
    {
        $teamRepository = $this->em->getRepository(Team::class);
        $githubUrl = $teamRepository->find($teamId)
            ->getGithubRepository()
        ;
        
        if (!preg_match('/([^\/]+)\/([^\/]+)$/', $githubUrl, $matches)) {
            throw new \Exception('Url github invalide.');
        }
        
        $user = $matches[1];
        $repositoryName = $matches[2];
        $clientId = $this->clientId;
        $clientSecret = $this->clientSecret;
        
        $apiUrl = sprintf(
            'https://api.github.com/repos/%s/%s/commits?client_id=%s&client_secret=%s',
            $user,
            $repositoryName,
            $clientId,
            $clientSecret
        );
        
        $client = new \GuzzleHttp\Client();
        $json = $client->request('GET', $apiUrl)
            ->getBody()
            ->getContents()
        ;
        
        $data = json_decode($json, true);
        shuffle($data);
        
        return $data;
    }
}
