<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(
 *     name="payment",
 *     indexes={
 *        @ORM\Index(name="payment_created_at_index", columns={"created_at"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="payment_id_index", columns={"id"}),
 *         @ORM\UniqueConstraint(name="payment_session_uuid_index", columns={"session_uuid"})
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 *
 * @OA\Schema()
 */
class Payment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", unique=true)
     *
     * @OA\Property(property="id", type="integer", example="34")
     */
    private int $id;

    /**
     * @ORM\Column(name="amount", type="json", nullable=false)
     *
     * @OA\Property(
     *     type="object",
     *     @OA\Property(property="value", type="float", example=1000.03),
     *     @OA\Property(property="currency", type="string", example="RUB", enum={"RUB"})
     * )
     */
    private array $amount;

    /**
     * @ORM\Column(name="purpose", type="string", nullable=false)
     *
     * @OA\Property(property="puprose", type="string", description="Назначение платежа")
     */
    private string $purpose;

    /**
     * @ORM\Column(name="session_uuid", type="guid", nullable=false)
     *
     * @OA\Property(property="session_uuid", type="string", example="97643036-9d94-11ea-b769-0242ac160002")
     */
    private string $sessionUUID;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PaymentSession", inversedBy="payment")
     * @ORM\JoinColumn(name="session_uuid", referencedColumnName="uuid")
     */
    private PaymentSession $session;

    /**
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     *
     * @OA\Property(property="created_at", type="string", format="datetime", example="2020-05-23 18:31:45")
     */
    private DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSession(): PaymentSession
    {
        return $this->session;
    }

    public function setSession(PaymentSession $session): Payment
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return array
     */
    public function getAmount(): array
    {
        return $this->amount;
    }

    /**
     * @param array $amount
     *
     * @return Payment
     */
    public function setAmount(array $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPurpose(): string
    {
        return $this->purpose;
    }

    /**
     * @param string $purpose
     *
     * @return Payment
     */
    public function setPurpose(string $purpose): self
    {
        $this->purpose = $purpose;

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
