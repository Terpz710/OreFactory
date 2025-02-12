<?php

declare(strict_types=1);

namespace terpz710\orefactory;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;

use pocketmine\block\VanillaBlocks;
use pocketmine\block\BlockTypeIds;

use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\enchantment\EnchantmentInstance;

use pocketmine\nbt\tag\StringTag;

use pocketmine\math\Facing;

use pocketmine\world\World;

use pocketmine\scheduler\ClosureTask;

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
            $randomOre = $this->ores[array_rand($this->ores)];
            $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($world, $block, $randomOre): void {
                $world->setBlock($block->getPosition(), $randomOre);
            }), 1);
        }

        if ($block->getTypeId() === BlockTypeIds::GLOWING_OBSIDIAN) {
            $event->setDrops([]);

            $item = VanillaBlocks::GLOWING_OBSIDIAN()->asItem();
            $item->setCustomName("§r§l§4Generator Block");
            $item->setLore([
                "",
                "§r§f(§e!§f) Once placed, a random ore will generate on top of the generator!",
                ""
            ]);
            $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FORTUNE(), 3));

            $nbt = $item->getNamedTag();
            $nbt->setTag("Generator", new StringTag("ore_gen"));
            $item->setNamedTag($nbt);

            $world->dropItem($block->getPosition(), $item);
        }
    }
}