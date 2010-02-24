<?php

/**
 * Creates a select box
 *
 * @access public
 * @param mixed $selected The selected value
 * @param array $options The select box options in a multi-dimensional array. Array keys are used as the option value, array values are used as the option label
 * @param string $input_name The name of the input eg: Lg_polls_ext[log_ip]
 * @param string $input_id A unique ID for this select. If no id is given the id will be created from the $input_name
 * @param boolean $use_lang Pass the option label through the lang() function or display in a raw state
 * @param array $attributes Any other attributes for the select box such as class, multiple, size etc
 * @return string Select box html
 */
if (function_exists('newSelectBox') === FALSE)
{
	function newSelectBox($input_name, $options, $selected, $input_id = FALSE, $use_lang = TRUE, $key_is_value = TRUE, $attributes = array())
	{
		$input_id = ($input_id === FALSE) ? str_replace(array("[", "]"), array("_", ""), $input_name) : $input_id;

		$attributes = array_merge(array(
			"name" => $input_name,
			"id" => strtolower($input_id)
		), $attributes);

		$attributes_str = "";
		foreach ($attributes as $key => $value)
		{
			$attributes_str .= " {$key}='{$value}' ";
		}

		$ret = "<select{$attributes_str}>";

		foreach($options as $option_value => $option_label)
		{
			$option_value = ($key_is_value === TRUE) ? $option_value : $option_label;
			$option_label = ($use_lang === TRUE) ? lang($option_label) : $option_label;
			$checked = ($selected == $option_value) ? " selected='selected' " : "";
			$ret .= "<option value='{$option_value}'{$checked}>{$option_label}</option>";
		}

		$ret .= "</select>";
		return $ret;
	}
}

/**
 * Creates a checkbox
 *
 * @access public
 * @param boolean $selected checkbox
 * @param string $input_name The name of the input eg: Lg_polls_ext[log_ip]
 * @param string $input_label The label for the input eg: Polls
 * @param string $input_id A unique ID for this select. If no id is given the id will be created from the $input_name
 * @param boolean $use_lang Pass the option label through the lang() function or display in a raw state
 * @param array $attributes Any other attributes for the select box such as class, multiple, size etc
 * @return string Select box html
 */
if (function_exists('newCheckBox') === FALSE)
{
	function newCheckBox($input_name, $input_label, $checked, $input_value = TRUE, $input_id = FALSE, $use_lang = TRUE, $generate_shadow = TRUE, $attributes = array())
	{
		$input_id = ($input_id === FALSE) ? str_replace(array("[", "]"), array("_", ""), $input_name) : $input_id;

		$label = ($use_lang == TRUE) ? lang($input_label) : $input_label;

		$checked = ($checked == TRUE) ? "checked='checked'" : "";

		$attributes = array_merge(array(
			"name" => $input_name,
			"id" => strtolower($input_id),
			"value" => $input_value,
		), $attributes);

		$attributes_str = "";
		foreach ($attributes as $key => $value)
		{
			$attributes_str .= " {$key}='{$value}' ";
		}

		$ret = '';
		if ($generate_shadow != FALSE)
		{
			$ret .= '<input type="hidden" name="' . $input_name . '" value="0"/>';
		}

		$ret .= '<label class="checkbox">';
		$ret .= '<input type="checkbox" '. $attributes_str . $checked . ' />';
		$ret .= $label . '</label>';
		return $ret;
	}
}

if (function_exists('getPossibleMissingValue') === FALSE)
{
	function getPossibleMissingValue($input)
	{
		return (isset($input) === FALSE) ? $input : FALSE;

	}
}

