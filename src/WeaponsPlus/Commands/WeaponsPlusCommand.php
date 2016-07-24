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
            case "landmine":
            case "landmines":
                if(!$sender->hasPermission("weaponsplus.command.landmine")) {
                    return false;
                }
                if($this->plugin->getLandmineStatus($sender)) {
                    $this->plugin->disableLandmines($sender);
                    $sender->sendMessage("§aLandmines disabled.");
                } else {
                    $this->plugin->enableLandmines($sender);
                    $sender->sendMessage("§aLandmines enabled.");
                }
                break;
            case "spear":
            case "spears":
                if(!$sender->hasPermission("weaponsplus.command.spear")) {
                    return false;
                }
                if($this->plugin->getSpearStatus($sender)) {
                    $this->plugin->disableSpears($sender);
                    $sender->sendMessage("§aSpears disabled.");
                } else {
                    $this->plugin->enableSpears($sender);
                    $sender->sendMessage("§aSpears enabled.");
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
            case "enderpearl":
            case "enderpearls":
                if(!$sender->hasPermission("weaponsplus.command.enderpearls")) {
                    return false;
                }
                if($this->plugin->getEnderpearlStatus($sender)) {
                    $this->plugin->disableEnderpearls($sender);
                    $sender->sendMessage("§aEnderpearls disabled.");
                } else {
                    $this->plugin->enableEnderpearls($sender);
                    $sender->sendMessage("§aEnderpearls enabled.");
                }
                break;
            case "list":
                if(!isset($args[1])) {
                    $pagenumber = 1;
                } else {
                    $pagenumber = $args[1];
                }
                if(!is_numeric($pagenumber) || $pagenumber < 1) {
                    $pagenumber = 1;
                }
                $cmds = array("bazuka", "effectblades", "enderpearls", "grenades", "landmines", "list", "spears");
                if($sender instanceof Player) {
                    $pages = array_chunk($cmds, 4);
                    $maxpage = count($pages);
                    if($pagenumber > $maxpage) {
                        $pagenumber = $maxpage;
                    }
                    $page = $pages[$pagenumber - 1];
                    $list = "--- Weapons Page " . $pagenumber . " of " . $maxpage . " ---\n§2";
                    $list = $list . implode("\n§2", $page);
                } else {
                    $list = "--- Weapons Page 1 of 1 ---\n§2";
                    $list = $list . implode("\n§2", $cmds);
                }
                $sender->sendMessage($list);
                break;
            default:
                break;
        }
        return true;
    }

}
