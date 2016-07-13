<?php
namespace WeaponsPlus;

use pocketmine\entity\Effect;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class EventListener implements Listener {
    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param EntityDamageEvent $event
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function onDamage(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if($entity instanceof Player) {
            if($event instanceof EntityDamageByEntityEvent && $event->getDamager() instanceof Player) {
                if(($this->plugin->getEBStatus($entity) || ($this->plugin->getConfig()->get("auto-enable-effect-blades") && !$this->plugin->getEBStatus($entity))) && $this->plugin->getConfig()->get("effect-blades")) {
                    foreach($this->plugin->getConfig()->get("effects") as $information) {
                        $info = explode(" ", $info);
                        $itemid = $info[0];
                        $itemdamage = $info[1];
                        $effectid = $info[2];
                        $effectlevel = $info[3];
                        $effecttime = $info[4];
                        $particlevisible = $info[5];
                        $item = $event->getDamager()->getInventory()->getItemInHand();
                        if($item->getId() == $itemid && $item->getDamage() == $itemdamage) {
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
    }

}
