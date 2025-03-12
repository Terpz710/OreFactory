<?php

declare(strict_types=1);

namespace terpz710\orefactory;

use pocketmine\plugin\PluginBase;

use pocketmine\data\bedrock\EnchantmentIdMap;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;

use terpz710\orefactory\generator\GeneratorBlock;
use terpz710\orefactory\generator\UpgradeManager;

use terpz710\orefactory\command\GeneratorCommand;

use CortexPE\Commando\PacketHooker;

class OreFactory extends PluginBase {

    protected static self $instance;

    protected GeneratorBlock $gen;

    protected UpgradeManager $upgrade;

    const FAKE_ENCH_ID = -1;

    protected function onLoad() : void{ self::$instance = $this; }

    protected function onEnable() : void{
        $this->saveDefaultConfig();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->getServer()->getCommandMap()->register("OreFactory", new GeneratorCommand($this, "generator", "Give ore generator block"));

        $this->gen = new GeneratorBlock($this);
        $this->upgrade = new UpgradeManager($this);

        $this->upgrade->table();

        EnchantmentIdMap::getInstance()->register(
            self::FAKE_ENCH_ID,
            new Enchantment("Glow", 1, ItemFlags::ALL, ItemFlags::NONE, 1)
        );
    }

    protected function onDisable() : void{ $this->upgrade->close(); }

    public static function getInstance() : self{ return self::$instance; }

    public function getGeneratorBlock() : GeneratorBlock{ return $this->gen; }

    public function getUpgradeManager() : UpgradeManager{ return $this->upgrade; }
}