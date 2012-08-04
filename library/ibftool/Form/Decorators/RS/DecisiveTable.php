<?php
class ibftool_Form_Decorators_RS_DecisiveTable extends Zend_Form_Decorator_Abstract
{
	protected $_format = '<dt id="%s"><label for="%s" class="note">%s</label></dt>';
	protected $_button = '<input type="radio" name="%s" id="%s" value="%s" data-col="%s" %s>';
	protected $_hiddenButton = '<input type="hidden" name="%s" id="%s" value="%s">';
	
	public function render($content)
	{
		$element = $this->getElement();
		$name    = htmlentities($element->getFullyQualifiedName());
		$label   = $element->getLabel();
		$value   = htmlentities($element->getValue());
		$options = $element->getMultiOptions();
		
		$id = $this->getOption("id");
		$ecu = $this->getOption("ecu");
		$firstColumnArray = explode("#", $this->getOption("firstColumn"));
		$secondColumnArray = explode("#", $this->getOption("secondColumn"));
		$thirdColumnArray = explode("#", $this->getOption("thirdColumn"));
		
		$markup = sprintf($this->_format, $id . "-label", $id, $label);
		
		$markup .= "<table class='table table-condensed table-striped'>";
		$markup .= "<thead>";
		$markup .= "<tr><th colspan='2'>Risikobehaftete Anlage</th><th colspan='2'>Entscheidung</th><th>Sichere Anlage</th></tr>";
		$markup .= "<tr><th>50% Wahrscheinlichkeit auf</th><th>50% Wahrscheinlichkeit auf</td><th>Ich bevorzuge die risikobehaftete Anlage</th><th>Ich bevorzuge die sichere Anlage</th><th>100% Wahrscheinlichkeit auf</th></tr>";
		$markup .= "</thead>";
		$i = 1;
		$fieldid = 1;
		
		$checkrisk = "checked";
		$checksure = "";
		
		foreach($options as $option) {
			
			if ($value == $option) {
				$checkrisk = "";
				$checksure = "checked";
			} else if ($value == "") {
				$checkrisk = "";
				$checksure = "";
			}
			
			$markup .= "<tr>";
			$markup .= "<td>" . $firstColumnArray[$i-1] . "% (" . $ecu * $firstColumnArray[$i-1]/100 . " ECU)</td>";
			$markup .= "<td>" . $secondColumnArray[$i-1] . "% (" . $ecu * $secondColumnArray[$i-1]/100 . " ECU)</td>";

			$markup .= "<td>";
			$markup .= sprintf($this->_button, $i, $fieldid, $option, "1", $checkrisk);
			$markup .= "</td>";
			
			$markup .= "<td>";
			$markup .= sprintf($this->_button, $i, ++$fieldid, $option, "2", $checksure);
			$markup .= "</td>";
			
			$markup .= "<td>" . $thirdColumnArray[$i-1] . "% (" . $ecu * $thirdColumnArray[$i-1]/100 . " ECU)</td>";
			$markup .= "</tr>";
			
			$i++;
			$fieldid++;
		}
		
		$markup .= "</table>";
		$markup .= '<div id="dialog" class="modal hide">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">×</button>
							<h3>Sind Sie sicher?</h3>
						</div>
						<div id="normal_body" class="modal-body">
						Im Vergleich zur sicheren Anlage (2%) bevorzugen Sie also die risikobehaftete Anlage (50%ige Wahrscheinlichkeit auf einen Ertrag von 50% (d.h. +5\'000 ECU)) 
						bis zu einem möglichen Verlust von 50% (d.h. -5�000 ECU). Ist das wirklich Ihre finale Entscheidung?
						</div>
						<div class="modal-footer">
							<a href="#" id="imsure" class="btn btn-primary">Ja, ich bin sicher</a>
							<a href="#" class="btn" data-dismiss="modal">Abbrechen</a>
						</div>
					</div>
					<div id="dialog_special" class="modal hide">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">×</button>
							<h3>Sind Sie sicher?</h3>
						</div>
						<div class="modal-body">
						Im Vergleich zur sicheren Anlage (2%) bevorzugen Sie also die risikobehaftete Anlage (50%ige Wahrscheinlichkeit auf einen Ertrag von 50% (d.h. +5\'000 ECU)) 
						bis zu einem möglichen Verlust von 50% (d.h. -5\'000 ECU). Ist das wirklich Ihre finale Entscheidung?
						</div>
						<div class="modal-footer">
							<a href="#" id="imsure" class="btn btn-primary">Ja, ich bin sicher</a>
							<a href="#" class="btn" data-dismiss="modal">Abbrechen</a>
						</div>
					</div>
					<div id="dialog_empty" class="modal hide">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">×</button>
							<h3>Bitte kreuzen Sie ein Feld an</h3>
						</div>
						<div class="modal-body">
						Sie müssen ihre Präferenz wählen.
						</div>
						<div class="modal-footer">
							<a href="#" class="btn" data-dismiss="modal">Okay</a>
						</div>
					</div>
					
					<span id="forward" class="btn btn-primary" style="margin: 20px; display: inline-block">Weiter</span>
					<script type="text/javascript">
					
					$("#imsure").click(function() {
						$("#question").submit();
					});
					
					$("input[type=radio]").click(function() {
						for (var i = 0; i < $(this).attr("id"); i++) {
							$(":radio[id=" + i + "][data-col=1]").attr("checked", "checked");
						}
						for (var i = ' . $fieldid . '; i > $(this).attr("id"); i--) {
							$(":radio[id=" + i + "][data-col=2]").attr("checked", "checked");
						}
						
						$(this).attr("checked", "checked");
						
						if($(this).attr("data-col") == 1) {
							var chosenNumber = Number($(this).attr("value"))-5;
							$(":hidden[id=' . $id . ']").attr("value", Number($(this).attr("value"))-5);
						} else if ($(this).attr("data-col") == 2) {
							var chosenNumber = $(this).attr("value");
							$(":hidden[id=' . $id . ']").attr("value", $(this).attr("value"));
						}
						
						$("#normal_body").html("Im Vergleich zur sicheren Anlage bevorzugen Sie also die risikobehaftete Anlage (50%ige Wahrscheinlichkeit auf einen Ertrag von 50% (d.h. +5\'000 ECU)) sofern der mögliche Verlust " + (Number(chosenNumber) + 5) + "% nicht übersteigt; ab einem möglichen Verlust von " + (Number(chosenNumber)) + "% bevorzugen Sie die sichere Anlage. Ist das wirklich Ihre finale Entscheidung?")
						
					});
					
					 $(document).ready(function() {
					 	$("#normal_body").html("Im Vergleich zur sicheren Anlage bevorzugen Sie also die risikobehaftete Anlage (50%ige Wahrscheinlichkeit auf einen Ertrag von50% (d.h. +5\'000 ECU)) sofern der mögliche Verlust " + (Number($(":hidden[id=' . $id . ']").attr("value"))+5) + "% nicht übersteigt; ab einem möglichen Verlust von " + (Number($(":hidden[id=' . $id . ']").attr("value"))) + "% bevorzugen Sie die sichere Anlage. Ist das wirklich Ihre finale Entscheidung?")
					 	$("#Weiter").hide();
					    
					    $("#dialog").modal("hide");
					    $("#dialog_empty").modal("hide");
					    $("#dialog_special").modal("hide");
					    
					    $("#forward").click(function() {
							if ($(":hidden[id=' . $id . ']").attr("value") == ' . $secondColumnArray[0] * $i . ') {
								$("#dialog_special").modal("show");
							} else if ($(":hidden[id=' . $id . ']").attr("value") == "") {
								$("#dialog_empty").modal("show");
							} else {
								$("#dialog").modal("show");
							}
						 });
					    
					  });
					</script>
					

					';
		
		return $markup;
	}
}