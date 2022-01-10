<?php

namespace App\Convention\Services\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

/**
 * Class Flusher
 *
 * @package App\Convention\Services\Doctrine
 */
class Flusher implements FlusherContract
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        EntityManagerInterface $manager
    ) {
        $this->manager = $manager;
    }

    /**
     * @inheritdoc
     */
    public function open(): void
    {
        if (!$this->isOpened()) {
            $this->manager->getConnection()->beginTransaction();
        }
    }

    /**
     * @inheritdoc
     */
    public function flush(): void
    {
        $this->manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function commit(bool $fake = false): void
    {
        $this->flush();

        if (!$fake && $this->isOpened()) {
            $this->manager->commit();
        }

    }

    /**
     * @inheritdoc
     */
    public function rollback(bool $fireBroadcast = false): void
    {
        while ($this->isOpened()) {
            $this->manager->rollback();
        }
    }

    /**
     * @inheritdoc
     */
    public function clear(string $objectName = null): void
    {
        $this->clearManager($objectName);
    }

    /**
     * @param string|null $objectName
     *
     * @throws RuntimeException
     */
    private function clearManager(string $objectName = null): void
    {
        $unitOfWork = $this->manager->getUnitOfWork();

        if (count($unitOfWork->getScheduledEntityInsertions())) {
            throw new RuntimeException('[ORM] INSERT:: Call clear method with filled unit of work.');
        }

        if (count($unitOfWork->getScheduledEntityUpdates())) {
            throw new RuntimeException('[ORM] UPDATE:: Call clear method with filled unit of work.');
        }

        if (count($unitOfWork->getScheduledEntityDeletions())) {
            throw new RuntimeException('[ORM] DELETE:: Call clear method with filled unit of work.');
        }

        $this->manager->clear($objectName);
    }

    /**
     * @inheritDoc
     */
    public function connect(): void
    {
        if (!$this->manager->getConnection()->isConnected()) {
            $this->manager->getConnection()->connect();
        }
    }

    /**
     * @inheritDoc
     */
    public function disconnect(): void
    {
        if ($this->manager->getConnection()->isConnected()) {
            $this->manager->getConnection()->close();
        }
    }

    /**
     * @return bool
     */
    private function isOpened(): bool
    {
        return $this->manager->getConnection()->getTransactionNestingLevel() !== 0;
    }
}
