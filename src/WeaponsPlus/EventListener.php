<?php
namespace WeaponsPlus;

use pocketmine\entity\Effect;
use pocketmine\entity\Snowball;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Listener;;
use pocketmine\level\Explosion;
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
                if($this->plugin->getEBStatus($entity) && $this->plugin->getConfig()->get("effect-blades")) {
                    foreach($this->plugin->getConfig()->get("effects") as $information) {
                        $info = explode(":", $information);
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

    public function onDespawn(EntityDespawnEvent $event) {
        $entity = $event->getEntity();
        if($entity instanceof Snowball) {
            $shooter = $entity->shootingEntity;
            if($shooter instanceof Player) {
                if($this->plugin->getGrenadeStatus($shooter) && $this->plugin->getConfig()->get("grenades")) {
                    if($shooter->getInventory()->getItemInHand()->getCustomName() == "Grenade") {
                        $strength = $this->plugin->getConfig()->get("grenade-strength");
                        if(!is_null($this->plugin->getServer()->getPluginManager()->getPlugin("BadPiggy"))) {
                            $explosion = new \BadPiggy\Utils\BadPiggyExplosion($entity, $strength, $shooter, $this->plugin->getServer()->getPluginManager()->getPlugin("BadPiggy"));
                        } else {
                            $explosion = new Explosion($entity, $strength, $shooter);
                        }
                        if($this->plugin->getConfig()->get("terrain-damage")) {
                            $explosion->explodeA();
                        }
                        $explosion->explodeB();
                    }
                }
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        if($this->plugin->getConfig()->get("auto-enable-eb")) {
            if(!isset($this->plugin->ebstatuses[strtolower($player->getName())])) {
                $this->plugin->enableGrenades($player);
            }
        }
        if($this->plugin->getConfig()->get("auto-enable-grenade")) {
            if(!isset($this->plugin->grenadestatuses[strtolower($player->getName())])) {
                $this->plugin->enableGrenades($player);
            }
        }
    }

    public function onMove(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        if($this->plugin->getConfig()->get("landmines")) {
            if($player->getLevel()->getBlock($player->floor())->getId() == $this->plugin->getConfig()->get("landmine")) {
                $strength = $this->plugin->getConfig()->get("landmine-strength");
                if(!is_null($this->plugin->getServer()->getPluginManager()->getPlugin("BadPiggy"))) {
                    $explosion = new \BadPiggy\Utils\BadPiggyExplosion($player, $strength, $player, $this->plugin->getServer()->getPluginManager()->getPlugin("BadPiggy"));
                } else {
                    $player = new Explosion($entity, $strength, $player);
                }
                $explosion->explodeA();
                $explosion->explodeB();
            }
        }
    }

}
