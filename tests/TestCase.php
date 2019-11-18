<?php

namespace Tests;

use Mockery;
use WPEmergeMagic\Support\CreatePath;
use Symfony\Component\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->generateTestFilesPath();
    }

    protected function assertTestFileExists(array $path)
    {
        if (!file_exists($fullPath = $this->getTestFilePath($path))) {
            $this->fail("File with path \"$fullPath\" doesn't exist!");

            return;
        }

        $this->assertTrue(true);
    }

    protected function assertTestDoesntFileExists(array $path)
    {
        if (file_exists($fullPath = $this->getTestFilePath($path))) {
            $this->fail("File with path \"$fullPath\" exists!");

            return;
        }

        $this->assertTrue(true);
    }

    protected function getTestFilePath(array $path)
    {
        return (new CreatePath())
            ->create(
                ROOT_TEST_DIR,
                array_merge(['test-files'], $path),
                false
            );
    }

    public function getTestFilePathContents(array $path): string
    {
        return file_get_contents($this->getTestFilePath($path));
    }

    /** @test */
    public function generateTestFilesPath()
    {
        (new Filesystem())
            ->mkdir(ROOT_TEST_DIR . DIRECTORY_SEPARATOR . 'test-files', 0777);
    }

    protected function mock()
    {
        return call_user_func_array([Mockery::class, 'mock'], func_get_args());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();

        (new Filesystem())
            ->remove(
                (new CreatePath())->create(ROOT_TEST_DIR, [
                    'test-files',
                ], false)
            );
    }
}
