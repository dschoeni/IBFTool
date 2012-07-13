<?php
class ibftool_Validate_DNumber_NotEmpty extends Zend_Validate_NotEmpty
{
	public function isValid($value)
	{
		if (is_array($value)) {
			if(isset($value[0]) && isset($value[1])) {
				if(($value[0] != "") && ($value[1] != "")) {
					if ($value[0] >= 0 && $value[0] <= 100) {
						if(is_numeric($value[0]) && is_numeric($value[1])) {
							if ($value[1] >= 0 && $value[1] <= 100) {
								return true;
							}
						}
					}
				}
			}
		}
		$this->_error(self::IS_EMPTY);
		return false;
	}
}
