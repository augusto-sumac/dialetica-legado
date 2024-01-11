<?php

abstract class Model
{

    /**
     * All of the model's attributes.
     *
     * @var array
     */
    public $attributes = array();

    /**
     * The model's attributes in their original state.
     *
     * @var array
     */
    public $original = array();

    /**
     * The relationships that have been loaded for the query.
     *
     * @var array
     */
    public $relationships = array();

    /**
     * Indicates if the model exists in the database.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * The relationships that should be eagerly loaded.
     *
     * @var array
     */
    public $includes = array();

    /**
     * The primary key for the model on the database table.
     *
     * @var string
     */
    public static $key = 'id';

    /**
     * The attributes that are accessible for mass assignment.
     *
     * @var array
     */
    public static $accessible;

    /**
     * The attributes that should be excluded from to_array.
     *
     * @var array
     */
    public static $hidden = array();

    /**
     * Indicates if the model has update and creation timestamps.
     *
     * @var bool
     */
    public static $timestamps = true;

    /**
     * The name of the table associated with the model.
     *
     * @var string
     */
    public static $table;

    /**
     * The name of the database connection that should be used for the model.
     *
     * @var string
     */
    public static $connection;

    /**
     * The name of the sequence associated with the model.
     *
     * @var string
     */
    public static $sequence;

    /**
     * The default number of models to show per page when paginating.
     *
     * @var int
     */
    public static $per_page = 20;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @param  bool   $exists
     * @return void
     */
    public function __construct($attributes = array(), $exists = false)
    {
        $this->exists = $exists;

        $this->fill($attributes);
    }

    /**
     * Hydrate the model with an array of attributes.
     *
     * @param  array  $attributes
     * @param  bool   $raw
     * @return Model
     */
    public function fill(array $attributes, $raw = false)
    {
        foreach ($attributes as $key => $value) {
            // If the "raw" flag is set, it means that we'll just load every value from
            // the array directly into the attributes, without any accessibility or
            // mutators being accounted for. What you pass in is what you get.
            if ($raw) {
                $this->set_attribute($key, $value);

                continue;
            }

            // If the "accessible" property is an array, the developer is limiting the
            // attributes that may be mass assigned, and we need to verify that the
            // current attribute is included in that list of allowed attributes.
            if (is_array(static::$accessible)) {
                if (in_array($key, static::$accessible)) {
                    $this->$key = $value;
                }
            }

            // If the "accessible" property is not an array, no attributes have been
            // white-listed and we are free to set the value of the attribute to
            // the value that has been passed into the method without a check.
            else {
                $this->$key = $value;
            }
        }

        // If the original attribute values have not been set, we will set
        // them to the values passed to this method allowing us to easily
        // check if the model has changed since hydration.
        if (count($this->original) === 0) {
            $this->original = $this->attributes;
        }

        return $this;
    }

    /**
     * Fill the model with the contents of the array.
     *
     * No mutators or accessibility checks will be accounted for.
     *
     * @param  array  $attributes
     * @return Model
     */
    public function fill_raw(array $attributes)
    {
        return $this->fill($attributes, true);
    }

    /**
     * Set the accessible attributes for the given model.
     *
     * @param  array  $attributes
     * @return void
     */
    public static function accessible($attributes = null)
    {
        if (is_null($attributes)) return static::$accessible;

        static::$accessible = $attributes;
    }

    /**
     * Create a new model and store it in the database.
     *
     * If save is successful, the model will be returned, otherwise false.
     *
     * @param  array        $attributes
     * @return Model|false
     */
    public static function create($attributes)
    {
        $model = new static($attributes);

        $success = $model->save();

        return ($success) ? $model : false;
    }

    /**
     * Update a model instance in the database.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     * @return int
     */
    public static function update($id, $attributes)
    {
        $model = new static(array(), true);

        $model->fill($attributes);

        if (static::$timestamps) $model->timestamp();

        return $model->query()->where($model->key(), '=', $id)->update($model->attributes);
    }

    /**
     * Get all of the models in the database.
     *
     * @return array
     */
    public static function all()
    {
        return with(new static)->query()->get();
    }

    /**
     * The relationships that should be eagerly loaded by the query.
     *
     * @param  array  $includes
     * @return Model
     */
    public function _with($includes)
    {
        $this->includes = (array) $includes;

        return $this;
    }

    /**
     * Get the query for a one-to-one association.
     *
     * @param  string        $model
     * @param  string        $foreign
     * @return Relationship
     */
    public function has_one($model, $foreign = null)
    {
        return $this->has_one_or_many(__FUNCTION__, $model, $foreign);
    }

    /**
     * Get the query for a one-to-many association.
     *
     * @param  string        $model
     * @param  string        $foreign
     * @return Relationship
     */
    public function has_many($model, $foreign = null)
    {
        return $this->has_one_or_many(__FUNCTION__, $model, $foreign);
    }

    /**
     * Get the query for a one-to-one / many association.
     *
     * @param  string        $type
     * @param  string        $model
     * @param  string        $foreign
     * @return Relationship
     */
    protected function has_one_or_many($type, $model, $foreign)
    {
        if ($type == 'has_one') {
            return new Relationship_Has_One($this, $model, $foreign);
        } else {
            return new Relationship_Has_Many($this, $model, $foreign);
        }
    }

