<?php
declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\FormBookingRepository')]
#[ORM\Table(name: 'form_booking')]
class FormBookingEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $arrivalDate = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $departureDate = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $numberOfPersons = '';

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $childBirthdate = null;

    #[ORM\Column(type: 'text')]
    private string $childAddress = '';

    #[ORM\Column(type: 'boolean')]
    private bool $hasSwimExperience = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $swimExperienceDetails = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'boolean')]
    private bool $maySwimWithoutAid = false;

    #[ORM\Column(type: 'string', length: 255)]
    private string $contactName = '';

    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    private ?string $contactPhone = null;

    #[ORM\Column(type: 'string', length: 200)]
    private string $contactEmail = '';

    #[ORM\Column(type: 'boolean')]
    private bool $isMemberOfClub = false;

    #[ORM\Column(type: 'string', length: 100)]
    private string $paymentMethod = '';

    #[ORM\Column(type: 'boolean')]
    private bool $participationConsent = false;

    #[ORM\Column(type: 'boolean')]
    private bool $liabilityAcknowledged = false;

    #[ORM\Column(type: 'boolean')]
    private bool $photoConsent = false;

    #[ORM\Column(type: 'boolean')]
    private bool $dataConsent = false;

    #[ORM\Column(type: 'boolean')]
    private bool $bookingConfirmation = false;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $confirmationToken;

    #[ORM\Column(type: 'boolean')]
    private bool $isConfirmed = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $confirmedAt = null;

    #[ORM\OneToOne(targetEntity: FormSubmissionMetaEntity::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(name: 'meta_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?FormSubmissionMetaEntity $meta = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->confirmationToken = bin2hex(random_bytes(32));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getArrivalDate(): ?DateTimeImmutable
    {
        return $this->arrivalDate;
    }

    public function setArrivalDate(?DateTimeImmutable $arrivalDate): self
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }

    public function getDepartureDate(): ?DateTimeImmutable
    {
        return $this->departureDate;
    }

    public function setDepartureDate(?DateTimeImmutable $departureDate): self
    {
        $this->departureDate = $departureDate;

        return $this;
    }

    public function getNumberOfPersons(): string
    {
        return $this->numberOfPersons;
    }

    public function setNumberOfPersons(string $numberOfPersons): self
    {
        $this->numberOfPersons = $numberOfPersons;

        return $this;
    }

    public function getChildBirthdate(): ?DateTimeImmutable
    {
        return $this->childBirthdate;
    }

    public function setChildBirthdate(?DateTimeImmutable $childBirthdate): self
    {
        $this->childBirthdate = $childBirthdate;

        return $this;
    }

    public function getChildAddress(): string
    {
        return $this->childAddress;
    }

    public function setChildAddress(string $childAddress): self
    {
        $this->childAddress = $childAddress;

        return $this;
    }

    public function hasSwimExperience(): bool
    {
        return $this->hasSwimExperience;
    }

    public function setHasSwimExperience(bool $hasSwimExperience): self
    {
        $this->hasSwimExperience = $hasSwimExperience;

        return $this;
    }

    public function getSwimExperienceDetails(): ?string
    {
        return $this->swimExperienceDetails;
    }

    public function setSwimExperienceDetails(?string $swimExperienceDetails): self
    {
        $this->swimExperienceDetails = $swimExperienceDetails;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function maySwimWithoutAid(): bool
    {
        return $this->maySwimWithoutAid;
    }

    public function setMaySwimWithoutAid(bool $maySwimWithoutAid): self
    {
        $this->maySwimWithoutAid = $maySwimWithoutAid;

        return $this;
    }

    public function getContactName(): string
    {
        return $this->contactName;
    }

    public function setContactName(string $contactName): self
    {
        $this->contactName = $contactName;

        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): self
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(string $contactEmail): self
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function isMemberOfClub(): bool
    {
        return $this->isMemberOfClub;
    }

    public function setIsMemberOfClub(bool $isMemberOfClub): self
    {
        $this->isMemberOfClub = $isMemberOfClub;

        return $this;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function hasParticipationConsent(): bool
    {
        return $this->participationConsent;
    }

    public function setParticipationConsent(bool $participationConsent): self
    {
        $this->participationConsent = $participationConsent;

        return $this;
    }

    public function hasLiabilityAcknowledged(): bool
    {
        return $this->liabilityAcknowledged;
    }

    public function setLiabilityAcknowledged(bool $liabilityAcknowledged): self
    {
        $this->liabilityAcknowledged = $liabilityAcknowledged;

        return $this;
    }

    public function hasPhotoConsent(): bool
    {
        return $this->photoConsent;
    }

    public function setPhotoConsent(bool $photoConsent): self
    {
        $this->photoConsent = $photoConsent;

        return $this;
    }

    public function hasDataConsent(): bool
    {
        return $this->dataConsent;
    }

    public function setDataConsent(bool $dataConsent): self
    {
        $this->dataConsent = $dataConsent;

        return $this;
    }

    public function hasBookingConfirmation(): bool
    {
        return $this->bookingConfirmation;
    }

    public function setBookingConfirmation(bool $bookingConfirmation): self
    {
        $this->bookingConfirmation = $bookingConfirmation;

        return $this;
    }

    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): self
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    public function getConfirmedAt(): ?DateTimeImmutable
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?DateTimeImmutable $confirmedAt): self
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    public function getMeta(): ?FormSubmissionMetaEntity
    {
        return $this->meta;
    }

    public function setMeta(?FormSubmissionMetaEntity $meta): self
    {
        $this->meta = $meta;

        return $this;
    }
}
