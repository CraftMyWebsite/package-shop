<?php

namespace CMW\Entity\Shop\HistoryOrders;

use CMW\Utils\Date;
use CMW\Entity\Users\UserEntity;

class ShopHistoryOrdersAfterSalesEntity
{
    private int $id;
    private UserEntity $author;
    private int $reason;
    private int $status;
    private ShopHistoryOrdersEntity $order;
    private string $created;
    private string $updated;

    /**
     * @param int $id
     * @param \CMW\Entity\Users\UserEntity $author
     * @param int $reason
     * @param int $status
     * @param \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order
     * @param string $created
     * @param string $updated
     */
    public function __construct(int $id, UserEntity $author, int $reason, int $status, ShopHistoryOrdersEntity $order, string $created, string $updated)
    {
        $this->id = $id;
        $this->author = $author;
        $this->reason = $reason;
        $this->status = $status;
        $this->order = $order;
        $this->created = $created;
        $this->updated = $updated;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthor(): UserEntity
    {
        return $this->author;
    }

    public function getReason(): int
    {
        return $this->reason;
    }

    public function getFormattedReason(): string
    {
        if ($this->reason === 0) {
            return 'Modification de commande';
        }
        if ($this->reason === 1) {
            return 'Erreur de commande';
        }
        if ($this->reason === 2) {
            return 'Produit défectueux';
        }
        if ($this->reason === 3) {
            return 'Produit endommagé';
        }
        if ($this->reason === 4) {
            return 'Produit manquant';
        }
        if ($this->reason === 5) {
            return 'Retard de livraison';
        }
        if ($this->reason === 6) {
            return 'Non-réception de la commande';
        }
        if ($this->reason === 7) {
            return 'Problème de taille ou de spécifications';
        }
        if ($this->reason === 8) {
            return 'Autres';
        }
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getFormattedStatus(): string
    {
        if ($this->status === 0) {
            return "<i class='fa-solid fa-spinner fa-spin' style='color: #1159d4;'></i>" . ' Attend une réponse';
        }
        if ($this->status === 1) {
            return "<i class='fa-solid fa-spinner fa-spin-pulse' style='color: #1bbba9;'></i>" . ' Réponse apportée';
        }

        return "<i class='fa-regular fa-circle-check' style='color: #15d518;'></i>" . ' Clos';
    }

    public function getOrder(): ShopHistoryOrdersEntity
    {
        return $this->order;
    }

    public function getCreated(): string
    {
        return Date::formatDate($this->created);
        return $this->created;
    }

    public function getUpdated(): string
    {
        return Date::formatDate($this->updated);
    }
}
