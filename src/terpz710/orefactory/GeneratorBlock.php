<?php

declare(strict_types=1);

namespace terpz710\orefactory;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\player\Player;

use pocketmine\item\Item;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\enchantment\EnchantmentInstance;

use pocketmine\block\VanillaBlocks;

use pocketmine\nbt\tag\StringTag;

final class GeneratorBlock {

    public function __construct(protected OreFactory $plugin) {
        $this->plugin = $plugin;
    }

    public function getGeneratorBlock(Player $player, int $amount = 1) : Item{
        $block = VanillaBlocks::GLOWING_OBSIDIAN()->asItem();
        $block->setCount($amount);
        $block->setCustomName("§r§l§4Generator Block");
        $block->setLore([
            "",
            "§r§f(§e!§f) Onced placed random ore will generate on top of the generator!",
            ""
        ]);
        $block->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(OreFactory::FAKE_ENCH_ID), 1));
        $nbt = $block->getNamedTag();
        $nbt->setTag("Generator", new StringTag("ore_gen"));
        $block->setNamedTag($nbt);
        return $block;
    }
}