<?php

class Messages
{

    /**
     * All of the registered messages.
     *
     * @var array
     */
    public $messages;

    /**
     * Default format for message output.
     *
     * @var string
     */
    public $format = ':message';

    /**
     * Create a new Messages instance.
     *
     * @param  array  $messages
     * @return void
     */
    public function __construct($messages = array())
    {
        $this->messages = (array) $messages;
    }

    /**
     * Add a message to the collector.
     *
     * <code>
     *      // Add a message for the e-mail attribute
     *      $messages->add('email', 'The e-mail address is invalid.');
     * </code>
     *
     * @param  string  $key
     * @param  string  $message
     * @return void
     */
    public function add($key, $message)
    {
        if ($this->unique($key, $message)) $this->messages[$key][] = $message;
    }

    /**
     * Determine if a key and message combination already exists.
     *
     * @param  string  $key
     * @param  string  $message
     * @return bool
     */
    protected function unique($key, $message)
    {
        return !isset($this->messages[$key]) or !in_array($message, $this->messages[$key]);
    }

    /**
     * Determine if messages exist for a given key.
     *
     * <code>
     *      // Is there a message for the e-mail attribute
     *      return $messages->has('email');
     *
     *      // Is there a message for the any attribute
     *      echo $messages->has();
     * </code>
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key = null)
    {
        return $this->first($key) !== '';
    }

    /**
     * Set the default message format for output.
     *
     * <code>
     *      // Apply a new default format.
     *      $messages->format('email', '<p>this is my :message</p>');
     * </code>
     *
     * @param  string  $format
     */
    public function format($format = ':message')
    {
        $this->format = $format;
    }

    /**
     * Get the first message from the container for a given key.
     *
     * <code>
     *      // Echo the first message out of all messages.
     *      echo $messages->first();
     *
     *      // Echo the first message for the e-mail attribute
     *      echo $messages->first('email');
     *
     *      // Format the first message for the e-mail attribute
     *      echo $messages->first('email', '<p>:message</p>');
     * </code>
     *
     * @param  string  $key
     * @param  string  $format
     * @return string
     */
    public function first($key = null, $format = null)
    {
        $format = ($format === null) ? $this->format : $format;

        $messages = is_null($key) ? $this->all($format) : $this->get($key, $format);

        return (count($messages) > 0) ? $messages[0] : '';
    }

    /**
     * Get all of the messages from the container for a given key.
     *
     * <code>
     *      // Echo all of the messages for the e-mail attribute
     *      echo $messages->get('email');
     *
     *      // Format all of the messages for the e-mail attribute
     *      echo $messages->get('email', '<p>:message</p>');
     * </code>
     *
     * @param  string  $key
     * @param  string  $format
     * @return array
     */
    public function get($key, $format = null)
    {
        $format = ($format === null) ? $this->format : $format;

        if (array_key_exists($key, $this->messages)) {
            return $this->transform($this->messages[$key], $format);
        }

        return array();
    }

    /**
     * Get all of the messages for every key in the container.
     *
     * <code>
     *      // Get all of the messages in the collector
     *      $all = $messages->all();
     *
     *      // Format all of the messages in the collector
     *      $all = $messages->all('<p>:message</p>');
     * </code>
     *
     * @param  string  $format
     * @return array
     */
    public function all($format = null)
    {
        $format = ($format === null) ? $this->format : $format;

        $all = array();

        foreach ($this->messages as $messages) {
            $all = array_merge($all, $this->transform($messages, $format));
        }

        return $all;
    }

    /**
     * Format an array of messages.
     *
     * @param  array   $messages
     * @param  string  $format
     * @return array
     */
    protected function transform($messages, $format)
    {
        $messages = (array) $messages;

        foreach ($messages as $key => &$message) {
            $message = str_replace(':message', $message, $format);
        }

        return $messages;
    }
}

class Validator
{

