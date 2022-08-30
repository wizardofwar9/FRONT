<?php
namespace WPUmbrella\Services;

if (!defined('ABSPATH')) {
    exit;
}

class Option
{
    /**
     * @var array
     */
    protected $optionsDefault = [
        'api_key' => '',
        'allowed' => false,
    ];

    /**
     * Get options default.
     *
     * @return array
     */
    public function getOptionsDefault()
    {
        return $this->optionsDefault;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $options = wp_parse_args(get_option(WP_UMBRELLA_SLUG), $this->getOptionsDefault());

        return apply_filters(
            'wp_umbrella_get_options',
            $options
        );
    }

    protected function getOptionByConstant($name)
    {
        $constant = null;
        switch ($name) {
            case 'project_id':
                $constant = 'WP_UMBRELLA_PROJECT_ID';
                break;
            case 'api_key':
                $constant = 'WP_UMBRELLA_API_KEY';
                break;
        }

        if ($constant === null) {
            return null;
        }

        if (!defined('WP_UMBRELLA_PROJECT_ID') && $constant === 'WP_UMBRELLA_PROJECT_ID') {
            return null;
        }
        if (!defined('WP_UMBRELLA_API_KEY') && $constant === 'WP_UMBRELLA_API_KEY') {
            return null;
        }

        return constant($constant);
    }

    /**
     * @param string $name Key name option
     *
     * @return array
     */
    public function getOption($name)
    {
        if ($name === 'allowed') {
            $projectId = $this->getOptionByConstant('project_id');
            $apiKey = $this->getOptionByConstant('api_key');
            if ($projectId && $apiKey) {
                return true;
            }
        }

        $value = $this->getOptionByConstant($name);

        if ($value !== null) {
            return $value;
        }

        $options = $this->getOptions();

        if (!array_key_exists($name, $options)) {
            return null;
        }

        return apply_filters('wp_umbrella_' . $name . '_option', $options[$name]);
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        update_option(WP_UMBRELLA_SLUG, $options);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOptionByKey($key, $value)
    {
        $options = $this->getOptions();
        $options[$key] = $value;
        $this->setOptions($options);

        return $this;
    }
}
