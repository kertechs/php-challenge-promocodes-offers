# Test technique Symfony - ekWateur

Bienvenue à toi cher.e candidat.e.

Tu trouveras ci-dessous les informations nécessaires pour te lancer dans ce test technique.

Si certaines parties de l’exercice ne te paraissent pas claires ou que tu as des questions, nous sommes là pour y
répondre. Nous t’encourageons même à les poser plutôt que de rester bloqué.e et de ne pas être sûr.e de ce qui t’est
demandé.

## Instructions

**Important :** Tu as trois jours pour réaliser ce projet perso. Si tu as des empêchements qui ne te permettent pas de
suffisamment avancer, n’hésite pas à nous en faire part, nous adapterons le timing :)

### Objectif

On veut pouvoir vérifier la validité d'un code promo, et récupérer les offres associées.

### Commande

Pour ça, on va créer une commande Symfony qui va prendre en argument le code promo à tester.

**Exemple**

```
bin/console promo-code:validate MON_CODE_PROMO_A_TESTER
```

**Si le code promo est valide ET que des offres compatibles sont trouvées**, la commande nous le stipule, crée un
fichier json et y écrit la liste de ces offres.

Le format attendu pour le fichier json est le suivant :

```json
{
  "promoCode": "MAGNETO-2021",
  "endDate": "2020-12-24",
  "discountValue": 2.0,
  "compatibleOfferList": [
    {
      "name": "EKWAELEC123",
      "type": "ELECTRICITY"
    },
    {
      "name": "EKWAELEC456",
      "type": "ELECTRICITY"
    }
  ]
}
```

**Si le code promo est invalide OU si aucune offre compatible n'est trouvée** (OU s'il y a une erreur), la commande nous
le stipule et ne crée pas de fichier json.

### Récupération des données

Afin de retourner les offres compatibles dans le format souhaité, il te faudra récupérer la liste des promos et la liste
des offres via des appels API :

- [Liste des offres](https://601025826c21e10017050013.mockapi.io/ekwatest/offerList)
- [Liste des codes promo](https://601025826c21e10017050013.mockapi.io/ekwatest/promoCodeList)

**Notes :** On considère un code promo valide s'il n'est pas périmé (`endDate` dans le futur).
On considère une offre compatible pour un code promo si celui-ci est listé dans ceux valides pour l'offre en question (`validPromoCodeList`).

## Comment soumettre ton travail

1. Créer un nouveau **public** repository sur un espace à toi (et **non** un fork de ce repo' sur l'espace gitlab ekWateur)
2. Travailler directement sur la branche `master` de ton repository
3. Enfin, nous communiquer par mail le lien de ton repository quand l'exercice est terminé

## Ce que l’on attend de toi

- Un travail fonctionnel : On doit pouvoir lancer la commande et générer le fichier json comme attendu
- Une gestion des cas d'erreur
- Un code clair, découpé et structuré, notamment en utilisant les design patterns qui te semblent les plus adaptés
- De la documentation et des commentaires (en anglais)
- Des tests unitaires
- Un historique git découpé, propre et compréhensible (en anglais)
- Versions à utiliser : PHP ≥ `7.4` et Symfony ≥ `5.2`
- Pas de dépendance autre que Symfony (cas particulier pour les dépendances dev que tu pourras utiliser pour les tests ou la qualité par exemple)

Cet exercice est assez simple et son implémentation fonctionnelle n'est pas le but en soit, c'est un prétexte pour te permettre de nous montrer de quoi tu es capable. N'hésite donc pas à mettre en oeuvre tout ce que tu sais faire en immaginant qu'il s'agit de la première brique d'une grosse application que tu devras maintenir à long terme.
On ne demande pas le code le plus performant possible, on demande le code le plus maintenable, modulaire et évolutif possible.

### Bonus

Toute initiative faisant preuve de tes bonnes pratiques sera la bienvenue.

Bon courage et bon test !
