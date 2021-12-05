#language: fr
Fonctionnalité: Validation du code promo par une commande symfony
  Contexte:
    Etant donné que une liste de promo-code "staticPromocodeList.json" qui se trouve dans "tests/fixtures/"
    Et une liste d'offres "staticOfferList.json"  qui se trouve dans "tests/fixtures/"
  Scénario: validation du code promo
    Etant donné que j'utilise la console symfony et que je lance la commande symfony (avec php bin/console) "promo-code:validate" et que cette commande prend un argument obligatoire "promocode"
    Lorsque je lance la commande avec comme argument "WOODY"
    Alors la commande doit retourner un message de succès qui sera définie au sein d'une constante "PromocodeValidateCommand::PROMOCODE_VALIDATION_SUCCESS_MESSAGE"
    Alors la commande doit générer un fichier au format json avec le nom "output.json" qui sera défini dans une constante "PromocodeValidateCommand::OUTPUT_FILENAME" dans le dossier "src/Infrastructure/Symfony/Command/Output/PromocodeValidateCommand/" qui sera configurable au sein d'une constante "PromocodeValidateCommand::OUTPUT_PATH"
    Alors le fichier généré remplacera un éventuel fichier précédemment existent et aura la structure suivante :
    """
    {
        "promocode": "WOODY",
        "endDate": "2022-05-29",
        "discountValue": 1.75,
        "compatibleOfferList": [
            {
              "name": "EKWAW2000",
              "type": "WOOD"
            },
            {
              "name": "EKWAW3000",
              "type": "WOOD"
            }
        ]
    }
    """
    Lorsque je lance la commande avec comme argument le promocode "WOODY2"
    Alors la commande doit retourner un message d'erreur définie au sein d'une constante "PROMOCODE_VALIDATION_FAILED_MESSAGE"
    Lorsque je lance la commande avec comme argument le promocode "WOODY_WOODPECKER"
    Alors la commande doit retourner un message d'erreur définie au sein d'une constante "PROMOCODE_VALIDATION_FAILED_MESSAGE"

