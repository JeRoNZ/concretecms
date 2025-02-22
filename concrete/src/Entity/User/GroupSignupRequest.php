<?php

namespace Concrete\Core\Entity\User;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\UserInfo;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="GroupSignupRequests"
 * )
 */
class GroupSignupRequest implements SubjectInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null NULL if not yet flushed to the database
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Notification\GroupSignupRequestNotification", mappedBy="signup", cascade={"remove"}),
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $notifications;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @var int
     */
    protected $gID;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="SET NULL")
     *
     * @var \Concrete\Core\Entity\User\User|null
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $requested;

    /**
     * @param \Concrete\Core\User\Group\Group|null $group
     * @param \Concrete\Core\User\User $user
     */
    public function __construct($group = null, $user = null)
    {
        $this->notifications = new ArrayCollection();
        if ($group instanceof Group) {
            $this->gID = $group->getGroupID();
        }
        if ($user instanceof \Concrete\Core\User\User) {
            if ($user->getUserInfoObject() instanceof UserInfo) {
                $this->user = $user->getUserInfoObject()->getEntityObject();
            }
        }
        $this->requested = new DateTime();
    }

    /**
     * @return \Concrete\Core\User\Group\Group|null
     */
    public function getGroup()
    {
        return Group::getByID($this->getGID());
    }

    /**
     * @return int|null return NULL if not yet flushed to the database
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getGID(): int
    {
        return $this->gID;
    }

    /**
     * @return $this
     */
    public function setGID(int $gID): self
    {
        $this->gID = $gID;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRequested(): DateTime
    {
        return $this->requested;
    }

    /**
     * @return $this
     */
    public function setRequested(DateTime $requested): self
    {
        $this->requested = $requested;

        return $this;
    }

    /**
     * Get the date of this notification.
     *
     * @return \DateTime
     */
    public function getNotificationDate()
    {
        return $this->requested;
    }

    public function getUsersToExcludeFromNotification()
    {
        return [];
    }

    /**
     * @return \Concrete\Core\Entity\Notification\GroupSignupRequestNotification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }
}
