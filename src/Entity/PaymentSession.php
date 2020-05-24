<?php

declare(strict_types=1);

namespace App\Entity;

use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use App\Repository\PaymentSessionRepository;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(
 *     name="payment_session",
 *     uniqueConstraints={@UniqueConstraint(name="payment_session_uuid_index", columns={"uuid"})}
 * )
 *
 * @ORM\Entity(repositoryClass=PaymentSessionRepository::class)
 *
 * @OA\Schema()
 */
class PaymentSession
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="uuid", type="guid", nullable=false)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @OA\Property(property="uuid", type="string", example="97643036-9d94-11ea-b769-0242ac160002")
     */
    private string $uuid;

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
     * @ORM\Column(name="confirmation_url", type="string", nullable=true)
     *
     * @OA\Property(property="confirmation_url", type="string", example="https://website.com/return_url")
     */
    private ?string $confirmationUrl;

    /**
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(name="expire_at", type="datetime_immutable", nullable=false)
     */
    private DateTimeImmutable $expireAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", mappedBy="session")
     */
    private ?Payment $payment;

    /**
     * PaymentSession constructor.
     */
    public function __construct()
    {
        $now = new DateTimeImmutable();

        $this->createdAt = $now;
        $this->expireAt = $now->add(new DateInterval('PT30M'));
    }


    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getAmount(): array
    {
        return $this->amount;
    }

    public function setAmount(array $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function setPurpose(string $purpose): self
    {
        $this->purpose = $purpose;

        return $this;
    }

    public function hasExpired(): bool
    {
        return $this->getExpireAt()->getTimestamp() < time();
    }

    public function getExpireAt(): DateTimeImmutable
    {
        return $this->expireAt;
    }

    public function hasNotExpired(): bool
    {
        return !($this->getExpireAt()->getTimestamp() < time());
    }

    public function isPaid(): bool
    {
        return $this->getPayment() !== null;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    /**
     * @return string
     */
    public function getConfirmationUrl(): ?string
    {
        return $this->confirmationUrl;
    }

    /**
     * @param string $confirmationUrl
     *
     * @return PaymentSession
     */
    public function setConfirmationUrl(string $confirmationUrl): self
    {
        $this->confirmationUrl = $confirmationUrl;

        return $this;
    }
}
