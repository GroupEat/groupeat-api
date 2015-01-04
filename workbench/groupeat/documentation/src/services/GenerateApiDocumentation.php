<?php namespace Groupeat\Documentation\Services;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateApiDocumentation {

    const PATH = '_generated-docs.html';

    /**
     * @var Filesystem
     */
    private $filesystem;


    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function call(OutputInterface $output = null)
    {
        $doc = '';

        foreach (listGroupeatPackages() as $package)
        {
            $docPath = realpath(workbench_path($package, '../docs/api.md'));

            if ($this->filesystem->exists($docPath))
            {
                $doc .= $this->filesystem->get($docPath);
            }
        }

        $tempPath = base_path('.tmp-api.md');

        $this->filesystem->put($tempPath, $doc);

        processAtProjectRoot("aglio -t flatly -i $tempPath -o ".public_path(static::PATH), $output);

        $this->filesystem->delete($tempPath);
    }

}
