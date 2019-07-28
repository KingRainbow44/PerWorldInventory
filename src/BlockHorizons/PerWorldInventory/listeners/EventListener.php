<?php

declare(strict_types = 1);

namespace BlockHorizons\PerWorldInventory\listeners;

use BlockHorizons\PerWorldInventory\PerWorldInventory;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\Player;

class EventListener implements Listener {

	/** @var PerWorldInventory */
	private $plugin;

	public function __construct(PerWorldInventory $plugin) {
		$this->plugin = $plugin;
	}

	/**
	 * @return PerWorldInventory
	 */
	public function getPlugin(): PerWorldInventory {
		return $this->plugin;
	}

	/**
	 * @param EntityLevelChangeEvent $event
	 *
	 * @priority HIGHEST
	 * @ignoreCancelled true
	 */
	public function onLevelChange(EntityLevelChangeEvent $event) : void {
		$player = $event->getEntity();
		

		$origin = $event->getOrigin();
		$target = $event->getTarget();

		$this->getPlugin()->storeInventory($player, $origin);
		

		if($this->getPlugin()->getParentWorld($origin->getFolderName()) === $this->getPlugin()->getParentWorld($target->getFolderName())) {
			return;
		}

		$this->getPlugin()->setInventory($player, $target);
	}

	/**
	 * @param PlayerQuitEvent $event
	 *
	 * @priority MONITOR
	 */
	public function onQuit(PlayerQuitEvent $event) : void {
		$player = $event->getPlayer();
		$this->getPlugin()->save($player, true);
	}

	/**
	 * @param PlayerLoginEvent $event
	 *
	 * @priority MONITOR
	 * @ignoreCancelled true
	 */
	public function onPlayerLogin(PlayerLoginEvent $event) : void {
		$player = $event->getPlayer();
	

		$this->getPlugin()->load($player);
	}

	/**
	 * @param InventoryTransactionEvent $event
	 *
	 * @priority HIGH
	 * @ignoreCancelled true
	 */
	public function onInventoryTransaction(InventoryTransactionEvent $event) : void {
		if($this->getPlugin()->isLoading($event->getTransaction()->getSource())) {
			$event->setCancelled();
		}
	}
}
