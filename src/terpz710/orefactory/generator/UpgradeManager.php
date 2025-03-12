<?php

declare(strict_types=1);

namespace terpz710\orefactory\generator;

use pocketmine\player\Player;

use terpz710\orefactory\OreFactory;

use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

final class UpgradeManager {

    protected DataConnector $db;

    protected array $generatorSpeeds = [
        1 => 10,
        2 => 9,
        3 => 8,
        4 => 7,
        5 => 6
    ];

    public function __construct(protected OreFactory $plugin) {
        $this->plugin = $plugin;
    }

    public function table() : void{
        $this->db = libasynql::create($this->plugin, $this->plugin->getConfig()->get("database"), [
            "sqlite" => "database/sqlite.sql",
            "mysql" => "database/mysql.sql"
        ]);

        $this->db->executeGeneric("table.generators");
    }

    public function init(Player $player) : void {
        $uuid = $player->getUniqueId()->toString();

        $this->db->executeSelect("generators.select_level", ["uuid" => $uuid], function(array $rows) use ($uuid) {
            if (empty($rows)) {
                $this->db->executeChange("generators.insert", ["uuid" => $uuid]);
            }
        });
    }

    public function getGeneratorLevel(Player $player, callable $callback) : void{
        $uuid = $player->getUniqueId()->toString();

        $this->db->executeSelect("generators.select_level", ["uuid" => $uuid], function(array $rows) use ($callback) {
            $callback($rows[0]["level"]);
        });
    }

    public function upgradeGenerator(Player $player) : void{
        $uuid = $player->getUniqueId()->toString();

        $this->getGeneratorLevel($player, function(int $currentLevel) use ($player, $uuid) {
            if ($currentLevel >= 5) {
                $player->sendMessage("§cYour generator is already at max level!");
                return;
            }

            $newLevel = $currentLevel + 1;
            $this->db->executeChange("generators.update_level", ["uuid" => $uuid, "level" => $newLevel]);

            $player->sendMessage("§aGenerator upgraded to level " . $newLevel . "!");
        });
    }

    public function getGenerationSpeed(Player $player, callable $callback) : void{
        $this->getGeneratorLevel($player, function(int $level) use ($callback) {
            $callback($this->generatorSpeeds[$level]);
        });
    }

    public function close() : void{
        $this->db->close();
    }
}
