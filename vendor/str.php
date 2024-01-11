<?php

function strStrings($key = null)
{

    $strings = array(

        /*
		|--------------------------------------------------------------------------
		| String Inflection
		|--------------------------------------------------------------------------
		|
		| This array contains the singular and plural forms of words. It's used by
		| the "singular" and "plural" methods in the Str class to convert a given
		| word from singular to plural and vice versa.
		|
		| Note that the regular expressions are only for inflecting English words.
		| To inflect a non-English string, simply add its singular and plural
		| form to the array of "irregular" word forms.
		|
		*/

        'plural' => array(
            '/(quiz)$/i' => "$1zes",
            '/^(ox)$/i' => "$1en",
            '/([m|l])ouse$/i' => "$1ice",
            '/(matr|vert|ind)ix|ex$/i' => "$1ices",
            '/(x|ch|ss|sh)$/i' => "$1es",
            '/([^aeiouy]|qu)y$/i' => "$1ies",
            '/(hive)$/i' => "$1s",
            '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
            '/(shea|lea|loa|thie)f$/i' => "$1ves",
            '/sis$/i' => "ses",
            '/([ti])um$/i' => "$1a",
            '/(tomat|potat|ech|her|vet)o$/i' => "$1oes",
            '/(bu)s$/i' => "$1ses",
            '/(alias)$/i' => "$1es",
            '/(octop)us$/i' => "$1i",
            '/(ax|test)is$/i' => "$1es",
            '/(us)$/i' => "$1es",
            '/s$/i' => "s",
            '/$/' => "s"
        ),

        'singular' => array(
            '/(quiz)zes$/i' => "$1",
            '/(matr)ices$/i' => "$1ix",
            '/(vert|ind)ices$/i' => "$1ex",
            '/^(ox)en$/i' => "$1",
            '/(alias)es$/i' => "$1",
            '/(octop|vir)i$/i' => "$1us",
            '/(cris|ax|test)es$/i' => "$1is",
            '/(shoe)s$/i' => "$1",
            '/(o)es$/i' => "$1",
            '/(bus)es$/i' => "$1",
            '/([m|l])ice$/i' => "$1ouse",
            '/(x|ch|ss|sh)es$/i' => "$1",
            '/(m)ovies$/i' => "$1ovie",
            '/(s)eries$/i' => "$1eries",
            '/([^aeiouy]|qu)ies$/i' => "$1y",
            '/([lr])ves$/i' => "$1f",
            '/(tive)s$/i' => "$1",
            '/(hive)s$/i' => "$1",
            '/(li|wi|kni)ves$/i' => "$1fe",
            '/(shea|loa|lea|thie)ves$/i' => "$1f",
            '/(^analy)ses$/i' => "$1sis",
            '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => "$1$2sis",
            '/([ti])a$/i' => "$1um",
            '/(n)ews$/i' => "$1ews",
            '/(h|bl)ouses$/i' => "$1ouse",
            '/(corpse)s$/i' => "$1",
            '/(us)es$/i' => "$1",
            '/(us|ss)$/i' => "$1",
            '/s$/i' => "",
        ),

        'irregular' => array(
            'child' => 'children',
            'foot' => 'feet',
            'goose' => 'geese',
            'man' => 'men',
            'move' => 'moves',
            'person' => 'people',
            'sex' => 'sexes',
            'tooth' => 'teeth',
        ),

        'uncountable' => array(
            'audio',
            'equipment',
            'deer',
            'fish',
            'gold',
            'information',
            'money',
            'rice',
            'police',
            'series',
            'sheep',
            'species',
            'moose',
            'chassis',
            'traffic',
        ),

        /*
		|--------------------------------------------------------------------------
		| ASCII Characters
		|--------------------------------------------------------------------------
		|
		| This array contains foreign characters and their 7-bit ASCII equivalents.
		| The array is used by the "ascii" method on the Str class to get strings
		| ready for inclusion in a URL slug.
		|
		| Of course, the "ascii" method may also be used by you for whatever your
		| application requires. Feel free to add any characters we missed, and be
		| sure to let us know about them!
		|
		*/

        'ascii' => array(

            '/æ|ǽ/' => 'ae',
            '/œ/' => 'oe',
            '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|А/' => 'A',
            '/à|á|â|ã|ä|å|ǻ|ā|ă|ą|ǎ|ª|а/' => 'a',
            '/Б/' => 'B',
            '/б/' => 'b',
            '/Ç|Ć|Ĉ|Ċ|Č|Ц/' => 'C',
            '/ç|ć|ĉ|ċ|č|ц/' => 'c',
            '/Ð|Ď|Đ|Д/' => 'Dj',
            '/ð|ď|đ|д/' => 'dj',
            '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Е|Ё|Э/' => 'E',
            '/è|é|ê|ë|ē|ĕ|ė|ę|ě|е|ё|э/' => 'e',
            '/Ф/' => 'F',
            '/ƒ|ф/' => 'f',
            '/Ĝ|Ğ|Ġ|Ģ|Г/' => 'G',
            '/ĝ|ğ|ġ|ģ|г/' => 'g',
            '/Ĥ|Ħ|Х/' => 'H',
            '/ĥ|ħ|х/' => 'h',
            '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|И/' => 'I',
            '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|и/' => 'i',
            '/Ĵ|Й/' => 'J',
            '/ĵ|й/' => 'j',
            '/Ķ|К/' => 'K',
            '/ķ|к/' => 'k',
            '/Ĺ|Ļ|Ľ|Ŀ|Ł|Л/' => 'L',
            '/ĺ|ļ|ľ|ŀ|ł|л/' => 'l',
            '/М/' => 'M',
            '/м/' => 'm',
            '/Ñ|Ń|Ņ|Ň|Н/' => 'N',
            '/ñ|ń|ņ|ň|ŉ|н/' => 'n',
            '/Ö|Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|О/' => 'O',
            '/ö|ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|о/' => 'o',
            '/П/' => 'P',
            '/п/' => 'p',
            '/Ŕ|Ŗ|Ř|Р/' => 'R',
            '/ŕ|ŗ|ř|р/' => 'r',
            '/Ś|Ŝ|Ş|Ș|Š|С/' => 'S',
            '/ś|ŝ|ş|ș|š|ſ|с/' => 's',
            '/Ţ|Ț|Ť|Ŧ|Т/' => 'T',
            '/ţ|ț|ť|ŧ|т/' => 't',
            '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ü|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|У/' => 'U',
            '/ù|ú|û|ũ|ū|ŭ|ů|ü|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|у/' => 'u',
            '/В/' => 'V',
            '/в/' => 'v',
            '/Ý|Ÿ|Ŷ|Ы/' => 'Y',
            '/ý|ÿ|ŷ|ы/' => 'y',
            '/Ŵ/' => 'W',
            '/ŵ/' => 'w',
            '/Ź|Ż|Ž|З/' => 'Z',
            '/ź|ż|ž|з/' => 'z',
            '/Æ|Ǽ/' => 'AE',
            '/ß/' => 'ss',
            '/Ĳ/' => 'IJ',
            '/ĳ/' => 'ij',
            '/Œ/' => 'OE',
            '/Ч/' => 'Ch',
            '/ч/' => 'ch',
            '/Ю/' => 'Ju',
            '/ю/' => 'ju',
            '/Я/' => 'Ja',
            '/я/' => 'ja',
            '/Ш/' => 'Sh',
            '/ш/' => 'sh',
            '/Щ/' => 'Shch',
            '/щ/' => 'shch',
            '/Ж/' => 'Zh',
            '/ж/' => 'zh',

        ),

    );

    return array_get($strings, $key, $strings);
}