    public $_lang = array(

        /*
		|--------------------------------------------------------------------------
		| Validation Language Lines
		|--------------------------------------------------------------------------
		|
		| The following language lines contain the default error messages used
		| by the validator class. Some of the rules contain multiple versions,
		| such as the size (max, min, between) rules. These versions are used
		| for different input types such as strings and files.
		|
		| These language lines may be easily changed to provide custom error
		| messages in your application. Error messages for custom validation
		| rules may also be added to this file.
		|
		*/

        "accepted"       => "Este campo deve ser aceito",
        "active_url"     => "O dado informado não é uma URL válida",
        "after"          => "Informe uma data após :date",
        "alpha"          => "Só pode conter letras",
        "alpha_dash"     => "Só pode conter letras, números e traços",
        "alpha_num"      => "Só pode conter letras e números",
        "before"         => "A data deve ser anterior à :date",
        "between"        => array(
            "numeric" => "O dado informado deve estar entre :min - :max",
            "file"    => "O dado informado deve estar entre :min - :max kilobytes",
            "string"  => "O dado informado deve estar entre :min - :max caracteres",
        ),
        "confirmed"      => "O dado informado não coincide",
        "different"      => "O :attribute e :other devem ser diferentes",
        "email"          => "O dado informado não é um e-mail válido",
        "exists"         => "O dado informado selecionado é inválido",
        "image"          => "O arquivo selecionado deve ser uma imagem",
        "in"             => "O dado selecionado é inválido",
        "integer"        => "O dado informado deve ser um inteiro",
        "ip"             => "Informe um endereço IP válido",
        "match"          => "O formato é inválido",
        "max"            => array(
            "numeric" => "O valor informado deve ser inferior a :max",
            "file"    => "O tamanho do arquivo deve ser inferior a :max kilobytes",
            "string"  => "O comprimento do texto deve ser inferior a :max caracteres",
        ),
        "mimes"          => "Selecione um arquivo do tipo: :values",
        "min"            => array(
            "numeric" => "O valor informado deve conter pelo menos :min",
            "file"    => "O valor informado deve conter pelo menos :min kilobytes",
            "string"  => "O valor informado deve conter pelo menos :min caracteres",
        ),
        "not_in"         => "O valor informado é inválido",
        "numeric"        => "O valor informado deve ser um número",
        "required"       => "Este campo é requerido",
        "same"           => "O :attribute e :other devem ser iguais",
        "size"           => array(
            "numeric" => "O valor informado deve ser :size",
            "file"    => "O valor informado deve ter :size kilobyte",
            "string"  => "O valor informado deve ter :size caracteres",
        ),
        "unique"         => "Já existe um registro com o valor informado",
        "url"            => "O formato do dado informado é inválido",

        /*
		|--------------------------------------------------------------------------
		| Custom Validation Language Lines
		|--------------------------------------------------------------------------
		|
		| Here you may specify custom validation messages for attributes using the
		| convention "attribute_rule" to name the lines. This helps keep your
		| custom validation clean and tidy.
		|
		| So, say you want to use a custom validation message when validating that
		| the "email" attribute is unique. Just add "email_unique" to this array
		| with your custom message. The Validator will handle the rest!
		|
		*/

        'custom' => array(
            "confirmed"     => "Confirmação não coincide",
        ),

        'cpf'           => 'O valor informando é inválido',
        'cnpj'          => 'O valor informando é inválido',
        'cpf_cnpj'      => 'O valor informando é inválido',

        'personname'    => 'O nome informado não é válido! Informe seu nome completo!',

        'date_format'   => 'Data inválida',

        /*
		|--------------------------------------------------------------------------
		| Validation Attributes
		|--------------------------------------------------------------------------
		|
		| The following language lines are used to swap attribute place-holders
		| with something more reader friendly such as "E-Mail Address" instead
		| of "email". Your users will thank you.
		|
		| The Validator class will automatically search this array of lines it
		| is attempting to replace the :attribute place-holder in messages.
		| It's pretty slick. We think you'll like it.
		|
		*/

        'attributes' => array(
            'senha_confirmation' => 'campo senha'
        ),

    );
    /**
     * The array being validated.
     *
     * @var array
     */
    public $attributes;

    /**
     * The post-validation error messages.
     *
     * @var Messages
     */
    public $errors;