    /**
     * Get the query for a one-to-one (inverse) relationship.
     *
     * @param  string        $model
     * @param  string        $foreign
     * @return Relationship
     */
    public function belongs_to($model, $foreign = null)
    {
        // If no foreign key is specified for the relationship, we will assume that the
        // name of the calling function matches the foreign key. For example, if the
        // calling function is "manager", we'll assume the key is "manager_id".
        if (is_null($foreign)) {
            list(, $caller) = debug_backtrace(false);

            $foreign = "{$caller['function']}_id";
        }

        return new Relationship_Belongs_To($this, $model, $foreign);
    }

    /**
     * Get the query for a many-to-many relationship.
     *
     * @param  string        $model
     * @param  string        $table
     * @param  string        $foreign
     * @param  string        $other
     * @return Has_Many_And_Belongs_To
     */
    public function has_many_and_belongs_to($model, $table = null, $foreign = null, $other = null)
    {
        return new Has_Many_And_Belongs_To($this, $model, $table, $foreign, $other);
    }

    /**
     * Save the model and all of its relations to the database.
     *
     * @return bool
     */
    public function push()
    {
        $this->save();

        // To sync all of the relationships to the database, we will simply spin through
        // the relationships, calling the "push" method on each of the models in that
        // given relationship, this should ensure that each model is saved.
        foreach ($this->relationships as $name => $models) {
            if (!is_array($models)) {
                $models = array($models);
            }

            foreach ($models as $model) {
                $model->push();
            }
        }
    }

    /**
     * Save the model instance to the database.
     *
     * @return bool
     */
    public function save()
    {
        if (!$this->dirty()) return true;

        if (static::$timestamps) {
            $this->timestamp();
        }

        $this->fire_event('saving');

        // If the model exists, we only need to update it in the database, and the update
        // will be considered successful if there is one affected row returned from the
        // fluent query instance. We'll set the where condition automatically.
        if ($this->exists) {
            $query = $this->query()->where(static::$key, '=', $this->get_key());

            $result = $query->update($this->get_dirty()) === 1;

            if ($result) $this->fire_event('updated');
        }

        // If the model does not exist, we will insert the record and retrieve the last
        // insert ID that is associated with the model. If the ID returned is numeric
        // then we can consider the insert successful.
        else {
            $id = $this->query()->insert_get_id($this->attributes, $this->key());

            $this->set_key($id);

            $this->exists = $result = is_numeric($this->get_key());

            if ($result) $this->fire_event('created');
        }

        // After the model has been "saved", we will set the original attributes to
        // match the current attributes so the model will not be viewed as being
        // dirty and subsequent calls won't hit the database.
        $this->original = $this->attributes;

        if ($result) {
            $this->fire_event('saved');
        }

        return $result;
    }

    /**
     * Delete the model from the database.
     *
     * @return int
     */
    public function delete()
    {
        if ($this->exists) {
            $this->fire_event('deleting');

            $result = $this->query()->where(static::$key, '=', $this->get_key())->delete();

            $this->fire_event('deleted');

            return $result;
        }
    }

    /**
     * Set the update and creation timestamps on the model.
     *
     * @return void
     */
    public function timestamp()
    {
        $this->updated_at = new \DateTime;

        if (!$this->exists) $this->created_at = $this->updated_at;
    }

    /**
     *Updates the timestamp on the model and immediately saves it.
     *
     * @return void
     */
    public function touch()
    {
        $this->timestamp();
        $this->save();
    }

    /**
     * Get a new fluent query builder instance for the model.
     *
     * @return Model_Query
     */
    protected function _query()
    {
        return new Model_Query($this);
    }

    /**
     * Sync the original attributes with the current attributes.
     *
     * @return bool
     */
    final public function sync()
    {
        $this->original = $this->attributes;

        return true;
    }

    /**
     * Determine if a given attribute has changed from its original state.
     *
     * @param  string  $attribute
     * @return bool
     */
    public function changed($attribute)
    {
        return array_get($this->attributes, $attribute) != array_get($this->original, $attribute);
    }

    /**
     * Determine if the model has been changed from its original state.
     *
     * Models that haven't been persisted to storage are always considered dirty.
     *
     * @return bool
     */
    public function dirty()
    {
        return !$this->exists or count($this->get_dirty()) > 0;
    }

    /**
     * Get the name of the table associated with the model.
     *
     * @return string
     */
    public function table()
    {
        return static::$table;
    }