class Pluralizer
{

    /**
     * The "strings" configuration array.
     *
     * @var array
     */
    protected $config;

    /**
     * The cached copies of the plural inflections.
     */
    protected $plural = array();

    /**
     * The cached copies of the singular inflections.
     *
     * @var array
     */
    protected $singular = array();

    /**
     * Create a new pluralizer instance.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Get the singular form of the given word.
     *
     * @param  string  $value
     * @return string
     */
    public function singular($value)
    {
        // First we'll check the cache of inflected values. We cache each word that
        // is inflected so we don't have to spin through the regular expressions
        // each time we need to inflect a given value for the developer.
        if (isset($this->singular[$value])) {
            return $this->singular[$value];
        }

        // English words may be automatically inflected using regular expressions.
        // If the word is English, we'll just pass off the word to the automatic
        // inflection method and return the result, which is cached.
        $irregular = $this->config['irregular'];

        $result = $this->auto($value, $this->config['singular'], $irregular);

        return $this->singular[$value] = $result ? $result : $value;
    }

    /**
     * Get the plural form of the given word.
     *
     * @param  string  $value
     * @param  int     $count
     * @return string
     */
    public function plural($value, $count = 2)
    {
        if ($count == 1) return $value;

        // First we'll check the cache of inflected values. We cache each word that
        // is inflected so we don't have to spin through the regular expressions
        // each time we need to inflect a given value for the developer.
        if (isset($this->plural[$value])) {
            return $this->plural[$value];
        }

        // English words may be automatically inflected using regular expressions.
        // If the word is English, we'll just pass off the word to the automatic
        // inflection method and return the result, which is cached.
        $irregular = array_flip($this->config['irregular']);

        $result = $this->auto($value, $this->config['plural'], $irregular);

        return $this->plural[$value] = $result;
    }

