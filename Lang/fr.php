<?php

use CMW\Manager\Env\EnvManager;

return [
    /*MENUS : */
    'menus' => [
        'settings' => [
            'setting' => 'Réglages',
            'global' => 'Globaux',
            'method' => 'Méthodes',
            'shipping' => 'Livraisons',
            'payment' => 'Paiements',
        ],
        'items' => [
            'item' => 'Articles & Catégories',
            'items' => 'Articles',
            'cats' => 'Catégories',
        ],
        'discounts' => [
            'discount' => 'Avantages Client',
            'discounts' => 'Promotions',
            'credit' => 'Avoirs / Credits',
            'gift' => 'Carte cadeau',
        ],
        'orders' => [
            'order' => 'Commandes',
            'inProgress' => 'En cours',
            'ended' => 'Terminé',
            'canceled' => 'Annulé',
        ],
        'sav' => 'S.A.V',
        'stats' => [
            'stat' => 'Stats',
            'global' => 'Statistiques global',
            'cart' => 'Paniers',
        ],
    ],

    /*ALERTS : */
    'alert' => [
        'mail' => [
            'title' => 'Important : Configuration des e-mails requise',
            'config' => 'Les e-mails ne sont pas configurés sur votre site. Une configuration correcte est essentielle pour assurer le bon fonctionnement du package Shop.',
            'notify' => 'Les notifications importantes, telles que les confirmations de commandes, les informations de suivi ..., dépendent d\'un système d\'e-mails fonctionnel.',
            'link' => 'Veuillez <a class="link" href="'. EnvManager::getInstance()->getValue("PATH_SUBFOLDER") .'cmw-admin/mail/configuration">configurer les paramètres d\'e-mails</a> dès que possible.',
        ],
    ],

    /*VIEWS : */
    'views' => [
        'carts' => [
            'carts' => [
                'title' => 'Paniers',
                'title2' => 'Paniers des utilisateurs',
                'items' => 'articles',
                'view' => 'Voir le panier',
                'cartSessions' => 'Paniers des sessions',
                'deleteAll' => 'Supprimer tout',
                'modal' => [
                    'titleAllSession' => 'Suppression de toutes les sessions ?',
                    'titleSession' => 'Suppression de : %session_name%',
                    'textSession' => 'Cette suppression est définitive.',
                ],
                'warning' => 'Les sessions sont des paniers temporaires.<br>Elle permet à vos utilisateurs non connectés de créer un panier.<br>Une fois connecté le panier sera automatique transmis vers un panier utilisateur, évitez de supprimer des sessions qui ont moins de 24 heures.',
                'delete' => 'Supprimer',
            ],
            'viewCart' => [
                'title' => 'Panier de %session_name%',
                'item' => 'Article',
                'quantity' => 'Quantité',
                'pu' => 'Prix unitaire',
                'pt' => 'Prix total',
                'date' => 'Date d\'ajout',
            ],
        ],
        'cat' => [
            'addSubCat' => [
                'title' => 'Ajout d\'une sous-catégorie dans %cat_name%',
                'name' => 'Nom',
                'placeholderName' => 'Pantalon',
                'icon' => 'Icon : <small>(Optionnel)</small>',
                'iconPlaceholder' => 'Sélectionner un icon',
                'desc' => 'Description : <small>(Optionnel)</small>',
                'descPlaceholder' => 'Des vêtements',
            ],
            'edit' => [
                'title' => 'Édition de %cat_name%',
                'move' => 'Déplacer vers :',
                'to' => 'Catégorie principale',
            ],
            'manage' => [
                'cat' => 'Catégories',
                'create' => 'Créer une catégorie',
                'items' => 'articles',
                'modalDelete' => [
                    'title' => 'Suppression de : %cat_name%',
                    'text' => 'Cette suppression est définitive.',
                ],
                'createBefore' => 'Merci de créer une catégorie pour commencer à utiliser la Boutique',
                'modalAdd' => [
                    'title' => 'Nouvelle catégorie',
                ],
                'tooltip' => [
                    'items' => 'Voir les articles lié',
                    'render' => 'Voir le rendue',
                    'subCat' => 'Ajouter une sous catégorie',
                    'edit' => 'Modifier la catégorie',
                    'delete' => 'Supprimer',
                ],
            ],
        ],
        'discount' => [
            'add' => [
                'title' => 'Nouvelle promotion',
                'infoTitle' => 'Seuls les articles en %symbol% et supérieur à 0 sont applicables.',
                'add' => 'Ajouter',
                'warn1' => 'N\'oubliez pas qu\'aucune promotion n\'est cumulable.',
                'warn2' => 'par ex, une promotion appliquée automatiquement sur un groupe d\'article sera prioritaire sur une promotion par CODE sur ces mêmes articles.',
                'info' => 'Informations',
                'name' => 'Nom',
                'duration' => 'Durée',
                'optional' => 'Non obligatoire !',
                'start' => 'Date de début :',
                'end' => 'Date de fin :',
                'limit' => 'Limites',
                'tooltipUse' => 'Le client peut utiliser le code sur plusieurs commandes différentes si cet option est active',
                'use' => 'Utilisation multiple par clients',
                'globalLimit' => 'Limite global d\'utilisation / stock :',
                'noLimit' => 'Pas de limites',
                'impact' => 'Impacte',
                'money' => 'Monétaire',
                'percent' => 'Pourcentage',
                'code' => 'Code',
                'tooltipAuto' => 'Vos clients n\'ont pas à rentrer de code si cet option est active',
                'auto' => 'S\'applique automatiquement',
                'tooltipCode' => 'Le CODE que vos clients doivent taper pour appliquer la réduction',
                'settings' => 'Réglages',
                'tooltipTest' => 'Ceci permet de tester vos promotions avant qu\'elle ne sois utilisable par vos clients si cet option est active',
                'test' => 'Mode test',
                'tooltipBeforeBuy' => 'Vos clients ont déjà passer une commande avant de pouvoir bénéficier de ce code si cet option est active',
                'beforeBuy' => 'Doit avoir déjà acheté',
                'tooltipQuantity' => 'La réduction s\'applique sur la quantité dans le panier si cet option est active',
                'quantity' => 'Appliquer sur la quantité',
                'linked' => 'Lié à',
                'allItems' => 'Tout les articles',
                'items' => 'Un ou Des article(s)',
                'cats' => 'Une ou Des catégorie(s)',
                'itemsLinked' => 'Article(s) lié(s)',
                'catsLinked' => 'Catégorie(s) lié(s)',
                'warnVirg' => 'Les nombres à virgule ne sont pas autorisé !',
                'warn99' => 'Vous ne pouvez pas dépasser 99% !',
                'warnEndDate' => 'La date de fin ne peut pas être inférieure à la date actuelle.',
                'warnStartDate' => 'La date de début doit être antérieure à la date de fin.',
            ],
            'credits' => [
                'title' => 'Avoirs / Credits',
                'generate' => 'Générer un avoir',
                'name' => 'Nom :',
                'placeholderName' => 'Avoir pour X',
                'amount' => 'Montant :',
                'placeholderAmount' => '18.99',
                'doGenerate' => 'Générer',
                'activ' => 'Avoir actif',
                'codeName' => 'Nom',
                'code' => 'CODE',
                'codeAmount' => 'Montant',
                'manage' => 'Gérer',
                'used' => 'Avoir utilisé',
                'deleteTitle' => 'Suppression de %name%',
                'deleteText' => 'Cette suppression est definitive.',
                'delete' => 'Supprimer',
            ],
            'discount' => [
                'title' => 'Promotions',
                'new' => 'Nouvelle promotion',
                'inProgress' => 'Promotions en cours',
                'name' => 'Nom',
                'code' => 'CODE',
                'linked' => 'Lié à',
                'impact' => 'Impacte',
                'start' => 'Début',
                'end' => 'Fin',
                'uses' => 'Utilisation',
                'manage' => 'Gérer',
                'autoApply' => 'S\'applique automatiquement',
                'report' => 'Report de %name%',
                'startDate' => 'Date de début :',
                'reportBtn' => 'Reporter',
                'disable' => 'Désactivation de %name%',
                'disableText' => 'Une fois désactivé, vous ne pourrez plus utiliser cette promotion.',
                'diableBtn' => 'Désactiver',
                'delete' => 'Suppression de %name%',
                'deleteText' => 'Cette suppression est definitive.',
                'deleteBtn' => 'Supprimer',
                'inComing' => 'Promotions à venir',
                'startIn' => 'Commence dans',
                'passed' => 'Promotions passées',
            ],
            'edit' => [
                'title' => 'Édition de %name%',
                'edit' => 'Éditer',
                'warnAll' => 'Cette promotion s\'applique à tous les articles de votre boutique.<br>Ceci n\'est pas modifiable, supprimer et recréer la promotion pour changer ceci',
                'warnItems' => 'Cette promotion s\'applique à un ou plusieurs articles.<br>Ceci n\'est pas modifiable, supprimer et recréer la promotion pour changer ceci<br>Voici la liste des articles pris en charge par cette promotion :',
                'warnCats' => 'Cette promotion s\'applique à une ou plusieurs catégories.<br>Ceci n\'est pas modifiable, supprimer et recréer la promotion pour changer ceci<br>Voici la liste des catégories prise en charge par cette promotion :',
            ],
            'giftCard' => [
                'title' => 'Carte cadeau',
                'generateBtn' => 'Générer une carte',
                'amount' => 'Montant :',
                'generate' => 'Générer',
                'active' => 'Carte active',
                'name' => 'Nom',
                'code' => 'CODE',
                'end' => 'Termine dans',
                'manage' => 'Gérer',
                'removeTitle' => 'Suppression de %name%',
                'removeText' => 'Cette suppression est definitive.',
                'removeBtn' => 'Supprimer',
                'passed' => 'Carte passée ou utilisé',
                'left' => 'Nombre d\'utilisation',
            ],
        ],
        'elements' => [
            'global' => [
                'creditLauncher' => [
                    '' => '',
                ],
                'giftCode' => [
                    '' => '',
                ],
                'invoice' => [
                    '' => '',
                ],
                'mailNotification' => [
                    '' => '',
                ],
                'reviewReminder' => [
                    '' => '',
                ],
                'withdrawPointMap' => [
                    '' => '',
                ],
            ],
            'payments' => [
                'save' => 'Sauvegarder',
                'fee' => 'Frais :',
                'coinbase' => [
                    'key' => 'Clé Secrète :',
                ],
                'paypal' => [
                    'client' => 'ClientID :',
                    'key' => 'Client Secret :',
                ],
                'stripe' => [
                    'info1' => 'Gérez les méthodes de paiement que vous voulez autoriser directement dans stripe, tous les paiements actif et configuré correctement seront transmis automatiquement, votre client pourra ainsi choisir la méthode qu\'il préfère.',
                    'info2' => '<a target="_blank" class="link" href="https://dashboard.stripe.com/settings/payment_methods?config_id=pmc_1NQztq2b9x8tnST4GWwYqyWt">Gérer mes moyens de paiement Stripe</a>',
                    'key' => 'Clé Secrète :',
                ],
            ],
            'shipping' => [
                'global' => [
                    'withdraw' => [
                        '' => '',
                    ],
                ],
            ],
            'virtual' => [
                'item' => [
                    'downloadable' => [
                        '' => '',
                    ],
                ],
            ],
            'dashboard' => [
                'shop' => 'Boutique',
                'inProgress' => 'Commandes à traité',
                'gainMonth' => 'Gains ce mois',
                'gainTotal' => 'Gains total',
                'sell' => 'Articles en vente',
            ],
        ],
        'items' => [
            'add' => [
                '' => '',
            ],
            'archived' => [
                '' => '',
            ],
            'edit' => [
                '' => '',
            ],
            'filterCat' => [
                '' => '',
            ],
            'manage' => [
                '' => '',
            ],
            'review' => [
                '' => '',
            ],
        ],
        'orders' => [
            'afterSales' => [
                'main' => [
                    '' => '',
                ],
                'manage' => [
                    '' => '',
                ],
            ],
            'manage' => [
                'cancel' => [
                    '' => '',
                ],
                'finish' => [
                    '' => '',
                ],
                'new' => [
                    '' => '',
                ],
                'send' => [
                    '' => '',
                ],
            ],
            'canceled' => [
                '' => '',
            ],
            'ended' => [
                '' => '',
            ],
            'inProgress' => [
                '' => '',
            ],
            'view' => [
                '' => '',
            ],
        ],
        'settings' => [
            'method' => [
                '' => '',
            ],
            'settings' => [
                '' => '',
            ],
        ],
        'shipping' => [
            'main' => [
                '' => '',
            ],
        ],
        'statistics' => [
            'main' => [
                '' => '',
            ],
        ],
        //  LangManager::translate('shop.views.payments.')
        /*  <?= LangManager::translate('shop.views.payments.') ?> */
        /*  <?= LangManager::translate('shop.views.payments.', ['cat_name' => $var]) ?> */
        'payments' => [
            'title' => 'Moyens de paiements',
            'actif' => 'Paiement atif.',
            'inactif' => 'Paiement inactif.',
            'warn' => 'Vous ne pouvez pas modifier cette méthode de paiement, car elle est obligatoire pour la vente d\'articles gratuits. <br> Ne vous inquiétez pas, cette méthode de paiement est entièrement automatique et ne sera disponible que si la totalité du contenu du panier est à 0.',
            'config' => 'Configuration des paiements avec %name%',
            'disable' => 'Désactiver %name%',
            'enable' => 'Activer %name%',
            'panel' => 'Panel %name%',
            'docs' => 'Documentations',
        ],
    ],
];
