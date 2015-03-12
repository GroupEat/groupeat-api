<?php
namespace Groupeat\Documentation\Services;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateApiDocumentation
{
    const PATH = '_generated-docs.html';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Repository
     */
    private $config;

    /**
     * @var array
     */
    private $orderedPackages;

    /**
     * @var bool
     */
    private $isLocal;

    /**
     * @param Filesystem $filesystem
     * @param Repository $config
     * @param array      $orderedPackages
     * @param bool       $isLocal
     */
    public function __construct(Filesystem $filesystem, Repository $config, array $orderedPackages, $isLocal)
    {
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->orderedPackages = $orderedPackages;
        $this->isLocal = $isLocal;
    }

    /**
     * @param OutputInterface $output
     *
     * @return string Error output
     */
    public function call(OutputInterface $output = null)
    {
        $docContent = $this->filesystem->get(__DIR__.'/../resources/api-docs-introduction.md');

        foreach ($this->orderedPackages as $package) {
            $paths = $this->getPathsForPackage($package);

            if ($this->filesystem->exists($paths['disk'])) {
                $packageDocContent = "\n<!-- include({$paths['include']}) -->\n";

                $docContent .= $packageDocContent;
            }
        }

        $inputPath = $this->getInputPath();
        $outputPath = $this->getOutputPath();

        $this->filesystem->put($inputPath, $docContent);

        $command = "aglio -t flatly --full-width -i $inputPath -o $outputPath";

        $status = process($command, $output)->getErrorOutput();

        $this->filesystem->put($outputPath, $this->parseConfig($this->filesystem->get($outputPath)));

        return $status;
    }

    /**
     * @return string
     */
    public function getHTML()
    {
        $path = $this->getOutputPath();

        if ($this->isLocal || !$this->filesystem->exists($path)) {
            $errorOutput = $this->call();

            if ($errorOutput) {
                return $errorOutput;
            }
        }

        return $this->filesystem->get($path);
    }

    private function parseConfig($doc)
    {
        return preg_replace_callback("/\{\{([^\}]*)\}\}/", function ($match) {
            return $this->config->get(trim($match[1]));
        }, $doc);
    }

    private function getPathsForPackage($package)
    {
        return [
            'disk' => realpath(app_path($package.'/docs/main.md')),
            'include' => "../../$package/docs/main.md",
        ];
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
        return app_path("Documentation/generated/documentation.$extension");
    }
}
