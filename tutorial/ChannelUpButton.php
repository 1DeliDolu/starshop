<?php

namespace App\Remote\Button;

final class ChannelUpButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Change the channel up');
    }
}
