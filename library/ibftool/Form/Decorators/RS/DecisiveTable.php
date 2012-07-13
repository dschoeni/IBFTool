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
		
		$markup .= "<table class='rs_decisivetable'>";
		$markup .= "<tr><td colspan='2'>Risikobehaftete Anlage</td><td colspan='2'>Entscheidung</td><td>Sichere Anlage</td></tr>";
		$markup .= "<tr><td>50% Wahrscheinlichkeit auf</td><td>50% Wahrscheinlichkeit auf</td><td>Ich bevorzuge die risikobehaftete Anlage</td><td>Ich bevorzuge die sichere Anlage</td><td>100% Wahrscheinlichkeit auf</td></tr>";
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
		$markup .= '<div id="dialog_special" style="display: none" title="Sind Sie sicher?">Im Vergleich zur sicheren Anlage (2%) bevorzugen Sie also die risikobehaftete Anlage (50%ige Wahrscheinlichkeit auf einen Ertrag von 50% (d.h. +5‘000 ECU)) bis zu einem möglichen Verlust von 50% (d.h. -5‘000 ECU). Ist das wirklich Ihre finale Entscheidung?</div>
					<div id="dialog" style="display: none" title="Sind Sie sicher?">TEXT</div>
					<div id="dialog_empty" style="display: none" title="Bitte kreuzen Sie ein Feld an">Sie müssen ihre Präferenz wählen.</div>
					
					<span id="forward" class="button" style="margin: 20px; display: inline-block" onmouseover="mouseover_forward()" onmouseout="mouseout_forward()">Weiter</span>
					<script type="text/javascript">
					
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
						
						$("#dialog").html("Im Vergleich zur sicheren Anlage bevorzugen Sie also die risikobehaftete Anlage (50%ige Wahrscheinlichkeit auf einen Ertrag von 50% (d.h. +5‘000 ECU)) sofern der mögliche Verlust " + (Number(chosenNumber) + 5) + "% nicht übersteigt; ab einem möglichen Verlust von " + (Number(chosenNumber)) + "% bevorzugen Sie die sichere Anlage. Ist das wirklich Ihre finale Entscheidung?")
						
					});
					
					 $(document).ready(function() {
					 	$("#dialog").html("Im Vergleich zur sicheren Anlage bevorzugen Sie also die risikobehaftete Anlage (50%ige Wahrscheinlichkeit auf einen Ertrag von 50% (d.h. +5‘000 ECU)) sofern der mögliche Verlust " + (Number($(":hidden[id=' . $id . ']").attr("value"))+5) + "% nicht übersteigt; ab einem möglichen Verlust von " + (Number($(":hidden[id=' . $id . ']").attr("value"))) + "% bevorzugen Sie die sichere Anlage. Ist das wirklich Ihre finale Entscheidung?")
					 	$("#Weiter").hide();
					    
					    $("#dialog").dialog({
					    	bgiframe: true,
					    	autoOpen: false,
					    	height: 300,
					    	modal: true,
					    	buttons: {
					    		Ja: function() {
					    	      $("#question").submit();
					    		  $(this).dialog("close");
					    		},
					    		Abbrechen: function() {
					    			$(this).dialog("close");
					    		}
					    	}
					    });
					    
					    $("#dialog_empty").dialog({
					    	bgiframe: true,
					    	autoOpen: false,
					    	height: 300,
					    	modal: true,
					    	buttons: {
					    		Abbrechen: function() {
					    			$(this).dialog("close");
					    		}
					    	}
					    });
					    
					     $("#dialog_special").dialog({
					    	bgiframe: true,
					    	autoOpen: false,
					    	height: 300,
					    	modal: true,
					    	buttons: {
					    		Ja: function() {
					    	      $("#question").submit();
					    		  $(this).dialog("close");
					    		},
					    		Abbrechen: function() {
					    		  $(this).dialog("close");
					    		}
					    	}
					    });
					    
					    $("#forward").click(function() {
							if ($(":hidden[id=' . $id . ']").attr("value") == ' . $secondColumnArray[0] * $i . ') {
								$("#dialog_special").dialog("open");
							} else if ($(":hidden[id=' . $id . ']").attr("value") == "") {
								$("#dialog_empty").dialog("open");
							} else {
								$("#dialog").dialog("open");
							}
						 });
					    
					  });
					  
					  	function mouseover_forward() { document.getElementById("forward").style.backgroundColor="#ababab"; }
						function mouseout_forward() { document.getElementById("forward").style.backgroundColor="#ffffff"; } 
					  	
					</script>
					

					';
		
		return $markup;
	}
}