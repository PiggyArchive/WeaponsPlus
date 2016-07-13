<?php
namespace WeaponsPlus;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;

class Main extends PluginBase {
    public $ebstatuses;
    public $ebstatuseslist;

    public function onEnable() {
        $this->ebstatuseslist = new Config($this->getDataFolder() . "eb.yml", Config::YAML);
        $this->loadEBStatuses();
        $this->saveDefaultConfig();
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
        if(!isset($this->ebstatuses[strtolower($player->getName())]))
            return false;
        return $this->ebstatuses[strtolower($player->getName())];
    }

}
