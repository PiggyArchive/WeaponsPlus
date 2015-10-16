<?php

namespace MCPEPIG\WeaponsPlus;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\entity\Effect;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
  public $disabledeb = array();
  public $disabledfb = array();
  
  public function onEnable(){
    @mkdir($this->getServer()->getDataPath() . "/plugins/WeaponsPlus/");
    $this->weaponsPlus = (new Config($this->getDataFolder()."config.yml", Config::YAML, array(
      "effects" => array(
         "276:2:5:5:false"
      ),
      "effect-blades-enabled" => true
      "flamebows-enabled" => true
    )));
    $this->getLogger()->info("§aWeaponsPlus by MCPEPIG loaded.");
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
  public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
    switch(strtolower($cmd->getName())){
      case "flamebows":
        if(count($args) !== 1){
          $sender->sendMessage("/flamebows on|off");
          return false;
        }
        switch($args[0]){
          case "true":
            break;
          case "false":
            break;
        }
        break;
      case "effectblades":
        if(count($args) !== 1){
          $sender->sendMessage("/effectblades on|off");
          return false;
        }
        switch($args[0]){
          case "true":
            break;
          case "false":
            break;
        }
        break;
    }
  }
  public function onEntityUseBow(EntityShootBowEvent $event){
    $entity = $event->getEntity();
    if($entity instanceof Player){
      if($entity->hasPermission("weaponsplus.flamebows.use") && $this->weaponsPlus->get("flamebows-enabled") === true && in_array($entity->getName(), $this->disabledfb) !== true){
        $event->getProjectile()->setOnFire(500000000 * 20);
      }
    }
  }
  public function onEntityDamage(EntityDamageEvent $event){
    $entity = $event->getEntity();
    if($event instanceof EntityDamageByEntityEvent && $event->getDamager() instanceof Player){
      foreach($this->weaponsPlus->get("effects") as $effectbladedata){
        $effectblade = explode(" ", $effectblade);
        $itemid = $effectblade[0];
        $effectid = $effectblade[1];
        $effectlevel = $effectblade[2];
        $effecttime = $effectblade[3];
        $particlevisible = $effectblade[4];
        if($event->getDamager()->getInventory()->getItemInHand()->getId() === $itemid && $event->getDamager()->hasPermission("weaponsplus.effectblades.use") && $this->weaponsPlus->get("effect-blades-enabled") === true && in_array($entity->getName(), $this->disabledeb) !== true){
          $effect = Effect::getEffect($effectid);
          $effect->setAmplifier($effectlevel);
          $effect->setDuration($effecttime * 20);
          $effect->setVisible($particlevisible);
          $entity->addEffect($effect);
        }
      }
    }
  }
}
