<?php

declare(strict_types=1);

namespace terpz710\orefactory;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\plugin\PluginBase;

class OreFactory extends PluginBase {

    protected static self $instance;
    
    public const FAKE_ENCH_ID = -1;

    protected GeneratorBlock $gen;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("OreGen", new GeneratorCommand($this));
        $this->gen = new GeneratorBlock($this);
        EnchantmentIdMap::getInstance()->register(
            self::FAKE_ENCH_ID,
            new Enchantment("Glow", 1, ItemFlags::ALL, ItemFlags::NONE, 1)
        );
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function getGenerator() : GeneratorBlock{
        return $this->gen;
    }
}
