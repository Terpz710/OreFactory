<?php

declare(strict_types=1);

namespace terpz710\orefactory\generator;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use terpz710\orefactory\OreFactory;

final class UpgradeManager {

    private Config $data;

    private array $generatorSpeeds = [
        1 => 10,
        2 => 9,
        3 => 8,
        4 => 7,
        5 => 6
    ];

    public function __construct(private OreFactory $plugin) {
        $this->plugin = $plugin;
        
        $this->data = new Config($this->plugin->getDataFolder() . "data.json");
    }

    public function init(Player $player) : void {
        $uuid = $player->getUniqueId()->toString();

        if (!$this->data->exists($uuid)) {
            $this->data->set($uuid, ["level" => 1]);
            $this->data->save();
        }
    }

    public function getGeneratorLevel(Player $player, callable $callback) : void{
        $uuid = $player->getUniqueId()->toString();
        $level = $this->data->get($uuid, ["level" => 1])["level"];
        $callback($level);
    }

    public function upgradeGenerator(Player $player) : void{
        $uuid = $player->getUniqueId()->toString();

        $this->getGeneratorLevel($player, function(int $currentLevel) use ($player, $uuid) {
            if ($currentLevel >= 5) {
                $player->sendMessage("§cYour generator is already at max level!");
                return;
            }

            $newLevel = $currentLevel + 1;
            $this->data->set($uuid, ["level" => $newLevel]);
            $this->data->save();

            $player->sendMessage("§aGenerator upgraded to level " . $newLevel . "!");
        });
    }

    public function getGenerationSpeed(Player $player, callable $callback) : void{
        $this->getGeneratorLevel($player, function(int $level) use ($callback) {
            $callback($this->generatorSpeeds[$level]);
        });
    }
}
