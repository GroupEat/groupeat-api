<?php
namespace Groupeat\Documentation\Services;

use Groupeat\Documentation\Values\OrderedPackages;
use Groupeat\Support\Values\Environment;
use Illuminate\Contracts\Config\Repository;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateApiDocumentation
{
    private $config;
    private $orderedPackages;
    private $isLocal;

    public function __construct(
        Repository $config,
        OrderedPackages $orderedPackages,
        Environment $environment
    ) {
        $this->config = $config;
        $this->orderedPackages = collect($orderedPackages->value());
        $this->isLocal = $environment->isLocal();
    }

    /**
     * @param OutputInterface $output
     *
     * @return string Error output
     */
    public function call(OutputInterface $output = null)
    {
        $docContent = $this->orderedPackages
            ->filter(function ($package) {
                $path = $this->getDiskPathFor($package);

                return file_exists($path);
            })
            ->map(function ($package) {
                $path = $this->getIncludePathFor($package);

                return "<!-- include($path) -->";
            })
            ->implode("\n");

        $inputPath = $this->getInputPath();
        $outputPath = $this->getOutputPath();

        file_put_contents($inputPath, $docContent);

        $command = "aglio -t flatly --full-width -i $inputPath -o $outputPath";

        $status = process($command, $output)->getErrorOutput();

        $html = file_get_contents($outputPath);
        $html = $this->parseConfig($html);

        file_put_contents($outputPath, $html);

        return $status;
    }

    /**
     * @return string
     */
    public function getHTML()
    {
        $path = $this->getOutputPath();

        if ($this->isLocal || !file_exists($path)) {
            $errorOutput = $this->call();

            if ($errorOutput) {
                return $errorOutput;
            }
        }

        return file_get_contents($path);
    }

    private function parseConfig($doc)
    {
        return preg_replace_callback("/\{\{([^\}]*)\}\}/", function ($match) {
            return $this->config->get(trim($match[1]));
        }, $doc);
    }

    private function getDiskPathFor($package)
    {
        return app_path($package.'/docs/main.md');
    }

    private function getIncludePathFor($package)
    {
        return "../../app/$package/docs/main.md";
    }

    private function getInputPath()
    {
        return $this->getPathForExtension('md');
    }

    private function getOutputPath()
    {
        return $this->getPathForExtension('html');
    }

    private function getPathForExtension($extension)
    {
        return storage_path("app/documentation.$extension");
    }
}
