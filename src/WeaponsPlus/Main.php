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

    public function onEnable() {
        $this->ebstatuseslist = new Config($this->getDataFolder() . "eb.yml", Config::YAML);
        $this->grenadestatuseslist = new Config($this->getDataFolder() . "grenades.yml", Config::YAML);
        $this->loadEBStatuses();
        $this->loadGrenadeStatuses();
        $this->saveDefaultConfig();
        $grenade = Item::get(Item::SNOWBALL, 0, 1);
        $grenade->setCustomName("Grenade");
        $this->getServer()->getCraftingManager()->registerRecipe((new BigShapedRecipe($grenade, "III", "IGI", "III"))->setIngredient("I", Item::get(Item::IRON_INGOT, null))->setIngredient("G", Item::get(Item::GUNPOWDER, null)));
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

}
