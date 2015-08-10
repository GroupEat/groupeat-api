<?php
namespace Groupeat\Admin\Console;

use Groupeat\Support\Console\Abstracts\Command;
use GuzzleHttp\Client;
use Illuminate\Contracts\Filesystem\Filesystem;

class Adminer extends Command
{
    const THEME = 'pappu687';

    protected $signature = 'adminer';
    protected $description = "Generate the Adminer files to manage the DB";

    private $client;
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->client = new Client;
        $this->filesystem = $filesystem;
    }

    public function handle()
    {
        $this->line("Listing available versions");

        $response = $this->client->get('https://api.github.com/repos/vrana/adminer/tags');

        $latestVersion = collect(json_decode($response->getBody(), true))
            ->map(function ($tag) {
                return trim($tag['name'], 'v');
            })
            ->sortByDesc(null)
            ->first();

        $this->line("Latest version found: $latestVersion");
        $this->line("Downloading PHP file");

        $this->client->get("http://downloads.sourceforge.net/adminer/adminer-$latestVersion-en.php", [
            'save_to' => storage_path('app/adminer.php'),
        ]);

        $this->line("Downloading CSS file");

        $css = (string) $this->client
            ->get('https://raw.github.com/vrana/adminer/master/designs/' . static::THEME . '/adminer.css')
            ->getBody();

        $this->line("Applying GroupEat theme");

        $css = str_replace('#34495e', '#e74c3c', $css);
        $css = str_replace(['#48A5BF', '#65ADC3', 'rgb(85, 112, 139)', '#2980b9'], '#e67e22', $css);

        $this->line("Saving CSS file");

        $this->filesystem->put('adminer.css', $css);
    }
}