    /**
     * Get the dirty attributes for the model.
     *
     * @return array
     */
    public function get_dirty()
    {
        $dirty = array();

        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original) or $value != $this->original[$key]) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Get the value of the primary key for the model.
     *
     * @return int
     */
    public function get_key()
    {
        return array_get($this->attributes, static::$key);
    }

    /**
     * Set the value of the primary key for the model.
     *
     * @param  int   $value
     * @return void
     */
    public function set_key($value)
    {
        return $this->set_attribute(static::$key, $value);
    }

    /**
     * Get a given attribute from the model.
     *
     * @param  string  $key
     */
    public function get_attribute($key)
    {
        return array_get($this->attributes, $key);
    }

    /**
     * Set an attribute's value on the model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function set_attribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Remove an attribute from the model.
     *
     * @param  string  $key
     */
    final public function purge($key)
    {
        unset($this->original[$key]);

        unset($this->attributes[$key]);
    }

    /**
     * Get the model attributes and relationships in array form.
     *
     * @return array
     */
    public function to_array()
    {
        $attributes = array();

        // First we need to gather all of the regular attributes. If the attribute
        // exists in the array of "hidden" attributes, it will not be added to
        // the array so we can easily exclude things like passwords, etc.
        foreach (array_keys($this->attributes) as $attribute) {
            if (!in_array($attribute, static::$hidden)) {
                $attributes[$attribute] = $this->$attribute;
            }
        }

        foreach ($this->relationships as $name => $models) {
            // Relationships can be marked as "hidden", too.
            if (in_array($name, static::$hidden)) continue;

            // If the relationship is not a "to-many" relationship, we can just
            // to_array the related model and add it as an attribute to the
            // array of existing regular attributes we gathered.
            if ($models instanceof Model) {
                $attributes[$name] = $models->to_array();
            }

            // If the relationship is a "to-many" relationship we need to spin
            // through each of the related models and add each one with the
            // to_array method, keying them both by name and ID.
            elseif (is_array($models)) {
                $attributes[$name] = array();

                foreach ($models as $id => $model) {
                    $attributes[$name][$id] = $model->to_array();
                }
            } elseif (is_null($models)) {
                $attributes[$name] = $models;
            }
        }

        return $attributes;
    }

    /**
     * Fire a given event for the model.
     *
     * @param  string  $event
     * @return array
     */
    protected function fire_event($event)
    {
        $events = array("eloquent.{$event}", "eloquent.{$event}: " . get_class($this));

        Event::fire($events, array($this));
    }

    /**
     * Handle the dynamic retrieval of attributes and associations.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        // First we will check to see if the requested key is an already loaded
        // relationship and return it if it is. All relationships are stored
        // in the special relationships array so they are not persisted.
        if (array_key_exists($key, $this->relationships)) {
            return $this->relationships[$key];
        }

        // Next we'll check if the requested key is in the array of attributes
        // for the model. These are simply regular properties that typically
        // correspond to a single column on the database for the model.
        elseif (array_key_exists($key, $this->attributes)) {
            return $this->{"get_{$key}"}();
        }

        // If the item is not a loaded relationship, it may be a relationship
        // that hasn't been loaded yet. If it is, we will lazy load it and
        // set the value of the relationship in the relationship array.
        elseif (method_exists($this, $key)) {
            return $this->relationships[$key] = $this->$key()->results();
        }

        // Finally we will just assume the requested key is just a regular
        // attribute and attempt to call the getter method for it, which
        // will fall into the __call method if one doesn't exist.
        else {
            return $this->{"get_{$key}"}();
        }
    }

    /**
     * Handle the dynamic setting of attributes.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->{"set_{$key}"}($value);
    }

    /**
     * Determine if an attribute exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        foreach (array('attributes', 'relationships') as $source) {
            if (array_key_exists($key, $this->{$source})) return !empty($this->{$source}[$key]);
        }

        return false;
    }

    /**
     * Remove an attribute from the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        foreach (array('attributes', 'relationships') as $source) {
            unset($this->{$source}[$key]);
        }
    }

    /**
     * Handle dynamic method calls on the model.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $meta = array('key', 'table', 'connection', 'sequence', 'per_page', 'timestamps');

        // If the method is actually the name of a static property on the model we'll
        // return the value of the static property. This makes it convenient for
        // relationships to access these values off of the instances.
        if (in_array($method, $meta)) {
            return static::$$method;
        }

        $underscored = array('with', 'query');

        // Some methods need to be accessed both staticly and non-staticly so we'll
        // keep underscored methods of those methods and intercept calls to them
        // here so they can be called either way on the model instance.
        if (in_array($method, $underscored)) {
            return call_user_func_array(array($this, '_' . $method), $parameters);
        }

        // First we want to see if the method is a getter / setter for an attribute.
        // If it is, we'll call the basic getter and setter method for the model
        // to perform the appropriate action based on the method.
        if (starts_with($method, 'get_')) {
            return $this->get_attribute(substr($method, 4));
        } elseif (starts_with($method, 'set_')) {
            $this->set_attribute(substr($method, 4), $parameters[0]);
        }

        // Finally we will assume that the method is actually the beginning of a
        // query, such as "where", and will create a new query instance and
        // call the method on the query instance, returning it after.
        else {
            return call_user_func_array(array($this->query(), $method), $parameters);
        }
    }

    /**
     * Dynamically handle static method calls on the model.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $model = get_called_class();

        return call_user_func_array(array(new $model, $method), $parameters);
    }
}

class Pivot extends Model
{

    /**
     * The name of the pivot table's table.
     *
     * @var string
     */
    protected $pivot_table;

    /**
     * The database connection used for this model.
     *
     * @var Connection
     */
    protected $pivot_connection;

    /**
     * Indicates if the model has update and creation timestamps.
     *
     * @var bool
     */
    public static $timestamps = true;

    /**
     * Create a new pivot table instance.
     *
     * @param  string  $table
     * @param  string  $connection
     * @return void
     */
    public function __construct($table, $connection = null)
    {
        $this->pivot_table = $table;
        $this->pivot_connection = $connection;

        parent::__construct(array(), true);
    }

    /**
     * Get the name of the pivot table.
     *
     * @return string
     */
    public function table()
    {
        return $this->pivot_table;
    }

    /**
     * Get the connection used by the pivot table.
     *
     * @return string
     */
    public function connection()
    {
        return $this->pivot_connection;
    }
}

