<?php

namespace Infrastructure\Actions\ValidatePromocode;

class ValidatePromocodeResponseJsonFormatter implements ValidatePromocodeResponseFormatterInterface
{
    public const OUTPUT_PATH = __DIR__ . '/../../../../var/log/PromocodeValidateCommand/';
    public const OUTPUT_FILENAME = 'out.json';

    private ?string $formattedOutput=null;

    public function __construct(private ?ValidatePromocodeResponse $response=null){}

    public function getFormattedOutput(): string
    {
        $this->format();
        return $this->formattedOutput;
    }

    public function save(): self
    {
        $file = $this->getFile();
        $file->fwrite($this->getFormattedOutput());
        $file->fflush();

        return $this;
    }

    public function getFile(): \SplFileObject
    {
        $file = new \SplFileObject(
            filename: self::OUTPUT_PATH . self::OUTPUT_FILENAME,
            mode: 'w'
        );
        $file->setFlags(\SplFileObject::DROP_NEW_LINE | \SplFileObject::SKIP_EMPTY | \SplFileObject::READ_AHEAD);

        return $file;
    }

    public function format(): self
    {
        if (!is_null($this->formattedOutput)) {
            return $this;
        }

        $this->formattedOutput = (
            new \Infrastructure\Ekwateur\Dto\ValidatedPromocode(
                validatedPromocode: $this->response->getValidatedPromocode(),
                validatedOffers: $this->response->getValidatedOffers()
            )
        )->toJson();

        return $this;
    }

    public function output(): self
    {
        $this->format();
        echo $this->getFormattedOutput();

        return $this;
    }

    public function __invoke(ValidatePromocodeResponse $response) :void
    {
        $this->response = $response;
        $this->format()
            ->output()
            ->save();
    }
}