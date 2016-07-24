<?php
namespace WeaponsPlus;

use pocketmine\block\Block;
use pocketmine\entity\Arrow;
use pocketmine\entity\Effect;
use pocketmine\entity\Egg;
use pocketmine\entity\Entity;
use pocketmine\entity\Snowball;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\inventory\InventoryPickupArrowEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\level\sound\LaunchSound;
use pocketmine\level\Explosion;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\protocol\PlayerActionPacket;
use pocketmine\Player;

class EventListener implements Listener {
    public function __construct($plugin) {
        $this->plugin = $plugin;
    }
    /**
     * @param BlockBreakEvent $event
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function onBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if($this->plugin->getConfig()->get("landmines")) {
            if($block->getId() == $this->plugin->getConfig()->get("landmine")) {
                $strength = $this->plugin->getConfig()->get("landmine-strength");
                if(!is_null($this->plugin->getServer()->getPluginManager()->getPlugin("BadPiggy"))) {
                    $explosion = new \BadPiggy\Utils\BadPiggyExplosion($block, $strength, null, $this->plugin->getServer()->getPluginManager()->getPlugin("BadPiggy"));
                } else {
                    $player = new Explosion($block, $strength);
                }
                $explosion->explodeA();
                $explosion->explodeB();
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function onPlace(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if($this->plugin->getConfig()->get("landmines")) {
            if($block->getId() == $this->plugin->getConfig()->get("landmine")) {
                if($this->plugin->getConfig()->get("name-required")) {
                    if(!$player->getInventory()->getItemInHand()->getCustomName() == "Landmine") {
                        $event->setCancelled();
                    }
                }
            }
        }
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
            if($event instanceof EntityDamageByEntityEvent && ($damager = $event->getDamager()) instanceof Player) {
                $item = $damager->getInventory()->getItemInHand();
                $damage = $item->getDamage();
                if($this->plugin->getEBStatus($entity) && $this->plugin->getConfig()->get("effect-blades")) {
                    foreach($this->plugin->getConfig()->get("effects") as $information) {
                        $info = explode(":", $information);
                        $itemid = $info[0];
                        $itemdamage = $info[1];
                        $effectid = $info[2];
                        $effectlevel = $info[3];
                        $effecttime = $info[4];
                        $particlevisible = $info[5];
                        if($item->getId() == $itemid && $item->getDamage() == $itemdamage) {
                            $effect = Effect::getEffect($effectid);
                            $effect->setAmplifier($effectlevel);
                            $effect->setDuration($effecttime * 20);
                            $effect->setVisible($particlevisible);
                            $entity->addEffect($effect);
                        }
                    }
                }
                if($this->plugin->getConfig()->get("spears")) {
                    if($item->getId() == $this->plugin->getConfig()->get("spears") && $item->getCustomName() == "Spear") {
                        $event->setDamage($this->plugin->getConfig()->get("spear-damage"));
                        if($damager->isSurvival()) {
                            if($damage < 36) {
                                $item->setDamage($damage + 1);
                            } else {
                                $item->setDamage(0); //The other spears shouldnt have the same durability as the one broken
                                $item->setCount($item->getCount() - 1);
                            }
                            $damager->getInventory()->setItemInHand($item->getCount() > 0 ? $item : Item::get(Item::AIR));
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
                    if($shooter->getInventory()->getItemInHand()->getCustomName() == "Grenade" || !$this->plugin->getConfig()->get("name-required")) {
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
        if($entity instanceof Egg) {
            $shooter = $entity->shootingEntity;
            if($shooter instanceof Player) {
                if($this->plugin->getEnderpearlStatus($shooter) && $this->plugin->getConfig()->get("enderpearls")) {
                    if($shooter->getInventory()->getItemInHand()->getCustomName() == "Enderpearl" || !$this->plugin->getConfig()->get("name-required")) {
                        $shooter->teleport($entity->add(0, 1));
                    }
                }
            }
        }
    }

    public function onExplode(ExplosionPrimeEvent $event) {
        $entity = $event->getEntity();
        if(isset($entity->namedtag["Bazuka"])) {
            if(!$this->plugin->getConfig()->get("terrain-damage")) {
                $event->setBlockBreaking(false);
            }
        }
    }

    public function onPickupArrow(InventoryPickupArrowEvent $event) {
        $player = $event->getInventory()->getHolder();
        $arrow = $event->getArrow();
        if($player instanceof Player) {
            if(isset($arrow->namedtag["Spear"]) && isset($arrow->namedtag["Durability"])) {
                if($arrow->namedtag["Durability"] < 36) {
                    $spear = Item::get($this->plugin->getConfig()->get("spear"), $arrow->namedtag["Durability"], 1);
                    $spear->setCustomName("Spear");
                    $player->getInventory()->addItem($spear);
                } else {
                    $fragments = array(
                        Item::get(Item::STICK),
                        Item::get(Item::STICK), //Yes, there are 2 so theres a chance of getting both sticks
                        Item::get(Item::IRON_INGOT),
                        Item::get(Item::AIR));
                    $fragment = array_rand($fragments, mt_rand(1, 2));
                    if(is_array($fragment)) {
                        foreach($fragment as $key) {
                            $fragment = $fragments[$key];
                            if($fragment->getId() == 0) {
                                return false;
                            }
                            $fragment->setCustomName("Spear Fragment");
                            $player->getInventory()->addItem($fragment);
                        }
                    } else {
                        $fragment = $fragments[$fragment];
                        if($fragment->getId() == 0) {
                            return false;
                        }
                        $fragment->setCustomName("Spear Fragment");
                        $player->getInventory()->addItem($fragment);
                    }
                }
                $arrow->kill();
                $event->setCancelled();
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $item = $item = $player->getInventory()->getItemInHand();
        $damage = $item->getDamage();
        if($this->plugin->getConfig()->get("spears")) {
            if($item->getId() == $this->plugin->getConfig()->get("spear") && $item->getCustomName() == "Spear") {
                if($player->isSurvival()) {
                    if($damage < 36) {
                        if($damage + 2 > 36) {
                            $damage = 36;
                        } else {
                            $damage = $damage + 2;
                        }
                    }
                    $item->setDamage(0); //The other spears shouldnt have the same durability as the one broken
                    $item->setCount($item->getCount() - 1);
                    $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : Item::get(Item::AIR));
                }
                $aimPos = $player->getDirectionVector();
                $nbt = new CompoundTag("", ["Pos" => new ListTag("Pos", [new DoubleTag("", $player->x), new DoubleTag("", $player->y + $player->getEyeHeight()), new DoubleTag("", $player->z)]), "Motion" => new ListTag("Motion", [new DoubleTag("", $aimPos->x), new DoubleTag("", $aimPos->y), new DoubleTag("", $aimPos->z)]), "Rotation" => new ListTag("Rotation", [new FloatTag("", $player->yaw), new FloatTag("", $player->pitch)]), "Spear" => new ByteTag("Spear", 1), "Durability" => new IntTag("Durability", $damage)]);
                $f = 1.5;
                $spear = Entity::createEntity("Arrow", $player->getLevel()->getChunk($player->getFloorX() >> 4, $player->getFloorZ() >> 4), $nbt, $player);
                $spear->setMotion($spear->getMotion()->multiply($f));
                $player->getLevel()->addSound(new LaunchSound($player), $player->getViewers());
                $spear->spawnToAll();
            }
        }
        if($this->plugin->getConfig()->get("bazukas")) {
            if($item->getId() == $this->plugin->getConfig()->get("bazuka") && $item->getCustomName() == "Bazuka") {
                if($player->isSurvival()) {
                    $damage = $item->getDamage();
                    if(!$player->getInventory()->contains(Item::get(Item::TNT, null, 1))) {
                        return false;
                    }
                    $player->getInventory()->removeItem(Item::get(Item::TNT, null, 1));
                    if($damage < 50) {
                        $item->setDamage($damage + 1);
                    } else {
                        $item->setDamage(0); //The other bazukas shouldnt have the same durability as the one broken
                        $item->setCount($item->getCount() - 1);
                    }
                    $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : Item::get(Item::AIR));
                }
                $aimPos = $player->getDirectionVector();
                $nbt = new CompoundTag("", ["Pos" => new ListTag("Pos", [new DoubleTag("", $player->x), new DoubleTag("", $player->y + $player->getEyeHeight()), new DoubleTag("", $player->z)]), "Motion" => new ListTag("Motion", [new DoubleTag("", -sin($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI)), new DoubleTag("", -sin($player->pitch / 180 * M_PI)), new DoubleTag("", cos($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI))]), "Rotation" => new ListTag("Rotation", [new FloatTag("", $player->yaw), new FloatTag("", $player->pitch)]), "Fuse" => new ByteTag("Fuse", 80), "Bazuka" => new ByteTag("Bazuka", 1)]);
                $f = 2;
                $tnt = Entity::createEntity("PrimedTNT", $player->getLevel()->getChunk($player->getFloorX() >> 4, $player->getFloorZ() >> 4), $nbt, $player);
                $tnt->setMotion($tnt->getMotion()->multiply($f));
                $player->getLevel()->addSound(new LaunchSound($player), $player->getViewers());
                $tnt->spawnToAll();
            }
        }
        if($this->plugin->getConfig()->get("landmines")) {
            if($block->getId() == $this->plugin->getConfig()->get("landmine")) {
                $strength = $this->plugin->getConfig()->get("landmine-strength");
                if(!is_null($this->plugin->getServer()->getPluginManager()->getPlugin("BadPiggy"))) {
                    $explosion = new \BadPiggy\Utils\BadPiggyExplosion($block, $strength, null, $this->plugin->getServer()->getPluginManager()->getPlugin("BadPiggy"));
                } else {
                    $player = new Explosion($block, $strength);
                }
                $player->getLevel()->setBlock($block, Block::get(Block::AIR));
                if($this->plugin->getConfig()->get("terrain-damage")) {
                    $explosion->explodeA();
                }
                $explosion->explodeB();
                $event->setCancelled();
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        if($this->plugin->getConfig()->get("auto-enable-eb")) {
            if(!isset($this->plugin->ebstatuses[strtolower($player->getName())])) {
                $this->plugin->enableEB($player);
            }
        }
        if($this->plugin->getConfig()->get("auto-enable-grenade")) {
            if(!isset($this->plugin->grenadestatuses[strtolower($player->getName())])) {
                $this->plugin->enableGrenades($player);
            }
        }
        if($this->plugin->getConfig()->get("auto-enable-bazukas")) {
            if(!isset($this->plugin->bazukastatuses[strtolower($player->getName())])) {
                $this->plugin->enableBazukas($player);
            }
        }
        if($this->plugin->getConfig()->get("auto-enable-enderpearls")) {
            if(!isset($this->plugin->enderpearlstatuses[strtolower($player->getName())])) {
                $this->plugin->enableEnderpearls($player);
            }
        }
    }

    public function onMove(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        if($this->plugin->getConfig()->get("landmines")) {
            if($player->getLevel()->getBlock($player->floor())->getId() == $this->plugin->getConfig()->get("landmine")) {
                $strength = $this->plugin->getConfig()->get("landmine-strength");
                if(!is_null($this->plugin->getServer()->getPluginManager()->getPlugin("BadPiggy"))) {
                    $explosion = new \BadPiggy\Utils\BadPiggyExplosion($player, $strength, null, $this->plugin->getServer()->getPluginManager()->getPlugin("BadPiggy"));
                } else {
                    $player = new Explosion($player, $strength);
                }
                $player->getLevel()->setBlock($player->floor(), Block::get(Block::AIR));
                if($this->plugin->getConfig()->get("terrain-damage")) {
                    $explosion->explodeA();
                }
                $explosion->explodeB();
            }
        }
    }

    public function onHit(ProjectileHitEvent $event) {
        $projectile = $event->getEntity();
        if($projectile instanceof Arrow) {
            if($this->plugin->getConfig()->get("landmines")) {
                if($projectile->getLevel()->getBlock($projectile->floor())->getId() == $this->plugin->getConfig()->get("landmine")) {
                    $strength = $this->plugin->getConfig()->get("landmine-strength");
                    if(!is_null($this->plugin->getServer()->getPluginManager()->getPlugin("BadPiggy"))) {
                        $explosion = new \BadPiggy\Utils\BadPiggyExplosion($projectile, $strength, null, $this->plugin->getServer()->getPluginManager()->getPlugin("BadPiggy"));
                    } else {
                        $explosion = new Explosion($projectile, $strength);
                    }
                    $projectile->getLevel()->setBlock($projectile->floor(), Block::get(Block::AIR));
                    if($this->plugin->getConfig()->get("terrain-damage")) {
                        $explosion->explodeA();
                    }
                    $explosion->explodeB();
                }
            }
        }
    }

}
