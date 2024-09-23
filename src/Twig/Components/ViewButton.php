<?php

namespace App\Twig\Components;

use App\Entity\Note;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class ViewButton
{
    public Note $note;

    // public function __construct(private Security $security)
    // {
    //     $this->currentUser = $this->security->getUser();
    // }

    public function getViews(): int
    {
        return $this->note->getViews()->count();
    }
}