    /**
     * The validation rules.
     *
     * @var array
     */
    protected $rules = array();

    /**
     * The validation messages.
     *
     * @var array
     */
    protected $messages = array();

    /**
     * The language that should be used when retrieving error messages.
     *
     * @var string
     */
    protected $language;

    /**
     * The size related validation rules.
     *
     * @var array
     */
    protected $size_rules = array('size', 'between', 'min', 'max');

    /**
     * The numeric related validation rules.
     *
     * @var array
     */
    protected $numeric_rules = array('numeric', 'integer');

    /**
     * The registered custom validators.
     *
     * @var array
     */
    protected static $validators = array();

    /**
     * Create a new validator instance.
     *
     * @param  mixed  $attributes
     * @param  array  $rules
     * @param  array  $messages
     * @return void
     */
    public function __construct($attributes, $rules, $messages = array())
    {
        foreach ($rules as $key => &$rule) {
            $rule = (is_string($rule)) ? explode('|', $rule) : $rule;
        }

        $this->rules = $rules;
        $this->messages = $messages;
        $this->attributes = (is_object($attributes)) ? get_object_vars($attributes) : $attributes;
    }

    /**
     * Create a new validator instance.
     *
     * @param  array      $attributes
     * @param  array      $rules
     * @param  array      $messages
     * @return Validator
     */
    public static function make($attributes, $rules, $messages = array())
    {
        return new Validator($attributes, $rules, $messages);
    }

    /**
     * Register a custom validator.
     *
     * @param  string   $name
     * @param  Closure  $validator
     * @return void
     */
    public static function register($name, $validator)
    {
        self::$validators[$name] = $validator;
    }

    /**
     * Validate the target array using the specified validation rules.
     *
     * @return bool
     */
    public function passes()
    {
        return $this->valid();
    }

    /**
     * Validate the target array using the specified validation rules.
     *
     * @return bool
     */
    public function fails()
    {
        return $this->invalid();
    }

    /**
     * Validate the target array using the specified validation rules.
     *
     * @return bool
     */
    public function invalid()
    {
        return !$this->valid();
    }

    /**
     * Validate the target array using the specified validation rules.
     *
     * @return bool
     */
    public function valid()
    {
        $this->errors = new Messages;

        foreach ($this->rules as $attribute => $rules) {
            foreach ($rules as $rule) $this->check($attribute, $rule);
        }

        return count($this->errors->messages) == 0;
    }

    /**
     * Evaluate an attribute against a validation rule.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return void
     */
    protected function check($attribute, $rule)
    {
        list($rule, $parameters) = $this->parse($rule);

        $value = array_get($this->attributes, $attribute);

        // Before running the validator, we need to verify that the attribute and rule
        // combination is actually validatable. Only the "accepted" rule implies that
        // the attribute is "required", so if the attribute does not exist, the other
        // rules will not be run for the attribute.
        $validatable = $this->validatable($rule, $attribute, $value);

        if ($validatable and !$this->{'validate_' . $rule}($attribute, $value, $parameters, $this)) {
            $this->error($attribute, $rule, $parameters);
        }
    }

    /**
     * Determine if an attribute is validatable.
     *
     * To be considered validatable, the attribute must either exist, or the rule
     * being checked must implicitly validate "required", such as the "required"
     * rule or the "accepted" rule.
     *
     * @param  string  $rule
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validatable($rule, $attribute, $value)
    {
        if (empty($rule)) {
            return false;
        }

        return $this->validate_required($attribute, $value) or $this->implicit($rule);
    }

    /**
     * Determine if a given rule implies that the attribute is required.
     *
     * @param  string  $rule
     * @return bool
     */
    protected function implicit($rule)
    {
        return $rule == 'required' or $rule == 'accepted' or $rule == 'required_with';
    }

    /**
     * Add an error message to the validator's collection of messages.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return void
     */
    protected function error($attribute, $rule, $parameters)
    {
        $message = $this->replace($this->message($attribute, $rule), $attribute, $rule, $parameters);

        $this->errors->add($attribute, $message);
    }

