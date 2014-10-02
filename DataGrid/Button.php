<?php

namespace DataGrid;

use \Nette\Utils\Html,
    \Nette\Application\UI\Presenter,
	DataGrid\Column;

/**
 * Description of \DataGrid\Button
 *
 * @author mesour <matous.nemec@mesour.com>
 * @package DataGrid
 */
class Button {
	/**
	 * Possible option key
	 */

	const ID = 'id',
	    STYLE = 'style',
	    CLASS_NAME = 'class_name',
	    ICON = 'icon',
	    ICON_COLOR = 'icon_color',
	    CONFIRM = 'confirm',
	    TITLE = 'title',
	    HREF = 'href',
	    HREF_LINK = 'href_link',
	    HREF_LINK_DATA = 'href_link_data',
	    DATA_TITLE = 'data_title',
	    DATA_HREF = 'data_href',
	    DATA_HREF_LINK = 'data_href_link',
	    DATA_HREF_LINK_DATA = 'data_href_link_data',
	    MODAL = 'modal',
	    ONLY_FROM_ID = 'only_from_id';

	/**
	 * Option for current button
	 *
	 * @var Array
	 */
	private $option = array();

	/**
	 * Row data for button
	 *
	 * @var Array
	 */
	private $data = array();

	/**
	 *
	 * @var \Nette\Application\UI\Presenter
	 */
	protected $presenter;

	/**
	 * @param array $option
	 * @param \Nette\Application\UI\Presenter $presenter
	 * @param Array|NULL $data
	 * @throws \DataGrid\Grid_Exception
	 */
	public function __construct(array $option, Presenter $presenter, $data = NULL) {
		if (array_key_exists(self::ID, $option) === FALSE) {
			throw new Grid_Exception('ID can not be empty in button options.');
		}
		if (empty($data) === FALSE) {
			$this->data = $data;
		}
		$this->presenter = $presenter;
		$this->option = $option;
	}

	/**
	 * Create button
	 *
	 * @param Array $data
	 * @return String
	 * @throws \DataGrid\Grid_Exception
	 */
	public function create($data = NULL) {
		if (empty($data) === FALSE) {
			$this->data = $data;
		}
		if (empty($this->data)) {
			throw new Grid_Exception('Empty data');
		}
		if(isset($this->option[self::ONLY_FROM_ID]) && $this->data[$this->option[self::ID]] < $this->option[self::ONLY_FROM_ID]) {
			return '';
		}

		if(array_key_exists(self::ICON, $this->option)) {
			$icon = $this->option[self::ICON];
			$icon_color = array_key_exists(self::ICON_COLOR, $this->option) === FALSE ? '' : $this->option[self::ICON_COLOR];
		}

		$button = Html::el('a', array('class' => ( array_key_exists(self::CLASS_NAME, $this->option) ? $this->option[self::CLASS_NAME] . ' ' : '' ) . 'btn btn-sm ' . ( array_key_exists(self::STYLE, $this->option) === FALSE ? '' : $this->option[self::STYLE] )));

		if (array_key_exists(self::MODAL, $this->option)) {
			$button->addAttributes(array(
			    'data-toggle' => 'modal',
			    'href' => '#defaultModal',
			    'data-title' => $this->option[self::MODAL]
			));
		}

		if (array_key_exists(self::CONFIRM, $this->option)) {
			$button->addAttributes(array(
			    'data-confirm' => $this->option[self::CONFIRM]
			));
		}

		if (array_key_exists(self::TITLE, $this->option)) {
			$button->addAttributes(array(
			    'title' => $this->option[self::TITLE]
			));
		}

		$output = $this->addLinkAttr($button, 'href', self::HREF, self::HREF_LINK, self::HREF_LINK_DATA);
		if($output === FALSE) {
			return '';
		}
		$output = $this->addLinkAttr($button, 'href', self::DATA_HREF,  self::DATA_HREF_LINK, self::DATA_HREF_LINK_DATA);
		if($output === FALSE) {
			return '';
		}

		if(isset($icon) && isset($icon_color)) {
			$button->add(Html::el('b', array('class' => 'glyphicon ' . $icon . ' ' . $icon_color)));
		}
		return $button;
	}

	public function addLinkAttr(& $button, $attr_name, $opt_name, $link_opt_name, $params_opt_name) {
		if (array_key_exists($opt_name, $this->option)) {
			$button->addAttributes(array(
			    $attr_name => $this->option[$opt_name]
			));
		} else if (array_key_exists($link_opt_name, $this->option)) {
			$link = Column\Base::getLink($this->option[$link_opt_name], isset($this->option[$params_opt_name]) ? array() : isset($this->option[$params_opt_name]), $this->data);
			if ($link === FALSE) {
				return FALSE;
			}
			list($to_href, $params) = $link;
			$button->addAttributes(array(
			    $attr_name => $this->presenter->link($to_href, $params)
			));
		}
		return TRUE;
	}

	/**
	 * See method create
	 *
	 * @return String
	 */
	public function __toString() {
		return $this->create()->render();
	}

}