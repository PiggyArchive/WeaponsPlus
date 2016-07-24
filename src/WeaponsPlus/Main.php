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
    public $landminestatuses;
    public $landminestatuseslist;
    public $spearstatuses;
    public $spearstatuseslist;
    public $bazukastatuses;
    public $bazukastatuseslist;
    public $enderpearlstatuses;
    public $enderpearlstatuseslist;

    public function onEnable() {
        $this->ebstatuseslist = new Config($this->getDataFolder() . "eb.yml", Config::YAML);
        $this->grenadestatuseslist = new Config($this->getDataFolder() . "grenades.yml", Config::YAML);
        $this->landminestatuseslist = new Config($this->getDataFolder() . "landmines.yml", Config::YAML);
        $this->spearstatuseslist = new Config($this->getDataFolder() . "spears.yml", Config::YAML);
        $this->bazukastatuseslist = new Config($this->getDataFolder() . "bazukas.yml", Config::YAML);
        $this->enderpearlstatuseslist = new Config($this->getDataFolder() . "enderpearls.yml", Config::YAML);
        $this->loadEBStatuses();
        $this->loadGrenadeStatuses();
        $this->loadLandmineStatuses();
        $this->loadSpearStatuses();
        $this->loadBazukaStatuses();
        $this->loadEnderpearlStatuses();
        $this->saveDefaultConfig();
        if($this->getConfig()->get("version") < 1) {
            if($this->getConfig()->get("update-config")) {
                rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config.yml.".time().".bak");
                $this->saveDefaultConfig(); //Recreate config :P
                $this->getLogger()->info("§aYour config was out of date and has been updated.");
            } else {
                $this->getLogger()->critical("§cYour config is out of date!");
            }
        }
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

    public function loadLandmineStatuses() {
        foreach($this->landminestatuseslist->getAll() as $name => $status) {
            $this->landminestatuses[strtolower($name)] = $status;
        }
    }

    public function saveLandmineStatuses() {
        foreach($this->landminestatuses as $name => $status) {
            $this->landminestatuseslist->set($name, $status);
        }
        $this->landminestatuseslist->save();
    }

    public function enableLandmines(Player $player) {
        $this->landminestatuses[strtolower($player->getName())] = true;
        $this->saveLandmineStatuses();
    }

    public function disableLandmines(Player $player) {
        $this->landminestatuses[strtolower($player->getName())] = false;
        $this->saveLandmineStatuses();
    }

    public function getLandmineStatus(Player $player) {
        if(!isset($this->landminestatuses[strtolower($player->getName())])) return false;
        return $this->landminestatuses[strtolower($player->getName())];
    }

    public function loadSpearStatuses() {
        foreach($this->spearstatuseslist->getAll() as $name => $status) {
            $this->spearstatuses[strtolower($name)] = $status;
        }
    }

    public function saveSpearStatuses() {
        foreach($this->spearstatuses as $name => $status) {
            $this->spearstatuseslist->set($name, $status);
        }
        $this->spearstatuseslist->save();
    }

    public function enableSpears(Player $player) {
        $this->spearstatuses[strtolower($player->getName())] = true;
        $this->saveSpearStatuses();
    }

    public function disableSpears(Player $player) {
        $this->spearstatuses[strtolower($player->getName())] = false;
        $this->saveSpearStatuses();
    }

    public function getSpearStatus(Player $player) {
        if(!isset($this->spearstatuses[strtolower($player->getName())])) return false;
        return $this->spearstatuses[strtolower($player->getName())];
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

    public function getBazukaStatus(Player $player) {
        if(!isset($this->bazukastatuses[strtolower($player->getName())])) return false;
        return $this->bazukastatuses[strtolower($player->getName())];
    }

    public function loadEnderpearlStatuses() {
        foreach($this->enderpearlstatuseslist->getAll() as $name => $status) {
            $this->enderpearlstatuses[strtolower($name)] = $status;
        }
    }

    public function saveEnderpearlStatuses() {
        foreach($this->enderpearlstatuses as $name => $status) {
            $this->enderpearlstatuseslist->set($name, $status);
        }
        $this->enderpearlstatuseslist->save();
    }

    public function enableEnderpearls(Player $player) {
        $this->enderpearlstatuses[strtolower($player->getName())] = true;
        $this->saveEnderpearlStatuses();
    }

    public function disableEnderpearls(Player $player) {
        $this->enderpearlstatuses[strtolower($player->getName())] = false;
        $this->saveEnderpearlStatuses();
    }

    public function getEnderpearlStatus(Player $player) {
        if(!isset($this->enderpearlstatuses[strtolower($player->getName())])) return false;
        return $this->enderpearlstatuses[strtolower($player->getName())];
    }

}