    /**
     * Validate that a required attribute exists in the attributes array.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_required($attribute, $value)
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) and trim($value) === '') {
            return false;
        } elseif (!is_null(array_get($_FILES, $attribute, null)) and is_array($value) and $value['tmp_name'] == '') {
            return false;
        }

        return true;
    }

    /**
     * Validate that an attribute exists in the attributes array, if another
     * attribute exists in the attributes array.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_required_with($attribute, $value, $parameters)
    {
        $other = $parameters[0];
        $other_value = array_get($this->attributes, $other);

        if ($this->validate_required($other, $other_value)) {
            return $this->validate_required($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute has a matching confirmation attribute.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_confirmed($attribute, $value)
    {
        return $this->validate_same($attribute, $value, array($attribute . '_confirmation'));
    }

    /**
     * Validate that an attribute was "accepted".
     *
     * This validation rule implies the attribute is "required".
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_accepted($attribute, $value)
    {
        return $this->validate_required($attribute, $value) and ($value == 'yes' or $value == '1' or $value == 'on');
    }

    /**
     * Validate that an attribute is the same as another attribute.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_same($attribute, $value, $parameters)
    {
        $other = $parameters[0];

        return array_key_exists($other, $this->attributes) and $value == $this->attributes[$other];
    }

    /**
     * Validate that an attribute is different from another attribute.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_different($attribute, $value, $parameters)
    {
        $other = $parameters[0];

        return array_key_exists($other, $this->attributes) and $value != $this->attributes[$other];
    }

    /**
     * Validate that an attribute is numeric.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_numeric($attribute, $value)
    {
        return is_numeric(toNumber($value));
    }

    /**
     * Validate that an attribute is an integer.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_integer($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate the size of an attribute.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_size($attribute, $value, $parameters)
    {
        return $this->size($attribute, $value) == $parameters[0];
    }

    /**
     * Validate the size of an attribute is between a set of values.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_between($attribute, $value, $parameters)
    {
        $size = $this->size($attribute, $value);

        return $size >= $parameters[0] and $size <= $parameters[1];
    }

    /**
     * Validate the size of an attribute is greater than a minimum value.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_min($attribute, $value, $parameters)
    {
        return $this->size($attribute, $value) >= $parameters[0];
    }

    /**
     * Validate the size of an attribute is less than a maximum value.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_max($attribute, $value, $parameters)
    {
        return $this->size($attribute, $value) <= $parameters[0];
    }

    /**
     * Get the size of an attribute.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return mixed
     */
    protected function size($attribute, $value)
    {
        // This method will determine if the attribute is a number, string, or file and
        // return the proper size accordingly. If it is a number, the number itself is
        // the size; if it is a file, the kilobytes is the size; if it is a
        // string, the length is the size.
        if (is_numeric($value) and $this->has_rule($attribute, $this->numeric_rules)) {
            return $this->attributes[$attribute];
        } elseif (array_key_exists($attribute, $_FILES)) {
            return $value['size'] / 1024;
        } else {
            return strlen(trim(Str::ascii($value)));
        }
    }