class Model_Query
{

    /**
     * The model instance being queried.
     *
     * @var Model
     */
    public $model;

    /**
     * The fluent query builder for the query instance.
     *
     * @var Query
     */
    public $table;

    /**
     * The relationships that should be eagerly loaded by the query.
     *
     * @var array
     */
    public $includes = array();

    /**
     * The methods that should be returned from the fluent query builder.
     *
     * @var array
     */
    public $passthru = array(
        'lists', 'only', 'insert', 'insert_get_id', 'update', 'increment',
        'delete', 'decrement', 'count', 'min', 'max', 'avg', 'sum',
    );

    /**
     * Creat a new query instance for a model.
     *
     * @param  Model  $model
     * @return void
     */
    public function __construct($model)
    {
        $this->model = ($model instanceof Model) ? $model : new $model;

        $this->table = $this->table();
    }

    /**
     * Find a model by its primary key.
     * 
     * @param  mixed  $id
     * @param  array  $columns
     * @return mixed
     */
    public function find($id, $columns = array('*'))
    {
        $model = $this->model;

        $this->table->where($model::$key, '=', $id);

        return $this->first($columns);
    }

    /**
     * Get the first model result for the query.
     *
     * @param  array  $columns
     * @return mixed
     */
    public function first($columns = array('*'))
    {
        $results = $this->hydrate($this->model, $this->table->take(1)->get($columns));

        return (count($results) > 0) ? head($results) : null;
    }

    /**
     * Get all of the model results for the query.
     *
     * @param  array  $columns
     * @return array
     */
    public function get($columns = array('*'))
    {
        return $this->hydrate($this->model, $this->table->get($columns));
    }

    /**
     * Get an array of paginated model results.
     *
     * @param  int        $per_page
     * @param  array      $columns
     * @return Paginator
     */
    public function paginate($per_page = null, $columns = array('*'))
    {
        $per_page = $per_page ?: $this->model->per_page();

        // First we'll grab the Paginator instance and get the results. Then we can
        // feed those raw database results into the hydrate method to get models
        // for the results, which we'll set on the paginator and return it.
        $paginator = $this->table->paginate($per_page, $columns);

        $paginator->results = $this->hydrate($this->model, $paginator->results);

        return $paginator;
    }

    /**
     * Hydrate an array of models from the given results.
     *
     * @param  Model  $model
     * @param  array  $results
     * @return array
     */
    public function hydrate($model, $results)
    {
        $class = get_class($model);

        $models = array();

        // We'll spin through the array of database results and hydrate a model
        // for each one of the records. We will also set the "exists" flag to
        // "true" so that the model will be updated when it is saved.
        foreach ((array) $results as $result) {
            $result = (array) $result;

            $new = new $class(array(), true);

            // We need to set the attributes manually in case the accessible property is
            // set on the array which will prevent the mass assignemnt of attributes if
            // we were to pass them in using the constructor or fill methods.
            $new->fill_raw($result);

            $models[] = $new;
        }

        if (count($results) > 0) {
            foreach ($this->model_includes() as $relationship => $constraints) {
                // If the relationship is nested, we will skip loading it here and let
                // the load method parse and set the nested eager loads on the right
                // relationship when it is getting ready to eager load.
                if (str_contains($relationship, '.')) {
                    continue;
                }

                $this->load($models, $relationship, $constraints);
            }
        }

        // The many to many relationships may have pivot table column on them
        // so we will call the "clean" method on the relationship to remove
        // any pivot columns that are on the model.
        if ($this instanceof Relationship_Has_Many_And_Belongs_To) {
            $this->hydrate_pivot($models);
        }

        return $models;
    }

    /**
     * Hydrate an eagerly loaded relationship on the model results.
     *
     * @param  array       $results
     * @param  string      $relationship
     * @param  array|null  $constraints
     * @return void
     */
    protected function load(&$results, $relationship, $constraints)
    {
        $query = $this->model->$relationship();

        $query->model->includes = $this->nested_includes($relationship);

        // We'll remove any of the where clauses from the relationship to give
        // the relationship the opportunity to set the constraints for an
        // eager relationship using a separate, specific method.
        $query->table->reset_where();

        $query->eagerly_constrain($results);

        // Constraints may be specified in-line for the eager load by passing
        // a Closure as the value portion of the eager load. We can use the
        // query builder's nested query support to add the constraints.
        if (!is_null($constraints)) {
            $query->table->where_nested($constraints);
        }

        $query->initialize($results, $relationship);

        $query->match($relationship, $results, $query->get());
    }

    /**
     * Gather the nested includes for a given relationship.
     *
     * @param  string  $relationship
     * @return array
     */
    protected function nested_includes($relationship)
    {
        $nested = array();

        foreach ($this->model_includes() as $include => $constraints) {
            // To get the nested includes, we want to find any includes that begin
            // the relationship and a dot, then we will strip off the leading
            // nesting indicator and set the include in the array.
            if (starts_with($include, $relationship . '.')) {
                $nested[substr($include, strlen($relationship . '.'))] = $constraints;
            }
        }

        return $nested;
    }