    /**
     * Perform auto inflection on an English word.
     *
     * @param  string  $value
     * @param  array   $source
     * @param  array   $irregular
     * @return string
     */
    protected function auto($value, $source, $irregular)
    {
        // If the word hasn't been cached, we'll check the list of words that
        // that are "uncountable". This should be a quick look up since we
        // can just hit the array directly for the value.
        if (in_array(Str::lower($value), $this->config['uncountable'])) {
            return $value;
        }

        // Next, we will check the "irregular" patterns, which contain words
        // like "children" and "teeth" which can not be inflected using the
        // typically used regular expression matching approach.
        foreach ($irregular as $irregular => $pattern) {
            if (preg_match($pattern = '/' . $pattern . '$/i', $value)) {
                return preg_replace($pattern, $irregular, $value);
            }
        }

        // Finally we'll spin through the array of regular expressions and
        // and look for matches for the word. If we find a match we will
        // cache and return the inflected value for quick look up.
        foreach ($source as $pattern => $inflected) {
            if (preg_match($pattern, $value)) {
                return preg_replace($pattern, $inflected, $value);
            }
        }
    }
}

if (!defined('MB_STRING')) {
    define('MB_STRING', (int) function_exists('mb_get_info'));
}

class Str
{

    /**
     * The pluralizer instance.
     *
     * @var Pluralizer
     */
    public static $pluralizer;

    /**
     * Cache application encoding locally to save expensive calls to Config::get().
     *
     * @var string
     */
    public static $encoding = null;

    /**
     * Get the appliction.encoding without needing to request it from Config::get() each time.
     *
     * @return string
     */
    protected static function encoding()
    {
        return self::$encoding ? self::$encoding : 'utf-8';
    }

    /**
     * Get the length of a string.
     *
     * <code>
     *		// Get the length of a string
     *		$length = Str::length('Taylor Otwell');
     *
     *		// Get the length of a multi-byte string
     *		$length = Str::length('Τάχιστη')
     * </code>
     *
     * @param  string  $value
     * @return int
     */
    public static function length($value)
    {
        return (MB_STRING) ? mb_strlen($value, self::encoding()) : strlen($value);
    }

    /**
     * Convert a string to lowercase.
     *
     * <code>
     *		// Convert a string to lowercase
     *		$lower = Str::lower('Taylor Otwell');
     *
     *		// Convert a multi-byte string to lowercase
     *		$lower = Str::lower('Τάχιστη');
     * </code>
     *
     * @param  string  $value
     * @return string
     */
    public static function lower($value)
    {
        return (MB_STRING) ? mb_strtolower($value, self::encoding()) : strtolower($value);
    }

    /**
     * Convert a string to uppercase.
     *
     * <code>
     *		// Convert a string to uppercase
     *		$upper = Str::upper('Taylor Otwell');
     *
     *		// Convert a multi-byte string to uppercase
     *		$upper = Str::upper('Τάχιστη');
     * </code>
     *
     * @param  string  $value
     * @return string
     */
    public static function upper($value)
    {
        return (MB_STRING) ? mb_strtoupper($value, self::encoding()) : strtoupper($value);
    }

    /**
     * Convert a string to title case (ucwords equivalent).
     *
     * <code>
     *		// Convert a string to title case
     *		$title = Str::title('taylor otwell');
     *
     *		// Convert a multi-byte string to title case
     *		$title = Str::title('νωθρού κυνός');
     * </code>
     *
     * @param  string  $value
     * @return string
     */
    public static function title($value)
    {
        if (MB_STRING) {
            return mb_convert_case($value, MB_CASE_TITLE, self::encoding());
        }

        return ucwords(strtolower($value));
    }

    /**
     * Limit the number of characters in a string.
     *
     * <code>
     *		// Returns "Tay..."
     *		echo Str::limit('Taylor Otwell', 3);
     *
     *		// Limit the number of characters and append a custom ending
     *		echo Str::limit('Taylor Otwell', 3, '---');
     * </code>
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    public static function limit($value, $limit = 100, $end = '...')
    {
        if (self::length($value) <= $limit) return $value;

        if (MB_STRING) {
            return mb_substr($value, 0, $limit, self::encoding()) . $end;
        }

        return substr($value, 0, $limit) . $end;
    }

    /**
     * Limit the number of chracters in a string including custom ending
     * 
     * <code>
     *		// Returns "Taylor..."
     *		echo Str::limit_exact('Taylor Otwell', 9);
     *
     *		// Limit the number of characters and append a custom ending
     *		echo Str::limit_exact('Taylor Otwell', 9, '---');
     * </code>
     * 
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    public static function limit_exact($value, $limit = 100, $end = '...')
    {
        if (self::length($value) <= $limit) return $value;

        $limit -= self::length($end);

        return self::limit($value, $limit, $end);
    }

    /**
     * Limit the number of words in a string.
     *
     * <code>
     *		// Returns "This is a..."
     *		echo Str::words('This is a sentence.', 3);
     *
     *		// Limit the number of words and append a custom ending
     *		echo Str::words('This is a sentence.', 3, '---');
     * </code>
     *
     * @param  string  $value
     * @param  int     $words
     * @param  string  $end
     * @return string
     */
    public static function words($value, $words = 100, $end = '...')
    {
        if (trim($value) == '') return '';

        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $value, $matches);

