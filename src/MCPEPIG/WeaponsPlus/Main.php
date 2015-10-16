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
  public function onEnable(){
    @mkdir($this->getServer()->getDataPath() . "/plugins/WeaponsPlus/");
    $this->weaponsPlus = (new Config($this->getDataFolder()."config.yml", Config::YAML, array(
      "effects" => array(
         "276:2:5:5"
      ),
      "effect-blades-enabled" => true
      "particles-visible" => false,
      "flamebows-enabled" => true
    )));
    $this->getLogger()->info("§aWeaponsPlus by MCPEPIG loaded.");
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
  public function onEntityUseBow(EntityShootBowEvent $event){
    $entity = $event->getEntity();
    if($entity instanceof Player){
      if($entity->hasPermission("weaponsplus.flamebows.use") && $this->weaponsPlus->get("flamebows-enabled") === true){
        $event->getProjectile()->setOnFire(500000000 * 20);
      }
    }
  }
  public function onEntityDamage(EntityDamageEvent $event){
    $entity = $event->getEntity();
    if($event instanceof EntityDamageByEntityEvent && $event->getDamager() instanceof Player){
      foreach($this->weaponsPlus->get("effects") as $itemid => $effectid){
        if($event->getDamager()->getInventory()->getItemInHand()->getId() === $itemid && $event->getDamager()->hasPermission("weaponsplus.effectblades.use") && $this->weaponsPlus->get("effect-blades-enabled") === true){
          $effectlevel = $this->weaponsPlus->get("effect-level") - 1; //For some reason, when giving the effect, the amplifier is the amplifier + 1...
          $effecttime = $this->weaponsPlus->get("effect-time");
          $particlevisible = $this->weaponsPlus->get("particles-visible");
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
