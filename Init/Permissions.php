<?php

namespace CMW\Permissions\Shop;

use CMW\Manager\Permission\IPermissionInit;
use CMW\Manager\Permission\PermissionInitType;

class Permissions implements IPermissionInit
{
    public function permissions(): array
    {
        return [
            new PermissionInitType(
                code: 'shop.config',
                description: 'Afficher la configuration',
            ),
            new PermissionInitType(
                code: 'shop.config.edit',
                description: 'Modifier',
            ),
            new PermissionInitType(
                code: 'shop.cat',
                description: 'Afficher les catégories',
            ),
            new PermissionInitType(
                code: 'shop.cat.add',
                description: 'Ajouter',
            ),
            new PermissionInitType(
                code: 'shop.cat.edit',
                description: 'Modifier',
            ),
            new PermissionInitType(
                code: 'shop.cat.delete',
                description: 'Supprimer',
            ),
            new PermissionInitType(
                code: 'shop.items',
                description: 'Afficher les articles',
            ),
            new PermissionInitType(
                code: 'shop.items.add',
                description: 'Ajouter',
            ),
            new PermissionInitType(
                code: 'shop.items.edit',
                description: 'Modifier',
            ),
            new PermissionInitType(
                code: 'shop.items.delete',
                description: 'Supprimer',
            ),
            new PermissionInitType(
                code: 'shop.items.deleteRating',
                description: 'Supprimer des avis',
            ),
            new PermissionInitType(
                code: 'shop.discount',
                description: 'Afficher les promotions',
            ),
            new PermissionInitType(
                code: 'shop.discount.add',
                description: 'Ajouter',
            ),
            new PermissionInitType(
                code: 'shop.discount.edit',
                description: 'Modifier',
            ),
            new PermissionInitType(
                code: 'shop.discount.delete',
                description: 'Supprimer',
            ),
            new PermissionInitType(
                code: 'shop.credit',
                description: 'Afficher les avoirs / credits',
            ),
            new PermissionInitType(
                code: 'shop.credit.add',
                description: 'Ajouter',
            ),
            new PermissionInitType(
                code: 'shop.credit.delete',
                description: 'Supprimer',
            ),
            new PermissionInitType(
                code: 'shop.gift',
                description: 'Afficher les cartes cadeau',
            ),
            new PermissionInitType(
                code: 'shop.gift.add',
                description: 'Ajouter',
            ),
            new PermissionInitType(
                code: 'shop.gift.delete',
                description: 'Supprimer',
            ),
            new PermissionInitType(
                code: 'shop.cart',
                description: 'Afficher les paniers',
            ),
            new PermissionInitType(
                code: 'shop.cart.delete',
                description: 'Supprimer',
            ),
            new PermissionInitType(
                code: 'shop.order',
                description: 'Afficher les commandes',
            ),
            new PermissionInitType(
                code: 'shop.order.manage',
                description: 'Gérer les en cours',
            ),
            new PermissionInitType(
                code: 'shop.order.manage.ready',
                description: 'Traité comme prête',
            ),
            new PermissionInitType(
                code: 'shop.order.manage.unrealizable',
                description: 'Traité comme irréalisable',
            ),
            new PermissionInitType(
                code: 'shop.order.manage.shipping',
                description: 'Envoyer le colis',
            ),
            new PermissionInitType(
                code: 'shop.order.manage.endSuccess',
                description: 'Traité comme terminé (commande réaliser)',
            ),
            new PermissionInitType(
                code: 'shop.order.manage.endFailed',
                description: 'Traité comme terminé (commande irréalisable)',
            ),
            new PermissionInitType(
                code: 'shop.order.manage.refund',
                description: 'Créer un avoir (commande irréalisable)',
            ),
            new PermissionInitType(
                code: 'shop.order.manage.passed',
                description: 'Gérer les passé',
            ),
            new PermissionInitType(
                code: 'shop.order.manage.passed.rating',
                description: 'Relancer un avis',
            ),
            new PermissionInitType(
                code: 'shop.afterSales',
                description: 'Afficher les S.A.V',
            ),
            new PermissionInitType(
                code: 'shop.afterSales.manage',
                description: 'Gérer les S.A.V',
            ),
            new PermissionInitType(
                code: 'shop.shipping',
                description: 'Afficher les méthodes de livraison',
            ),
            new PermissionInitType(
                code: 'shop.shipping.add',
                description: 'Ajouter',
            ),
            new PermissionInitType(
                code: 'shop.shipping.edit',
                description: 'Modifier',
            ),
            new PermissionInitType(
                code: 'shop.shipping.delete',
                description: 'Supprimer',
            ),
            new PermissionInitType(
                code: 'shop.payment',
                description: 'Afficher les méthodes de paiement',
            ),
            new PermissionInitType(
                code: 'shop.payment.edit',
                description: 'Modifier',
            ),
            new PermissionInitType(
                code: 'shop.stats',
                description: 'Afficher les statistiques',
            ),
        ];
    }
}
