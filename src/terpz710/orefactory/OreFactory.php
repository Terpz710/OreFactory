<?php

declare(strict_types=1);

namespace terpz710\orefactory;

use pocketmine\plugin\PluginBase;

class OreFactory extends PluginBase {

    protected static self $instance;

    protected GeneratorBlock $gen;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("OreGen", new GeneratorCommand($this));
        $this->gen = new GeneratorBlock($this);
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function getGenerator() : GeneratorBlock{
        return $this->gen;
    }
}