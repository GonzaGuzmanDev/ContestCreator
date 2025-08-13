<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"             => "El :attribute debe ser aceptado.",
	"active_url"           => ":attribute no es una URL válida.",
	"after"                => ":attribute debe ser una fecha posterior a :date.",
	"alpha"                => ":attribute sólo puede contener letras.",
	"alpha_dash"           => ":attribute sólo puede contener letras, números y guiones.",
	"alpha_num"            => ":attribute sólo puede contener letras y números.",
	"array"                => ":attribute debe ser un array.",
	"before"               => ":attribute debe ser una fecha anterior a :date.",
	"between"              => array(
		"numeric" => ":attribute debe estar entre :min y :max.",
		"file"    => ":attribute debe estar entre :min y :max kilobytes.",
		"string"  => ":attribute debe estar entre :min y :max caractéres.",
		"array"   => ":attribute debe tener entre :min y :max items.",
	),
	"boolean"              => ":attribute debe ser verdadero o falso.",
	"captcha"			   => "El código de verificación es incorrecto. Por favor, ingrese el código que se muestra en la imagen",
	"confirmed"            => ":attribute, su confirmación no concuerda.",
	"date"                 => ":attribute no es una fecha válida.",
	"date_format"          => ":attribute no concuerda con el formato :format.",
	"different"            => ":attribute y :other deben ser diferentes.",
	"digits"               => ":attribute debe tener :digits digitos.",
	"digits_between"       => ":attribute debe tener entre :min y :max digitos.",
	"email"                => ":attribute debe ser una dirección válida de email.",
	"exists"               => "El :attribute seleccionado ya existe.",
	"image"                => ":attribute debe ser una imagen.",
	"in"                   => "El :attribute selecionado es inválido.",
	"integer"              => ":attribute debe ser un número entero.",
	"ip"                   => ":attribute debe ser una dirección válida de IP.",
	"max"                  => array(
		"numeric" => ":attribute no puede ser mayor a :max.",
		"file"    => ":attribute no puede ser mayor a :max kilobytes.",
		"string"  => ":attribute no puede tener más de :max caractéres.",
		"array"   => ":attribute no puede ser mayor a :max items.",
	),
	"mimes"                => "El :attribute debe ser un archivo type: :values.",
	"min"                  => array(
		"numeric" => ":attribute debe ser al menos :min.",
		"file"    => ":attribute debe ser al menos :min kilobytes.",
		"string"  => ":attribute debe tener al menos :min caractéres.",
		"array"   => ":attribute debe tener al menos :min items.",
	),
	"not_in"               => "El :attribute seleccionado es inválido.",
	"numeric"              => ":attribute debe ser un número.",
	"regex"                => "El formato de :attribute es inválido.",
	"required"             => "El campo :attribute es requerido.",
	"required_if"          => "El campo :attribute es requerido si :other es :value.",
	"required_with"        => "El campo :attribute es requerido si está presente :values.",
	"required_with_all"    => "El campo :attribute es requerido si :values están presentes.",
	"required_without"     => "El campo :attribute es requerido si :values no está presente.",
	"required_without_all" => "El campo :attribute es requerido cuando ninguno de :values estén presentes.",
	"same"                 => ":attribute y :other deben coincidir.",
	"size"                 => array(
		"numeric" => ":attribute debe ser :size.",
		"file"    => ":attribute debe ser :size kilobytes.",
		"string"  => ":attribute debe ser :size caractéres.",
		"array"   => ":attribute debe contener :size items.",
	),
	"unique"               => "El :attribute ya fue utilizado.",
	"url"                  => "El formato de :attribute es inválido.",
	"timezone"             => ":attribute debe ser una zona válida.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(
		'attribute-name' => array(
			'rule-name' => 'custom-message',
		),
	),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(
        'first_name' => Lang::get('register.firstName'),
        'last_name' => Lang::get('register.lastName'),
        'new_password' => Lang::get('register.password'),
        'repeat_password' => Lang::get('register.repeatPassword')
    ),

);