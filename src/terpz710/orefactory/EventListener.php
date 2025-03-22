<?php

declare(strict_types=1);

namespace terpz710\orefactory;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\block\VanillaBlocks;
use pocketmine\block\BlockTypeIds;

use pocketmine\math\Facing;
use pocketmine\world\World;

use pocketmine\scheduler\ClosureTask;

use terpz710\orefactory\form\FormManager;

class EventListener implements Listener {

    protected array $ores = [];

    public function __construct(protected OreFactory $plugin) {
        $this->plugin = $plugin;
        
        $this->ores = [
            VanillaBlocks::COAL_ORE(),
            VanillaBlocks::IRON_ORE(),
            VanillaBlocks::GOLD_ORE(),
            VanillaBlocks::REDSTONE_ORE(),
            VanillaBlocks::LAPIS_LAZULI_ORE(),
            VanillaBlocks::DIAMOND_ORE(),
            VanillaBlocks::EMERALD_ORE()
        ];
    }

    public function onBlockPlace(BlockPlaceEvent $event) : void{
        $item = $event->getItem();
        $nbt = $item->getNamedTag();

        if ($nbt->getTag("Generator")) {
            foreach ($event->getTransaction()->getBlocks() as [$x, $y, $z, $block]) {
                $positionAbove = $block->getPosition()->getSide(Facing::UP);
                $randomOre = $this->ores[array_rand($this->ores)];
                $block->getPosition()->getWorld()->setBlock($positionAbove, $randomOre);
            }
        }
    }

    public function onBlockBreak(BlockBreakEvent $event) : void{
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $world = $block->getPosition()->getWorld();
        $positionBelow = $block->getPosition()->getSide(Facing::DOWN);
        $blockBelow = $world->getBlock($positionBelow);

        if (in_array($block->getTypeId(), [
            BlockTypeIds::COAL_ORE,
            BlockTypeIds::IRON_ORE,
            BlockTypeIds::GOLD_ORE,
            BlockTypeIds::REDSTONE_ORE,
            BlockTypeIds::LAPIS_LAZULI_ORE,
            BlockTypeIds::DIAMOND_ORE,
            BlockTypeIds::EMERALD_ORE
        ]) && $blockBelow->getTypeId() === BlockTypeIds::GLOWING_OBSIDIAN) {
            
            $this->plugin->getUpgradeManager()->getGenerationSpeed($player, function(int $speed) use ($world, $block) {
                $randomOre = $this->ores[array_rand($this->ores)];
                
                $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($world, $block, $randomOre) : void {
                    $world->setBlock($block->getPosition(), $randomOre);
                }), $speed * 20);
            });
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event) : void{
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if ($block->getTypeId() === BlockTypeIds::GLOWING_OBSIDIAN) {
            FormManager::getInstance()->showGeneratorOptions($player);
        }
    }

    public function onJoin(PlayerJoinEvent $event) : void{
        $this->plugin->getUpgradeManager()->init($event->getPlayer());
    }
}
