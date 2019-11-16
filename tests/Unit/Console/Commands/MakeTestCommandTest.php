<?php

namespace Tests\Unit\Console\Commands;

use Tests\CommandTestCase;

class MakeTestCommandTest extends CommandTestCase
{
    /** @test */
    public function it_creates_a_tests_folder()
    {
        $this->runCommand('make:test', [
            'name' => 'TestingTest',
        ]);
        $this->assertTestFileExists(['tests']);
    }

    /** @test */
    public function it_creates_a_test_file_in_unit_folder()
    {
        $this->runCommand('make:test', [
            'name' => 'TestingTest',
        ]);
        $this->assertTestFileExists(['tests', 'Unit', 'TestingTest.php']);
    }

    /** @test */
    public function it_can_create_a_test_with_namespace()
    {
        $this->runCommand('make:test', [
            'name' => 'TestingTest',
            '--namespace' => 'Testing/Test/InTest',
        ]);

        $testFilePath = [
            'tests',
            'Unit',
            'Testing',
            'Test',
            'InTest',
            'TestingTest.php',
        ];

        $this->assertTestFileExists($testFilePath);

        $testFileContents = file_get_contents($this->getTestFilePath($testFilePath));
        $namespaceMatched = preg_match('~Tests\\\Unit\\\Testing\\\Test\\\InTest~', $testFileContents);

        if (!$namespaceMatched) {
            $this->fail('Namespace couldn\'t be found in test file.');

            return;
        }

        $this->assertTrue(true);
    }
}