    /**
     * Validate an attribute is contained within a list of values.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_in($attribute, $value, $parameters)
    {
        return in_array($value, $parameters);
    }

    /**
     * Validate an attribute is not contained within a list of values.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_not_in($attribute, $value, $parameters)
    {
        return !in_array($value, $parameters);
    }

    /**
     * Validate the uniqueness of an attribute value on a given database table.
     *
     * If a database column is not specified, the attribute will be used.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_unique($attribute, $value, $parameters)
    {
        // echo pr(compact('attribute', 'value', 'parameters'));

        // We allow the table column to be specified just in case the column does
        // not have the same name as the attribute. It must be within the second
        // parameter position, right after the database table name.
        if (isset($parameters[1])) {
            $attribute = $parameters[1];
        }

        if (preg_match('/model\./i', $parameters[0])) {
            $model = str_replace('model.', '', $parameters[0]);
            $db = DB::connection($model::$connection)->table($model::$table);
        } else {
            $db = DB::table($parameters[0]);
        }

        // We also allow an ID to be specified that will not be included in the
        // uniqueness check. This makes updating columns easier since it is
        // fine for the given ID to exist in the table.

        if (isset($parameters[2])) {
            $id = (isset($parameters[1])) ? $parameters[1] : 'id';
            $db->where("{$id}", '!=', $parameters[2]);
        }

        $query = $db->where($attribute, $value);

        return $query->count() == 0;
    }

    /**
     * Validate the existence of an attribute value in a database table.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_exists($attribute, $value, $parameters)
    {
        if (isset($parameters[1])) $attribute = $parameters[1];

        // Grab the number of elements we are looking for. If the given value is
        // in array, we'll count all of the values in the array, otherwise we
        // can just make sure the count is greater or equal to one.
        $count = (is_array($value)) ? count($value) : 1;

        if (preg_match('/model\./i', $parameters[0])) {
            $model = str_replace('model.', '', $parameters[0]);
            $db = DB::connection($model::$connection)->table($model::$table);
        } else {
            $db = DB::table($parameters[0]);
        }

        // If the given value is an array, we will check for the existence of
        // all the values in the database, otherwise we'll check for the
        // presence of the single given value in the database.
        if (is_array($value)) {
            $db->where($attribute, 'in', implode(',', $value));
        } else {
            $db->where($attribute, $value);
        }

        return $db->count() >= $count;
    }

    /**
     * Validate that an attribute is a valid IP.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_ip($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate that an attribute is a valid e-mail address.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_email($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate that an attribute is a valid URL.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_url($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate that an attribute is an active URL.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_active_url($attribute, $value)
    {
        $url = str_replace(array('http://', 'https://', 'ftp://'), '', strtolower($value));

        return (trim($url) !== '') ? checkdnsrr($url) : false;
    }

    /**
     * Validate the MIME type of a file is an image MIME type.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_image($attribute, $value)
    {
        return $this->validate_mimes($attribute, $value, array('jpg', 'png', 'gif', 'bmp'));
    }

    /**
     * Validate that an attribute contains only alphabetic characters.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_alpha($attribute, $value)
    {
        return preg_match('/^([a-z])+$/i', $value);
    }

    /**
     * Validate that an attribute contains only alpha-numeric characters.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_alpha_num($attribute, $value)
    {
        return preg_match('/^([a-z0-9])+$/i', $value);
    }

    /**
     * Validate that an attribute contains only alpha-numeric characters, dashes, and underscores.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_alpha_dash($attribute, $value)
    {
        return preg_match('/^([-a-z0-9_-])+$/i', $value);
    }

    /**
     * Validate that an attribute passes a regular expression check.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_match($attribute, $value, $parameters)
    {
        return preg_match($parameters[0], $value);
    }

    /**
     * Validate the MIME type of a file upload attribute is in a set of MIME types.
     *
     * @param  string  $attribute
     * @param  array   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_mimes($attribute, $value, $parameters)
    {
        if (!is_array($value) or array_get($value, 'tmp_name', '') == '') return true;

        foreach ($parameters as $extension) {
            if ($extension === File::extension($value['tmp_name'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate that an attribute is an array
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validate_array($attribute, $value)
    {
        return is_array($value);
    }

    /**
     * Validate that an attribute of type array has a specific count
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_count($attribute, $value, $parameters)
    {
        return (is_array($value) && count($value) == $parameters[0]);
    }

    /**
     * Validate that an attribute of type array has a minimum of elements.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_countmin($attribute, $value, $parameters)
    {
        return (is_array($value) && count($value) >= $parameters[0]);
    }

    /**
     * Validate that an attribute of type array has a maximum of elements.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_countmax($attribute, $value, $parameters)
    {
        return (is_array($value) && count($value) <= $parameters[0]);
    }

    /**
     * Validate that an attribute of type array has elements between max and min.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_countbetween($attribute, $value, $parameters)
    {
        return (is_array($value) && count($value) >= $parameters[0] && count($value) <= $parameters[1]);
    }

    /**
     * Validate the date is before a given date.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_before($attribute, $value, $parameters)
    {
        return (strtotime($value) < strtotime($parameters[0]));
    }

    /**
     * Validate the date is after a given date.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_after($attribute, $value, $parameters)
    {
        return (strtotime($value) > strtotime($parameters[0]));
    }

    protected function validate_personname($attribute, $value, $parameters)
    {
        $value = preg_replace('/\s+/i', ' ', trim($value));

        return count(explode(' ', $value)) > 1;
    }

    /**
     * Validate the date conforms to a given format.
     * 
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validate_date_format($attribute, $value, $parameters)
    {
        $date = date_create_from_format($parameters[0], $value);
        return $date ? $date->format($parameters[0]) === $value : false;
    }

    public function validate_cpf_cnpj($attribute, $value, $parameters)
    {
        $value = preg_replace('/\D/', '', $value);
        return strlen($value) <= 11 ? $this->validate_cpf($attribute, $value, $parameters) : $this->validate_cnpj($attribute, $value, $parameters);
    }

    public function validate_cpf($attribute, $value, $parameters)
    {
        /*
         * Salva em $cpf apenas numeros, isso permite receber o cpf em diferentes formatos,
         * como "000.000.000-00", "00000000000", "000 000 000 00"
         */
        $cpf = preg_replace('/\D/', '', $value);
        $num = array();

