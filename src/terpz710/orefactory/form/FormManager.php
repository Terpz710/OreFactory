<?php

declare(strict_types=1);

namespace terpz710\orefactory\form;

use pocketmine\player\Player;

use pocketmine\block\VanillaBlocks;
use pocketmine\block\BlockTypeIds;

use pocketmine\data\bedrock\EnchantmentIdMap;

use pocketmine\item\enchantment\EnchantmentInstance;

use pocketmine\nbt\tag\StringTag;

use pocketmine\utils\SingletonTrait;

use terpz710\orefactory\OreFactory;

use terpz710\pocketforms\SimpleForm;

final class FormManager {
    use SingletonTrait;

    protected OreFactory $plugin;

    public function __construct() {
        $this->plugin = OreFactory::getInstance();
    }

    public function showGeneratorOptions(Player $player) : void{
        $form = (new SimpleForm())
            ->setTitle("Generator Options")
            ->setContent("Choose an option:")
            ->addButton("Remove Generator")
            ->addButton("Upgrade Generator")
            ->setCallback(function (Player $player, ?int $data) {
                if ($data === null) return;

                if ($data === 0) {
                    $this->removeGenerator($player);
                } elseif ($data === 1) {
                    $this->handleUpgradeGenerator($player);
                }
            });

        $player->sendForm($form);
    }

    private function removeGenerator(Player $player) : void{
        $block = $player->getWorld()->getBlock($player->getEyePos()->addVector($player->getDirectionVector()->multiply(2)));

        if ($block->getTypeId() === BlockTypeIds::GLOWING_OBSIDIAN) {
            $item = VanillaBlocks::GLOWING_OBSIDIAN()->asItem();
            $item->setCustomName("§r§l§4Generator Block");
            $item->setLore([
                "",
                "§r§f(§e!§f) Once placed, a random ore will generate on top of the generator!",
                ""
            ]);
            $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(OreFactory::FAKE_ENCH_ID), 1));

            $nbt = $item->getNamedTag();
            $nbt->setTag("Generator", new StringTag("ore_gen"));
            $item->setNamedTag($nbt);

            $player->getInventory()->addItem($item);
            $player->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());

            $player->sendMessage("§cGenerator removed!");
        } else {
            $player->sendMessage("§cYou are not looking at a generator!");
        }
    }

    private function handleUpgradeGenerator(Player $player) : void{
        $upgradeManager = $this->plugin->getUpgradeManager();
        
        $upgradeManager->getGeneratorLevel($player, function(int $currentLevel) use ($player, $upgradeManager) {
            if ($currentLevel >= 5) {
                $player->sendMessage("§cYour generator is already at max level!");
                return;
            }

            $upgradeManager->upgradeGenerator($player);
        });
    }
}