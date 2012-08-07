<?php
namespace TYPO3\TYPO3\View;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.TYPO3".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Controller for displaying nodes in the frontend
 *
 */
class TypoScriptView extends \TYPO3\FLOW3\Mvc\View\AbstractView {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\TYPO3\Domain\Service\TypoScriptService
	 */
	protected $typoScriptService;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeRepository
	 */
	protected $nodeRepository;

	/**
	 * Renders the view
	 *
	 * @return string The rendered view
	 * @throws \TYPO3\TYPO3\Exception
	 * @api
	 */
	public function render() {
		$currentNode = isset($this->variables['value']) ? $this->variables['value'] : NULL;
		if (!$currentNode instanceof \TYPO3\TYPO3CR\Domain\Model\NodeInterface) {
			throw new \TYPO3\TYPO3\Exception('TypoScriptView needs a node as argument.', 1329736456);
		}

		$currentSiteNode = $this->nodeRepository->getContext()->getCurrentSiteNode();

			// TODO: find closest folder node from this node...
		$closestFolderNode = $currentNode;
		$typoScriptConfiguration = $this->typoScriptService->getMergedTypoScriptObjectTree($currentSiteNode, $closestFolderNode);

			// TODO: make TypoScriptPath overridable
		$typoScriptPath = 'page';

		$typoScriptRuntime = new \TYPO3\TypoScript\Core\Runtime($typoScriptConfiguration, $this->controllerContext);
		$typoScriptRuntime->pushContextArray(array('node' => $currentNode));
		$output = $typoScriptRuntime->render($typoScriptPath);
		$typoScriptRuntime->popContext();
		return $output;
	}
}
?>