    /**
     * Get the eagerly loaded relationships for the model.
     *
     * @return array
     */
    protected function model_includes()
    {
        $includes = array();

        foreach ($this->model->includes as $relationship => $constraints) {
            // When eager loading relationships, constraints may be set on the eager
            // load definition; however, is none are set, we need to swap the key
            // and the value of the array since there are no constraints.
            if (is_numeric($relationship)) {
                list($relationship, $constraints) = array($constraints, null);
            }

            $includes[$relationship] = $constraints;
        }

        return $includes;
    }

    /**
     * Get a fluent query builder for the model.
     *
     * @return Query
     */
    protected function table()
    {
        return $this->connection()->table($this->model->table());
    }

    /**
     * Get the database connection for the model.
     *
     * @return Connection
     */
    public function connection()
    {
        return Database::connection($this->model->connection());
    }

    /**
     * Handle dynamic method calls to the query.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $result = call_user_func_array(array($this->table, $method), $parameters);

        // Some methods may get their results straight from the fluent query
        // builder such as the aggregate methods. If the called method is
        // one of these, we will just return the result straight away.
        if (in_array($method, $this->passthru)) {
            return $result;
        }

        return $this;
    }
}

abstract class Relationship extends Model_Query
{

    /**
     * The base model for the relationship.
     *
     * @var Model
     */
    protected $base;

    protected $foreign;

    /**
     * Create a new has one or many association instance.
     *
     * @param  Model   $model
     * @param  string  $associated
     * @param  string  $foreign
     * @return void
     */
    public function __construct($model, $associated, $foreign)
    {
        $this->foreign = $foreign;

        // We will go ahead and set the model and associated instances on the
        // relationship to match the relationship targets passed in from the
        // model. These will allow us to gather the relationship info.
        if ($associated instanceof Model) {
            $this->model = $associated;
        } else {
            $this->model = new $associated;
        }

        // For relationships, we'll set the base model to be the model being
        // associated from. This model contains the value of the foreign
        // key needed to connect to the associated model.
        if ($model instanceof Model) {
            $this->base = $model;
        } else {
            $this->base = new $model;
        }

        // Next we'll set the fluent query builder for the relationship and
        // constrain the query such that it only returns the models that
        // are appropriate for the relationship.
        $this->table = $this->table();

        $this->constrain();
    }

    /**
     * Get the foreign key name for the given model.
     *
     * @param  string  $model
     * @param  string  $foreign
     * @return string
     */
    public static function foreign($model, $foreign = null)
    {
        if (!is_null($foreign)) return $foreign;

        // If the model is an object we'll simply get the class of the object and
        // then take the basename, which is simply the object name minus the
        // namespace, and we'll append "_id" to the name.
        if (is_object($model)) {
            $model = class_basename($model);
        }

        return strtolower(basename($model) . '_id');
    }

    /**
     * Get a freshly instantiated instance of the related model class.
     *
     * @param  array  $attributes
     * @return Model
     */
    protected function fresh_model($attributes = array())
    {
        $class = get_class($this->model);

        return new $class($attributes);
    }

    /**
     * Get the foreign key for the relationship.
     *
     * @return string
     */
    public function foreign_key()
    {
        return static::foreign($this->base, $this->foreign);
    }

    /**
     * Gather all the primary keys from a result set.
     *
     * @param  array  $results
     * @return array
     */
    public function keys($results)
    {
        $keys = array();

        foreach ($results as $result) {
            $keys[] = $result->get_key();
        }

        return array_unique($keys);
    }

    /**
     * The relationships that should be eagerly loaded by the query.
     *
     * @param  array  $includes
     * @return Relationship
     */
    public function with($includes)
    {
        $this->model->includes = (array) $includes;

        return $this;
    }
}

class Relationship_Has_One_Or_Many extends Relationship
{

    /**
     * Insert a new record for the association.
     *
     * If save is successful, the model will be returned, otherwise false.
     *
     * @param  Model|array  $attributes
     * @return Model|false
     */
    public function insert($attributes)
    {
        if ($attributes instanceof Model) {
            $attributes->set_attribute($this->foreign_key(), $this->base->get_key());

            return $attributes->save() ? $attributes : false;
        } else {
            $attributes[$this->foreign_key()] = $this->base->get_key();

            return $this->model->create($attributes);
        }
    }

    /**
     * Update a record for the association.
     *
     * @param  array  $attributes
     * @return bool
     */
    public function update(array $attributes)
    {
        if ($this->model->timestamps()) {
            $attributes['updated_at'] = new \DateTime;
        }

        return $this->table->update($attributes);
    }

    /**
     * Set the proper constraints on the relationship table.
     *
     * @return void
     */
    protected function constrain()
    {
        $this->table->where($this->foreign_key(), '=', $this->base->get_key());
    }

    /**
     * Set the proper constraints on the relationship table for an eager load.
     *
     * @param  array  $results
     * @return void
     */
    public function eagerly_constrain($results)
    {
        $this->table->where_in($this->foreign_key(), $this->keys($results));
    }
}

class Relationship_Has_One extends Relationship_Has_One_Or_Many
{

