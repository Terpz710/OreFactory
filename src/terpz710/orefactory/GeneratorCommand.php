<?php

declare(strict_types=1);

namespace terpz710\orefactory;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\Server;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use function count;
use function intval;

class GeneratorCommand extends Command implements PluginOwned {

    public function __construct(protected OreFactory $plugin) {
        parent::__construct("generator");
        $this->setDescription("Give ore generator block");
        $this->setUsage("Usage: /generator <player> <amount>");
        $this->setPermission("orefactory.cmd");

        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$this->testPermission($sender)) {
            return false;
        }

        if (count($args) !== 2) {
            $sender->sendMessage($this->getUsage());
            return false;
        }

        $playerName = $args[0];
        $amount = intval($args[1]);

        if ($amount <= 0) {
            $sender->sendMessage("Amount must be a positive integer!");
            return false;
        }

        $targetPlayer = Server::getInstance()->getPlayerByPrefix($playerName);
        
        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage("The player " . $playerName . " was not found, Make sure they're online or exist...");
            return false;
        }

        $block = OreFactory::getInstance()->getGenerator()->getGeneratorBlock($targetPlayer, $amount);
        $targetPlayer->getInventory()->addItem($block);

        $targetPlayer->sendMessage("Recieved " . $amount . " ore generator from " . $sender->getName() . "!");
        $sender->sendMessage("Gave " . $amount . " ore generator to " . $targetPlayer->getName() . "!");
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}