<?php

namespace App\EventSubscriber;

use App\Entity\Annonces;
use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setCrearedAt'],
            BeforeEntityUpdatedEvent::class => ['setUpdatedAt']
        ];
    }

    public function setCreatedAt(BeforeEntityPersistedEvent $event)
    {
        $entityInstance = $event->getEntityInstance();

        if (!$entityInstance instanceof Annonces && !$entityInstance instanceof Category) return;

        $entityInstance->setCreatedAt(new \DateTimeImmutable);
    }

    public function setUpdatedAt(BeforeEntityPersistedEvent $event)
    {
        $entityInstance = $event->getEntityInstance();

        if (!$entityInstance instanceof Annonces && !$entityInstance instanceof Category) return;

        $entityInstance->setUpdatedAt(new \DateTimeImmutable);
    }
}
