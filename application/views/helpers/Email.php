<?php
class Zend_View_Helper_Email {
	public function email($address) {
		$string = '<a href="mailto:'.$address.'">'.$address.'</a>';

		for($x = 0, $y = strlen($string); $x < $y; $x++ ) {
			$ord[] = ord($string[$x]);
		}

		$_ret = "<script type=\"text/javascript\" language=\"javascript\">\n";
		$_ret .= "<!--\n";
		$_ret .= "{document.write(String.fromCharCode(";
		$_ret .= implode(',',$ord);
		$_ret .= "))";
		$_ret .= "}\n";
		$_ret .= "//-->\n";
		$_ret .= "</script>\n";

		return $_ret;
	}
}
