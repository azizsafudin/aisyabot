# ﷽

# AisyaBot

This repository is a bot made for msociety's telegram group chat. Using Botman.io, it can be expanded to other platforms such as FB Messenger and Slack.

## Requires

- Laravel 5.4
- MySQL 5.6

## Installation

Run the usual stuff like `composer install` and `php artisan migrate` after cloning to the server.

Bots can only work from secure https domains. Set up webhook to `domain.com/botman`.

Add wit.ai tokens and `bot_name` in the `.env` file. 

## Features

The following are the intents that AisyaBot is listening out for:

- salam
- set_intro
- start_conversation
- create_poll

Validate responses on the wit.ai app to ensure AisyaBot's NLP works smoother.
