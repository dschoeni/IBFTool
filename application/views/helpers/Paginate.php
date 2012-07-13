<?php
class Zend_View_Helper_Paginate  {
	/**
	 * @var Zend_View_Interface
	 */
	public $view;

	/**
	 * Set the view object
	 *
	 * @param Zend_View_Interface $view
	 * @return void
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}

	public function paginate($url) {
		$paginate = $this->view->paginate;

		$html = '<div class="pagination"><div class="col-0">';
		if ($paginate->getCurrentSite() > 0) {
			$html .= $this->getLink($url . "site/0", "first");
			$html .= $this->getLink($url . "site/" . $paginate->getPreviousSite(), "previous");
		}

		$html .= '</div><div class="col-1">';

		//$html .= 'Einträge: ' . $this->view->paginate->getTotal() ." | ";
		if ($paginate->getSites() > 0) {
			//$html .= ' Seite: '.($paginate->getCurrentSite() + 1)."/".($paginate->getSites()+1).".";
				
			// Sutter Anpassung
			$html .= ' Seite: ';
			for ($i = 1; $i<$paginate->getSites()+2; $i++) {
				if ($paginate->getCurrentSite()+1 == $i) {
					$html.= '<strong>'. $i . '</strong> ';
				} else {
					$html.= '' . $i . ' ';
				}
			}
			// Ende Sutter Anpassung
		}

		$html .= '</div><div class="col-2">';

		if ($paginate->getSites() > $paginate->getCurrentSite()) {
			$html .= $this->getLink($url . "site/" . $paginate->getNextSite(), "next");
			$html .= $this->getLink($url . "site/" . $paginate->getSites(), "last");
		}

		$html .= '</div>';

		return $html . '<div class="clear"></div></div>';
	}

	public function getLink($url, $icon) {
		//		echo $url."<br />";
		//		return $this->view->ajaxLink("<img src='/_files/images/icons/resultset_$icon.png'>",
		//                    "$url/format/html/",
		//                    array('update' => '#paginated_div'));
		return '<a href="'.$url.'"><img src="/_files/images/icons/resultset_'.$icon.'.png"></a>';
	}
}
