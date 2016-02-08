# laravel-localization-even
Allow you to even laravel localization files.

Foreach english missing translation, it creates a new empty entry with an inline comment, containing the spanish lemma.

Useful for translators.

##Usage
```php
php localization.php
```

###Example
````php
  'payments' => 
  array (
    'cost' => 'Coste',
    'download' => 'Descargar factura',
    'history' => 'Historial de pagos',
    'period' => 'Periodo',
  ),
```

will generate:
```php
	"payments" => array(
		"cost" => "", //Coste
		"download" => "", //Descargar factura
		"history" => "", //Historial de pagos
		"period" => "", //Periodo
	),
```

##TODO
* Allow to define the initial (native) language.
* Allow user to change needed languages.
* Pack it as Laravel dependency.
