<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Infrastructure\Ekwateur\EkwateurApi\EkwateurApiInMemoryConnector;
use Infrastructure\Ekwateur\EkwateurManager\EkwateurManager;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private EkwateurManager $ekwateurManager;
    private \Domain\Eshop\Promocode\PromocodeListInterface $staticPromocodeList;
    private \Domain\Eshop\Offer\OfferListInterface $staticOfferList;
    private string $symfonyCommand;
    private \Symfony\Component\Console\Application $application;
    private \Symfony\Component\Console\Output\BufferedOutput $output;
    private $kernel;
    private string $jsonOutputFile;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->ekwateurManager = new EkwateurManager(new EkwateurApiInMemoryConnector());

        $this->kernel = new \Infrastructure\Symfony\Kernel(environment: 'test', debug: true);
        $this->application = new \Symfony\Component\Console\Application();
        $this->application->add(new \Infrastructure\Symfony\Command\PromocodeValidateCommand(ekwateurManager: $this->ekwateurManager));
        $this->output = new \Symfony\Component\Console\Output\BufferedOutput();
    }

    private function runCommand(string $command, string $parameters='')
    {
        $input = new \Symfony\Component\Console\Input\ArgvInput(['behat-test', $command, $parameters]);
        try {
            $this->application->doRun($input, $this->output);
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @Given une liste d'offres :arg1
     */
    public function uneListeDoffres($arg1)
    {
        dump(__METHOD__);
        $this->staticOfferList = $this->ekwateurManager->getOffers();
    }

    /**
     * @Given ces :arg2 lists au format json sont présentes dans :arg1
     */
    public function cesListsAuFormatJsonSontPresentesDans($arg1, $arg2)
    {
        dump(__METHOD__);

        //Skip
    }

    /**
     * @When je lance la commande avec comme argument le promocode :arg1
     */
    public function jeLanceLaCommandeAvecCommeArgumentLePromocode($arg1)
    {
        dump(__METHOD__);

        try {
            $this->runCommand(
                command: $this->symfonyCommand,
                parameters: $arg1,
            );
        } catch (\Throwable $e) {
            dd($e);
        }
    }

    /**
     * @Then la commande doit retourner un message de succès qui sera définie au sein d'une constante :arg1
     */
    public function laCommandeDoitRetournerUnMessageDeSuccesQuiSeraDefinieAuSeinDuneConstante($arg1)
    {
        dump(__METHOD__);
        try {
            $output = $this->output->fetch();

            /*dump([
                'output' => $output,
                'expected' => $arg1,
            ]);*/

            \Webmozart\Assert\Assert::contains(
                value: $output,
                subString: '[OK] ' . \Infrastructure\Symfony\Command\PromocodeValidateCommand::PROMOCODE_VALIDATION_SUCCESS_MESSAGE,
            );
        } catch (\Throwable $e) {
            dump($e);
        }
    }

    /**
     * @Then la commande doit retourner un message d'erreur définie au sein d'une constante :arg1
     */
    public function laCommandeDoitRetournerUnMessageDerreurDefinieAuSeinDuneConstante($arg1)
    {
        dump(__METHOD__);

        try {
            $output = $this->output->fetch();

            /*dump([
                'output' => $output,
                'expected' => $arg1,
            ]);*/

            \Webmozart\Assert\Assert::contains(
                value: $output,
                subString: constant("\Infrastructure\Symfony\Command\PromocodeValidateCommand::$arg1"),
            );
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @When /^une liste de promo\-code "([^"]*)" qui se trouve dans "([^"]*)"$/
     */
    public function uneListeDePromoCodeQuiSeTrouveDans($arg1, $arg2)
    {
        dump(__METHOD__);
        $this->staticPromocodeList = $this->ekwateurManager->getPromocodes();
        //dump($this->staticPromocodeList);
    }

    /**
     * @When /^une liste d'offres "([^"]*)"  qui se trouve dans "([^"]*)"$/
     */
    public function uneListeDOffresQuiSeTrouveDans($arg1, $arg2)
    {
        dump(__METHOD__);
        $this->staticOfferList = $this->ekwateurManager->getOffers();
        //dump($this->staticOfferList);
    }

    /**
     * @When /^j'utilise la console symfony et que je lance la commande symfony \(avec php bin\/console\) "([^"]*)" et que cette commande prend un argument obligatoire "([^"]*)"$/
     */
    public function jUtiliseLaConsoleSymfonyEtQueJeLanceLaCommandeSymfonyAvecPhpBinConsoleEtQueCetteCommandePrendUnArgumentObligatoire($arg1, $arg2)
    {
        dump(__METHOD__);
        //jUtiliseLaConsoleSymfony
        \Webmozart\Assert\Assert::same('cli', php_sapi_name());

        //la commande symfony
        $this->symfonyCommand = $arg1;
    }

    /**
     * @When /^je lance la commande avec comme argument "([^"]*)"$/
     */
    public function jeLanceLaCommandeAvecCommeArgument($arg1)
    {
        dump(__METHOD__);

        $this->runCommand(
            command: $this->symfonyCommand,
            parameters: $arg1,
        );
    }

    /**
     * @When /^la commande doit générer un fichier au format json avec le nom "([^"]*)" qui sera défini dans une constante "([^"]*)" dans le dossier "([^"]*)" qui sera configurable au sein d'une constante "([^"]*)"$/
     */
    public function laCommandeDoitGénérerUnFichierAuFormatJsonAvecLeNomQuiSeraDéfiniDansUneConstanteDansLeDossierQuiSeraConfigurableAuSeinDUneConstante($arg1, $arg2, $arg3, $arg4)
    {
        dump(__METHOD__);
        $jsonOutputFile =
            \Infrastructure\Actions\ValidatePromocode\ValidatePromocodeResponseJsonFormatter::OUTPUT_PATH .
            \Infrastructure\Actions\ValidatePromocode\ValidatePromocodeResponseJsonFormatter::OUTPUT_FILENAME;
        \PHPUnit\Framework\assertFileExists($jsonOutputFile);
        $this->jsonOutputFile = $jsonOutputFile;
    }

    /**
     * @When /^le fichier généré remplacera un éventuel fichier précédemment existent et aura la structure suivante :$/
     */
    public function leFichierGénéréRemplaceraUnÉventuelFichierPrécédemmentExistentEtAuraLaStructureSuivante(PyStringNode $string)
    {
        dump(__METHOD__);


        $jsonTest = json_decode($string->getRaw());
        $jsonOutputFile = json_decode(file_get_contents($this->jsonOutputFile));

        /*dump([
            'jsonOutputFile' => $jsonOutputFile,
            '$jsonTest' => $jsonTest,
        ]);*/

        \PHPUnit\Framework\assertEquals(
            json_decode($string->getRaw()),
            json_decode(file_get_contents($this->jsonOutputFile)),
        );
    }
}
