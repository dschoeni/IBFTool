<?php
class ibftool_Form extends Zend_Form {
	
	public function setValues(Zend_Db_Table_Row_Abstract $row) {
		$this->setValuesFromArray($row->toArray());
	}

	public function setValuesFromArray($array) {
		foreach ($this->getElements() as $element) {
			$name = $element->getName();

			if (!isset($array[$name])) {
				continue;
			}

			if ($array[$name] == null && $element instanceof Zend_Form_Element_Multi) {
				if (in_array("", array_keys($element->getMultiOptions()))) {
					$element->setValue("");
					continue;
				}
			}

			$element->setValue($array[$name]);
		}
	}

	public function persistCreateOrUpdate($table, $key = "id") {
		$rowSet = $table->find($this->getValue($key));

		if (count($rowSet) == 0) {
			$row = $table->createRow();
		} else {
			$row = $rowSet->current();
		}

		$row->setFromArray($this->getValues());
		$row->save();

		return $row;
	}
}