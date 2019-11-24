<?php

namespace WPEmergeMagic\Tasks\Vue;

use WPEmergeMagic\Support\App;
use WPEmergeMagic\Support\Path;
use WPEmergeMagic\Support\CreatePath;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use WPEmergeMagic\Exceptions\TaskFailedException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallVuePackageTask
{
    protected $packageManager = 'npm';

    /**
     * An array of dependencies to install
     *
     * @var array
     */
    protected $dependencies = [];

    /**
     * An array of dev dependencies to install
     *
     * @var array
     */
    protected $devDependencies = [];

    public function handle(InputInterface $input, OutputInterface $output, Command $command)
    {
        $output->writeln('Installing Vue...');

        $this->checkIfWeHavePackageFile();

        if (App::isOnTestMode()) {
            return;
        }

        $this->checkForYarnSupport();
        $this->assertDependenciesToInstall();
        $this->installDevDependencies();
        $this->installDependencies();
    }

    public function assertDependenciesToInstall(): void
    {
        $packageJsonContents = json_decode(file_get_contents($this->getPackageJsonFilePath()), true);

        if (!array_key_exists('dependencies', $packageJsonContents)) {
            $this->dependencies = $this->getAllDependencies();
        }

        if (!array_key_exists('devDependencies', $packageJsonContents)) {
            $this->devDependencies = $this->getAllDevDependencies();
        }

        // if we alreadu populated dependencies and dev dependencies
        // do not go further
        if ($this->dependencies && $this->devDependencies) {
            return;
        }

        if ($this->dependencies) {
            $this->populateDependencies($packageJsonContents['dependencies']);
        }

        if ($this->devDependencies) {
            $this->populateDevDependencies($packageJsonContents['devDependencies']);
        }
    }

    protected function populateDependencies(array $dependenciesArray): void
    {
        foreach ($this->getAllDependencies() as $dependencyName) {
            if (!array_key_exists($dependencyName, $dependenciesArray)) {
                $this->dependencies[] = $dependencyName;
            }
        }
    }

    protected function populateDevDependencies(array $devDependenciesArray): void
    {
        foreach ($this->getAllDevDependencies() as $devDependencyName) {
            if (!array_key_exists($devDependencyName, $devDependenciesArray)) {
                $this->devDependencies[] = $devDependencyName;
            }
        }
    }

    public function installDependencies(): void
    {
        $installDependenciesProcess = new Process(array_merge([
            $this->packageManager,
            $this->isUsingYarn() ? 'add' : 'install',
        ], $this->dependencies));

        $installDependenciesProcess->setTimeout(600);
        $installDependenciesProcess->run();

        // executes after the command finishes
        if (!$installDependenciesProcess->isSuccessful()) {
            throw new TaskFailedException('Couldn\'t install Vue dependencies!');
        }
    }

    public function installDevDependencies(): void
    {
        $installDevDependencies = new Process(array_merge([
            $this->packageManager,
            $this->isUsingYarn() ? 'add' : 'install',
            $this->isUsingYarn() ? '--dev' : '--save-dev',
        ], $this->devDependencies));

        $installDevDependencies->setTimeout(600);
        $installDevDependencies->run();

        // executes after the command finishes
        if (!$installDevDependencies->isSuccessful()) {
            throw new TaskFailedException('Couldn\'t install development dependencies!');
        }
    }

    public function checkIfWeHavePackageFile()
    {
        $doesPackageJsonFileExist = file_exists($this->getPackageJsonFilePath());

        if (App::isOnTestMode()) {
            !$doesPackageJsonFileExist && (new Filesystem())->dumpFile($this->getPackageJsonFilePath(), $this->getTestPackageJsonData());

            return;
        }

        if (!$doesPackageJsonFileExist) {
            $npmInitProcess = new Process([
                $this->packageManager,
                'init',
                '-y',
            ]);

            $npmInitProcess->run();

            // executes after the command finishes
            if (!$npmInitProcess->isSuccessful()) {
                throw new TaskFailedException('Couldn\'t init Composer!');
            }
        }
    }

    protected function getTestPackageJsonData(): string
    {
        $composerData = [
            'name' => 'wpemerge-magic',
            'version' => '1.0.0',
            'description' => '---',
            'main' => 'index.js',
            'directories' => [
                'test' => 'tests',
            ],
            'scripts' => [
                'test' => 'echo \'Error: no test specified\' && exit 1',
            ],
            'license' => 'ISC',
            'dependencies' => $this->getAllDependencies(true),
            'devDependencies' => $this->getAllDevDependencies(true),
        ];

        return json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    protected function getPackageJsonFilePath(): string
    {
        return (new CreatePath)->create(Path::getCurrentWorkingDirectory(), [
            'package.json',
        ], false);
    }

    protected function getAllDependencies(bool $withVersions = false): array
    {
        if ($withVersions) {
            return [
                'vue' => '*',
            ];
        }

        return [
            'vue',
        ];
    }

    protected function getAllDevDependencies(bool $withVersions = false): array
    {
        if ($withVersions) {
            return [
                'webpack' => '*',
                'webpack-cli' => '*',
                'vue-loader' => '*',
            ];
        }

        return [
            'webpack',
            'webpack-cli',
            'vue-loader',
        ];
    }

    protected function checkForYarnSupport()
    {
        $yarnLockFile = (new CreatePath)->create(Path::getCurrentWorkingDirectory(), [
            'yarn.lock',
        ], false);

        if (file_exists($yarnLockFile)) {
            $this->packageManager = 'yarn';
        }
    }

    protected function isUsingYarn(): string
    {
        return $this->packageManager === 'yarn';
    }
}
