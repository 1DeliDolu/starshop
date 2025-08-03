<?php

namespace App\Remote\Button;

final class VolumeUpButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Change the volume up');
    }
}
