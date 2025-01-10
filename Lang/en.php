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
                'title' => 'New Promotion',
                'infoTitle' => 'Only items in %symbol% and greater than 0 are applicable.',
                'add' => 'Add',
                'warn1' => 'Remember that no promotion is cumulative.',
                'warn2' => 'For example, a promotion automatically applied to a group of items will take priority over a promotion by CODE on the same items.',
                'info' => 'Information',
                'name' => 'Name',
                'duration' => 'Duration',
                'optional' => 'Optional!',
                'start' => 'Start Date:',
                'end' => 'End Date:',
                'limit' => 'Limits',
                'tooltipUse' => 'The client can use the code on multiple different orders if this option is active',
                'use' => 'Multiple use by clients',
                'globalLimit' => 'Global usage/stock limit:',
                'noLimit' => 'No limits',
                'impact' => 'Impact',
                'money' => 'Monetary',
                'percent' => 'Percentage',
                'code' => 'Code',
                'tooltipAuto' => 'Your clients don’t need to enter a code if this option is active',
                'auto' => 'Applies automatically',
                'tooltipCode' => 'The CODE your clients need to enter to apply the discount',
                'settings' => 'Settings',
                'tooltipTest' => 'This allows testing your promotions before they are usable by your clients if this option is active',
                'test' => 'Test Mode',
                'tooltipBeforeBuy' => 'Your clients must have already placed an order before they can use this code if this option is active',
                'beforeBuy' => 'Must have already purchased',
                'tooltipQuantity' => 'The discount applies to the quantity in the cart if this option is active',
                'quantity' => 'Apply to quantity',
                'linked' => 'Linked to',
                'allItems' => 'All items',
                'items' => 'One or Multiple item(s)',
                'cats' => 'One or Multiple category(ies)',
                'itemsLinked' => 'Linked Item(s)',
                'catsLinked' => 'Linked Category(ies)',
                'warnVirg' => 'Decimal numbers are not allowed!',
                'warn99' => 'You cannot exceed 99%!',
                'warnEndDate' => 'The end date cannot be earlier than the current date.',
                'warnStartDate' => 'The start date must be earlier than the end date.',
            ],
            'credits' => [
                'title' => 'Credits',
                'generate' => 'Generate a credit',
                'name' => 'Name:',
                'placeholderName' => 'Credit for X',
                'amount' => 'Amount:',
                'placeholderAmount' => '18.99',
                'doGenerate' => 'Generate',
                'activ' => 'Active credit',
                'codeName' => 'Name',
                'code' => 'CODE',
                'codeAmount' => 'Amount',
                'manage' => 'Manage',
                'used' => 'Used credit',
                'deleteTitle' => 'Deleting %name%',
                'deleteText' => 'This deletion is permanent.',
                'delete' => 'Delete',
            ],
            'discount' => [
                'title' => 'Promotions',
                'new' => 'New Promotion',
                'inProgress' => 'Ongoing Promotions',
                'name' => 'Name',
                'code' => 'CODE',
                'linked' => 'Linked to',
                'impact' => 'Impact',
                'start' => 'Start',
                'end' => 'End',
                'uses' => 'Usage',
                'manage' => 'Manage',
                'autoApply' => 'Applies Automatically',
                'report' => 'Postpone %name%',
                'startDate' => 'Start Date:',
                'reportBtn' => 'Postpone',
                'disable' => 'Disable %name%',
                'disableText' => 'Once disabled, this promotion will no longer be usable.',
                'diableBtn' => 'Disable',
                'delete' => 'Delete %name%',
                'deleteText' => 'This deletion is permanent.',
                'deleteBtn' => 'Delete',
                'inComing' => 'Upcoming Promotions',
                'startIn' => 'Starts in',
                'passed' => 'Past Promotions',
            ],
            'edit' => [
                'title' => 'Editing %name%',
                'edit' => 'Edit',
                'warnAll' => 'This promotion applies to all items in your shop.<br>This cannot be changed; delete and recreate the promotion to modify this.',
                'warnItems' => 'This promotion applies to one or more items.<br>This cannot be changed; delete and recreate the promotion to modify this.<br>Here is the list of items covered by this promotion:',
                'warnCats' => 'This promotion applies to one or more categories.<br>This cannot be changed; delete and recreate the promotion to modify this.<br>Here is the list of categories covered by this promotion:',
            ],
            'giftCard' => [
                'title' => 'Gift Card',
                'generateBtn' => 'Generate a Card',
                'amount' => 'Amount:',
                'generate' => 'Generate',
                'active' => 'Active Card',
                'name' => 'Name',
                'code' => 'CODE',
                'end' => 'Ends in',
                'manage' => 'Manage',
                'removeTitle' => 'Deleting %name%',
                'removeText' => 'This deletion is permanent.',
                'removeBtn' => 'Delete',
                'passed' => 'Past or Used Card',
                'left' => 'Number of Uses Left',
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