        /* Cria um array com os valores */
        for ($i = 0; $i < (strlen($cpf)); $i++) {

            $num[] = $cpf[$i];
        }

        if (count($num) != 11) {
            return false;
        } else {
            /*
            Combinações como 00000000000 e 22222222222 embora
            não sejam cpfs reais resultariam em cpfs
            válidos após o calculo dos dígitos verificares e
            por isso precisam ser filtradas nesta parte.
            */
            for ($i = 0; $i < 10; $i++) {
                if (
                    $num[0] == $i && $num[1] == $i && $num[2] == $i
                    && $num[3] == $i && $num[4] == $i && $num[5] == $i
                    && $num[6] == $i && $num[7] == $i && $num[8] == $i
                ) {
                    return false;
                    break;
                }
            }
        }
        /*
        Calcula e compara o
        primeiro dígito verificador.
        */
        $j = 10;
        for ($i = 0; $i < 9; $i++) {
            $multiplica[$i] = $num[$i] * $j;
            $j--;
        }
        $soma = array_sum($multiplica);
        $resto = $soma % 11;
        if ($resto < 2) {
            $dg = 0;
        } else {
            $dg = 11 - $resto;
        }
        if ($dg != $num[9]) {
            return false;
        }
        /*
        Calcula e compara o
        segundo dígito verificador.
        */
        $j = 11;
        for ($i = 0; $i < 10; $i++) {
            $multiplica[$i] = $num[$i] * $j;
            $j--;
        }
        $soma = array_sum($multiplica);
        $resto = $soma % 11;
        if ($resto < 2) {
            $dg = 0;
        } else {
            $dg = 11 - $resto;
        }
        if ($dg != $num[10]) {
            return false;
        } else {
            return true;
        }
    }

    public function validate_cnpj($attribute, $value, $parameters)
    {
        /*
        Etapa 1: Cria um array com apenas os digitos numéricos,
        isso permite receber o cnpj em diferentes
        formatos como "00.000.000/0000-00", "00000000000000", "00 000 000 0000 00"
        etc...
        */
        $cnpj = preg_replace('/\D/', '', $value);
        $num = array();

        /* Cria um array com os valores */
        for ($i = 0; $i < (strlen($cnpj)); $i++) {

            $num[] = $cnpj[$i];
        }
        //Etapa 2: Conta os dígitos, um Cnpj válido possui 14 dígitos numéricos.
        if (count($num) != 14) {
            return false;
        }
        /*
         Etapa 3: O número 00000000000 embora não seja um cnpj real resultaria
         um cnpj válido após o calculo dos dígitos verificares
         e por isso precisa ser filtradas nesta etapa.
         */
        if (
            $num[0] == 0 && $num[1] == 0 && $num[2] == 0
            && $num[3] == 0 && $num[4] == 0 && $num[5] == 0
            && $num[6] == 0 && $num[7] == 0 && $num[8] == 0
            && $num[9] == 0 && $num[10] == 0 && $num[11] == 0
        ) {
            return false;
        }
        //Etapa 4: Calcula e compara o primeiro dígito verificador.
        else {
            $j = 5;
            for ($i = 0; $i < 4; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $j = 9;
            for ($i = 4; $i < 12; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $resto = $soma % 11;
            if ($resto < 2) {
                $dg = 0;
            } else {
                $dg = 11 - $resto;
            }
            if ($dg != $num[12]) {
                return false;
            }
        }
        //Etapa 5: Calcula e compara o segundo dígito verificador.
        $j = 6;
        for ($i = 0; $i < 5; $i++) {
            $multiplica[$i] = $num[$i] * $j;
            $j--;
        }
        $soma = array_sum($multiplica);
        $j = 9;
        for ($i = 5; $i < 13; $i++) {
            $multiplica[$i] = $num[$i] * $j;
            $j--;
        }
        $soma = array_sum($multiplica);
        $resto = $soma % 11;
        if ($resto < 2) {
            $dg = 0;
        } else {
            $dg = 11 - $resto;
        }
        if ($dg != $num[13]) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the proper error message for an attribute and rule.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return string
     */
    protected function message($attribute, $rule)
    {
        // First we'll check for developer specified, attribute specific messages.
        // These messages take first priority. They allow the fine-grained tuning
        // of error messages for each rule.
        $custom = $attribute . '_' . $rule;

        if (array_key_exists($custom, $this->messages)) {
            return $this->messages[$custom];
        } elseif ($line = array_get($this->_lang, $custom, false)) {
            return $line;
        }

        // Next we'll check for developer specified, rule specific error messages.
        // These allow the developer to override the error message for an entire
        // rule, regardless of the attribute being validated by that rule.
        elseif (array_key_exists($rule, $this->messages)) {
            return $this->messages[$rule];
        }

        // If the rule being validated is a "size" rule, we will need to gather
        // the specific size message for the type of attribute being validated,
        // either a number, file, or string.
        elseif (in_array($rule, $this->size_rules)) {
            return $this->size_message($attribute, $rule);
        }

        // If no developer specified messages have been set, and no other special
        // messages apply to the rule, we will just pull the default validation
        // message from the validation language file.
        else {
            $line = array_get($this->_lang, $rule, '');

            return $line;
        }
    }

    /**
     * Get the proper error message for an attribute and size rule.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return string
     */
    protected function size_message($attribute, $rule)
    {
        // There are three different types of size validations. The attribute
        // may be either a number, file, or a string, so we'll check a few
        // things to figure out which one it is.
        if ($this->has_rule($attribute, $this->numeric_rules)) {
            $line = 'numeric';
        }
        // We assume that attributes present in the $_FILES array are files,
        // which makes sense. If the attribute doesn't have numeric rules
        // and isn't a file, it's a string.
        elseif (array_get($_FILES, $attribute, null)) {
            $line = 'file';
        } else {
            $line = 'string';
        }

        return array_get($this->_lang, "{$rule}.{$line}", '');
    }

    /**
     * Replace all error message place-holders with actual values.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace($message, $attribute, $rule, $parameters)
    {
        $message = str_replace(':attribute', 'campo "' . $this->attribute($attribute) . '"', $message);

        if (method_exists($this, $replacer = 'replace_' . $rule)) {
            $message = $this->$replacer($message, $attribute, $rule, $parameters);
        }

        return $message;
    }

    /**
     * Replace all place-holders for the required_with rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_required_with($message, $attribute, $rule, $parameters)
    {
        return str_replace(':field', $this->attribute($parameters[0]), $message);
    }

    /**
     * Replace all place-holders for the between rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_between($message, $attribute, $rule, $parameters)
    {
        return str_replace(array(':min', ':max'), $parameters, $message);
    }

    /**
     * Replace all place-holders for the size rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_size($message, $attribute, $rule, $parameters)
    {
        return str_replace(':size', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the min rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_min($message, $attribute, $rule, $parameters)
    {
        return str_replace(':min', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the max rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_max($message, $attribute, $rule, $parameters)
    {
        return str_replace(':max', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the in rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_in($message, $attribute, $rule, $parameters)
    {
        return str_replace(':values', implode(', ', $parameters), $message);
    }

    /**
     * Replace all place-holders for the not_in rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_not_in($message, $attribute, $rule, $parameters)
    {
        return str_replace(':values', implode(', ', $parameters), $message);
    }

    /**
     * Replace all place-holders for the mimes rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_mimes($message, $attribute, $rule, $parameters)
    {
        return str_replace(':values', implode(', ', $parameters), $message);
    }

    /**
     * Replace all place-holders for the same rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_same($message, $attribute, $rule, $parameters)
    {
        return str_replace(':other', $this->attribute($parameters[0]), $message);
    }

    /**
     * Replace all place-holders for the different rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_different($message, $attribute, $rule, $parameters)
    {
        return str_replace(':other', $this->attribute($parameters[0]), $message);
    }

    /**
     * Replace all place-holders for the before rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_before($message, $attribute, $rule, $parameters)
    {
        return str_replace(':date', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the after rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_after($message, $attribute, $rule, $parameters)
    {
        return str_replace(':date', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the count rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_count($message, $attribute, $rule, $parameters)
    {
        return str_replace(':count', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the countmin rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_countmin($message, $attribute, $rule, $parameters)
    {
        return str_replace(':min', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the countmax rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_countmax($message, $attribute, $rule, $parameters)
    {
        return str_replace(':max', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the between rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replace_countbetween($message, $attribute, $rule, $parameters)
    {
        return str_replace(array(':min', ':max'), $parameters, $message);
    }

    /**
     * Get the displayable name for a given attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function attribute($attribute)
    {
        // More reader friendly versions of the attribute names may be stored
        // in the validation language file, allowing a more readable version
        // of the attribute name in the message.
        $line = "attributes.{$attribute}";

        if ($line = array_get($this->_lang, $line, false)) {
            return $line;
        }

        // If no language line has been specified for the attribute, all of
        // the underscores are removed from the attribute name and that
        // will be used as the attribute name.
        else {
            return str_replace('_', ' ', $attribute);
        }
    }

    /**
     * Determine if an attribute has a rule assigned to it.
     *
     * @param  string  $attribute
     * @param  array   $rules
     * @return bool
     */
    protected function has_rule($attribute, $rules)
    {
        foreach ($this->rules[$attribute] as $rule) {
            list($rule, $parameters) = $this->parse($rule);

            if (in_array($rule, $rules)) return true;
        }

        return false;
    }

    /**
     * Extract the rule name and parameters from a rule.
     *
     * @param  string  $rule
     * @return array
     */
    protected function parse($rule)
    {
        $parameters = array();

        // The format for specifying validation rules and parameters follows a
        // {rule}:{parameters} formatting convention. For instance, the rule
        // "max:3" specifies that the value may only be 3 characters long.
        if (($colon = strpos($rule, ':')) !== false) {
            $parameters = substr($rule, $colon + 1);
            $parameters = substr($rule, 0, $colon) === 'match' ? [$parameters] : str_getcsv($parameters);
        }

        return array(is_numeric($colon) ? substr($rule, 0, $colon) : $rule, $parameters);

        /*[$rule, $parameters] = explode(':', $rule . (strpos($rule, ':') === false ? ':' : ''));

        $parameters = $rule === 'match' ? [$parameters] : str_getcsv($parameters);

        return [$rule, $parameters];*/
    }

    /**
     * Dynamically handle calls to custom registered validators.
     */
    public function __call($method, $parameters)
    {
        // First we will slice the "validate_" prefix off of the validator since
        // custom validators aren't registered with such a prefix, then we can
        // just call the method with the given parameters.
        if (isset(self::$validators[$method = substr($method, 9)])) {
            return call_user_func_array(self::$validators[$method], $parameters);
        }

        throw new Exception("Method [$method] does not exist.");
    }
}
