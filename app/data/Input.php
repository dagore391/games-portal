<?php
namespace app\data;
// TODO: Revisar todos los métodos mixed. Al sanitizar hay que hacerlo por tipos y no suponer que siempre es un string.
final class Input {
	public static function exists(string $type) : bool {
		switch($type) {
			case 'get' :
				return !empty($_GET);
			case 'post':
				return !empty($_POST);
			default:
				return false;
		}
	}
		
	public static function get(string $name) : mixed {
		if(empty($_REQUEST[$name])) {
			return '';
		} elseif(is_string($_REQUEST[$name])) {
			return Sanitize::trimText($_REQUEST[$name]);
		} else {
			return $_REQUEST[$name];
		}
	}
	
	public static function cleanPost(string $name) : void {
		if(!empty($_POST[$name])) {
			$_POST[$name] = null;
		}
	}
		
	public static function getPost(string $name) : mixed {
		if(empty($_POST[$name])) {
			return '';
		} elseif(is_string($_POST[$name])) {
			return Sanitize::trimText($_POST[$name]);
		} else {
			return $_POST[$name];
		}
	}
		
	public static function getSanitizePost(string $name) : mixed {
		return !empty($_POST[$name]) ? Sanitize::fullClean($_POST[$name]) : '';
	}
		
	public static function getPostWithDefaultValue(string $name, $defaultValue) : mixed {
		if(empty($_POST[$name])) {
			return $defaultValue;
		} elseif(is_string($_POST[$name])) {
			return Sanitize::trimText($_POST[$name]);
		} else {
			return $_POST[$name];
		}
	}
	
	/**
	 * Imprime el código HTML de un campo, con dos columnas, de tipo selector múltiple.
	 * @param $name string Nombre que tendrá el campo de selección múltiple.
	 * @param $label string Etiqueta descriptiva del campo de selección múltiple.
	 * @param $elements array Elementos que se mostraran en el select. El formato puede ser como uno de los dos ejemplos siguiente: array(array('tag' => '', 'value' => '')) ó array(array('group' => '', 'tag' => '', 'value' => ''));
	 * @param $selectedValues array Elementos que se encuentran seleccionados. El formato es el del ejemplo que sigue: array(1, 2, 3, 4);
	 * @param $size int Número de elementos que serán visibles en cada columna;
	 */
	public static function htmlMultiselect(string $name, string $label, array $elements, array $selectedValues, int $size, bool $group = true) : void {
		echo "<div class=\"vmargin5 width-100\">
			<label class=\"vmargin5\" for=\"{$name}\"><b>{$label}</b></label><br>
			<div class=\"flex flex-halign-justify width-100\">
				<div class=\"width-40\">
					<select class=\"vmargin5 width-100\" id=\"o{$name}\" name=\"o{$name}[]\" size=\"{$size}\" multiple>";
						$currentGroupName = null;
						$optionGroupString = '';
						foreach($elements as $element) {
							$currentGroupName = $currentGroupName === null && !empty($element->group) ? $element->group : $currentGroupName;
							if(!isset($element->tag, $element->value)) {
								continue;
							}
							if(isset($element->group)) {
								if($currentGroupName != $element->group) {
									echo "<optgroup label=\"{$currentGroupName}\">{$optionGroupString}</optgroup>";
									$optionGroupString = '';
									$currentGroupName = $element->group;
								}
							} else {
								echo $optionGroupString;
								$optionGroupString = '';
							}
							if(!in_array($element->value, $selectedValues)) {
								$optionGroupString .= "<option value=\"{$element->value}\">{$element->tag}</option>";
							}
						}
						if(!$group && $optionGroupString != '') {
							echo $optionGroupString;
						} else if($group && $currentGroupName !== null && $optionGroupString === '') {
							echo "<optgroup label=\"{$currentGroupName}\"></optgroup>";
						} else if($group) {
							echo "<optgroup label=\"{$currentGroupName}\">{$optionGroupString}</optgroup>";
						}
					echo "</select>
				</div>
				<div class=\"width-20\">
					<div class=\"padding10 text-center\">
						<button class=\"button button-red vmargin5 width-100\" id=\"add{$name}\" onclick=\"twoColumnsSelect('o{$name}', '{$name}', '" . (isset($element->group) ? true : false) . "');\" type=\"button\">" . LANG_ADD . " &#11208;</button>
						<button class=\"button button-red vmargin5 width-100\" id=\"remove{$name}\" onclick=\"twoColumnsSelect('{$name}', 'o{$name}', '" . (isset($element->group) ? true : false) . "');\" type=\"button\">&#11207; " . LANG_REMOVE . "</button>
					</div>
				</div>
				<div class=\"width-40\">
					<select class=\"vmargin5 width-100\" id=\"{$name}\" name=\"{$name}[]\" size=\"{$size}\" multiple>";
						$currentGroupName = null;
						$optionGroupString = '';
						foreach($elements as $element) {
							$currentGroupName = $currentGroupName === null && isset($element->group) ? $element->group : $currentGroupName;
							if(!isset($element->tag, $element->value)) {
								continue;
							}
							if(isset($element->group)) {
								if($currentGroupName != $element->group) {
									echo "<optgroup label=\"{$currentGroupName}\">{$optionGroupString}</optgroup>";
									$optionGroupString = '';
									$currentGroupName = $element->group;
								}
							} else {
								echo $optionGroupString;
								$optionGroupString = '';
							}
							if(in_array($element->value, $selectedValues)) {
								$optionGroupString .= "<option value=\"{$element->value}\">{$element->tag}</option>";
							}
						}
						if(!$group && $optionGroupString !== '') {
							echo $optionGroupString;
						} else if($currentGroupName !== null && $optionGroupString === '') {
							echo "<optgroup label=\"{$currentGroupName}\"></optgroup>";
						} else if($currentGroupName !== null) {
							echo "<optgroup label=\"{$currentGroupName}\">{$optionGroupString}</optgroup>";
						}
					echo "</select>
				</div>
			</div>
		</div>";
	}
}
