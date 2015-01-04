<?php namespace Groupeat\Documentation\Services;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateApiDocumentation {

    const PATH = '_generated-docs.html';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param OutputInterface $output
     */
    public function call(OutputInterface $output = null)
    {
        $docContent = '';

        foreach (listGroupeatPackages() as $package)
        {
            $docPath = realpath(workbench_path($package, '../docs/api.md'));

            if ($this->filesystem->exists($docPath))
            {
                $docContent .= $this->filesystem->get($docPath);
            }
        }

        $inputPath = $this->getInputPath();
        $outputPath = $this->getOutputPath();

        $this->filesystem->put($inputPath, $docContent);

        $command = "aglio -t flatly -i $inputPath -o $outputPath";

        processAtProjectRoot($command, $output);
    }

    /**
     * @return string
     */
    public function getHTML()
    {
        $path = $this->getOutputPath();

        if (!$this->filesystem->exists($path))
        {
            $this->call();
        }

        return $this->filesystem->get($path);
    }

    private function getInputPath()
    {
        return $this->getPathFor('md');
    }

    private function getOutputPath()
    {
        return $this->getPathFor('html');
    }

    private function getPathFor($extension)
    {
        return workbench_path('documentation', "generated/documentation.$extension");
    }

}
