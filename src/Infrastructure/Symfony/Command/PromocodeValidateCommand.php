<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Command;

use Infrastructure\Actions\ValidatePromocode\ValidatePromocode;
use Infrastructure\Actions\ValidatePromocode\ValidatePromocodeInterface;
use Infrastructure\Actions\ValidatePromocode\ValidatePromocodeRequest;
use Infrastructure\Actions\ValidatePromocode\ValidatePromocodeResponseFormatterInterface;
use Infrastructure\Actions\ValidatePromocode\ValidatePromocodeResponseJsonFormatter;
use Infrastructure\Ekwateur\EkwateurManager\EkwateurManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'promo-code:validate',
    description: 'Checks weither a promo code is valid or not',
)]
class PromocodeValidateCommand extends Command
{
    public const ERROR_INVALID_PROMOCODE_NO_OFFER_ASSOCIATED = 'Invalid promocode (no associated offer)';
    public const ERROR_INVALID_PROMOCODE_EXPIRED = 'Invalid promocode (expired)';
    public const PROMOCODE_VALIDATION_SUCCESS_MESSAGE = 'The promocode is valid';
    public const PROMOCODE_VALIDATION_FAILED_MESSAGE = 'The promocode is not valid';
    private ValidatePromocode $validatePromocode;

    public function __construct(
        private EkwateurManagerInterface $ekwateurManager,
        private ?ValidatePromocodeResponseFormatterInterface $validatePromocodeResponseFormatter=null,
    )
    {
        parent::__construct();
        $this->validatePromocode = new ValidatePromocode($this->ekwateurManager);
        $this->validatePromocodeResponseFormatter = $this->validatePromocodeResponseFormatter ?? new ValidatePromocodeResponseJsonFormatter();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                name:'promo-code',
                mode:InputArgument::REQUIRED,
                description: 'The promotional code to be validated'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /**
         * @var string $promocodeInput
         */
        $promocodeInput = $input->getArgument('promo-code');

        if ($promocodeInput) {
            $request = new ValidatePromocodeRequest($promocodeInput);
            $response = ($this->validatePromocode)($request);

            if ($response->isSuccess()) {
                $io->success(self::PROMOCODE_VALIDATION_SUCCESS_MESSAGE);
                /**
                 * @var ValidatePromocodeResponseFormatterInterface $responseFormatter
                 */
                $responseFormatter = $this->validatePromocodeResponseFormatter;
                $responseFormatter($response);
            } else {
                $io->error(self::PROMOCODE_VALIDATION_FAILED_MESSAGE);
                //todo: add verbose option to get more detailled information and implement ResponseErrorFormatter ?
                //todo: ($this->validatePromocodeResponseErrorFormatter)($response);
            }
        }

        return Command::SUCCESS;
    }
}
