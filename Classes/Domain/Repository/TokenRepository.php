<?php
/**
 * (c) Dominique Feyer <dfeyer@ttree.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Resume
 *
 * This class ...
 *
 * @package	Tx_Expose_Domain_Repository_TokenRepository
 * @author	 Dominique Feyer <dfeyer@ttree.ch>
 */
class Tx_Expose_Domain_Repository_TokenRepository extends Tx_Extbase_Persistence_Repository {

	public function createQuery() {
		$query = parent::createQuery();

		$query->getQuerySettings()->setRespectStoragePage(FALSE);

		return $query;
	}
}