    /**
     * Get the properly hydrated results for the relationship.
     *
     * @return Model
     */
    public function results()
    {
        return parent::first();
    }

    /**
     * Initialize a relationship on an array of parent models.
     *
     * @param  array   $parents
     * @param  string  $relationship
     * @return void
     */
    public function initialize(&$parents, $relationship)
    {
        foreach ($parents as &$parent) {
            $parent->relationships[$relationship] = null;
        }
    }

    /**
     * Match eagerly loaded child models to their parent models.
     *
     * @param  array  $parents
     * @param  array  $children
     * @return void
     */
    public function match($relationship, &$parents, $children)
    {
        $foreign = $this->foreign_key();

        $dictionary = array();

        foreach ($children as $child) {
            $dictionary[$child->$foreign] = $child;
        }

        foreach ($parents as $parent) {
            if (array_key_exists($key = $parent->get_key(), $dictionary)) {
                $parent->relationships[$relationship] = $dictionary[$key];
            }
        }
    }
}

class Relationship_Has_Many_And_Belongs_To extends Relationship
{

    /**
     * The name of the intermediate, joining table.
     *
     * @var string
     */
    protected $joining;

    /**
     * The other or "associated" key. This is the foreign key of the related model.
     *
     * @var string
     */
    protected $other;

    /**
     * The columns on the joining table that should be fetched.
     *
     * @var array
     */
    protected $with = array('id');

    /**
     * Create a new many to many relationship instance.
     *
     * @param  Model   $model
     * @param  string  $associated
     * @param  string  $table
     * @param  string  $foreign
     * @param  string  $other
     * @return void
     */
    public function __construct($model, $associated, $table, $foreign, $other)
    {
        $this->other = $other;

        $this->joining = $table ?: $this->joining($model, $associated);

        // If the Pivot table is timestamped, we'll set the timestamp columns to be
        // fetched when the pivot table models are fetched by the developer else
        // the ID will be the only "extra" column fetched in by default.
        if (Pivot::$timestamps) {
            $this->with[] = 'created_at';

            $this->with[] = 'updated_at';
        }

        parent::__construct($model, $associated, $foreign);
    }

    /**
     * Determine the joining table name for the relationship.
     *
     * By default, the name is the models sorted and joined with underscores.
     *
     * @return string
     */
    protected function joining($model, $associated)
    {
        $models = array(class_basename($model), class_basename($associated));

        sort($models);

        return strtolower($models[0] . '_' . $models[1]);
    }

    /**
     * Get the properly hydrated results for the relationship.
     *
     * @return array
     */
    public function results()
    {
        return parent::get();
    }

    /**
     * Insert a new record into the joining table of the association.
     *
     * @param  Model|int    $id
     * @param  array  $attributes
     * @return bool
     */
    public function attach($id, $attributes = array())
    {
        if ($id instanceof Model) $id = $id->get_key();

        $joining = array_merge($this->join_record($id), $attributes);

        return $this->insert_joining($joining);
    }

    /**
     * Detach a record from the joining table of the association.
     *
     * @param  array|Model|int   $ids
     * @return bool
     */
    public function detach($ids)
    {
        if ($ids instanceof Model) $ids = array($ids->get_key());
        elseif (!is_array($ids)) $ids = array($ids);

        return $this->pivot()->where_in($this->other_key(), $ids)->delete();
    }

    /**
     * Sync the joining table with the array of given IDs.
     *
     * @param  array  $ids
     * @return bool
     */
    public function sync($ids)
    {
        $current = $this->pivot()->lists($this->other_key());
        $ids = (array) $ids;

        // First we need to attach any of the associated models that are not currently
        // in the joining table. We'll spin through the given IDs, checking to see
        // if they exist in the array of current ones, and if not we insert.
        foreach ($ids as $id) {
            if (!in_array($id, $current)) {
                $this->attach($id);
            }
        }

        // Next we will take the difference of the current and given IDs and detach
        // all of the entities that exists in the current array but are not in
        // the array of IDs given to the method, finishing the sync.
        $detach = array_diff($current, $ids);

        if (count($detach) > 0) {
            $this->detach($detach);
        }
    }

    /**
     * Insert a new record for the association.
     *
     * @param  Model|array  $attributes
     * @param  array        $joining
     * @return bool
     */
    public function insert($attributes, $joining = array())
    {
        // If the attributes are actually an instance of a model, we'll just grab the
        // array of attributes off of the model for saving, allowing the developer
        // to easily validate the joining models before inserting them.
        if ($attributes instanceof Model) {
            $attributes = $attributes->attributes;
        }

        $model = $this->model->create($attributes);

        // If the insert was successful, we'll insert a record into the joining table
        // using the new ID that was just inserted into the related table, allowing
        // the developer to not worry about maintaining the join table.
        if ($model instanceof Model) {
            $joining = array_merge($this->join_record($model->get_key()), $joining);

            $result = $this->insert_joining($joining);
        }

        return $model instanceof Model and $result;
    }

    /**
     * Delete all of the records from the joining table for the model.
     *
     * @return int
     */
    public function delete()
    {
        return $this->pivot()->delete();
    }

