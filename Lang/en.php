<?php

use CMW\Manager\Env\EnvManager;

return [
    /*MENUS : */
    'menus' => [
        'shop' => 'Shop',
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
                'save' => 'Save',
                'fee' => 'Fees:',
                'free' => [
                    'title' => 'Free order',
                ],
                'coinbase' => [
                    'key' => 'Secret Key:',
                ],
                'paypal' => [
                    'client' => 'ClientID:',
                    'key' => 'Client Secret:',
                ],
                'stripe' => [
                    'info1' => 'Manage the payment methods you want to authorize directly in Stripe. All active and properly configured payments will be automatically transmitted, allowing your client to choose their preferred method.',
                    'info2' => '<a target="_blank" class="link" href="https://dashboard.stripe.com/settings/payment_methods?config_id=pmc_1NQztq2b9x8tnST4GWwYqyWt">Manage my Stripe payment methods</a>',
                    'key' => 'Secret Key:',
                ],
            ],
            'shipping' => [
                'global' => [
                    'withdraw' => [
                        'object' => 'Mail subject:',
                        'mail_setting' => 'Mail settings:',
                        'title' => 'Title:',
                        'message' => 'Message:',
                        'footer_message' => 'Footer message:',
                        'address' => 'Address:',
                        'preview' => 'Mail preview:',
                        'style' => 'Appearance:',
                        'background' => 'Background color',
                        'card' => 'Frame background color',
                        'background_code' => 'Code background color',
                        'code' => 'Code color',
                        'text' => 'Text color',
                        'title_color' => 'Title color',
                        'placeholder' => [
                            'waiting_title' => 'Package awaiting pickup',
                            'waiting' => 'Pending pickup',
                            'ready_to_withdraw' => 'Your order is ready to be picked up at our center!',
                            'show_this' => 'Show this email to collect your package!',
                            'center' => 'Center address:',
                        ],
                    ],
                ],
                'withdraw_point' => [
                    'title' => 'Possible withdrawal notification',
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
                'shop' => 'Shop',
                'inProgress' => 'Orders to Process',
                'gainMonth' => 'Earnings This Month',
                'gainTotal' => 'Total Earnings',
                'sell' => 'Items for Sale',
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
                'title' => 'Shipping',
                'shipping_method' => 'Shipping method',
                'withdraw_method' => 'Withdrawal method',
                'add_shipping_method' => 'Add a shipping method',
                'name' => 'Name',
                'action' => 'Action zone',
                'price' => 'Price',
                'min' => 'Min',
                'max' => 'Max',
                'weight_max' => 'Max weight',
                'action_btn' => 'Action',
                'delete' => 'Delete',
                'sure' => 'Are you sure?',
                'edit' => 'Editing ',
                'method' => 'Method:',
                'conditions' => 'Trigger condition:',
                'weight_max_g' => 'Max weight (g)',
                'mini_cart' => 'MINI cart cost',
                'maxi_cart' => 'MAXI cart cost',
                'warning_cart' => 'Cart cost and weight are triggering conditions, allowing you to have the same shipping method with different prices based on the customer\'s cart. For example: you have a shipping method at 5 %symbol% when the cart total does not exceed 10 %symbol%, then you set the same shipping method at 0 %symbol% if the cart exceeds 10 %symbol%',
                'edit_btn' => 'Edit',
                'add_withdraw_method' => 'Add a withdrawal method',
                'deposit' => 'Deposits',
                'settings' => 'Settings',
                'deposit_address' => 'Deposit address',
                'add' => 'Add',
                'max_dist_allowed' => 'Max allowed distance:',
                'max_dist_km' => 'Max distance (km)',
                'warn_dist' => 'Determines the display distance of this withdrawal point in relation to the customer\'s address',
                'address' => 'Address',
                'city' => 'City',
                'cp' => 'Postal code',
                'country' => 'Country',
                'all_the_world' => 'Worldwide',
                'zone' => 'Served zone',
                'method_settings' => 'Method settings',
                'add_zone' => 'Add a Zone',
                'add_depot' => 'Add a Depot',
                'add_shipping_withdraw' => 'Add a Withdrawal Method',
            ],
        ],
        'statistics' => [
            'main' => [
                '' => '',
            ],
        ],
        'payments' => [
            'title' => 'Payment Methods',
            'actif' => 'Active Payment.',
            'inactif' => 'Inactive Payment.',
            'warn' => 'You cannot modify this payment method because it is mandatory for selling free items. <br> Don’t worry, this payment method is fully automatic and will only be available if the entire cart content is free.',
            'config' => 'Payment configuration with %name%',
            'disable' => 'Disable %name%',
            'enable' => 'Enable %name%',
            'panel' => '%name% Panel',
            'docs' => 'Documentation',
        ],
    ],
];
