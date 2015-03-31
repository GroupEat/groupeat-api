<?php
namespace Groupeat\Admin\Services;

use GuzzleHttp\Client;
use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateAdminerFiles
{
    private $client;
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->client = new Client;
        $this->filesystem = $filesystem;
    }

    public function call(OutputInterface $output = null)
    {
        $output->writeln("Listing available versions");

        $response = $this->client->get('https://api.github.com/repos/vrana/adminer/tags');

        $latestVersion = collect(json_decode($response->getBody(), true))
            ->map(function ($tag) {
                return trim($tag['name'], 'v');
            })
            ->sortByDesc(null)
            ->first();

        $output->writeln("Latest version found: $latestVersion");
        $output->writeln("Downloading PHP file");

        $this->client->get("http://downloads.sourceforge.net/adminer/adminer-$latestVersion-en.php", [
            'save_to' => storage_path('app/adminer.php'),
        ]);

        $output->writeln("Downloading CSS file");

        $css = (string) $this->client
            ->get('https://raw.github.com/vrana/adminer/master/designs/pappu687/adminer.css')
            ->getBody();

        $output->writeln("Applying GroupEat theme");

        $css = str_replace('#34495e', '#e74c3c', $css);
        $css = str_replace(['#48A5BF', '#65ADC3', 'rgb(85, 112, 139)', '#2980b9'], '#e67e22', $css);

        $output->writeln("Saving CSS file");

        $this->filesystem->put('adminer.css', $css);
    }
}