    /**
     * Create an array representing a new joining record for the association.
     *
     * @param  int    $id
     * @return array
     */
    protected function join_record($id)
    {
        return array($this->foreign_key() => $this->base->get_key(), $this->other_key() => $id);
    }

    /**
     * Insert a new record into the joining table of the association.
     *
     * @param  array  $attributes
     * @return void
     */
    protected function insert_joining($attributes)
    {
        if (Pivot::$timestamps) {
            $attributes['created_at'] = new \DateTime;

            $attributes['updated_at'] = $attributes['created_at'];
        }

        return $this->joining_table()->insert($attributes);
    }

    /**
     * Get a fluent query for the joining table of the relationship.
     *
     * @return Query
     */
    protected function joining_table()
    {
        return $this->connection()->table($this->joining);
    }

    /**
     * Set the proper constraints on the relationship table.
     *
     * @return void
     */
    protected function constrain()
    {
        $other = $this->other_key();

        $foreign = $this->foreign_key();

        $this->set_select($foreign, $other)->set_join($other)->set_where($foreign);
    }

    /**
     * Set the SELECT clause on the query builder for the relationship.
     *
     * @param  string  $foreign
     * @param  string  $other
     * @return this
     */
    protected function set_select($foreign, $other)
    {
        $columns = array($this->model->table() . '.*');

        $this->with = array_merge($this->with, array($foreign, $other));

        // Since pivot tables may have extra information on them that the developer
        // needs we allow an extra array of columns to be specified that will be
        // fetched from the pivot table and hydrate into the pivot model.
        foreach ($this->with as $column) {
            $columns[] = $this->joining . '.' . $column . ' as pivot_' . $column;
        }

        $this->table->select($columns);

        return $this;
    }

    /**
     * Set the JOIN clause on the query builder for the relationship.
     *
     * @param  string  $other
     * @return void
     */
    protected function set_join($other)
    {
        $this->table->join($this->joining, $this->associated_key(), '=', $this->joining . '.' . $other);

        return $this;
    }

    /**
     * Set the WHERE clause on the query builder for the relationship.
     *
     * @param  string  $foreign
     * @return void
     */
    protected function set_where($foreign)
    {
        $this->table->where($this->joining . '.' . $foreign, '=', $this->base->get_key());

        return $this;
    }

    /**
     * Initialize a relationship on an array of parent models.
     *
     * @param  array   $parents
     * @param  string  $relationship
     * @return void
     */
    public function initialize(&$parents, $relationship)
    {
        foreach ($parents as &$parent) {
            $parent->relationships[$relationship] = array();
        }
    }

    /**
     * Set the proper constraints on the relationship table for an eager load.
     *
     * @param  array  $results
     * @return void
     */
    public function eagerly_constrain($results)
    {
        $this->table->where_in($this->joining . '.' . $this->foreign_key(), $this->keys($results));
    }

    /**
     * Match eagerly loaded child models to their parent models.
     *
     * @param  array  $parents
     * @param  array  $children
     * @return void
     */
    public function match($relationship, &$parents, $children)
    {
        $foreign = $this->foreign_key();

        $dictionary = array();

        foreach ($children as $child) {
            $dictionary[$child->pivot->$foreign][] = $child;
        }

        foreach ($parents as $parent) {
            if (array_key_exists($key = $parent->get_key(), $dictionary)) {
                $parent->relationships[$relationship] = $dictionary[$key];
            }
        }
    }

    /**
     * Hydrate the Pivot model on an array of results.
     *
     * @param  array  $results
     * @return void
     */
    protected function hydrate_pivot(&$results)
    {
        foreach ($results as &$result) {
            // Every model result for a many-to-many relationship needs a Pivot instance
            // to represent the pivot table's columns. Sometimes extra columns are on
            // the pivot table that may need to be accessed by the developer.
            $pivot = new Pivot($this->joining, $this->model->connection());

            // If the attribute key starts with "pivot_", we know this is a column on
            // the pivot table, so we will move it to the Pivot model and purge it
            // from the model since it actually belongs to the pivot model.
            foreach ($result->attributes as $key => $value) {
                if (starts_with($key, 'pivot_')) {
                    $pivot->{substr($key, 6)} = $value;

                    $result->purge($key);
                }
            }

            // Once we have completed hydrating the pivot model instance, we'll set
            // it on the result model's relationships array so the developer can
            // quickly and easily access any pivot table information.
            $result->relationships['pivot'] = $pivot;

            $pivot->sync() and $result->sync();
        }
    }

    /**
     * Set the columns on the joining table that should be fetched.
     *
     * @param  array         $column
     * @return Relationship
     */
    public function with($columns)
    {
        $columns = (is_array($columns)) ? $columns : func_get_args();

        // The "with" array contains a couple of columns by default, so we will just
        // merge in the developer specified columns here, and we will make sure
        // the values of the array are unique to avoid duplicates.
        $this->with = array_unique(array_merge($this->with, $columns));

        $this->set_select($this->foreign_key(), $this->other_key());

        return $this;
    }

    /**
     * Get a relationship instance of the pivot table.
     *
     * @return Relationship_Has_Many
     */
    public function pivot()
    {
        $pivot = new Pivot($this->joining, $this->model->connection());

        return new Relationship_Has_Many($this->base, $pivot, $this->foreign_key());
    }

