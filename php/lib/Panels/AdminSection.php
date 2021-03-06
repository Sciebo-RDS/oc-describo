<?php

namespace OCA\Describo\Panels;

use OCP\IL10N;
use OCP\Settings\ISection;

class AdminSection implements ISection {
	/** @var IL10N  $l*/
	protected $l;

	public function __construct(IL10N $l) {
		$this->l = $l;
	}

	public function getPriority() {
		return 40;
	}

	public function getIconName() {
		return 'research-white';
	}

	public function getID() {
		return 'describo';
	}

	public function getName() {
		return $this->l->t('Describo');
	}
}
