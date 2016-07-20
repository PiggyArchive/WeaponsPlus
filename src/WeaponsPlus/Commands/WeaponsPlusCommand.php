<?php
namespace WeaponsPlus\Commands;

use pocketmine\command\defaults\VanillaCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class BreakReplaceCommand extends VanillaCommand {
    public function __construct($name, $plugin) {
        parent::__construct($name, "Toggle weapons", "/weaponsplus");
        $this->setPermission("weaponsplus.command");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, $currentAlias, array $args) {
        if(!$this->testPermission($sender)) {
            return true;
        }
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cYou must use the command in-game.");
            return false;
        }
        if(!isset($args[0])) {
            $sender->sendMessage("/weaponplus <weapon|list>");
            return false;
        }
        switch($args[0]) {
            case "effectblade":
            case "eb":
                if(!$sender->hasPermission("weaponsplus.command.effectblade")) {
                    return false;
                }
                if($this->plugin->getEBStatus($sender)) {
                    $this->plugin->disableEB($sender);
                    $sender->sendMessage("§aEffectBlades disabled.");
                } else {
                    $this->plugin->enableEB($sender);
                    $sender->sendMessage("§aEffectBlades enabled.");
                }
                break;
            case "list":
                if(!isset($args[1])) {
                    $page = 1;
                } else {
                    $page = $args[1];
                }
                if(!is_numeric($page)) {
                    $page = 1;
                }
                if($page > 1) {
                    $page = 1;
                }
                switch($page) {
                    case 0:
                    case 1:
                        $sender->sendMessage("--- Weapons Page 1 of 1---\n§2effectblades");
                        break;
                }
                return true;
        }
        return true;
    }

}
