<?php

declare(strict_types=1);

namespace Infrastructure\Actions\ValidatePromocode;

use Domain\Eshop\Offer\OfferInterface;
use Domain\Eshop\Promocode\PromocodeInterface;
use Domain\Eshop\Offer\OfferList;
use Infrastructure\Ekwateur\Dto\Error;
use Infrastructure\Ekwateur\EkwateurApi\EkwateurApiHttpConnector;
use Infrastructure\Ekwateur\EkwateurApi\EkwateurApiInMemoryConnector;
use Infrastructure\Ekwateur\EkwateurManager\EkwateurManager;
use Infrastructure\Ekwateur\EkwateurManager\EkwateurManagerInterface;

class ValidatePromocode
{
    public function __construct(private ?EkwateurManagerInterface $ekwateurManager=null)
    {
        $this->ekwateurManager = $this->ekwateurManager ?? new EkwateurManager(
                new EkwateurApiHttpConnector(options: ['use-cache' => true])
            );

        /*$this->ekwateurManager = $ekwateurManager ?? new EkwateurManager(
                new EkwateurApiInMemoryConnector()
            );*/
    }

    //todo: discuss improvements with team : filtering on api side, pagination, limits to avoid retrieving all promocodes/offers
    public function __invoke(ValidatePromocodeRequest $request): ValidatePromocodeResponse
    {
        $response = new ValidatePromocodeResponse();
        $promocodeInput = $request->getPromocode();

        $allPromocodes = $this->ekwateurManager->getPromocodes()->getAll();
        $validatedPromocode = null;

        $allOffers = $this->ekwateurManager->getOffers()->getAll();
        $validatedOffers = new OfferList();

        //Search for a matching promocode through ther configured connector in memory ou http)
        $matchingPromocodes = array_filter($allPromocodes,
            function(PromocodeInterface $promocode) use ($promocodeInput) {
                return $promocode->getCode() === $promocodeInput;
            }
        );

        if (!count($matchingPromocodes)) {
            $response->addError(
                new Error(
                    message: 'Promocode "' . $promocodeInput . '" not found',
                    code: $request->getPromocode(),
                    dateTime: new \DateTime(),
                )
            );

            goto end;
        }

        if (count($matchingPromocodes) > 1) {
            $response->addError(
                new Error(
                    message: 'Promocode "' . $promocodeInput . '" is not unique',
                    code: $request->getPromocode(),
                    dateTime: new \DateTime(),
                )
            );

            goto end;
        }

        //Verify the promocode is not expired
        $notExpiredPromocodes = array_filter($matchingPromocodes, function(PromocodeInterface $promocode) {
            return $promocode->isValid();
        });

        if (!count($notExpiredPromocodes) && count($matchingPromocodes)) {
            $response->addError(
                new Error(
                    message: 'Promocode "' . $promocodeInput . '" found but already expired',
                    code: $request->getPromocode(),
                    dateTime: new \DateTime(),
                )
            );

            goto end;
        }

        //Promocode is valid
        $validatedPromocode = array_shift($matchingPromocodes);

        //Verify availability of offers for the considered promocode
        $matchingOffers = [];
        foreach ($notExpiredPromocodes as $promocode) {
            $matchingOffers += array_filter($allOffers,
                function (OfferInterface $offer) use ($promocode, &$validatedOffers) {
                    if ($promocode->isValidForOffer($offer)) {
                        try {
                            $promocode->addOffer($offer);
                        } catch (\Throwable $e) {
                            //todo: log exception. Offer already added. We should not add it again
                        }

                        try {
                            $offer->addPromocode($promocode);
                        } catch (\Throwable $e) {
                            //todo: discuss with team : what to do in case of duplicate promocode
                        }

                        try {
                            $validatedOffers->addOffer($offer);
                        } catch (\Throwable $e) {
                            //todo: log exception.
                        }

                        return true;
                    }

                    return false;
                }
            );
        }

        if (!count($matchingOffers) && count($notExpiredPromocodes)) {
            $response->addError(
                new Error(
                    message: 'Promocode "' . $promocodeInput . '" found but no offer is valid',
                    code: $request->getPromocode(),
                    dateTime: new \DateTime(),
                )
            );

            goto end;
        }

        //Final promocode validation
        if (!$response->hasErrors()) {
            $response->setValidatedPromocode($validatedPromocode);
            $response->setValidatedOffers($validatedOffers);

            if ($response->hasValidOffers()) {
                $response->setPromocodeValidated();
            }
        }

        end:
        return $response;
    }
}