        if (self::length($value) == self::length($matches[0])) {
            $end = '';
        }

        return rtrim($matches[0]) . $end;
    }

    /**
     * Get the singular form of the given word.
     *
     * @param  string  $value
     * @return string
     */
    public static function singular($value)
    {
        return self::pluralizer()->singular($value);
    }

    /**
     * Get the plural form of the given word.
     *
     * <code>
     *		// Returns the plural form of "child"
     *		$plural = Str::plural('child', 10);
     *
     *		// Returns the singular form of "octocat" since count is one
     *		$plural = Str::plural('octocat', 1);
     * </code>
     *
     * @param  string  $value
     * @param  int     $count
     * @return string
     */
    public static function plural($value, $count = 2)
    {
        return self::pluralizer()->plural($value, $count);
    }

    /**
     * Get the pluralizer instance.
     *
     * @return Pluralizer
     */
    protected static function pluralizer()
    {
        return self::$pluralizer ? self::$pluralizer : self::$pluralizer = new Pluralizer(strStrings());
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * <code>
     *		// Returns "this-is-my-blog-post"
     *		$slug = Str::slug('This is my blog post!');
     *
     *		// Returns "this_is_my_blog_post"
     *		$slug = Str::slug('This is my blog post!', '_');
     * </code>
     *
     * @param  string  $title
     * @param  string  $separator
     * @return string
     */
    public static function slug($title, $separator = '-')
    {
        $title = self::ascii($title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', self::lower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Convert a string to 7-bit ASCII.
     *
     * This is helpful for converting UTF-8 strings for usage in URLs, etc.
     *
     * @param  string  $value
     * @return string
     */
    public static function ascii($value)
    {
        $foreign = strStrings('ascii');

        $value = preg_replace(array_keys($foreign), array_values($foreign), $value);

        return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $value);
    }

    /**
     * Convert a string to an underscored, camel-cased class name.
     *
     * This method is primarily used to format task and controller names.
     *
     * <code>
     *		// Returns "Task_Name"
     *		$class = Str::classify('task_name');
     *
     *		// Returns "Taylor_Otwell"
     *		$class = Str::classify('taylor otwell')
     * </code>
     *
     * @param  string  $value
     * @return string
     */
    public static function classify($value)
    {
        $search = array('_', '-', '.', '/');

        return str_replace(' ', '_', self::title(str_replace($search, ' ', $value)));
    }

    /**
     * Return the "URI" style segments in a given string.
     *
     * @param  string  $value
     * @return array
     */
    public static function segments($value)
    {
        return array_diff(explode('/', trim($value, '/')), array(''));
    }

    /**
     * Generate a random alpha or alpha-numeric string.
     *
     * <code>
     *		// Generate a 40 character random alpha-numeric string
     *		echo Str::random(40);
     *
     *		// Generate a 16 character random alphabetic string
     *		echo Str::random(16, 'alpha');
     * </code>
     *
     * @param  int	   $length
     * @param  string  $type
     * @return string
     */
    public static function random($length, $type = 'alnum')
    {
        return substr(str_shuffle(str_repeat(self::pool($type), 5)), 0, $length);
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  string  $pattern
     * @param  string  $value
     * @return bool
     */
    public static function is($pattern, $value)
    {
        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the URI starts with a given pattern
        // such as "library/*". This is only done when not root.
        if ($pattern !== '/') {
            $pattern = str_replace('*', '(.*)', $pattern) . '\z';
        } else {
            $pattern = '^/$';
        }

        return preg_match('#' . $pattern . '#', $value);
    }

    /**
     * Get the character pool for a given type of random string.
     *
     * @param  string  $type
     * @return string
     */
    protected static function pool($type)
    {
        switch ($type) {
            case 'alpha':
                return 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

            case 'alnum':
                return '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

            default:
                throw new Exception("Invalid random string type [$type].");
        }
    }
}
