<?php

namespace App\EventSubscriber;

use App\Entity\AuditLog;
use App\Entity\Train;
use App\Entity\MaintenanceTrain;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use ReflectionClass;

class AuditSubscriber implements EventSubscriber
{
    private EntityManagerInterface $em;
    private array $pendingLogs = [];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::preRemove, // ✅ preRemove not postRemove
            Events::onFlush,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->prepareSimpleLog($args->getObject(), 'CREATE');
    }

    public function preRemove(LifecycleEventArgs $args): void // ✅ preRemove
    {
        $this->prepareSimpleLog($args->getObject(), 'DELETE');
    }

    private function prepareSimpleLog(object $entity, string $action): void
    {
        if (!$entity instanceof Train && !$entity instanceof MaintenanceTrain) {
            return;
        }

        $entityType = (new ReflectionClass($entity))->getShortName();
        $entityId = $entity instanceof Train ? $entity->getIdTrain() : $entity->getIdMaintenance();

        if (!$entityId) {
            return;
        }

        $this->pendingLogs[] = [
            'entityType' => $entityType,
            'entityId' => $entityId,
            'action' => $action,
            'data' => null,
        ];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        if (!$entityManager instanceof \Doctrine\ORM\EntityManager) {
            return;
        }

        $uow = $entityManager->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof Train && !$entity instanceof MaintenanceTrain) {
                continue;
            }

            $changes = $uow->getEntityChangeSet($entity);

            $formattedChanges = [];
            foreach ($changes as $field => [$oldValue, $newValue]) {
                $formattedChanges[$field] = [
                    'old' => $this->normalizeValue($oldValue),
                    'new' => $this->normalizeValue($newValue),
                ];
            }

            $entityType = (new ReflectionClass($entity))->getShortName();
            $entityId = $entity instanceof Train ? $entity->getIdTrain() : $entity->getIdMaintenance();

            $this->pendingLogs[] = [
                'entityType' => $entityType,
                'entityId' => $entityId,
                'action' => 'UPDATE',
                'data' => $formattedChanges,
            ];
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->pendingLogs)) {
            return;
        }

        foreach ($this->pendingLogs as $logInfo) {
            $auditLog = new AuditLog();
            $auditLog->setEntityType($logInfo['entityType']);
            $auditLog->setEntityId($logInfo['entityId']);
            $auditLog->setAction($logInfo['action']);
            $auditLog->setLoggedAt(new \DateTimeImmutable());
            $auditLog->setData($logInfo['data']);

            $this->em->persist($auditLog);
        }

        $this->pendingLogs = [];
        $this->em->flush();
    }

    private function normalizeValue($value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        if (is_object($value)) {
            return method_exists($value, '__toString') ? (string) $value : get_class($value);
        }
        return $value;
    }
}