    /**
     * Get the other or associated key for the relationship.
     *
     * @return string
     */
    protected function other_key()
    {
        return Relationship::foreign($this->model, $this->other);
    }

    /**
     * Get the fully qualified associated table's primary key.
     *
     * @return string
     */
    protected function associated_key()
    {
        return $this->model->table() . '.' . $this->model->key();
    }
}

class Relationship_Has_Many extends Relationship_Has_One_Or_Many
{

    /**
     * Get the properly hydrated results for the relationship.
     *
     * @return array
     */
    public function results()
    {
        return parent::get();
    }

    /**
     * Sync the association table with an array of models.
     *
     * @param  mixed  $models
     * @return bool
     */
    public function save($models)
    {
        // If the given "models" are not an array, we'll force them into an array so
        // we can conveniently loop through them and insert all of them into the
        // related database table assigned to the associated model instance.
        if (!is_array($models)) $models = array($models);

        $current = $this->table->lists($this->model->key());

        foreach ($models as $attributes) {
            $class = get_class($this->model);

            // If the "attributes" are actually an array of the related model we'll
            // just use the existing instance instead of creating a fresh model
            // instance for the attributes. This allows for validation.
            if ($attributes instanceof $class) {
                $model = $attributes;
            } else {
                $model = $this->fresh_model($attributes);
            }

            // We'll need to associate the model with its parent, so we'll set the
            // foreign key on the model to the key of the parent model, making
            // sure that the two models are associated in the database.
            $foreign = $this->foreign_key();

            $model->$foreign = $this->base->get_key();

            $id = $model->get_key();

            $model->exists = (!is_null($id) and in_array($id, $current));

            // Before saving we'll force the entire model to be "dirty" so all of
            // the attributes are saved. It shouldn't affect the updates as
            // saving all the attributes shouldn't hurt anything.
            $model->original = array();

            $model->save();
        }

        return true;
    }

    /**
     * Initialize a relationship on an array of parent models.
     *
     * @param  array   $parents
     * @param  string  $relationship
     * @return void
     */
    public function initialize(&$parents, $relationship)
    {
        foreach ($parents as &$parent) {
            $parent->relationships[$relationship] = array();
        }
    }

    /**
     * Match eagerly loaded child models to their parent models.
     *
     * @param  array  $parents
     * @param  array  $children
     * @return void
     */
    public function match($relationship, &$parents, $children)
    {
        $foreign = $this->foreign_key();

        $dictionary = array();

        foreach ($children as $child) {
            $dictionary[$child->$foreign][] = $child;
        }

        foreach ($parents as $parent) {
            if (array_key_exists($key = $parent->get_key(), $dictionary)) {
                $parent->relationships[$relationship] = $dictionary[$key];
            }
        }
    }
}

class Relationship_Belongs_To extends Relationship
{

    /**
     * Get the properly hydrated results for the relationship.
     *
     * @return Model
     */
    public function results()
    {
        return parent::first();
    }

    /**
     * Update the parent model of the relationship.
     *
     * @param  Model|array  $attributes
     * @return int
     */
    public function update($attributes)
    {
        $attributes = ($attributes instanceof Model) ? $attributes->get_dirty() : $attributes;

        return $this->model->update($this->foreign_value(), $attributes);
    }

    /**
     * Set the proper constraints on the relationship table.
     *
     * @return void
     */
    protected function constrain()
    {
        $this->table->where($this->model->key(), '=', $this->foreign_value());
    }

    /**
     * Initialize a relationship on an array of parent models.
     *
     * @param  array   $parents
     * @param  string  $relationship
     * @return void
     */
    public function initialize(&$parents, $relationship)
    {
        foreach ($parents as &$parent) {
            $parent->relationships[$relationship] = null;
        }
    }

    /**
     * Set the proper constraints on the relationship table for an eager load.
     *
     * @param  array  $results
     * @return void
     */
    public function eagerly_constrain($results)
    {
        $keys = array();

        // Inverse one-to-many relationships require us to gather the keys from the
        // parent models and use those keys when setting the constraint since we
        // are looking for the parent of a child model in this relationship.
        foreach ($results as $result) {
            if (!is_null($key = $result->{$this->foreign_key()})) {
                $keys[] = $key;
            }
        }

        if (count($keys) == 0) $keys = array(0);

        $this->table->where_in($this->model->key(), array_unique($keys));
    }

    /**
     * Match eagerly loaded child models to their parent models.
     *
     * @param  array  $children
     * @param  array  $parents
     * @return void
     */
    public function match($relationship, &$children, $parents)
    {
        $foreign = $this->foreign_key();

        $dictionary = array();

        foreach ($parents as $parent) {
            $dictionary[$parent->get_key()] = $parent;
        }

        foreach ($children as $child) {
            if (array_key_exists($child->$foreign, $dictionary)) {
                $child->relationships[$relationship] = $dictionary[$child->$foreign];
            }
        }
    }

    /**
     * Get the value of the foreign key from the base model.
     *
     * @return mixed
     */
    public function foreign_value()
    {
        return $this->base->get_attribute($this->foreign);
    }

    /**
     * Bind an object over a belongs-to relation using its id.
     *
     * @return Eloquent
     */

    public function bind($id)
    {
        $this->base->fill(array($this->foreign => $id))->save();

        return $this->base;
    }
}
