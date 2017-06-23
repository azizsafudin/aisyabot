<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bot extends Model
{
    const FALLBACK = [
        'I\'m sorry, I didn\'t quite catch that..',
        'Hmm?',
        'Sorry, what?',
        'I don\'t understand. :(',
        'Perhaps, you could repeat that?',
        'I don\'t understand what you just said.',
        'Could you repeat again? Please.. :)',
        'I\'m sorry, what?'
    ];
}
