<?php

namespace App\Conversations;

use Mpociot\BotMan\Answer;
use Mpociot\BotMan\Conversation;
use Mpociot\BotMan\Question;

class RandomNumberConversation extends Conversation
{

    protected $answer;

    protected $intro;
    /**
     * First question
     */
    public function giveNumber()
    {
        $rand = rand();
        return $this->ask('Sure, is '.$rand.' ok?', [
            [
                'pattern' => 'yes|yep|y|yup',
                'callback' => function () {
                    $this->say('Okay - we\'ll keep going.');
                    $this->askNumber();
                }
            ],
            [
                'pattern' => 'nah|no|nope|n|nop',
                'callback' => function () {
                    $this->say('Awesome, glad to be able to help!');
                }
            ]
        ]);
    }
    public function askNumber(){
        $rand = rand();
        return $this->ask('Would you like another random number?', [
            [
                'pattern' => 'yes|yep|y|yup',
                'callback' => function () {
                    $this->say('Okay - we\'ll keep going.');
                    $this->askNumber();
                }
            ],
            [
                'pattern' => 'nah|no|nope|n|nop',
                'callback' => function () {
                    $this->say('Nice, glad to be of service!');
                }
            ]
        ]);
    }


    /**
     * Start the conversation
     */
    public function run()
    {
        $this->giveNumber();
    }

}