<?php

namespace App\Remote\Button;

final class ChannelDownButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Change the channel down');
    }
}
