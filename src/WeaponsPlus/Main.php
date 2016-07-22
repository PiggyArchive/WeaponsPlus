<?php
namespace WeaponsPlus;

use pocketmine\inventory\BigShapedRecipe;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;

use WeaponsPlus\Commands\WeaponsPlusCommand;

class Main extends PluginBase {
    public $ebstatuses;
    public $ebstatuseslist;
    public $grenadestatuses;
    public $grenadestatuseslist;
    public $bazukastatuses;
    public $bazukastatuseslist;

    public function onEnable() {
        $this->ebstatuseslist = new Config($this->getDataFolder() . "eb.yml", Config::YAML);
        $this->grenadestatuseslist = new Config($this->getDataFolder() . "grenades.yml", Config::YAML);
        $this->bazukastatuseslist = new Config($this->getDataFolder() . "bazukas.yml", Config::YAML);
        $this->loadEBStatuses();
        $this->loadGrenadeStatuses();
        $this->loadBazukaStatuses();
        $this->saveDefaultConfig();
        $grenade = Item::get(Item::SNOWBALL, 0, 1);
        $grenade->setCustomName("Grenade");
        $this->getServer()->getCraftingManager()->registerRecipe((new BigShapedRecipe($grenade, "III", "IGI", "III"))->setIngredient("I", Item::get(Item::IRON_INGOT, null))->setIngredient("G", Item::get(Item::GUNPOWDER, null)));
        $landmine = Item::get($this->getConfig()->get("landmine"), 0, 1);
        $landmine->setCustomName("Landmine");
        $this->getServer()->getCraftingManager()->registerRecipe((new BigShapedRecipe($landmine, "QPQ", "QTQ", "QQQ"))->setIngredient("Q", Item::get(Item::QUARTZ, null))->setIngredient("P", Item::get(70, null))->setIngredient("T", Item::get(Item::TNT, null)));
        $spear = Item::get($this->getConfig()->get("spear"), 0, 1);
        $spear->setCustomName("Spear");
        $this->getServer()->getCraftingManager()->registerRecipe((new BigShapedRecipe($spear, "  I", " S ", "S  "))->setIngredient("S", Item::get(Item::STICK, null))->setIngredient("I", Item::get(Item::IRON_INGOT, null)));
        $this->getServer()->getCommandMap()->register('weaponsplus', new WeaponsPlusCommand('weaponsplus', $this));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getLogger()->info("§aEnabled.");
    }

    public function loadEBStatuses() {
        foreach($this->ebstatuseslist->getAll() as $name => $status) {
            $this->ebstatuses[strtolower($name)] = $status;
        }
    }

    public function saveEBStatuses() {
        foreach($this->ebstatuses as $name => $status) {
            $this->ebstatuseslist->set($name, $status);
        }
        $this->ebstatuseslist->save();
    }

    public function enableEB(Player $player) {
        $this->ebstatuses[strtolower($player->getName())] = true;
        $this->saveEBStatuses();
    }

    public function disableEB(Player $player) {
        $this->ebstatuses[strtolower($player->getName())] = false;
        $this->saveEBStatuses();
    }

    public function getEBStatus(Player $player) {
        if(!isset($this->ebstatuses[strtolower($player->getName())])) return false;
        return $this->ebstatuses[strtolower($player->getName())];
    }

    public function loadGrenadeStatuses() {
        foreach($this->grenadestatuseslist->getAll() as $name => $status) {
            $this->grenadestatuses[strtolower($name)] = $status;
        }
    }

    public function saveGrenadeStatuses() {
        foreach($this->grenadestatuses as $name => $status) {
            $this->grenadestatuseslist->set($name, $status);
        }
        $this->grenadestatuseslist->save();
    }

    public function enableGrenades(Player $player) {
        $this->grenadestatuses[strtolower($player->getName())] = true;
        $this->saveGrenadeStatuses();
    }

    public function disableGrenades(Player $player) {
        $this->grenadestatuses[strtolower($player->getName())] = false;
        $this->saveGrenadeStatuses();
    }

    public function getGrenadeStatus(Player $player) {
        if(!isset($this->grenadestatuses[strtolower($player->getName())])) return false;
        return $this->grenadestatuses[strtolower($player->getName())];
    }

    public function loadBazukaStatuses() {
        foreach($this->bazukastatuseslist->getAll() as $name => $status) {
            $this->bazukastatuses[strtolower($name)] = $status;
        }
    }

    public function saveBazukaStatuses() {
        foreach($this->bazukastatuses as $name => $status) {
            $this->bazukastatuseslist->set($name, $status);
        }
        $this->bazukastatuseslist->save();
    }

    public function enableBazukas(Player $player) {
        $this->bazukastatuses[strtolower($player->getName())] = true;
        $this->saveBazukaStatuses();
    }

    public function disableBazukas(Player $player) {
        $this->bazukastatuses[strtolower($player->getName())] = false;
        $this->saveBazukaStatuses();
    }

    public function getBazukasStatus(Player $player) {
        if(!isset($this->bazukastatuses[strtolower($player->getName())])) return false;
        return $this->bazukastatuses[strtolower($player->getName())];
    }

}
