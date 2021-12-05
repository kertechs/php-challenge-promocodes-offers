<?php

namespace Tests\Command;

use Infrastructure\Actions\ValidatePromocode\ValidatePromocodeResponseJsonFormatter;
use Infrastructure\Ekwateur\EkwateurApi\EkwateurApiInMemoryConnector;
use Infrastructure\Ekwateur\EkwateurManager\EkwateurManager;
use Infrastructure\Symfony\Command\PromocodeValidateCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

class PromocodeValidateCommandTest extends TestCase
{
    private Application $application;
    private BufferedOutput $output;

    public function setUp(): void
    {
        parent::setUp();
        $ekwateurManager = new EkwateurManager(new EkwateurApiInMemoryConnector());

        $this->application = new Application();
        $this->application->add(new PromocodeValidateCommand(ekwateurManager: $ekwateurManager));
        $this->output = new BufferedOutput();
    }

    //Verify that a non expired promocode with no associated offer can't be validated
    public function testPromocodeWoody2ShouldNotBeValid()
    {
        $input = new ArgvInput(['phpunit-test', 'promo-code:validate', 'WOODY2']);
        try {
            $this->application->doRun($input, $this->output);
            $output = $this->output->fetch();
            $this->assertStringContainsString(
                needle: PromocodeValidateCommand::PROMOCODE_VALIDATION_FAILED_MESSAGE,
                haystack: $output,
            );
        } catch (\Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    //Verify that an expired promocode can't be validated
    public function testPromocodeWoodywoodpeeckerShouldNotBeValid()
    {
        $input = new ArgvInput(['phpunit-test', 'promo-code:validate', 'WOODY_WOODPECKER']);
        try {
            $this->application->doRun($input, $this->output);
            $output = $this->output->fetch();
            $this->assertStringContainsString(
                needle: PromocodeValidateCommand::PROMOCODE_VALIDATION_FAILED_MESSAGE,
                haystack: $output,
            );
        } catch (\Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    //Verify that an unknown promocode can't be validated
    public function testPromocodeUnknownShouldNotBeValid()
    {
        $input = new ArgvInput(['phpunit-test', 'promo-code:validate', 'UNKNOWN']);
        try {
            $this->application->doRun($input, $this->output);
            $output = $this->output->fetch();
            $this->assertStringContainsString(
                needle: PromocodeValidateCommand::PROMOCODE_VALIDATION_FAILED_MESSAGE,
                haystack: $output,
            );
        } catch (\Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    //Verify that we can't validate a promocode that is present twice in the promocde list
    public function testPromocodeBuzzShouldNotBeValid()
    {
        $input = new ArgvInput(['phpunit-test', 'promo-code:validate', 'BUZZ']);
        try {
            $this->application->doRun($input, $this->output);
            $output = $this->output->fetch();
            $this->assertStringContainsString(
                needle: PromocodeValidateCommand::PROMOCODE_VALIDATION_FAILED_MESSAGE,
                haystack: $output,
            );
        } catch (\Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    //Verify that we can validate a non expired promocode with associated offers that is present once in the promocode list can be validated
    public function testPromocodeWoodyShouldBeValid()
    {
        $input = new ArgvInput(['phpunit-test', 'promo-code:validate', 'WOODY']);
        try {
            $this->application->doRun($input, $this->output);
            $output = $this->output->fetch();
            $this->assertStringContainsString(
                needle: PromocodeValidateCommand::PROMOCODE_VALIDATION_SUCCESS_MESSAGE,
                haystack: $output,
            );

            $jsonOutputFile =
                ValidatePromocodeResponseJsonFormatter::OUTPUT_PATH .
                ValidatePromocodeResponseJsonFormatter::OUTPUT_FILENAME;

            $this->assertFileExists($jsonOutputFile);

            $jsonOutput = file_get_contents($jsonOutputFile);
            $jsonExpectedOutput = file_get_contents(__DIR__ . '/../Fixtures/out.static.json');
            $this->assertEquals(json_decode($jsonExpectedOutput), json_decode($jsonOutput));
        } catch (\Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

}