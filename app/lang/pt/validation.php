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

	"accepted"             => "O :attribute deve ser aceito.",
	"active_url"           => ":attribute não é uma URL válida.",
	"after"                => ":attribute deve ser uma data posterior a :date.",
	"alpha"                => ":attribute só pode conter letras.",
	"alpha_dash"           => ":attribute só pode conter letras, números e travessões.",
	"alpha_num"            => ":attribute só pode conter letras letras e números.",
	"array"                => ":attribute deve ser um array.",
	"before"               => ":attribute deve ser uma data anterior a :date.",
	"between"              => array(
		"numeric" => ":attribute deve estar entre :min e :max.",
		"file"    => ":attribute deve estar entre :min e :max kilobytes.",
		"string"  => ":attribute deve estar entre :min e :max caracteres.",
		"array"   => ":attribute deve ter entre :min e :max itens.",
	),
	"boolean"              => ":attribute deve ser verdadeiro ou falso.",
	"captcha"			   => "O código verificação está incorreto. Por favor, preencha com o código que aparece na imagem",
	"confirmed"            => ":attribute confirmação não não corresponde.",
	"date"                 => ":attribute não é uma data válida.",
	"date_format"          => ":attribute não corresponde com o formato :format.",
	"different"            => ":attribute e :other precisam ser diferentes.",
	"digits"               => ":attribute precisa ter :digits dígitos.",
	"digits_between"       => ":attribute deve ter entre :min e :max dígitos.",
	"email"                => ":attribute deve ser um e-mail válido.",
	"exists"               => "O :attribute selecionado já foi utilizado.",
	"image"                => ":attribute deve ser uma imagem.",
	"in"                   => "O :attribute selecionado já existe.",
	"integer"              => ":attribute deve ser um número inteiro.",
	"ip"                   => ":attribute precisa ser um IP válido.",
	"max"                  => array(
		"numeric" => ":attribute não deve ser maior do que :max.",
		"file"    => ":attribute não deve ser maior do que :max kilobytes.",
		"string"  => ":attribute não deve ter maior do que :max caracteres.",
		"array"   => ":attribute não deve ser maior do que :max itens.",
	),
	"mimes"                => "O :attribute deve ser um arquivo type: :values.",
	"min"                  => array(
		"numeric" => ":attribute deve ser ao menos :min.",
		"file"    => ":attribute deve ser ao menos :min kilobytes.",
		"string"  => ":attribute deve ter ao menos :min caracteres.",
		"array"   => ":attribute deve ter ao menos :min itens.",
	),
	"not_in"               => "O :attribute é inválido.",
	"numeric"              => ":attribute precisa ser um número.",
	"regex"                => "O formato :attribute é inválido.",
	"required"             => "O campo :attribute é obrigatório.",
	"required_if"          => "O campo :attribute é obrigatório se :other for :value.",
	"required_with"        => "O campo :attribute é obrigatório se :values estiver presente.",
	"required_with_all"    => "O campo :attribute é obrigatório se :values estiverem presentes.",
	"required_without"     => "O campo :attribute é obrigatório se :values não estiver presente.",
	"required_without_all" => "O campo :attribute é obrigatório quando nenhum dos :values estiverem presentes.",
	"same"                 => ":attribute e :other devem coincidir.",
	"size"                 => array(
		"numeric" => ":attribute deve ter :size.",
		"file"    => ":attribute deve ter :size kilobytes.",
		"string"  => ":attribute deve ter :size caracteres.",
		"array"   => ":attribute deve conter :size itens.",
	),
	"unique"               => "O :attribute já foi utilizado.",
	"url"                  => "O formato :attribute é inválido.",
	"timezone"             => ":attribute deve ser uma zona válida.",

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

	'attributes' => array(),

);
