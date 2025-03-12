<?php

declare(strict_types=1);

namespace terpz710\orefactory\command;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\orefactory\OreFactory;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;

class GeneratorCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("orefactory.cmd");

        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new IntegerArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$this->testPermission($sender)) {
            return;
        }

        if (!isset($args["player"], $args["amount"])) {
            $sender->sendMessage("Usage: /generator <player> <amount>");
            return;
        }

        $playerName = $args["player"];
        $amount = $args["amount"];

        if ($amount <= 0) {
            $sender->sendMessage("Amount must be a positive integer!");
            return;
        }

        $targetPlayer = Server::getInstance()->getPlayerByPrefix($playerName);

        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage("The player " . $playerName . " was not found, Make sure they're online or exist...");
            return;
        }

        $block = OreFactory::getInstance()->getGeneratorBlock()->giveGeneratorBlock($targetPlayer, $amount);
        $targetPlayer->getInventory()->addItem($block);

        $targetPlayer->sendMessage("Received " . $amount . " ore generator from " . $sender->getName() . "!");
        $sender->sendMessage("Gave " . $amount . " ore generator to " . $targetPlayer->getName() . "!");
    }
}