<?php
namespace WeaponsPlus\Commands;

use pocketmine\command\defaults\VanillaCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class WeaponsPlusCommand extends VanillaCommand {
    public function __construct($name, $plugin) {
        parent::__construct($name, "Toggle weapons", "/weaponsplus", ["wp"]);
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
            case "effectblades":
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
            case "grenade":
            case "grenades":
                if(!$sender->hasPermission("weaponsplus.command.grenade")) {
                    return false;
                }
                if($this->plugin->getGrenadeStatus($sender)) {
                    $this->plugin->disableGrenades($sender);
                    $sender->sendMessage("§aGrenades disabled.");
                } else {
                    $this->plugin->enableGrenades($sender);
                    $sender->sendMessage("§aGrenades enabled.");
                }
                break;
            case "bazuka":
            case "bazukas":
                if(!$sender->hasPermission("weaponsplus.command.bazukas")) {
                    return false;
                }
                if($this->plugin->getBazukaStatus($sender)) {
                    $this->plugin->disableBazukas($sender);
                    $sender->sendMessage("§aBazukas disabled.");
                } else {
                    $this->plugin->enableBazukas($sender);
                    $sender->sendMessage("§aBazukas enabled.");
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
                $maxpage = 1;
                if($page > $maxpage) {
                    $page = $maxpage;
                }
                switch($page) {
                    case 0:
                    case 1:
                        $sender->sendMessage("--- Weapons Page 1 of " . $maxpage . "---\n§2effectblades\n§2grenades\n§2bazukas");
                        break;
                }
                return true;
            default:
        }
        return true;
    }

}
