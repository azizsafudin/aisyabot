<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpociot\BotMan\BotMan;

class BotCommands extends Controller
{
    public function getName($bot){
        $bot->typesAndWaits(3);
        $firstname = $bot->getFirstName();
        $bot->reply('Hello, '. $firstname);
    }
}
