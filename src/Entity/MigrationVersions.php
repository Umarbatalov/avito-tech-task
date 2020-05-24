<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="migration_versions")
 * @ORM\Entity
 */
class MigrationVersions
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="version", type="string", length=14)
     */
    private string $version;

    /**
     * @ORM\Column(name="executed_at", type="datetime_immutable")
     */
    private DateTimeImmutable $executedAt;
}
