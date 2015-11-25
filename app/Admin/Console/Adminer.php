<?php
namespace Groupeat\Admin\Console;

use Groupeat\Support\Console\Abstracts\Command;
use GuzzleHttp\Client;
use Illuminate\Contracts\Filesystem\Filesystem;

class Adminer extends Command
{
    const THEME = 'pappu687';
    const RED = '#ff4e50';
    const GREEN = '#23AF50';

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
        // TODO: use latest version (use git history)
        $latestVersion = '4.2.2';

        $this->line("Downloading PHP file");

        $this->client->get("http://downloads.sourceforge.net/adminer/adminer-$latestVersion-en.php", [
            'save_to' => storage_path('app/adminer.php'),
        ]);

        $this->line("Downloading CSS file");

        $css = (string) $this->client
            ->get('https://raw.github.com/vrana/adminer/master/designs/' . static::THEME . '/adminer.css')
            ->getBody();

        $this->line("Applying GroupEat theme");

        $css = str_replace('#34495e', static::RED, $css);
        $css = str_replace(['#48A5BF', '#65ADC3', 'rgb(85, 112, 139)', '#2980b9'], static::GREEN, $css);

        $this->line("Saving CSS file");

        $this->filesystem->put('adminer.css', $css);
    }
}
