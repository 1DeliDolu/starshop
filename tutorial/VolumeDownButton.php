<?php

namespace App\Remote\Button;

final class VolumeDownButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Change the volume down');
    }
}
