<?php

/**
 * This class is a port of Laravel's blade templating system.
 * See usage docs in Laravel framework site.
 *
 * @author      Miguel Ayllón
 * @package     Blade
 * @category    Libraries
 * @version     1.0.2
 * @url         https://github.com/laperla/codeigniter-Blade 
 *
 */
class Blade
{

    /**
     * All of the compiler methods used by Blade.
     *
     * @var array
     */
    protected $_compilers = array(
        'extensions',
        'comments',
        'layouts',
        'extends',
        'echos',
        'forelse',
        'empty',
        'endforelse',
        'structure_openings',
        'structure_closings',
        'else',
        'unless',
        'endunless',
        'includes',
        'render_each',
        'render',
        'yields',
        'yield_sections',
        'section_start',
        'section_end',
    );

    /**
     * Stack of current sections being buffered
     *
     * @var array
     */
    protected $_last_section = array();

    /**
     * Array of sections content
     *
     * @var array
     */
    protected $_sections = array();

    /**
     * An array of user defined compilers.
     *
     * @var array
     */
    protected static $_extensions = array();

    /**
     * Global data array for templates
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Template file extension
     *
     * @var string
     */
    public static $blade_ext = '.blade.php';

    /**
     * Cache expire time
     *
     * @var int
     */
    public $cache_time = 3600;


    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }


    public function __unset($name)
    {
        unset($this->_data[$name]);
    }


    public function __get($name)
    {
        if (key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }
        return false;
    }

    public $content;


    /**
     * Sets global data for template
     *
     * @param string $name
     * @param mixed $value
     * @return Blade
     */
    public function set($name, $value)
    {
        $this->_data[$name] = $value;
        return $this;
    }


    /**
     * Appends or concats a value to a template global data if type is array
     * or string respectively
     *
     * @param string $name
     * @param mixed $value
     * @return Blade
     */
    public function append($name, $value)
    {
        if (is_array($this->_data[$name])) {
            $this->_data[$name][] = $value;
        } else {
            $this->_data[$name] .= $value;
        }

        return $this;
    }


    /**
     * Sets multiple global data in array format
     *
     * @param array $data
     * @return Blade
     */
    public function set_data($data)
    {
        $this->_data = array_merge($this->_data, $data);
        return $this;
    }


    /**
     * Adds a custom compiler function
     *
     * @param mixed $compiler
     * @return Blade
     */
    public static function extend($compiler)
    {
        self::$_extensions[] = $compiler;
    }


    /**
     * Outputs template content. You can also pass an array of global data.
     * If $return is TRUE then returns the template as a string.
     *
     * @param string $template
     * @param array $data
     * @param bool $return
     * @return string
     */
    public static function render($template, $data = NULL, $return = FALSE)
    {
        $o = new Blade;

        if (isset($data)) {
            $o->set_data($data);
        }

        /**
         * FIXME desnecessário
         */
        /*if(class_exists('Session')) {
            $o->set('errors', array_get($_SESSION, 'errors', new Messages));
        }*/

        // Compile and run template
        $compiled = $o->_compile($template);
        $content = $o->_run($compiled, $o->_data);

        /**
         * Restore vue | handlersbar
         * 
         * ${ var }} -> {{ var }}
         * 
         * Don't effect on js `string${with_var}string`
         */
        $content = preg_replace('#\$\{(.*?)\}\}#si', '{{$1}}', $content);

        $o->content = $content;

        return $content;
    }


    /**
     * Find the full path to a view file. Shows an error if file not found.
     *
     * @param string $view
     * @return string
     */
    protected function _find_view($view)
    {
        $path = viewPath($view);

        // File not found
        if (!$path) {
            throw new Exception('[Blade] Unable to find view: ' . $view);
        }

        return $path;
    }


    /**
     * Compiles a template and stores it in the cache.
     *
     * @param string $template
     * @return string
     */
    protected function _compile($template)
    {
        // Prepare template info
        $view_path = $this->_find_view($template);

        $cache_dir = ROOT_PATH . 'cache_views';

        $cache_enabled = false;

        if (is_dir($cache_dir) && is_writable($cache_dir)) {

            $cache_enabled = true;

            $cache_id = 'view-' . md5($view_path);

            $cache_file = $cache_dir . '/' . $cache_id;

            // Test if a compiled version exists in the cache
            if (file_exists($cache_file) && $compiled = file_get_contents($cache_file)) {
                // Return cache version if the template was not updated
                if (filemtime($cache_file) > filemtime($view_path)) {
                    //return $compiled;
                }
            }
        }

        // Template content
        $template = file_get_contents($view_path);

        // Compilers
        foreach ($this->_compilers as $compiler) {
            $method = "_compile_{$compiler}";
            $template = $this->$method($template);
        }

        if ($cache_enabled) {
            file_put_contents($cache_file, $template);
        }

        // Return compiled template
        return $template;
    }


    /**
     * Runs a compiled template with its variables
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function _run($template, $data = NULL)
    {
        if (is_array($data)) {
            extract($data);
        }

        ob_start();
        eval(' ?>' . $template . '<?php ');
        $content = ob_get_clean();

        return $content;
    }


    /**
     * Get a template content for use inside the current template. Inherits
     * all global data, combined with local data passed as second argument.
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function _include($template, $data = NULL)
    {
        // Merge local data with global data
        $data = isset($data) ? array_merge($this->_data, $data) : $this->_data;

        // Compile and run template
        $compiled = $this->_compile($template);
        return $this->_run($compiled, $data);
    }


    /**
     * Gets a section content
     *
     * @param string $section
     * @return string
     */
    protected function _yield($section)
    {
        return isset($this->_sections[$section]) ? str_replace('@parent', '', $this->_sections[$section]) : '';
    }


    /**
     * Starts buffering section content
     *
     * @param string $section
     */
    protected function _section_start($section)
    {
        array_push($this->_last_section, $section);
        ob_start();
    }


    /**
     * Stops buffering section content. Returns the current section name.
     *
     * @return string
     */
    protected function _section_end()
    {
        $last = array_pop($this->_last_section);
        $this->_section_extend($last, ob_get_clean());

        return $last;
    }


    /**
     * Stores section content, replacing '@parent' with the previous section
     * content if any.
     *
     * @param string $section
     * @param string $content
     */
    protected function _section_extend($section, $content)
    {
        if (isset($this->_sections[$section])) {
            $this->_sections[$section] = str_replace('@parent', $content, $this->_sections[$section]);
        } else {
            $this->_sections[$section] = $content;
        }
    }


    // -------------------------------------------------------
    //
    //                       COMPILERS
    //
    // -------------------------------------------------------


    /**
     * Get the regular expression for a generic Blade function.
     *
     * @param  string  $function
     * @return string
     */
    public function matcher($function)
    {
        return '/(\s*)@' . $function . '(\s*\(.*\))/';
    }


    /**
     * Rewrites Blade comments into PHP comments.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_comments($value)
    {
        $value = preg_replace('/\{\{--(.+?)(--\}\})?\n/', "<?php // $1 ?>", $value);

        return preg_replace('/\{\{--((.|\s)*?)--\}\}/', "<?php /* $1 */ ?>\n", $value);
    }


    /**
     * Rewrites Blade echo statements into PHP echo statements.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_echos($value)
    {
        return preg_replace('/\{\{(.+?)\}\}/', '<?php echo $1; ?>', $value);
    }


    /**
     * Rewrites Blade "for else" statements into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_forelse($value)
    {
        preg_match_all('/(\s*)@forelse(\s*\(.*\))(\s*)/', $value, $matches);

        foreach ($matches[0] as $forelse) {
            preg_match('/\$[^\s]*/', $forelse, $variable);

            // Once we have extracted the variable being looped against, we can add
            // an if statement to the start of the loop that checks if the count
            // of the variable being looped against is greater than zero.
            $if = "<?php if (count( {$variable[0]} ) > 0): ?>";

            $search = '/(\s*)@forelse(\s*\(.*\))/';

            $replace = '$1' . $if . '<?php foreach$2: ?>';

            $blade = preg_replace($search, $replace, $forelse);

            // Finally, once we have the check prepended to the loop we'll replace
            // all instances of this forelse syntax in the view content of the
            // view being compiled to Blade syntax with real PHP syntax.
            $value = str_replace($forelse, $blade, $value);
        }

        return $value;
    }


    /**
     * Rewrites Blade "empty" statements into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_empty($value)
    {
        return str_replace('@empty', '<?php endforeach; ?><?php else: ?>', $value);
    }


    /**
     * Rewrites Blade "forelse" endings into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_endforelse($value)
    {
        return str_replace('@endforelse', '<?php endif; ?>', $value);
    }


    /**
     * Rewrites Blade structure openings into PHP structure openings.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_structure_openings($value)
    {
        $pattern = '/(\s*)@(if|elseif|foreach|for|while)(\s*\(.*\))/';

        return preg_replace($pattern, '$1<?php $2$3: ?>', $value);
    }


    /**
     * Rewrites Blade structure closings into PHP structure closings.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_structure_closings($value)
    {
        $pattern = '/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/';

        return preg_replace($pattern, '$1<?php $2; ?>$3', $value);
    }


    /**
     * Rewrites Blade else statements into PHP else statements.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_else($value)
    {
        return preg_replace('/(\s*)@(else)(\s*)/', '$1<?php $2: ?>$3', $value);
    }


    /**
     * Rewrites Blade "unless" statements into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_unless($value)
    {
        $pattern = '/(\s*)@unless(\s*\(.*\))/';

        return preg_replace($pattern, '$1<?php if( ! ($2)): ?>', $value);
    }


    /**
     * Rewrites Blade "unless" endings into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_endunless($value)
    {
        return str_replace('@endunless', '<?php endif; ?>', $value);
    }


    /**
     * Execute user defined compilers.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_extensions($value)
    {
        foreach (self::$_extensions as $compiler) {
            $value = call_user_func($compiler, $value);
        }

        return $value;
    }


    /**
     * Rewrites Blade @include statements into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_includes($value)
    {
        $pattern = self::matcher('include');

        return preg_replace($pattern, '$1<?php echo $this->_include$2; ?>', $value);
    }

    /**
     * Rewrites Blade @render statements into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_render($value)
    {
        $pattern = self::matcher('render');

        return preg_replace($pattern, '$1<?php echo render$2; ?>', $value);
    }

    /**
     * Rewrites Blade @render_each statements into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_render_each($value)
    {
        $pattern = static::matcher('render_each');

        return preg_replace($pattern, '$1<?php echo render_each$2; ?>', $value);
    }


    /**
     * Rewrites Blade "@layout" expressions into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_layouts($value, $matcher = 'layout')
    {
        $pattern = $this->matcher($matcher);

        // Find "@layout|extend" expressions
        if (!preg_match_all($pattern, $value, $matches, PREG_SET_ORDER)) {
            return $value;
        }

        // Delete "@layout|extend" expressions
        $value = preg_replace(str_replace(')/', ')(\n*)/', $pattern), '', $value);

        // Include layouts at the end of template
        foreach ($matches as $set) {
            $value .= "\n\n" . $set[1] . '<?php echo $this->_include' . $set[2] . "; ?>\n";
        }

        return $value;
    }

    protected function _compile_extends($value, $matcher = 'extend')
    {
        return $this->_compile_layouts($value, $matcher);
    }


    /**
     * Rewrites Blade @yield statements into Section statements.
     *
     * The Blade @yield statement is a shortcut to the Section::yield method.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_yields($value)
    {
        $pattern = $this->matcher('yield');

        return preg_replace($pattern, '$1<?php echo $this->_yield$2; ?>', $value);
    }


    /**
     * Rewrites Blade @section statements into Section statements.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_section_start($value)
    {
        $pattern = $this->matcher('section');

        return preg_replace($pattern, '$1<?php $this->_section_start$2; ?>', $value);
    }


    /**
     * Rewrites Blade @endsection statements into Section statements.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_section_end($value)
    {
        $replace = '<?php $this->_section_end(); ?>';

        return str_replace(array('@endsection', '@end'), $replace, $value);
    }


    /**
     * Rewrites Blade yield section statements into valid PHP.
     *
     * @return string
     */
    protected function _compile_yield_sections($value)
    {
        $replace = '<?php echo $this->_yield($this->_section_end()); ?>';

        return str_replace('@yield_section', $replace, $value);
    }
}


// END Blade class

/* End of file Blade.php */