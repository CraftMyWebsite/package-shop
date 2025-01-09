<?php

use CMW\Manager\Env\EnvManager;

//  LangManager::translate('shop.alert.mail.title')
/*  <?= LangManager::translate('shop.alert.mail.config') ?> */
/*  <?= LangManager::translate('shop.views.carts.carts.modal.titleSession', ['session_name' => $var]) ?> */

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
                '' => '',
            ],
            'edit' => [
                '' => '',
            ],
            'manage' => [
                '' => '',
            ],
        ],
        'discount' => [
            'add' => [
                '' => '',
            ],
            'credits' => [
                '' => '',
            ],
            'discount' => [
                '' => '',
            ],
            'edit' => [
                '' => '',
            ],
            'giftCard' => [
                '' => '',
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
                'coinbase' => [
                    '' => '',
                ],
                'paypal' => [
                    '' => '',
                ],
                'stripe' => [
                    '' => '',
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
                '' => '',
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
        'payments' => [
            '' => '',
        ],
    ],
];
