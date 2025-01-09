<?php

use CMW\Manager\Env\EnvManager;

return [
    /*MENUS : */
    'menus' => [
        'settings' => [
            'setting' => 'Settings',
            'global' => 'Global',
            'method' => 'Methods',
            'shipping' => 'Shipping',
            'payment' => 'Payments',
        ],
        'items' => [
            'item' => 'Items & Categories',
            'items' => 'Items',
            'cats' => 'Categories',
        ],
        'discounts' => [
            'discount' => 'Customer Benefits',
            'discounts' => 'Promotions',
            'credit' => 'Credits',
            'gift' => 'Gift Card',
        ],
        'orders' => [
            'order' => 'Orders',
            'inProgress' => 'In Progress',
            'ended' => 'Completed',
            'canceled' => 'Canceled',
        ],
        'sav' => 'Customer Support',
        'stats' => [
            'stat' => 'Stats',
            'global' => 'Global Statistics',
            'cart' => 'Carts',
        ],
    ],

    /*ALERTS : */
    'alert' => [
        'mail' => [
            'title' => 'Important: Email Configuration Required',
            'config' => 'Emails are not configured on your site. Proper configuration is essential to ensure the proper functioning of the Shop package.',
            'notify' => 'Important notifications, such as order confirmations, tracking information, etc., depend on a functional email system.',
            'link' => 'Please <a class="link" href="'. EnvManager::getInstance()->getValue("PATH_SUBFOLDER") .'cmw-admin/mail/configuration">configure email settings</a> as soon as possible.',
        ],
    ],

    /*VIEWS : */
    'views' => [
        'carts' => [
            'carts' => [
                'title' => 'Carts',
                'title2' => 'User Carts',
                'items' => 'items',
                'view' => 'View Cart',
                'cartSessions' => 'Session Carts',
                'deleteAll' => 'Delete All',
                'modal' => [
                    'titleAllSession' => 'Delete all sessions?',
                    'titleSession' => 'Deleting: %session_name%',
                    'textSession' => 'This deletion is permanent.',
                ],
                'warning' => 'Sessions are temporary carts.<br>They allow your non-logged-in users to create a cart.<br>Once logged in, the cart will automatically transfer to a user cart. Avoid deleting sessions that are less than 24 hours old.',
                'delete' => 'Delete',
            ],
            'viewCart' => [
                'title' => 'Cart of %session_name%',
                'item' => 'Item',
                'quantity' => 'Quantity',
                'pu' => 'Unit Price',
                'pt' => 'Total Price',
                'date' => 'Date Added',
            ],
        ],
        'cat' => [
            'addSubCat' => [
                'title' => 'Add a sub-category in %cat_name%',
                'name' => 'Name',
                'placeholderName' => 'Pants',
                'icon' => 'Icon: <small>(Optional)</small>',
                'iconPlaceholder' => 'Select an icon',
                'desc' => 'Description: <small>(Optional)</small>',
                'descPlaceholder' => 'Clothing',
            ],
            'edit' => [
                'title' => 'Editing %cat_name%',
                'move' => 'Move to:',
                'to' => 'Main Category',
            ],
            'manage' => [
                'cat' => 'Categories',
                'create' => 'Create a category',
                'items' => 'items',
                'modalDelete' => [
                    'title' => 'Deleting: %cat_name%',
                    'text' => 'This deletion is permanent.',
                ],
                'createBefore' => 'Please create a category to start using the Shop',
                'modalAdd' => [
                    'title' => 'New Category',
                ],
                'tooltip' => [
                    'items' => 'View related items',
                    'render' => 'View the render',
                    'subCat' => 'Add a sub-category',
                    'edit' => 'Edit category',
                    'delete' => 'Delete',
                ],
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
