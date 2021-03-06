<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2011 Daniel Pötzinger
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * A cache frontend for any kinds of PHP variables that writes into cache log
 *
 * @package TYPO3
 * @author Daniel Pötzinger
 * @api
 * @scope prototype
 */
class Tx_Cachemgm_Cache_Frontend_LogableVariableFrontend extends t3lib_cache_frontend_VariableFrontend {
	
	/**
	 * @var Tx_Cachemgm_Cache_MemoryLogWriter
	 */
	protected $cacheLog;

	/**
	 * Initializes this cache frontend
	 *
	 * @return void
	 */
	public function initializeObject() {
		parent::initializeObject();
		$this->cacheLog = t3lib_div::makeInstance('Tx_Cachemgm_Cache_MemoryLogWriter');
	}

	/**
	 * Saves the value of a PHP variable in the cache. Note that the variable
	 * will be serialized if necessary.
	 *
	 * @param string $entryIdentifier An identifier used for this cache entry
	 * @param mixed $variable The variable to cache
	 * @param array $tags Tags to associate with this cache entry
	 * @param integer $lifetime Lifetime of this cache entry in seconds. If NULL is specified, the default lifetime is used. "0" means unlimited liftime.
	 * @return void
	 * @throws \InvalidArgumentException if the identifier or tag is not valid
	 * @api
	 */
	public function set($entryIdentifier, $variable, array $tags = array(), $lifetime = NULL) {
		$startTime = $this->cacheLog->log($this->getIdentifier(),$entryIdentifier,Tx_Cachemgm_Cache_MemoryLogWriter::ACTION_SETSTART);
		$result = parent::set($entryIdentifier, $variable,  $tags , $lifetime );
		$this->cacheLog->log($this->getIdentifier(),$entryIdentifier,Tx_Cachemgm_Cache_MemoryLogWriter::ACTION_SETEND, $startTime);
		return $result;
	}

	/**
	 * Finds and returns a variable value from the cache.
	 *
	 * @param string $entryIdentifier Identifier of the cache entry to fetch
	 * @return mixed The value
	 * @throws \InvalidArgumentException if the identifier is not valid
	 * @api
	 */
	public function get($entryIdentifier) {
		$startTime = $this->cacheLog->log($this->getIdentifier(),$entryIdentifier,Tx_Cachemgm_Cache_MemoryLogWriter::ACTION_GETSTART);
		$result = parent::get($entryIdentifier, $variable,  $tags , $lifetime );
		if ($result !== false) {
			$this->cacheLog->log($this->getIdentifier(),$entryIdentifier,Tx_Cachemgm_Cache_MemoryLogWriter::ACTION_HIT, $startTime);
		}
		else {
			$this->cacheLog->log($this->getIdentifier(),$entryIdentifier,Tx_Cachemgm_Cache_MemoryLogWriter::ACTION_MISS, $startTime);
		}		
		return $result;
	}

	/**
	 * Checks if a cache entry with the specified identifier exists.
	 *
	 * @param string $entryIdentifier An identifier specifying the cache entry
	 * @return boolean TRUE if such an entry exists, FALSE if not
	 * @throws \InvalidArgumentException If $entryIdentifier is invalid
	 * @api
	 */
	public function has($entryIdentifier) {
		$startTime = $this->cacheLog->log($this->getIdentifier(),$entryIdentifier,Tx_Cachemgm_Cache_MemoryLogWriter::ACTION_HASSTART);
		$result = parent::get($entryIdentifier, $variable,  $tags , $lifetime );
		if ($result !== false) {
			$this->cacheLog->log($this->getIdentifier(),$entryIdentifier,Tx_Cachemgm_Cache_MemoryLogWriter::ACTION_HASHIT, $startTime);
		}
		else {
			$this->cacheLog->log($this->getIdentifier(),$entryIdentifier,Tx_Cachemgm_Cache_MemoryLogWriter::ACTION_HASMISS, $startTime);
		}		
		return $result;
	}
}
?>